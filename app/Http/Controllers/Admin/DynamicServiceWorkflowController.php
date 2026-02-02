<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DynamicServiceOrder;
use App\Models\DynamicServiceWorkflow;
use App\Services\DynamicServiceWorkflowService;
use Illuminate\Http\Request;

class DynamicServiceWorkflowController extends Controller
{
    protected $workflowService;

    public function __construct(DynamicServiceWorkflowService $workflowService)
    {
        $this->workflowService = $workflowService;
    }

    public function show(DynamicServiceOrder $dynamicServiceOrder)
    {
        $data = $this->workflowService->loadWorkflowData($dynamicServiceOrder);
        
        return view('admin.dynamic-service-workflows.show', $data);
    }

    public function transition(Request $request, DynamicServiceWorkflow $workflow)
    {
        // Load category-specific relationship
        $workflow->load($workflow->category === 'training' ? 'training' : ($workflow->category === 'assistance' ? 'assistance' : []));

        // Get validation rules from service
        $validationRules = $this->workflowService->getValidationRules($workflow);
        $request->validate($validationRules);

        if (!$workflow->canTransitionTo($request->to_status)) {
            return back()->withErrors(['to_status' => 'Invalid transition from current status']);
        }

        // Process transition through service
        $this->workflowService->transition($request, $workflow);

        return redirect()->route('admin.dynamic-service-workflows.show', $workflow->dynamicServiceOrder)
            ->with('success', 'تم تحديث حالة العملية بنجاح');
    }

    public function updateAttendance(Request $request, DynamicServiceWorkflow $workflow)
    {
        $request->validate([
            'session_id' => 'required|string', 
            'notes' => 'nullable|string',
        ]);

        try {
            $this->workflowService->updateAttendance($request, $workflow);
            return back()->with('success', 'تم تحديث بيانات الحضور');
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function updateAccounting(Request $request, DynamicServiceWorkflow $workflow)
    {
        $request->validate([
            'entries' => 'required|array',
            'entries.*.description' => 'required|string',
            'entries.*.amount' => 'required|numeric',
            'entries.*.date' => 'required|date',
        ]);

        try {
            $this->workflowService->updateAccounting($request, $workflow);
            return back()->with('success', 'تم تحديث القيود المحاسبية');
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function updateSatisfaction(Request $request, DynamicServiceWorkflow $workflow)
    {
        $request->validate([
            'satisfaction_data' => 'required|array',
        ]);

        try {
            $this->workflowService->updateSatisfaction($request, $workflow);
            return back()->with('success', 'تم حفظ تقييم الرضا');
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
