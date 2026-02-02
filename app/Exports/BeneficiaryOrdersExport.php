<?php

namespace App\Exports;

use App\Models\BeneficiaryOrder;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class BeneficiaryOrdersExport implements FromCollection, WithHeadings, WithMapping
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection(): Collection
    {
        $query = BeneficiaryOrder::with(['beneficiary.user', 'service', 'status', 'specialist'])
            ->when($this->filters['q'] ?? null, function ($q, $value) {
                $q->whereHas('beneficiary.user', function ($uq) use ($value) {
                    $uq->where('name', 'like', "%$value%")
                        ->orWhere('email', 'like', "%$value%")
                        ->orWhere('username', 'like', "%$value%");
                })->orWhere('title', 'like', "%$value%")
                  ->orWhere('description', 'like', "%$value%")
                  ->orWhere('id', (int) $value);
            })
            ->when($this->filters['service_id'] ?? null, function ($q, $value) {
                $q->where('service_id', $value);
            })
            ->when($this->filters['status_id'] ?? null, function ($q, $value) {
                $q->where('status_id', $value);
            })
            ->when($this->filters['accept_status'] ?? null, function ($q, $value) {
                $q->where('accept_status', $value);
            })
            ->when(isset($this->filters['done']) && $this->filters['done'] !== '', function ($q) {
                $q->where('done', (int) $this->filters['done']);
            })
            ->when(isset($this->filters['is_archived']) && $this->filters['is_archived'] !== '', function ($q) {
                $q->where('is_archived', (int) $this->filters['is_archived']);
            })
            ->when($this->filters['specialist_id'] ?? null, function ($q, $value) {
                $q->where('specialist_id', $value);
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
            'المستفيد',
            'الخدمة',
            'حالة الطلب',
            'القبول',
            'الأخصائي',
            'العنوان',
            'منتهي',
            'مؤرشف',
            'تاريخ الإنشاء',
        ];
    }

    /**
     * @param \App\Models\BeneficiaryOrder $order
     */
    public function map($order): array
    {
        return [
            $order->id,
            optional(optional($order->beneficiary)->user)->name,
            optional($order->service)->title,
            optional($order->status)->name,
            \App\Models\BeneficiaryOrder::ACCEPT_STATUS_RADIO[$order->accept_status] ?? '-',
            optional($order->specialist)->name,
            $order->title,
            $order->done ? 'نعم' : 'لا',
            $order->is_archived ? 'نعم' : 'لا',
            optional($order->created_at)?->format('Y-m-d'),
        ];
    }
}


