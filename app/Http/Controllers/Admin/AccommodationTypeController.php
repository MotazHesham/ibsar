<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MassDestroyAccommodationTypeRequest;
use App\Http\Requests\Admin\StoreAccommodationTypeRequest;
use App\Http\Requests\Admin\UpdateAccommodationTypeRequest;
use App\Models\AccommodationType;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class AccommodationTypeController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('accommodation_type_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = AccommodationType::query()->select(sprintf('%s.*', (new AccommodationType)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'accommodation_type_show';
                $editGate      = 'accommodation_type_edit';
                $deleteGate    = 'accommodation_type_delete';
                $crudRoutePart = 'accommodation-types';

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

            $table->rawColumns(['actions', 'placeholder']);

            return $table->make(true);
        }

        return view('admin.accommodationTypes.index');
    }

    public function create()
    {
        abort_if(Gate::denies('accommodation_type_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.accommodationTypes.create');
    }

    public function store(StoreAccommodationTypeRequest $request)
    {
        $accommodationType = AccommodationType::create($request->all());

        return redirect()->route('admin.accommodation-types.index');
    }

    public function edit(AccommodationType $accommodationType)
    {
        abort_if(Gate::denies('accommodation_type_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.accommodationTypes.edit', compact('accommodationType'));
    }

    public function update(UpdateAccommodationTypeRequest $request, AccommodationType $accommodationType)
    {
        $accommodationType->setTranslation('name', $request->lang, $request->name);
        $accommodationType->save();

        return redirect()->route('admin.accommodation-types.index');
    }

    public function show(AccommodationType $accommodationType)
    {
        abort_if(Gate::denies('accommodation_type_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.accommodationTypes.show', compact('accommodationType'));
    }

    public function destroy(AccommodationType $accommodationType)
    {
        abort_if(Gate::denies('accommodation_type_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $accommodationType->delete();

        return back();
    }

    public function massDestroy(MassDestroyAccommodationTypeRequest $request)
    {
        $accommodationTypes = AccommodationType::find(request('ids'));

        foreach ($accommodationTypes as $accommodationType) {
            $accommodationType->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
} 