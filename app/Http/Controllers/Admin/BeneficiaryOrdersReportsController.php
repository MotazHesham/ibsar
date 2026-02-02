<?php

namespace App\Http\Controllers\Admin;

use App\Exports\BeneficiaryOrdersExport;
use App\Http\Controllers\Controller;
use App\Models\BeneficiaryOrder;
use App\Models\Service;
use App\Models\ServiceStatus;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\Response;

class BeneficiaryOrdersReportsController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('beneficiary_orders_report_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $filters = $this->validateFilters($request);

        $query = BeneficiaryOrder::with(['beneficiary.user', 'service', 'status', 'specialist'])
            ->when($filters['q'] ?? null, function ($q, $value) {
                $q->whereHas('beneficiary.user', function ($uq) use ($value) {
                    $uq->where('name', 'like', "%$value%")
                        ->orWhere('email', 'like', "%$value%")
                        ->orWhere('username', 'like', "%$value%");
                })->orWhere('title', 'like', "%$value%")
                  ->orWhere('description', 'like', "%$value%")
                  ->orWhere('id', (int) $value);
            })
            ->when($filters['service_id'] ?? null, function ($q, $value) {
                $q->where('service_id', $value);
            })
            ->when($filters['status_id'] ?? null, function ($q, $value) {
                $q->where('status_id', $value);
            })
            ->when($filters['accept_status'] ?? null, function ($q, $value) {
                $q->where('accept_status', $value);
            })
            ->when(isset($filters['done']) && $filters['done'] !== '', function ($q, $value) use ($filters) {
                $q->where('done', (int) $filters['done']);
            })
            ->when(isset($filters['is_archived']) && $filters['is_archived'] !== '', function ($q) use ($filters) {
                $q->where('is_archived', (int) $filters['is_archived']);
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

        $orders = $query->paginate(10)->appends($filters);

        $services = Service::orderBy('title')->pluck('title', 'id');
        $statuses = ServiceStatus::orderBy('name')->pluck('name', 'id');
        $acceptOptions = BeneficiaryOrder::ACCEPT_STATUS_RADIO;
        $specialists = User::where('user_type', 'staff')->where('employee_type', 'specialist')->orderBy('name')->pluck('name', 'id');

        return view('admin.beneficiaryOrdersReports.index', compact(
            'orders',
            'filters',
            'services',
            'statuses',
            'acceptOptions',
            'specialists'
        ));
    }

    public function export(Request $request)
    {
        abort_if(Gate::denies('beneficiary_orders_report_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $filters = $this->validateFilters($request);

        return Excel::download(new BeneficiaryOrdersExport($filters), 'beneficiary_orders_report.xlsx');
    }

    private function validateFilters(Request $request): array
    {
        return $request->validate([
            'q' => ['nullable', 'string'],
            'service_id' => ['nullable', 'integer'],
            'status_id' => ['nullable', 'integer'],
            'accept_status' => ['nullable', 'in:' . implode(',', array_keys(BeneficiaryOrder::ACCEPT_STATUS_RADIO))],
            'done' => ['nullable', 'in:0,1'],
            'is_archived' => ['nullable', 'in:0,1'],
            'specialist_id' => ['nullable', 'integer'],
            'created_from' => ['nullable', 'date'],
            'created_to' => ['nullable', 'date'],
        ]);
    }
}
