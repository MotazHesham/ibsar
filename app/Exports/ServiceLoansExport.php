<?php

namespace App\Exports;

use App\Models\ServiceLoan;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class ServiceLoansExport implements FromCollection, WithHeadings, WithMapping
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection(): Collection
    {
        $query = ServiceLoan::with([
            'beneficiary_order.beneficiary.user',
            'members.beneficiary.user',
            'installments',
            'payments'
        ])
            ->when($this->filters['q'] ?? null, function ($q, $value) {
                $q->whereHas('beneficiary_order.beneficiary.user', function ($uq) use ($value) {
                    $uq->where('name', 'like', "%$value%")
                        ->orWhere('email', 'like', "%$value%")
                        ->orWhere('username', 'like', "%$value%")
                        ->orWhere('identity_num', 'like', "%$value%");
                })->orWhere('group_name', 'like', "%$value%")
                  ->orWhere('id', (int) $value);
            })
            ->when($this->filters['status'] ?? null, function ($q, $value) {
                $q->where('status', $value);
            })
            ->when(isset($this->filters['payment_filter']) && $this->filters['payment_filter'] !== '', function ($q) {
                if ($this->filters['payment_filter'] === 'overdue') {
                    $q->whereHas('installments', function ($iq) {
                        $iq->whereDate('installment_date', '<', now()->toDateString())
                          ->where('payment_status', '!=', 'paid');
                    });
                } elseif ($this->filters['payment_filter'] === 'unpaid') {
                    $q->whereHas('installments', function ($iq) {
                        $iq->where('payment_status', 'pending');
                    });
                } elseif ($this->filters['payment_filter'] === 'paid') {
                    $q->whereDoesntHave('installments', function ($iq) {
                        $iq->where('payment_status', '!=', 'paid');
                    });
                }
            })
            ->when(($this->filters['installment_date_from'] ?? null) && ($this->filters['installment_date_to'] ?? null), function ($q) {
                $q->whereHas('installments', function ($iq) {
                    $iq->whereDate('installment_date', '>=', $this->filters['installment_date_from'])
                      ->whereDate('installment_date', '<=', $this->filters['installment_date_to']);
                });
            }, function ($q) {
                if (!empty($this->filters['installment_date_from'])) {
                    $q->whereHas('installments', function ($iq) {
                        $iq->whereDate('installment_date', '>=', $this->filters['installment_date_from']);
                    });
                }
                if (!empty($this->filters['installment_date_to'])) {
                    $q->whereHas('installments', function ($iq) {
                        $iq->whereDate('installment_date', '<=', $this->filters['installment_date_to']);
                    });
                }
            })
            ->when(($this->filters['created_from'] ?? null) && ($this->filters['created_to'] ?? null), function ($q) {
                $q->whereDate('created_at', '>=', $this->filters['created_from'])
                  ->whereDate('created_at', '<=', $this->filters['created_to']);
            }, function ($q) {
                if (!empty($this->filters['created_from'])) {
                    $q->whereDate('created_at', '>=', $this->filters['created_from']);
                }
                if (!empty($this->filters['created_to'])) {
                    $q->whereDate('created_at', '<=', $this->filters['created_to']);
                }
            })
            ->orderByDesc('id');

        return $query->get();
    }

    public function headings(): array
    {
        return [
            '#',
            'اسم المجموعة',
            'المستفيد',
            'رقم الهوية',
            'مبلغ القرض',
            'القسط الشهري',
            'عدد الأشهر',
            'المبلغ المدفوع',
            'المبلغ المتبقي',
            'عدد الأقساط المتأخرة',
            'عدد الأقساط غير المدفوعة',
            'حالة القرض',
            'تاريخ الإنشاء',
        ];
    }

    /**
     * @param \App\Models\ServiceLoan $loan
     */
    public function map($loan): array
    {
        $beneficiary = $loan->beneficiary_order->beneficiary ?? null;
        $user = $beneficiary->user ?? null;
        
        $totalPaid = $loan->payments->where('payment_status', 'paid')->sum('amount');
        $remainingAmount = $loan->amount - $totalPaid;
        
        $overdueCount = $loan->installments->filter(function ($installment) {
            // Get raw date from database (format should be Y-m-d)
            $installmentDate = $installment->getAttributes()['installment_date'] ?? null;
            if (!$installmentDate) {
                return false;
            }
            // Parse the date - it should already be in Y-m-d format from database
            try {
                $date = Carbon::parse($installmentDate)->format('Y-m-d');
                return $date < now()->toDateString() && $installment->payment_status !== 'paid';
            } catch (\Exception $e) {
                return false;
            }
        })->count();
        
        $unpaidCount = $loan->installments->where('payment_status', 'pending')->count();

        return [
            $loan->id,
            $loan->group_name ?? '-',
            $user->name ?? '-',
            $user->identity_num ?? '-',
            $loan->amount ?? 0,
            $loan->installment ?? 0,
            $loan->months ?? 0,
            $totalPaid,
            $remainingAmount,
            $overdueCount,
            $unpaidCount,
            ServiceLoan::STATUS_SELECT[$loan->status] ?? $loan->status,
            optional($loan->created_at)?->format('Y-m-d'),
        ];
    }
}

