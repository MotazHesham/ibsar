<?php

namespace App\Services;

use App\Models\BeneficiaryOrder;
use App\Models\ServiceLoan;
use App\Models\ServiceLoanPayment;
use App\Models\Setting;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class OdooService
{

    /**
     * Ensure partner exists and create outbound payment for loan disbursement.
     */
    public function syncLoanPayment(BeneficiaryOrder $beneficiaryOrder): void
    {
        if(getSetting('odoo_activation') == 'disable'){
            return;
        }
        try {
            if (!$beneficiaryOrder->relationLoaded('beneficiary')) {
                $beneficiaryOrder->load('beneficiary.user', 'serviceLoan');
            }

            $uid = $this->authenticate();
            if (!$uid) {
                Log::warning('Odoo auth failed');
                return;
            }

            $partnerId = $this->ensurePartner($uid, $beneficiaryOrder);
            if (!$partnerId) {
                Log::warning('Odoo partner creation/lookup failed');
                return;
            }

            $amount = (float) ($beneficiaryOrder->serviceLoan->amount ?? 0);
            if ($amount <= 0) {
                Log::info('Odoo payment skipped: non-positive amount', ['order_id' => $beneficiaryOrder->id, 'amount' => $amount]);
                return;
            }

            $this->createOutboundPayment($uid, $partnerId, $amount, 'Loan disbursement for Order #' . $beneficiaryOrder->id);
        } catch (\Throwable $e) {
            Log::error('Odoo syncLoanPayment error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
        }
    }

    /**
     * Ensure partner exists and create inbound payment for a loan installment (repayment).
     */
    public function payInstallment(ServiceLoanPayment $payment, ServiceLoan $serviceLoan): void
    {
        if(getSetting('odoo_activation') == 'disable'){
            return;
        }
        try {
            if (!$serviceLoan->relationLoaded('beneficiary_order')) {
                $serviceLoan->load('beneficiary_order.beneficiary.user');
            }

            $beneficiaryOrder = $serviceLoan->beneficiary_order;
            if (!$beneficiaryOrder) {
                Log::warning('Odoo payInstallment skipped: missing beneficiary_order', ['service_loan_id' => $serviceLoan->id]);
                return;
            }

            $uid = $this->authenticate();
            if (!$uid) {
                Log::warning('Odoo auth failed');
                return;
            }

            $partnerId = $this->ensurePartner($uid, $beneficiaryOrder);
            if (!$partnerId) {
                Log::warning('Odoo partner creation/lookup failed');
                return;
            }

            $this->createInboundPayment($uid, $partnerId, $payment->amount, 'Loan installment for Order #' . $beneficiaryOrder->id);
        } catch (\Throwable $e) {
            Log::error('Odoo payInstallment error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
        }
    }

    protected function authenticate(): ?int
    {
        $payload = [
            'jsonrpc' => '2.0',
            'method' => 'call',
            'params' => [
                'service' => 'common',
                'method' => 'authenticate',
                'args' => [
                    getSetting('odoo_db'),
                    getSetting('odoo_username'),
                    getSetting('odoo_password'),
                    [],
                ],
            ],
            'id' => time(),
        ];

        $response = (new Client([
            'base_uri' => getSetting('odoo_url'),
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'timeout' => 15,
        ]))->post('/jsonrpc', [
            'body' => json_encode($payload),
        ]);
        $data = json_decode((string) $response->getBody(), true);

        $result = $data['result'] ?? null;
        Log::info('Odoo authentication result', [
            'success' => !is_null($result),
            'user_id' => $result,
            'response_data' => $data
        ]);

        return $result;
    }

    protected function ensurePartner(int $uid, BeneficiaryOrder $beneficiaryOrder): ?int
    {
        $beneficiary = $beneficiaryOrder->beneficiary;
        $user = $beneficiary?->user;
        $name = $user?->name ?: ('Beneficiary #' . $beneficiary?->id);
        $ref = $user->id;
        $email = $user?->email;
        $phone = $user?->phone;

        // Search existing partner by ref
        $ids = $this->executeKw($uid, 'res.partner', 'search', [[['ref', '=', $ref]]], ['limit' => 1]);
        Log::info('Odoo partner search result', [
            'search_ref' => $ref,
            'found_ids' => $ids,
            'count' => is_array($ids) ? count($ids) : 0
        ]);

        if (is_array($ids) && count($ids) > 0) {
            $partnerId = (int) $ids[0];
            Log::info('Odoo partner found', ['partner_id' => $partnerId]);
            // Ensure partner is marked as vendor (supplier)
            $this->executeKw($uid, 'res.partner', 'write', [[(int) $partnerId], ['supplier_rank' => 1]]);
            return $partnerId;
        }

        // Create new partner
        $partnerData = [
            'name' => $name,
            'ref' => $ref,
            'email' => $email,
            'phone' => $phone,
            'customer_rank' => 1,
            'supplier_rank' => 1,
        ];

        $partnerId = $this->executeKw($uid, 'res.partner', 'create', [$partnerData]);

        Log::info('Odoo partner creation result', [
            'partner_data' => $partnerData,
            'created_id' => $partnerId,
            'success' => !is_null($partnerId)
        ]);

        return is_int($partnerId) ? $partnerId : (is_numeric($partnerId) ? (int) $partnerId : null);
    }

    protected function createOutboundPayment(int $uid, int $partnerId, float $amount, string $memo): void
    {
        // Always discover a valid journal and outbound payment method line dynamically (prefer manual method for immediate posting)
        $fallback = $this->findJournalWithManualPaymentMethodLine($uid, 'outbound')
            ?: $this->findJournalWithPaymentMethodLine($uid, 'outbound');
        if (!is_array($fallback)) {
            Log::error('Odoo payment creation failed: No journal with outbound payment method lines found', []);
            return;
        }
        $journalId = (int) $fallback[0];
        $methodLineId = (int) $fallback[1];
        Log::info('Odoo using discovered journal and payment method line', [
            'journal_id' => $journalId,
            'payment_method_line_id' => $methodLineId,
        ]);

        // Create payment record
        $paymentData = [
            'payment_type' => 'outbound', // money going out from company
            'partner_type' => 'supplier',
            'partner_id' => $partnerId,
            'amount' => $amount,
            'journal_id' => $journalId,
            'payment_method_line_id' => $methodLineId,
            'date' => date('Y-m-d'),
            'payment_reference' => $memo,
            'state' => 'paid',
        ];

        $paymentId = $this->executeKw($uid, 'account.payment', 'create', [$paymentData]);

        Log::info('Odoo payment creation result', [
            'payment_data' => $paymentData,
            'created_payment_id' => $paymentId,
            'success' => !is_null($paymentId)
        ]);
    }

    protected function createInboundPayment(int $uid, int $partnerId, float $amount, string $memo): void
    {
        // Discover a valid inbound journal/payment method line (prefer manual)
        $fallback = $this->findJournalWithManualPaymentMethodLine($uid, 'inbound')
            ?: $this->findJournalWithPaymentMethodLine($uid, 'inbound');
        if (!is_array($fallback)) {
            Log::error('Odoo inbound payment creation failed: No journal with inbound payment method lines found', []);
            return;
        }
        $journalId = (int) $fallback[0];
        $methodLineId = (int) $fallback[1];
        Log::info('Odoo using discovered inbound journal and payment method line', [
            'journal_id' => $journalId,
            'payment_method_line_id' => $methodLineId,
        ]);

        $paymentData = [
            'payment_type' => 'inbound', // money coming in to company
            'partner_type' => 'customer',
            'partner_id' => $partnerId,
            'amount' => $amount,
            'journal_id' => $journalId,
            'payment_method_line_id' => $methodLineId,
            'date' => date('Y-m-d'),
            'payment_reference' => $memo,
            'state' => 'paid',
        ];

        $paymentId = $this->executeKw($uid, 'account.payment', 'create', [$paymentData]);

        Log::info('Odoo inbound payment creation result', [
            'payment_data' => $paymentData,
            'created_payment_id' => $paymentId,
            'success' => !is_null($paymentId)
        ]);
    }

    /**
     * Resolve a payment method line compatible with the journal and payment type.
     */
    protected function resolvePaymentMethodLineId(int $uid, int $journalId, string $paymentType): ?int
    {
        // Read journal's payment method line ids
        $field = $paymentType === 'inbound' ? 'inbound_payment_method_line_ids' : 'outbound_payment_method_line_ids';
        $journals = $this->executeKw($uid, 'account.journal', 'read', [[(int) $journalId], [$field]]);

        Log::info('Odoo journal read result', [
            'journal_id' => $journalId,
            'payment_type' => $paymentType,
            'field' => $field,
            'journals_data' => $journals,
            'count' => is_array($journals) ? count($journals) : 0
        ]);

        if (!is_array($journals) || count($journals) === 0) {
            Log::warning('Odoo journal not found or empty', ['journal_id' => $journalId]);
            return null;
        }
        $journal = $journals[0] ?? [];
        $lines = $journal[$field] ?? [];

        Log::info('Odoo payment method lines found', [
            'journal_id' => $journalId,
            'field' => $field,
            'lines' => $lines,
            'lines_count' => is_array($lines) ? count($lines) : 0
        ]);

        if (is_array($lines) && count($lines) > 0) {
            $methodLineId = (int) $lines[0];
            Log::info('Odoo payment method line selected', ['method_line_id' => $methodLineId]);
            return $methodLineId;
        }

        Log::warning('Odoo no payment method lines available', ['journal_id' => $journalId, 'field' => $field]);
        return null;
    }

    /**
     * Find any journal that has a payment method line for the given type.
     * Returns [journalId, methodLineId] or null
     */
    protected function findJournalWithPaymentMethodLine(int $uid, string $paymentType): ?array
    {
        $field = $paymentType === 'inbound' ? 'inbound_payment_method_line_ids' : 'outbound_payment_method_line_ids';
        // Search all journals that have at least one method line
        $journalIds = $this->executeKw($uid, 'account.journal', 'search', [[[$field, '!=', false]]], ['limit' => 10]);
        Log::info('Odoo journals with payment method lines search', [
            'payment_type' => $paymentType,
            'field' => $field,
            'found_ids' => $journalIds,
        ]);

        if (!is_array($journalIds) || count($journalIds) === 0) {
            return null;
        }

        $journals = $this->executeKw($uid, 'account.journal', 'read', [$journalIds, [$field]]);
        if (!is_array($journals) || count($journals) === 0) {
            return null;
        }
        foreach ($journals as $journal) {
            $lines = $journal[$field] ?? [];
            if (is_array($lines) && count($lines) > 0) {
                return [(int) ($journal['id'] ?? 0), (int) $lines[0]];
            }
        }
        return null;
    }

    /**
     * Prefer journals whose payment method line uses a 'manual' payment method code
     * Returns [journalId, methodLineId] or null
     */
    protected function findJournalWithManualPaymentMethodLine(int $uid, string $paymentType): ?array
    {
        $field = $paymentType === 'inbound' ? 'inbound_payment_method_line_ids' : 'outbound_payment_method_line_ids';
        $journalIds = $this->executeKw($uid, 'account.journal', 'search', [[[$field, '!=', false]]], ['limit' => 20]);
        if (!is_array($journalIds) || count($journalIds) === 0) {
            return null;
        }
        $journals = $this->executeKw($uid, 'account.journal', 'read', [$journalIds, [$field]]);
        if (!is_array($journals) || count($journals) === 0) {
            return null;
        }

        foreach ($journals as $journal) {
            $lines = $journal[$field] ?? [];
            if (!is_array($lines) || count($lines) === 0) continue;
            // Load method lines to inspect their payment method code
            $methodLines = $this->executeKw($uid, 'account.payment.method.line', 'read', [$lines, ['payment_method_id']]);
            if (!is_array($methodLines)) continue;
            foreach ($methodLines as $line) {
                $paymentMethodId = $line['payment_method_id'][0] ?? null;
                if (!$paymentMethodId) continue;
                $method = $this->executeKw($uid, 'account.payment.method', 'read', [[(int) $paymentMethodId], ['code']]);
                $code = is_array($method) && isset($method[0]['code']) ? $method[0]['code'] : null;
                if ($code === 'manual') {
                    return [(int) ($journal['id'] ?? 0), (int) ($line['id'] ?? 0)];
                }
            }
        }
        return null;
    }

    /**
     * Validate that a payment method line is available on the journal for the given payment type.
     * If invalid, pick the first available one.
     */
    protected function validatePaymentMethodLineId(int $uid, int $journalId, string $paymentType, ?int $methodLineId): ?int
    {
        $field = $paymentType === 'inbound' ? 'inbound_payment_method_line_ids' : 'outbound_payment_method_line_ids';
        $journals = $this->executeKw($uid, 'account.journal', 'read', [[(int) $journalId], [$field]]);

        if (!is_array($journals) || count($journals) === 0) {
            return null;
        }
        $journal = $journals[0] ?? [];
        $lines = $journal[$field] ?? [];

        // If provided methodLineId is not in lines, fall back to first available
        if (is_int($methodLineId) && in_array($methodLineId, $lines, true)) {
            return $methodLineId;
        }

        if (is_array($lines) && count($lines) > 0) {
            $fallback = (int) $lines[0];
            Log::info('Odoo payment method line fallback selected', [
                'requested_method_line_id' => $methodLineId,
                'fallback_method_line_id' => $fallback,
                'journal_id' => $journalId,
                'payment_type' => $paymentType,
            ]);
            return $fallback;
        }

        return null;
    }

    /**
     * Wrapper for object service execute_kw
     */
    protected function executeKw(int $uid, string $model, string $method, array $args = [], array $kwargs = [])
    {
        $payload = [
            'jsonrpc' => '2.0',
            'method' => 'call',
            'params' => [
                'service' => 'object',
                'method' => 'execute_kw',
                'args' => [
                    getSetting('odoo_db'),
                    $uid,
                    getSetting('odoo_password'),
                    $model,
                    $method,
                    $args,
                    $kwargs,
                ],
            ],
            'id' => time(),
        ];

        Log::info('Odoo execute_kw request', [
            'model' => $model,
            'method' => $method,
            'args' => $args,
            'kwargs' => $kwargs,
            'uid' => $uid
        ]);

        $response = (new Client([
            'base_uri' => getSetting('odoo_url'),
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'timeout' => 15,
        ]))->post('/jsonrpc', [
            'body' => json_encode($payload),
        ]);
        $data = json_decode((string) $response->getBody(), true);

        if (isset($data['error'])) {
            Log::error('Odoo execute_kw error', [
                'model' => $model,
                'method' => $method,
                'error' => $data['error'],
                'args' => $args,
                'kwargs' => $kwargs
            ]);
            return null;
        }

        $result = $data['result'] ?? null;
        Log::info('Odoo execute_kw success', [
            'model' => $model,
            'method' => $method,
            'result' => $result,
            'result_type' => gettype($result),
            'result_count' => is_array($result) ? count($result) : null
        ]);

        return $result;
    }
}
