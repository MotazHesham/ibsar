<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MassDestroyBuildingRequest;
use App\Http\Requests\Admin\StoreBuildingRequest;
use App\Http\Requests\Admin\UpdateBuildingRequest;
use App\Models\Building;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class BuildingsController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('building_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Building::query()->select(sprintf('%s.*', (new Building)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'building_show';
                $editGate      = 'building_edit';
                $deleteGate    = 'building_delete';
                $crudRoutePart = 'buildings';

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
            $table->editColumn('number_of_floors', function ($row) {
                return $row->number_of_floors ? $row->number_of_floors : '';
            });
            $table->editColumn('number_of_apartments', function ($row) {
                return $row->number_of_apartments ? $row->number_of_apartments : '';
            });
            $table->editColumn('address', function ($row) {
                return $row->address ? $row->address : '';
            });

            $table->rawColumns(['actions', 'placeholder']);

            return $table->make(true);
        }

        return view('admin.buildings.index');
    }

    public function create()
    {
        abort_if(Gate::denies('building_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.buildings.create');
    }

    public function store(StoreBuildingRequest $request)
    {
        $building = Building::create($request->all());

        return redirect()->route('admin.buildings.index');
    }

    public function edit(Building $building)
    {
        abort_if(Gate::denies('building_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.buildings.edit', compact('building'));
    }

    public function update(UpdateBuildingRequest $request, Building $building)
    {
        $building->update($request->all());

        return redirect()->route('admin.buildings.index');
    }

    public function show(Building $building)
    {
        abort_if(Gate::denies('building_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.buildings.show', compact('building'));
    }

    public function destroy(Building $building)
    {
        abort_if(Gate::denies('building_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $building->delete();

        return back();
    }

    public function massDestroy(MassDestroyBuildingRequest $request)
    {
        $buildings = Building::find(request('ids'));

        foreach ($buildings as $building) {
            $building->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
