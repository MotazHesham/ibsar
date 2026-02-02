<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MassDestroyAccommodationEntityRequest;
use App\Http\Requests\Admin\StoreAccommodationEntityRequest;
use App\Http\Requests\Admin\UpdateAccommodationEntityRequest;
use App\Models\AccommodationEntity;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class AccommodationEntityController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('accommodation_entity_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = AccommodationEntity::query()->select(sprintf('%s.*', (new AccommodationEntity)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'accommodation_entity_show';
                $editGate      = 'accommodation_entity_edit';
                $deleteGate    = 'accommodation_entity_delete';
                $crudRoutePart = 'accommodation-entities';

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
            $table->editColumn('type', function ($row) {
                return $row->type ? AccommodationEntity::$TYPE_SELECT[$row->type] : '';
            });

            $table->rawColumns(['actions', 'placeholder']);

            return $table->make(true);
        }

        return view('admin.accommodationEntities.index');
    }

    public function create()
    {
        abort_if(Gate::denies('accommodation_entity_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.accommodationEntities.create');
    }

    public function store(StoreAccommodationEntityRequest $request)
    {
        $accommodationEntity = AccommodationEntity::create($request->all());

        return redirect()->route('admin.accommodation-entities.index');
    }

    public function edit(AccommodationEntity $accommodationEntity)
    {
        abort_if(Gate::denies('accommodation_entity_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.accommodationEntities.edit', compact('accommodationEntity'));
    }

    public function update(UpdateAccommodationEntityRequest $request, AccommodationEntity $accommodationEntity)
    {
        $accommodationEntity->setTranslation('name', $request->lang, $request->name);
        $accommodationEntity->type = $request->type;
        $accommodationEntity->save();

        return redirect()->route('admin.accommodation-entities.index');
    }

    public function show(AccommodationEntity $accommodationEntity)
    {
        abort_if(Gate::denies('accommodation_entity_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.accommodationEntities.show', compact('accommodationEntity'));
    }

    public function destroy(AccommodationEntity $accommodationEntity)
    {
        abort_if(Gate::denies('accommodation_entity_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $accommodationEntity->delete();

        return back();
    }

    public function massDestroy(MassDestroyAccommodationEntityRequest $request)
    {
        $accommodationEntities = AccommodationEntity::find(request('ids'));

        foreach ($accommodationEntities as $accommodationEntity) {
            $accommodationEntity->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
} 