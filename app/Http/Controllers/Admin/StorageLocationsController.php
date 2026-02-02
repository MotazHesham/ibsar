<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MassDestroyStorageLocationRequest;
use App\Http\Requests\Admin\StoreStorageLocationRequest;
use App\Http\Requests\Admin\UpdateStorageLocationRequest;
use App\Models\StorageLocation;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class StorageLocationsController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('storage_location_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = StorageLocation::with(['parent'])->select(sprintf('%s.*', (new StorageLocation)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'storage_location_show';
                $editGate      = 'storage_location_edit';
                $deleteGate    = 'storage_location_delete';
                $crudRoutePart = 'storage-locations';

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
                return $row->type ? StorageLocation::TYPE_SELECT[$row->type] : '';
            });
            $table->editColumn('code', function ($row) {
                return $row->code ? $row->code : '';
            });
            $table->editColumn('name', function ($row) {
                return $row->name ? $row->name : '';
            });
            $table->addColumn('parent_code', function ($row) {
                return $row->parent ? $row->parent->code : '';
            });

            $table->editColumn('parent.name', function ($row) {
                return $row->parent ? (is_string($row->parent) ? $row->parent : $row->parent->name) : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'parent']);

            return $table->make(true);
        }

        return view('admin.storageLocations.index');
    }

    public function create()
    {
        abort_if(Gate::denies('storage_location_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $parents = StorageLocation::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.storageLocations.create', compact('parents'));
    }

    public function store(StoreStorageLocationRequest $request)
    {
        $storageLocation = StorageLocation::create($request->all());

        return redirect()->route('admin.storage-locations.index');
    }

    public function edit(StorageLocation $storageLocation)
    {
        abort_if(Gate::denies('storage_location_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $parents = StorageLocation::where('id', '!=', $storageLocation->id)->get()->pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        $storageLocation->load('parent');

        return view('admin.storageLocations.edit', compact('parents', 'storageLocation'));
    }

    public function update(UpdateStorageLocationRequest $request, StorageLocation $storageLocation)
    {
        $storageLocation->update($request->all());

        return redirect()->route('admin.storage-locations.index');
    }

    public function show(StorageLocation $storageLocation)
    {
        abort_if(Gate::denies('storage_location_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $storageLocation->load('parent', 'parentStorageLocations', 'storageLocationArchives');

        return view('admin.storageLocations.show', compact('storageLocation'));
    }

    public function destroy(StorageLocation $storageLocation)
    {
        abort_if(Gate::denies('storage_location_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $storageLocation->delete();

        return back();
    }

    public function massDestroy(MassDestroyStorageLocationRequest $request)
    {
        $storageLocations = StorageLocation::find(request('ids'));

        foreach ($storageLocations as $storageLocation) {
            $storageLocation->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
