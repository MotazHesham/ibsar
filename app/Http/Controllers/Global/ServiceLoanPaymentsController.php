<?php

namespace App\Http\Controllers\Global;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Models\ServiceLoanPayment;
use App\Models\ServiceLoan;
use App\Services\ServiceLoanPaymentService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class ServiceLoanPaymentsController extends Controller
{
    use MediaUploadingTrait;
    protected $paymentService;

    public function __construct(ServiceLoanPaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Store a newly created payment
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'service_loan_id' => 'required|exists:service_loans,id',
            'payment_method' => 'required|string',
            'payment_reference_number' => 'nullable|string|unique:service_loan_payments,payment_reference_number',
            'paid_date' => 'required|date_format:'. config('panel.date_format'),
            'amount' => 'required|numeric|min:0.01',
            'note' => 'nullable|string',
        ]);

        try {
            // Validate payment amount
            $validation = $this->paymentService->validatePaymentAmount(
                $request->service_loan_id, 
                $request->amount
            );

            if (!$validation['is_valid']) {
                return response()->json([
                    'success' => false,
                    'message' => $validation['message']
                ], 422);
            }

            $paymentData = $request->only([
                'service_loan_id',
                'payment_method',
                'payment_reference_number',
                'paid_date',
                'amount',
                'note'
            ]);

            // Process payment
            $result = $this->paymentService->processPayment($paymentData, $request->file('payment_receipt'));
            
            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Payment processed successfully',
                    'payment' => $result['payment']
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message']
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Error processing payment: ' . $e);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing the payment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified payment
     */
    public function show(ServiceLoanPayment $payment): JsonResponse
    {
        $payment->load('serviceLoan');
        
        return response()->json([
            'id' => $payment->id,
            'payment_method' => $payment->payment_method,
            'payment_reference_number' => $payment->payment_reference_number,
            'paid_date' => $payment->paid_date,
            'amount' => $payment->amount,
            'note' => $payment->note,
            'payment_receipt' => $payment->payment_receipt ? $payment->payment_receipt->getUrl() : null,
            'created_at' => $payment->created_at->format('Y-m-d H:i:s')
        ]);
    }

    /**
     * Get payment summary for a service loan
     */
    public function getSummary(ServiceLoan $serviceLoan): JsonResponse
    {
        $summary = $this->paymentService->getPaymentSummary($serviceLoan);
        
        return response()->json($summary);
    }

    /**
     * Validate payment amount before submission
     */
    public function validateAmount(Request $request): JsonResponse
    {
        $request->validate([
            'service_loan_id' => 'required|exists:service_loans,id',
            'amount' => 'required|numeric|min:0.01'
        ]);

        $validation = $this->paymentService->validatePaymentAmount(
            $request->service_loan_id,
            $request->amount
        );

        return response()->json($validation);
    }

    /**
     * Get next installment information
     */
    public function getNextInstallment(ServiceLoan $serviceLoan): JsonResponse
    {
        $nextInstallment = $this->paymentService->getNextUnpaidInstallment($serviceLoan);
        $nextAmount = $this->paymentService->getNextInstallmentAmount($serviceLoan);

        return response()->json([
            'next_installment' => $nextInstallment,
            'next_amount_needed' => $nextAmount,
            'has_unpaid_installments' => $nextInstallment !== null
        ]);
    }

    /**
     * Accept a payment request
     */
    public function acceptSpecialist(ServiceLoanPayment $payment): JsonResponse
    {
        try { 
            if ($payment->payment_status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'لا يمكن قبول الأخصائية إلا إذا كانت في حالة طلب قيد الانتظار'
                ], 422);
            }

            // Process the payment acceptance
            $result = $this->paymentService->acceptSpecialist($payment);
            
            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم قبول الأخصائية بنجاح',
                    'payment' => $result['payment']
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message']
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Error accepting specialist: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء قبول الأخصائية: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Accept a payment request
     */
    public function accept(ServiceLoanPayment $payment): JsonResponse
    {
        try { 
            if ($payment->payment_status !== 'approved_specialist') {
                return response()->json([
                    'success' => false,
                    'message' => 'لا يمكن قبول الدفعة إلا إذا كانت في حالة موافقة الأخصائية'
                ], 422);
            }

            // Process the payment acceptance
            $result = $this->paymentService->acceptPayment($payment);
            
            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم قبول الدفعة بنجاح',
                    'payment' => $result['payment']
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message']
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Error accepting payment: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء قبول الدفعة: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject a payment request
     */
    public function reject(Request $request, ServiceLoanPayment $payment): JsonResponse
    {
        try {
            $request->validate([
                'rejection_reason' => 'required|string|max:500'
            ]);

            if ($payment->payment_status !== 'approved_specialist') {
                return response()->json([
                    'success' => false,
                    'message' => 'لا يمكن رفض الدفعة إلا إذا كانت في حالة موافقة الأخصائية'
                ], 422);
            }

            // Process the payment rejection
            $result = $this->paymentService->rejectPayment($payment, $request->rejection_reason);
            
            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم رفض الدفعة بنجاح',
                    'payment' => $result['payment']
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message']
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Error rejecting payment: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء رفض الدفعة: ' . $e->getMessage()
            ], 500);
        }
    }
}
