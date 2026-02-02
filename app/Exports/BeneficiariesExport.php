<?php

namespace App\Exports;

use App\Models\Beneficiary;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class BeneficiariesExport implements FromCollection, WithHeadings, WithMapping
{
    protected array $filters;
    protected array $availableColumns;
    protected array $selectedColumns;

    public function __construct(array $filters = [], array $availableColumns = [], array $selectedColumns = [])
    {
        $this->filters = $filters;
        $this->availableColumns = $availableColumns;
        $this->selectedColumns = !empty($selectedColumns) ? $selectedColumns : array_keys($availableColumns);
    }

    public function collection(): Collection
    {
        $query = Beneficiary::with(['user', 'region', 'city', 'district', 'nationality', 'marital_status', 'beneficiary_category', 'specialist'])
            ->when($this->filters['name'] ?? null, function ($q, $value) {
                $q->whereHas('user', function ($uq) use ($value) {
                    $uq->where('name', 'like', "%$value%")
                        ->orWhere('email', 'like', "%$value%")
                        ->orWhere('username', 'like', "%$value%");
                });
            })
            ->when($this->filters['identity_num'] ?? null, function ($q, $value) {
                $q->whereHas('user', function ($uq) use ($value) {
                    $uq->where('identity_num', 'like', "%$value%");
                });
            })
            ->when($this->filters['phone'] ?? null, function ($q, $value) {
                $q->whereHas('user', function ($uq) use ($value) {
                    $uq->where('phone', 'like', "%$value%")
                        ->orWhere('phone_2', 'like', "%$value%");
                });
            })
            ->when($this->filters['profile_status'] ?? null, function ($q, $value) {
                $q->where('profile_status', $value);
            })
            ->when($this->filters['region_id'] ?? null, function ($q, $value) {
                $q->where('region_id', $value);
            })
            ->when($this->filters['city_id'] ?? null, function ($q, $value) {
                $q->where('city_id', $value);
            })
            ->when($this->filters['marital_status_id'] ?? null, function ($q, $value) {
                $q->where('marital_status_id', $value);
            })
            ->when($this->filters['beneficiary_category_id'] ?? null, function ($q, $value) {
                $q->where('beneficiary_category_id', $value);
            })
            ->when($this->filters['can_work'] ?? null, function ($q, $value) {
                $q->where('can_work', $value);
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
        $headings = [];
        foreach ($this->selectedColumns as $columnKey) {
            if (isset($this->availableColumns[$columnKey])) {
                $headings[] = $this->availableColumns[$columnKey];
            }
        }
        return $headings;
    }

    /**
     * @param \App\Models\Beneficiary $beneficiary
     */
    public function map($beneficiary): array
    {
        $row = [];
        foreach ($this->selectedColumns as $columnKey) {
            if (!isset($this->availableColumns[$columnKey])) {
                continue;
            }
            
            switch ($columnKey) {
                case 'id':
                    $row[] = $beneficiary->id;
                    break;
                case 'name':
                    $row[] = optional($beneficiary->user)->name;
                    break;
                case 'email':
                    $row[] = optional($beneficiary->user)->email;
                    break;
                case 'identity_num':
                    $row[] = optional($beneficiary->user)->identity_num;
                    break;
                case 'phone':
                    $row[] = optional($beneficiary->user)->phone;
                    break;
                case 'profile_status':
                    $row[] = \App\Models\Beneficiary::PROFILE_STATUS_SELECT[$beneficiary->profile_status] ?? $beneficiary->profile_status;
                    break;
                case 'region':
                    $row[] = optional($beneficiary->region)->name;
                    break;
                case 'city':
                    $row[] = optional($beneficiary->city)->name;
                    break;
                case 'district':
                    $row[] = optional($beneficiary->district)->name;
                    break;
                case 'nationality':
                    $row[] = optional($beneficiary->nationality)->name;
                    break;
                case 'marital_status':
                    $row[] = optional($beneficiary->marital_status)->name;
                    break;
                case 'beneficiary_category':
                    $row[] = optional($beneficiary->beneficiary_category)->name;
                    break;
                case 'can_work':
                    $row[] = \App\Models\Beneficiary::CAN_WORK_SELECT[$beneficiary->can_work] ?? $beneficiary->can_work;
                    break;
                case 'specialist':
                    $row[] = optional($beneficiary->specialist)->name;
                    break;
                case 'created_at':
                    $row[] = optional($beneficiary->created_at)?->format('Y-m-d');
                    break;
                default:
                    $row[] = '-';
            }
        }
        return $row;
    }
}


