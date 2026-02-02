<?php

namespace App\Services;

use App\Services\OdooService;
use App\Models\ServiceLoan;
use App\Models\ServiceLoanPayment;
use App\Models\ServiceLoanInstallment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ServiceLoanPaymentService
{
    protected $odooService;

    public function __construct(OdooService $odooService)
    {
        $this->odooService = $odooService;
    }
    /**
     * Process a new payment and distribute it across installments
     */
    public function processPayment(array $paymentData, $paymentReceipt): array
    {
        try {
            DB::beginTransaction();

            // Validate payment amount
            $serviceLoan = ServiceLoan::findOrFail($paymentData['service_loan_id']);
            $totalPaid = $serviceLoan->payments->where('payment_status', 'paid')->sum('amount');
            $remainingAmount = $serviceLoan->amount - $totalPaid;

            if ($paymentData['amount'] > $remainingAmount) {
                throw new \Exception("Payment amount cannot exceed remaining loan amount: {$remainingAmount}");
            } 

            // Create the payment record
            $payment = ServiceLoanPayment::create($paymentData);


            if ($paymentReceipt) {
                $payment->addMedia(storage_path('tmp/uploads/' . basename($paymentReceipt)))->toMediaCollection('payment_receipt');
            }

            if($payment->payment_status == 'paid'){
                // Distribute payment across installments
                $this->distributePaymentToInstallments($payment, $serviceLoan); 
            }

            DB::commit();

            return [
                'success' => true,
                'payment' => $payment,
                'message' => 'Payment processed successfully'
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment processing failed: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Payment processing failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Distribute payment amount across unpaid installments
     */
    protected function distributePaymentToInstallments(ServiceLoanPayment $payment, ServiceLoan $serviceLoan): void
    {
        $remainingPaymentAmount = $payment->amount;
        $unpaidInstallments = $serviceLoan->installments()
            ->where('payment_status', '!=', 'paid')
            ->orderBy('installment_date')
            ->get();

        foreach ($unpaidInstallments as $installment) {
            if ($remainingPaymentAmount <= 0) {
                break;
            }

            $installmentAmount = $serviceLoan->installment;
            $currentPaidAmount = $installment->paid_amount ?? 0;
            $remainingInstallmentAmount = $installmentAmount - $currentPaidAmount;

            if ($remainingInstallmentAmount <= 0) {
                continue; // This installment is already fully paid
            }

            // Calculate how much of the payment goes to this installment
            $amountToPay = min($remainingPaymentAmount, $remainingInstallmentAmount);
            
            // Update installment paid amount
            $installment->paid_amount = $currentPaidAmount + $amountToPay;
            
            // Update payment status
            if ($installment->paid_amount >= $installmentAmount) {
                $installment->payment_status = 'paid';
            } else {
                $installment->payment_status = 'pending';
            }
            
            $installment->save();
            
            $remainingPaymentAmount -= $amountToPay;
        }
        $totalPaid = ServiceLoanPayment::where('service_loan_id', $serviceLoan->id)->where('payment_status', 'paid')->sum('amount'); 

        if($totalPaid >= $serviceLoan->amount){
            $serviceLoan->beneficiary_order->done = 1;
            $serviceLoan->beneficiary_order->save();
        }

        // If there's still remaining payment amount, it means the loan is overpaid
        if ($remainingPaymentAmount > 0) {
            Log::warning("Payment amount {$payment->amount} exceeds total unpaid installments. Overpayment: {$remainingPaymentAmount}");
        }
    }

    /**
     * Get payment summary for a service loan
     */
    public function getPaymentSummary(ServiceLoan $serviceLoan): array
    {
        $totalPaid = $serviceLoan->payments->sum('amount');
        $totalAmount = $serviceLoan->amount;
        $remainingAmount = $totalAmount - $totalPaid;
        $paidInstallments = $serviceLoan->installments()->where('payment_status', 'paid')->count();
        $totalInstallments = $serviceLoan->installments()->count();
        $pendingInstallments = $totalInstallments - $paidInstallments;

        return [
            'total_amount' => $totalAmount,
            'total_paid' => $totalPaid,
            'remaining_amount' => $remainingAmount,
            'paid_installments' => $paidInstallments,
            'total_installments' => $totalInstallments,
            'pending_installments' => $pendingInstallments,
            'payment_percentage' => $totalAmount > 0 ? round(($totalPaid / $totalAmount) * 100, 2) : 0
        ];
    }

    /**
     * Check if a payment amount is valid for a service loan
     */
    public function validatePaymentAmount(int $serviceLoanId, float $amount): array
    {
        $serviceLoan = ServiceLoan::findOrFail($serviceLoanId);
        $totalPaid = $serviceLoan->payments->where('payment_status', 'paid')->sum('amount');
        $remainingAmount = $serviceLoan->amount - $totalPaid;

        return [
            'is_valid' => $amount <= $remainingAmount,
            'remaining_amount' => $remainingAmount,
            'max_allowed' => $remainingAmount,
            'message' => $amount > $remainingAmount 
                ? "Payment amount cannot exceed remaining loan amount: {$remainingAmount}" 
                : "Payment amount is valid"
        ];
    }

    /**
     * Get next unpaid installment for a service loan
     */
    public function getNextUnpaidInstallment(ServiceLoan $serviceLoan): ?ServiceLoanInstallment
    {
        return $serviceLoan->installments()
            ->where('payment_status', '!=', 'paid')
            ->orderBy('installment_date')
            ->first();
    }

    /**
     * Calculate how much is needed for the next installment
     */
    public function getNextInstallmentAmount(ServiceLoan $serviceLoan): float
    {
        $nextInstallment = $this->getNextUnpaidInstallment($serviceLoan);
        
        if (!$nextInstallment) {
            return 0; // All installments are paid
        }

        $installmentAmount = $serviceLoan->installment;
        $currentPaidAmount = $nextInstallment->paid_amount ?? 0;
        
        return $installmentAmount - $currentPaidAmount;
    }

    /**
     * Accept a payment request and process it
     */
    public function acceptSpecialist(ServiceLoanPayment $payment): array
    {
        try { 
            // Update payment status to paid
            $payment->payment_status = 'approved_specialist'; 
            $payment->save();  

            return [
                'success' => true,
                'payment' => $payment,
                'message' => 'Payment approved successfully'
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment acceptance failed: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Payment acceptance failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Accept a payment request and process it
     */
    public function acceptPayment(ServiceLoanPayment $payment): array
    {
        try {
            DB::beginTransaction();

            // Update payment status to paid
            $payment->payment_status = 'paid'; 
            $payment->save();

            // Get the service loan
            $serviceLoan = $payment->serviceLoan;
            
            // Distribute payment across installments
            $this->distributePaymentToInstallments($payment, $serviceLoan); 

            DB::commit();

            // Sync to Odoo
            $this->odooService->payInstallment($payment, $serviceLoan);

            return [
                'success' => true,
                'payment' => $payment,
                'message' => 'Payment accepted and processed successfully'
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment acceptance failed: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Payment acceptance failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Reject a payment request
     */
    public function rejectPayment(ServiceLoanPayment $payment, string $rejectionReason): array
    {
        try {
            DB::beginTransaction();

            // Update payment status to rejected
            $payment->payment_status = 'rejected'; 
            $payment->rejection_reason = $rejectionReason;
            $payment->save();

            DB::commit();

            return [
                'success' => true,
                'payment' => $payment,
                'message' => 'Payment rejected successfully'
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment rejection failed: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Payment rejection failed: ' . $e->getMessage()
            ];
        }
    } 
}
