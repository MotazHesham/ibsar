<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MassDestroyServiceRequest;
use App\Http\Requests\Admin\StoreServiceRequest;
use App\Http\Requests\Admin\UpdateServiceRequest;
use App\Models\Building;
use App\Models\Course;
use App\Models\Service;
use App\Models\Consultant;
use App\Models\DynamicService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class ServicesController extends Controller
{
    public function servicesByType(Request $request)
    {
        $services = Service::where('type', $request->service_type)->get()->map(function($service) {
            return [
                'id' => $service->id,
                'title' => $service->title
            ];
        });
        return response()->json($services);
    }

    public function list(Request $request)
    {
        abort_if(Gate::denies('service_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        
        $serviceCounts = Service::selectRaw('type, count(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray(); 
        $coursesCount = Course::count();
        $buildingsCount = Building::count();
        $consultantsCount = Consultant::count();
        
        // Get dynamic services count if feature is enabled
        $dynamicServicesCount = 0;
        $dynamicServicesCount = DynamicService::active()->count();
        
        return view('admin.services.list', compact('serviceCounts', 'coursesCount', 'buildingsCount', 'consultantsCount', 'dynamicServicesCount'));
    }

    public function index(Request $request){
        
        abort_if(Gate::denies('service_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            $query = Service::query();
            if($request->type && in_array($request->type, array_keys(Service::TYPE_SELECT))){ 
                $query->where('type', $request->type);
            }else{
                abort(404);
            }
            $query->select(sprintf('%s.*', (new Service)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = false;
                $editGate      = 'service_edit';
                $deleteGate    = 'service_delete';
                $crudRoutePart = 'services';

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
            $table->editColumn('type', function ($row) {
                return $row->type ? Service::TYPE_SELECT[$row->type] : '';
            });
            $table->editColumn('title', function ($row) {
                return $row->title ? $row->title : '';
            });
            $table->editColumn('quantity', function ($row) {
                return $row->quantity ? $row->quantity : '';
            });

            $table->editColumn('active', function ($row) {
                $value = $row->active ? 'checked' : '';  
                return '<div class="custom-toggle-switch toggle-md ms-2">
                    <input onchange="updateStatuses(this, \'active\', \'' . addslashes("App\Models\Service") . '\')" 
                        value="' . $row->id . '"  id="active-' . $row->id . '" type="checkbox" ' . $value . '>
                    <label for="active-' . $row->id . '" class="label-success mb-2"></label>
                </div>';
            });

            $table->rawColumns(['actions', 'placeholder', 'active']);

            return $table->make(true);
        }
        return view('admin.services.index');
    }

    public function create(Request $request)
    {
        abort_if(Gate::denies('service_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if($request->type && in_array($request->type, array_keys(Service::TYPE_SELECT))){ 
            $type = $request->type;
            return view('admin.services.create', compact('type'));
        }else{
            abort(404);
        }
    }

    public function store(StoreServiceRequest $request)
    {
        $service = Service::create($request->all());

        return redirect()->route('admin.services.index', ['type' => $request->type]);
    }

    public function edit(Service $service)
    {
        abort_if(Gate::denies('service_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.services.edit', compact('service'));
    }

    public function update(UpdateServiceRequest $request, Service $service)
    {
        $service->update($request->all());

        return redirect()->route('admin.services.index', ['type' => $service->type]);
    }

    public function show(Service $service)
    {
        abort_if(Gate::denies('service_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $service->load('serviceBeneficiaryOrders');

        return view('admin.services.show', compact('service'));
    }

    public function destroy(Service $service)
    {
        abort_if(Gate::denies('service_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $service->delete();

        return back();
    }

    public function massDestroy(MassDestroyServiceRequest $request)
    {
        $services = Service::find(request('ids'));

        foreach ($services as $service) {
            $service->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
