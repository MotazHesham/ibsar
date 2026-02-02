<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MassDestroyRegionRequest;
use App\Http\Requests\Admin\StoreRegionRequest;
use App\Http\Requests\Admin\UpdateRegionRequest;
use App\Models\City;
use App\Models\Region;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class RegionsController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('region_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Region::with(['cities'])->select(sprintf('%s.*', (new Region)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'region_show';
                $editGate      = 'region_edit';
                $deleteGate    = 'region_delete';
                $crudRoutePart = 'regions';

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
            $table->editColumn('cities', function ($row) {
                $labels = [];
                foreach ($row->cities as $city) {
                    $labels[] = sprintf('<span class="badge bg-info label-many">%s</span>', $city->name);
                }

                return implode(' ', $labels);
            });

            $table->rawColumns(['actions', 'placeholder', 'cities']);

            return $table->make(true);
        }

        return view('admin.regions.index');
    }

    public function create()
    {
        abort_if(Gate::denies('region_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $cities = City::pluck('name', 'id');

        return view('admin.regions.create', compact('cities'));
    }

    public function store(StoreRegionRequest $request)
    {
        $region = Region::create($request->all());
        $region->cities()->sync($request->input('cities', []));

        return redirect()->route('admin.regions.index');
    }

    public function edit(Region $region)
    {
        abort_if(Gate::denies('region_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $cities = City::pluck('name', 'id');

        $region->load('cities');

        return view('admin.regions.edit', compact('cities', 'region'));
    }

    public function update(UpdateRegionRequest $request, Region $region)
    { 
        $region->setTranslation('name', $request->lang, $request->name);
        $region->save();
        
        $region->cities()->sync($request->input('cities', []));

        return redirect()->route('admin.regions.index');
    }

    public function show(Region $region)
    {
        abort_if(Gate::denies('region_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $region->load('cities');

        return view('admin.regions.show', compact('region'));
    }

    public function destroy(Region $region)
    {
        abort_if(Gate::denies('region_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $region->delete();

        return back();
    }

    public function massDestroy(MassDestroyRegionRequest $request)
    {
        $regions = Region::find(request('ids'));

        foreach ($regions as $region) {
            $region->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
