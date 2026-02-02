<?php

namespace App\Http\Controllers\Admin;

use App\Exports\BeneficiariesExport;
use App\Http\Controllers\Controller;
use App\Models\Beneficiary;
use App\Models\BeneficiaryCategory;
use App\Models\City;
use App\Models\MaritalStatus;
use App\Models\Region;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\Response;

class BeneficiaryReportController extends Controller
{
    /**
     * Available columns for the report table
     */
    private function getAvailableColumns(): array
    {
        return [
            'id' => '#',
            'name' => 'الاسم',
            'email' => 'البريد',
            'identity_num' => 'رقم الهوية',
            'phone' => 'الهاتف',
            'profile_status' => 'الحالة',
            'region' => 'المنطقة',
            'city' => 'المدينة',
            'district' => 'الحي',
            'nationality' => 'الجنسية',
            'marital_status' => 'الحالة الاجتماعية',
            'beneficiary_category' => 'فئة المستفيد',
            'can_work' => 'قابل للعمل',
            'specialist' => 'الأخصائي',
            'created_at' => 'تاريخ الإنشاء',
        ];
    }

    public function index(Request $request)
    {
        abort_if(Gate::denies('beneficiary_report_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $filters = $this->validateFilters($request);
        
        // Get selected columns or use default
        $availableColumns = $this->getAvailableColumns();
        $selectedColumns = $request->input('columns', []);
        
        // Validate selected columns
        $selectedColumns = array_intersect($selectedColumns, array_keys($availableColumns));
        
        // If no columns selected, use all as default
        if (empty($selectedColumns)) {
            $selectedColumns = array_keys($availableColumns);
        }
        
        // Ensure at least ID column is always included
        if (!in_array('id', $selectedColumns)) {
            array_unshift($selectedColumns, 'id');
        }

        $query = Beneficiary::with(['user', 'region', 'city', 'district', 'nationality', 'marital_status', 'beneficiary_category', 'specialist'])
            ->when($filters['name'] ?? null, function ($q, $value) {
                $q->whereHas('user', function ($uq) use ($value) {
                    $uq->where('name', 'like', "%$value%")
                        ->orWhere('email', 'like', "%$value%")
                        ->orWhere('username', 'like', "%$value%");
                });
            })
            ->when($filters['identity_num'] ?? null, function ($q, $value) {
                $q->whereHas('user', function ($uq) use ($value) {
                    $uq->where('identity_num', 'like', "%$value%");
                });
            })
            ->when($filters['phone'] ?? null, function ($q, $value) {
                $q->whereHas('user', function ($uq) use ($value) {
                    $uq->where('phone', 'like', "%$value%")
                        ->orWhere('phone_2', 'like', "%$value%");
                });
            })
            ->when($filters['profile_status'] ?? null, function ($q, $value) {
                $q->where('profile_status', $value);
            })
            ->when($filters['region_id'] ?? null, function ($q, $value) {
                $q->where('region_id', $value);
            })
            ->when($filters['city_id'] ?? null, function ($q, $value) {
                $q->where('city_id', $value);
            })
            ->when($filters['marital_status_id'] ?? null, function ($q, $value) {
                $q->where('marital_status_id', $value);
            })
            ->when($filters['beneficiary_category_id'] ?? null, function ($q, $value) {
                $q->where('beneficiary_category_id', $value);
            })
            ->when($filters['can_work'] ?? null, function ($q, $value) {
                $q->where('can_work', $value);
            })
            ->when($filters['specialist_id'] ?? null, function ($q, $value) {
                $q->where('specialist_id', $value);
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

        $beneficiaries = $query->paginate(10)->appends($filters);

        $profileStatusOptions = Beneficiary::PROFILE_STATUS_SELECT;
        $canWorkOptions = Beneficiary::CAN_WORK_SELECT;
        $regions = Region::orderBy('name')->pluck('name', 'id');
        $cities = City::orderBy('name')->pluck('name', 'id');
        $maritalStatuses = MaritalStatus::orderBy('name')->pluck('name', 'id');
        $beneficiaryCategories = BeneficiaryCategory::orderBy('name')->pluck('name', 'id');
        $specialists = User::where('user_type', 'staff')->where('employee_type', 'specialist')->orderBy('name')->pluck('name', 'id');

        // Add selected columns to filters for pagination
        $filters['columns'] = $selectedColumns;

        return view('admin.beneficiaryReports.index', compact(
            'beneficiaries',
            'filters',
            'profileStatusOptions',
            'canWorkOptions',
            'regions',
            'cities',
            'maritalStatuses',
            'beneficiaryCategories',
            'specialists',
            'availableColumns',
            'selectedColumns'
        ));
    }

    public function export(Request $request)
    {
        abort_if(Gate::denies('beneficiary_report_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $filters = $this->validateFilters($request);
        
        // Get selected columns or use default
        $availableColumns = $this->getAvailableColumns();
        $selectedColumns = $request->input('columns', []);
        
        // Validate selected columns
        $selectedColumns = array_intersect($selectedColumns, array_keys($availableColumns));
        
        // If no columns selected, use all as default
        if (empty($selectedColumns)) {
            $selectedColumns = array_keys($availableColumns);
        }
        
        // Ensure at least ID column is always included
        if (!in_array('id', $selectedColumns)) {
            array_unshift($selectedColumns, 'id');
        }

        return Excel::download(new BeneficiariesExport($filters, $availableColumns, $selectedColumns), 'beneficiaries_report.xlsx');
    }

    private function validateFilters(Request $request): array
    {
        $validated = $request->validate([
            'name' => ['nullable', 'string'],
            'identity_num' => ['nullable', 'string'],
            'phone' => ['nullable', 'string'],
            'profile_status' => ['nullable', 'in:' . implode(',', array_keys(Beneficiary::PROFILE_STATUS_SELECT))],
            'region_id' => ['nullable', 'integer'],
            'city_id' => ['nullable', 'integer'],
            'marital_status_id' => ['nullable', 'integer'],
            'beneficiary_category_id' => ['nullable', 'integer'],
            'can_work' => ['nullable', 'in:' . implode(',', array_keys(Beneficiary::CAN_WORK_SELECT))],
            'specialist_id' => ['nullable', 'integer'],
            'created_from' => ['nullable', 'date'],
            'created_to' => ['nullable', 'date'],
            'columns' => ['nullable', 'array'],
            'columns.*' => ['nullable', 'string'],
        ]);
        
        return $validated;
    }
}
