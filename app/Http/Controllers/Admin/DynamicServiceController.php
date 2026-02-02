<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\DestroyDynamicServiceRequest;
use App\Http\Requests\MassDestroyDynamicServiceRequest;
use App\Http\Requests\StoreDynamicServiceRequest;
use App\Http\Requests\UpdateDynamicServiceRequest;
use App\Models\DynamicService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class DynamicServiceController extends Controller
{
    use MediaUploadingTrait;
    public function index(Request $request)
    {
        abort_if(Gate::denies('dynamic_service_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = DynamicService::select(sprintf('%s.*', (new DynamicService())->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate = 'dynamic_service_show';
                $editGate = 'dynamic_service_edit';
                $deleteGate = 'dynamic_service_delete';
                $crudRoutePart = 'dynamic-services';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->editColumn('id', function ($row) {
                return $row->id ? $row->id : '';
            });
            $table->editColumn('title', function ($row) {
                return $row->title ? $row->title : '';
            });
            $table->editColumn('description', function ($row) {
                return $row->description ? $row->description : '';
            });
            $table->editColumn('status', function ($row) {
                return $row->status ? trans('global.' . $row->status) : '';
            });
            $table->editColumn('form_fields_count', function ($row) {
                $formFields = $row->form_fields;
                if (is_string($formFields)) {
                    $formFields = json_decode($formFields, true);
                }
                return is_array($formFields) ? count($formFields) : '0';
            });

            $table->rawColumns(['actions', 'placeholder']);

            return $table->make(true);
        }

        return view('admin.dynamic-services.index');
    }

    public function create()
    {
        abort_if(Gate::denies('dynamic_service_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.dynamic-services.create');
    }

    public function store(StoreDynamicServiceRequest $request)
    {
        $dynamicService = DynamicService::create($request->all());

        if ($request->input('icon', false)) {
            $dynamicService->addMedia(storage_path('tmp/uploads/' . basename($request->input('icon'))))->toMediaCollection('icon');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $dynamicService->id]);
        }

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('admin.dynamic-services.index');
    }

    public function show(DynamicService $dynamicService)
    {
        abort_if(Gate::denies('dynamic_service_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // Load beneficiary orders for this dynamic service
        $dynamicServiceOrders = \App\Models\DynamicServiceOrder::where('dynamic_service_id', $dynamicService->id)
            ->with(['beneficiaryOrder.beneficiary.user', 'beneficiaryOrder.status', 'beneficiaryOrder.specialist'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.dynamic-services.show', compact('dynamicService', 'dynamicServiceOrders'));
    }

    public function edit(DynamicService $dynamicService)
    {
        abort_if(Gate::denies('dynamic_service_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.dynamic-services.edit', compact('dynamicService'));
    }

    public function update(UpdateDynamicServiceRequest $request, DynamicService $dynamicService)
    {
        $dynamicService->update($request->all());

        if ($request->input('icon', false)) {
            $icon = $dynamicService->getFirstMedia('icon');
            if (! $icon || $request->input('icon') !== $icon->file_name) {
                if ($icon) {
                    $icon->delete();
                }
                $dynamicService->addMedia(storage_path('tmp/uploads/' . basename($request->input('icon'))))->toMediaCollection('icon');
            }
        } else {
            $icon = $dynamicService->getFirstMedia('icon');
            if ($icon) {
                $icon->delete();
            }
        }

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('admin.dynamic-services.index');
    }

    public function destroy(DestroyDynamicServiceRequest $request, DynamicService $dynamicService)
    {
        $dynamicService->delete();

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return back();
    }

    public function massDestroy(MassDestroyDynamicServiceRequest $request)
    {
        DynamicService::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function updateProgramMeetings(Request $request, DynamicService $dynamicService)
    {
        abort_if(Gate::denies('dynamic_service_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // Validate that this is a training service
        if ($dynamicService->category !== 'training') {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'يمكن إدارة الاجتماعات فقط للخدمات التدريبية'], 400);
            }
            return back()->withErrors(['error' => 'يمكن إدارة الاجتماعات فقط للخدمات التدريبية']);
        }

        // Handle program_meetings
        $meetings = [];
        if ($request->has('program_meetings')) {
            $meetings = array_filter($request->program_meetings, function($meeting) {
                return !empty($meeting['date']) || !empty($meeting['title']);
            });
            $meetings = !empty($meetings) ? array_values($meetings) : null;
        }

        $dynamicService->update(['program_meetings' => $meetings]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'تم حفظ جدول اللقاءات بنجاح']);
        }

        return redirect()->route('admin.dynamic-services.show', $dynamicService->id)
            ->with('success', 'تم حفظ جدول اللقاءات بنجاح');
    }

    public function meetingAttendance(Request $request)
    {
        $request->validate([
            'dynamic_service_id' => 'required|exists:dynamic_services,id',
            'meeting_index' => 'required|integer|min:0',
        ]);

        $dynamicService = DynamicService::findOrFail($request->dynamic_service_id);

        // Validate that this is a training service
        if ($dynamicService->category !== 'training') {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'يمكن إدارة الحضور فقط للخدمات التدريبية'], 400);
            }
            return back()->withErrors(['error' => 'يمكن إدارة الحضور فقط للخدمات التدريبية']);
        }

        // Handle GET request - return attendance list
        if ($request->isMethod('get')) {
            return $this->getMeetingAttendance($request, $dynamicService);
        }

        // Handle POST request - save attendance
        $request->validate([
            'beneficiary_id' => 'required|exists:beneficiaries,id',
            'attended' => 'required|boolean',
            'notes' => 'nullable|string',
        ]);

        return $this->saveMeetingAttendance($request, $dynamicService);
    }

    protected function getMeetingAttendance(Request $request, DynamicService $dynamicService)
    {
        $meetingIndex = $request->meeting_index;
        $meetings = $dynamicService->program_meetings ?? [];
        
        if (!isset($meetings[$meetingIndex])) {
            return response()->json([
                'success' => false,
                'message' => 'الاجتماع غير موجود'
            ], 404);
        }

        // Get all dynamic service orders for this service
        $dynamicServiceOrders = \App\Models\DynamicServiceOrder::where('dynamic_service_id', $dynamicService->id)
            ->with(['beneficiaryOrder.beneficiary.user', 'workflow.training'])
            ->get();

        $attendanceList = [];

        foreach ($dynamicServiceOrders as $order) {
            $workflow = $order->workflow;
            if (!$workflow || !$workflow->training) {
                continue;
            }

            $beneficiary = $order->beneficiaryOrder->beneficiary ?? null;
            if (!$beneficiary) {
                continue;
            }

            $attendanceData = $workflow->training->attendance_data ?? [];
            $sessionKey = 'meeting_' . $meetingIndex;
            $attendance = $attendanceData[$sessionKey] ?? null;

            $attendanceList[] = [
                'beneficiary_id' => $beneficiary->id,
                'beneficiary_name' => $beneficiary->user->name ?? '-',
                'identity_number' => $beneficiary->user->identity_num ?? '-',
                'attended' => $attendance['attended'] ?? false,
                'notes' => $attendance['notes'] ?? null,
                'updated_at' => $attendance['updated_at'] ?? null,
            ];
        }

        return response()->json([
            'success' => true,
            'attendance' => $attendanceList
        ]);
    }

    protected function saveMeetingAttendance(Request $request, DynamicService $dynamicService)
    {
        $beneficiaryId = $request->beneficiary_id;
        $meetingIndex = $request->meeting_index;
        $attended = $request->attended ?? true;
        $notes = $request->notes;

        // Find the dynamic service order for this beneficiary and service
        $dynamicServiceOrder = \App\Models\DynamicServiceOrder::where('dynamic_service_id', $dynamicService->id)
            ->whereHas('beneficiaryOrder', function($query) use ($beneficiaryId) {
                $query->where('beneficiary_id', $beneficiaryId);
            })
            ->with(['workflow.training'])
            ->first();

        if (!$dynamicServiceOrder) {
            return response()->json([
                'success' => false,
                'message' => 'لم يتم العثور على طلب للخدمة لهذا المستفيد'
            ], 404);
        }

        $workflow = $dynamicServiceOrder->workflow;
        if (!$workflow) {
            return response()->json([
                'success' => false,
                'message' => 'لم يتم العثور على سير العمل'
            ], 404);
        }

        // Use the workflow service to update attendance
        $workflowService = app(\App\Services\DynamicServiceWorkflowService::class);
        
        // Create a request object for the service method
        $attendanceRequest = new \Illuminate\Http\Request();
        $attendanceRequest->merge([
            'session_id' => 'meeting_' . $meetingIndex,
            'attended' => $attended,
            'notes' => $notes,
        ]);

        try {
            $workflowService->updateAttendance($attendanceRequest, $workflow);
            
            return response()->json([
                'success' => true,
                'message' => 'تم تسجيل الحضور بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
