<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateWorkflowFinanceRequestRequest;
use App\Models\WorkflowFinanceRequest;
use App\Services\WorkflowFinanceRequestService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class WorkflowFinanceRequestsController extends Controller
{
    public function __construct(
        protected WorkflowFinanceRequestService $financeRequestService
    ) {
    }

    public function index(Request $request)
    {
        abort_if(Gate::denies('workflow_finance_request_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = WorkflowFinanceRequest::query()
                ->with(['beneficiaryOrder.beneficiary.user'])
                ->select('workflow_finance_requests.*');

            if ($request->has('status') && $request->status !== '' && $request->status !== null) {
                if (in_array($request->status, ['unposted', 'posted'], true)) {
                    $query->where('status', $request->status);
                }
            }

            if ($request->filled('workflow_category')) {
                $query->where('workflow_category', $request->workflow_category);
            }

            $table = DataTables::of($query);
            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                return view('partials.datatablesActions', [
                    'viewGate' => 'workflow_finance_request_show',
                    'editGate' => false,
                    'deleteGate' => false,
                    'crudRoutePart' => 'workflow-finance-requests',
                    'row' => $row,
                ])->render();
            });

            $table->editColumn('id', fn ($row) => $row->id);
            $table->addColumn('beneficiary_name', fn ($row) => $row->beneficiaryOrder?->beneficiary?->user?->name ?? '—');
            $table->addColumn('beneficiary_order_id', fn ($row) => $row->beneficiary_order_id);
            $table->editColumn('title', fn ($row) => $row->title);
            $table->editColumn('workflow_category_label', fn ($row) => $row->workflow_category_label);
            $table->editColumn('amount', fn ($row) => $row->amount !== null ? number_format((float) $row->amount, 2) : '—');
            $table->editColumn('status', function ($row) {
                $class = $row->status === WorkflowFinanceRequest::STATUS_POSTED ? 'success' : 'warning';

                return '<span class="badge bg-' . $class . '-transparent">'
                    . (WorkflowFinanceRequest::STATUS_SELECT[$row->status] ?? $row->status)
                    . '</span>';
            });
            $table->editColumn('created_at', fn ($row) => $row->created_at?->format('Y-m-d H:i') ?? '—');

            $table->rawColumns(['actions', 'placeholder', 'status']);

            return $table->make(true);
        }

        $categories = \Modules\DynamicServices\Models\DynamicService::CATEGORIES;

        return view('admin.workflowFinanceRequests.index', compact('categories'));
    }

    public function show(WorkflowFinanceRequest $workflowFinanceRequest)
    {
        abort_if(Gate::denies('workflow_finance_request_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $workflowFinanceRequest->load([
            'beneficiaryOrder.beneficiary.user',
            'beneficiaryOrder.dynamicServiceOrder.dynamicService',
            'reference',
            'processedBy',
            'createdBy',
        ]);

        return view('admin.workflowFinanceRequests.show', compact('workflowFinanceRequest'));
    }

    public function update(
        UpdateWorkflowFinanceRequestRequest $request,
        WorkflowFinanceRequest $workflowFinanceRequest
    ) {
        try {
            if ($request->input('action') === 'post') {
                $this->financeRequestService->post($workflowFinanceRequest, $request->validated());
                $message = 'تم ترحيل القيد بنجاح.';
            } else {
                $this->financeRequestService->updateCost($workflowFinanceRequest, $request->validated());
                $message = 'تم حفظ التكلفة بنجاح.';
            }
        } catch (ValidationException $e) {
            return redirect()
                ->route('admin.workflow-finance-requests.show', $workflowFinanceRequest)
                ->withErrors($e->errors());
        }

        return redirect()
            ->route('admin.workflow-finance-requests.show', $workflowFinanceRequest)
            ->with('success', $message);
    }
}
