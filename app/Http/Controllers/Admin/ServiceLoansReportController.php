<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ServiceLoansExport;
use App\Http\Controllers\Controller;
use App\Models\ServiceLoan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;

class ServiceLoansReportController extends Controller
{
    public function index(Request $request)
    {

        $filters = $this->validateFilters($request);

        $query = ServiceLoan::with([
            'beneficiary_order.beneficiary.user',
            'members.beneficiary.user',
            'installments',
            'payments'
        ])
            ->when($filters['q'] ?? null, function ($q, $value) {
                $q->whereHas('beneficiary_order.beneficiary.user', function ($uq) use ($value) {
                    $uq->where('name', 'like', "%$value%")
                        ->orWhere('email', 'like', "%$value%")
                        ->orWhere('username', 'like', "%$value%")
                        ->orWhere('identity_num', 'like', "%$value%");
                })->orWhere('group_name', 'like', "%$value%")
                    ->orWhere('id', (int) $value);
            })
            ->when($filters['status'] ?? null, function ($q, $value) {
                $q->where('status', $value);
            })
            ->when(isset($filters['payment_filter']) && $filters['payment_filter'] !== '', function ($q) use ($filters) {
                if ($filters['payment_filter'] === 'overdue') {
                    $q->whereHas('installments', function ($iq) {
                        $iq->whereDate('installment_date', '<', now()->toDateString())
                            ->where('payment_status', '!=', 'paid');
                    });
                } elseif ($filters['payment_filter'] === 'unpaid') {
                    $q->whereHas('installments', function ($iq) {
                        $iq->where('payment_status', 'pending');
                    });
                } elseif ($filters['payment_filter'] === 'paid') {
                    $q->whereDoesntHave('installments', function ($iq) {
                        $iq->where('payment_status', '!=', 'paid');
                    });
                }
            })
            ->when(($filters['installment_date_from'] ?? null) && ($filters['installment_date_to'] ?? null), function ($q) use ($filters) {
                $q->whereHas('installments', function ($iq) use ($filters) {
                    $iq->whereDate('installment_date', '>=', $filters['installment_date_from'])
                        ->whereDate('installment_date', '<=', $filters['installment_date_to']);
                });
            }, function ($q) use ($filters) {
                if (!empty($filters['installment_date_from'])) {
                    $q->whereHas('installments', function ($iq) use ($filters) {
                        $iq->whereDate('installment_date', '>=', $filters['installment_date_from']);
                    });
                }
                if (!empty($filters['installment_date_to'])) {
                    $q->whereHas('installments', function ($iq) use ($filters) {
                        $iq->whereDate('installment_date', '<=', $filters['installment_date_to']);
                    });
                }
            })
            ->when(($filters['created_from'] ?? null) && ($filters['created_to'] ?? null), function ($q) use ($filters) {
                $q->whereDate('created_at', '>=', $filters['created_from'])
                    ->whereDate('created_at', '<=', $filters['created_to']);
            }, function ($q) use ($filters) {
                if (!empty($filters['created_from'])) {
                    $q->whereDate('created_at', '>=', $filters['created_from']);
                }
                if (!empty($filters['created_to'])) {
                    $q->whereDate('created_at', '<=', $filters['created_to']);
                }
            })
            ->orderByDesc('id');

        $loans = $query->paginate(10)->appends($filters);

        $statusOptions = ServiceLoan::STATUS_SELECT;
        $paymentFilterOptions = [
            'overdue' => 'متأخر',
            'unpaid' => 'غير مدفوع',
            'paid' => 'مدفوع بالكامل',
        ];

        return view('admin.serviceLoansReports.index', compact(
            'loans',
            'filters',
            'statusOptions',
            'paymentFilterOptions'
        ));
    }

    public function export(Request $request)
    {

        $filters = $this->validateFilters($request);

        return Excel::download(new ServiceLoansExport($filters), 'service_loans_report.xlsx');
    }

    private function validateFilters(Request $request): array
    {
        return $request->validate([
            'q' => ['nullable', 'string'],
            'status' => ['nullable', 'in:' . implode(',', array_keys(ServiceLoan::STATUS_SELECT))],
            'payment_filter' => ['nullable', 'in:overdue,unpaid,paid'],
            'installment_date_from' => ['nullable', 'date'],
            'installment_date_to' => ['nullable', 'date'],
            'created_from' => ['nullable', 'date'],
            'created_to' => ['nullable', 'date'],
        ]);
    }
}
