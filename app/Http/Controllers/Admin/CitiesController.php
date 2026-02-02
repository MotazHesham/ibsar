<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MassDestroyCityRequest;
use App\Http\Requests\Admin\StoreCityRequest;
use App\Http\Requests\Admin\UpdateCityRequest;
use App\Models\City;
use App\Models\District;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class CitiesController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('city_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = City::with(['districts'])->select(sprintf('%s.*', (new City)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'city_show';
                $editGate      = 'city_edit';
                $deleteGate    = 'city_delete';
                $crudRoutePart = 'cities';

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
            $table->editColumn('districts', function ($row) {
                $labels = [];
                foreach ($row->districts as $district) {
                    $labels[] = sprintf('<span class="badge bg-info label-many">%s</span>', $district->name);
                }

                return implode(' ', $labels);
            });

            $table->rawColumns(['actions', 'placeholder', 'districts']);

            return $table->make(true);
        }

        return view('admin.cities.index');
    }

    public function create()
    {
        abort_if(Gate::denies('city_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $districts = District::pluck('name', 'id');

        return view('admin.cities.create', compact('districts'));
    }

    public function store(StoreCityRequest $request)
    {
        $city = City::create($request->all());
        $city->districts()->sync($request->input('districts', []));

        return redirect()->route('admin.cities.index');
    }

    public function edit(City $city)
    {
        abort_if(Gate::denies('city_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $districts = District::pluck('name', 'id');

        $city->load('districts');

        return view('admin.cities.edit', compact('city', 'districts'));
    }

    public function update(UpdateCityRequest $request, City $city)
    { 
        $city->setTranslation('name', $request->lang, $request->name);
        $city->save();
        $city->districts()->sync($request->input('districts', []));

        return redirect()->route('admin.cities.index');
    }

    public function show(City $city)
    {
        abort_if(Gate::denies('city_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $city->load('districts');

        return view('admin.cities.show', compact('city'));
    }

    public function destroy(City $city)
    {
        abort_if(Gate::denies('city_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $city->delete();

        return back();
    }

    public function massDestroy(MassDestroyCityRequest $request)
    {
        $cities = City::find(request('ids'));

        foreach ($cities as $city) {
            $city->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
