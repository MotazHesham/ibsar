<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MassDestroyServiceStatusRequest;
use App\Http\Requests\Admin\StoreServiceStatusRequest;
use App\Http\Requests\Admin\UpdateServiceStatusRequest;
use App\Models\ServiceStatus;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class ServiceStatusesController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('service_status_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = ServiceStatus::query()->select(sprintf('%s.*', (new ServiceStatus)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'service_status_show';
                $editGate      = 'service_status_edit';
                $deleteGate    = 'service_status_delete';
                $crudRoutePart = 'service-statuses';

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
            $table->editColumn('name', function ($row) {
                return $row->name ? $row->name : '';
            });
            $table->editColumn('badge_class', function ($row) {
                return $row->badge_class ? ServiceStatus::BADGE_CLASS_SELECT[$row->badge_class] : '';
            });
            $table->editColumn('ordering', function ($row) {
                return $row->ordering ? $row->ordering : '';
            });

            $table->rawColumns(['actions', 'placeholder']);

            return $table->make(true);
        }

        return view('admin.serviceStatuses.index');
    }

    public function create()
    {
        abort_if(Gate::denies('service_status_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.serviceStatuses.create');
    }

    public function store(StoreServiceStatusRequest $request)
    {
        $serviceStatus = ServiceStatus::create($request->all());

        return redirect()->route('admin.service-statuses.index');
    }

    public function edit(ServiceStatus $serviceStatus)
    {
        abort_if(Gate::denies('service_status_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.serviceStatuses.edit', compact('serviceStatus'));
    }

    public function update(UpdateServiceStatusRequest $request, ServiceStatus $serviceStatus)
    {
        $serviceStatus->update($request->except('lang','name'));
        $serviceStatus->setTranslation('name', $request->lang, $request->name);
        $serviceStatus->save();

        return redirect()->route('admin.service-statuses.index');
    }

    public function show(ServiceStatus $serviceStatus)
    {
        abort_if(Gate::denies('service_status_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.serviceStatuses.show', compact('serviceStatus'));
    }

    public function destroy(ServiceStatus $serviceStatus)
    {
        abort_if(Gate::denies('service_status_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $serviceStatus->delete();

        return back();
    }

    public function massDestroy(MassDestroyServiceStatusRequest $request)
    {
        $serviceStatuses = ServiceStatus::find(request('ids'));

        foreach ($serviceStatuses as $serviceStatus) {
            $serviceStatus->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
