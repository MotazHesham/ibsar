<?php

namespace Modules\DynamicServices\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Modules\DynamicServices\Http\Requests\DestroyDynamicServiceRequest;
use Modules\DynamicServices\Http\Requests\MassDestroyDynamicServiceRequest;
use Modules\DynamicServices\Http\Requests\StoreDynamicServiceRequest;
use Modules\DynamicServices\Http\Requests\UpdateDynamicServiceRequest;
use Modules\DynamicServices\Models\DynamicService;
use Modules\DynamicServices\Models\DynamicServiceOrder;
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

        return view('dynamicservices::admin.dynamic-services.index');
    }

    public function create()
    {
        abort_if(Gate::denies('dynamic_service_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('dynamicservices::admin.dynamic-services.create');
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

        $dynamicServiceOrders = DynamicServiceOrder::where('dynamic_service_id', $dynamicService->id)
            ->with(['beneficiaryOrder.beneficiary.user', 'beneficiaryOrder.status', 'beneficiaryOrder.specialist'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('dynamicservices::admin.dynamic-services.show', compact('dynamicService', 'dynamicServiceOrders'));
    }

    public function edit(DynamicService $dynamicService)
    {
        abort_if(Gate::denies('dynamic_service_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('dynamicservices::admin.dynamic-services.edit', compact('dynamicService'));
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

        if ($dynamicService->category !== 'training') {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'يمكن إدارة الاجتماعات فقط للخدمات التدريبية'], 400);
            }

            return back()->withErrors(['error' => 'يمكن إدارة الاجتماعات فقط للخدمات التدريبية']);
        }

        $meetings = [];
        if ($request->has('program_meetings')) {
            $meetings = array_filter($request->program_meetings, function ($meeting) {
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
}
