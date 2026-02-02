<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MassDestroyArchiveRequest;
use App\Http\Requests\Admin\StoreArchiveRequest;
use App\Http\Requests\Admin\UpdateArchiveRequest;
use App\Models\Archive;
use App\Models\StorageLocation;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class ArchivesController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('archive_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Archive::with(['archived_by', 'storage_location'])->select(sprintf('%s.*', (new Archive)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = false;
                $editGate      = false;
                $deleteGate    = false;
                $crudRoutePart = 'archives';

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
            $table->editColumn('archiveable_type', function ($row) {
                return $row->archiveable_type ? Archive::ARCHIVEABLE_TYPES[$row->archiveable_type] : '';
            });
            $table->addColumn('archived_by_name', function ($row) {
                return $row->archived_by ? $row->archived_by->name : '';
            });
            $table->editColumn('archiveable_id', function ($row) { 
                if($row->archiveable_type == 'App\Models\Beneficiary'){
                    return  '<a class="btn btn-link" href="'.route('admin.beneficiaries.show', $row->archiveable_id).'">'.$row->archiveable_id.'</a>';
                }elseif($row->archiveable_type == 'App\Models\IncomingLetter'){
                    return  '<a class="btn btn-link" href="'.route('admin.incoming-letters.show', $row->archiveable_id).'">'.$row->archiveable_id.'</a>';
                }elseif($row->archiveable_type == 'App\Models\OutgoingLetter'){
                    return  '<a class="btn btn-link" href="'.route('admin.outgoing-letters.show', $row->archiveable_id).'">'.$row->archiveable_id.'</a>';
                }elseif($row->archiveable_type == 'App\Models\BeneficiaryOrder'){
                    return '<a class="btn btn-link" href="'.route('admin.beneficiary-orders.show', $row->archiveable_id).'">'.$row->archiveable_id.'</a>';
                }else{
                    return 'NAN';
                } 
            });

            $table->editColumn('archive_reason', function ($row) {
                return $row->archive_reason ? $row->archive_reason : '';
            });
            $table->editColumn('metadata', function ($row) {
                return $row->metadata ? $row->metadata : '';
            });
            $table->addColumn('storage_location_code', function ($row) {
                return $row->storage_location ? $row->storage_location->code : '';
            });

            $table->editColumn('storage_location.name', function ($row) {
                return $row->storage_location ? (is_string($row->storage_location) ? $row->storage_location : $row->storage_location->name) : '';
            });
            $table->editColumn('storage_location.code', function ($row) {
                return $row->storage_location ? (is_string($row->storage_location) ? $row->storage_location : $row->storage_location->code) : '';
            });
            $table->editColumn('storage_location.type', function ($row) {
                return $row->storage_location ? (is_string($row->storage_location) ? $row->storage_location : $row->storage_location->type) : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'archived_by', 'storage_location' , 'archiveable_id']);

            return $table->make(true);
        }

        return view('admin.archives.index');
    }

    public function create()
    {
        abort_if(Gate::denies('archive_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $archived_bies = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $storage_locations = StorageLocation::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.archives.create', compact('archived_bies', 'storage_locations'));
    }

    public function store(StoreArchiveRequest $request)
    {
        $model = 'App\Models\\' . $request->archiveable_model;
        $validatedRequest = $request->validated();
        $validatedRequest['archiveable_type'] = $model;
        $validatedRequest['archiveable_id'] = $request->archiveable_id;
        $validatedRequest['archived_by_id'] = auth()->user()->id;
        $validatedRequest['storage_location_id'] = $request->storage_location_id;
        $validatedRequest['archived_at'] = now()->format(config('panel.date_format') . ' ' . config('panel.time_format'));
        $validatedRequest['metadata'] = $request->metadata;
        $validatedRequest['archive_reason'] = $request->archive_reason;

        Archive::create($validatedRequest);

        $archiveable = $model::find($request->archiveable_id);
        $archiveable->is_archived = true;
        $archiveable->save();

        return redirect()->back();
    }

    public function edit(Archive $archive)
    {
        abort_if(Gate::denies('archive_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $archived_bies = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $storage_locations = StorageLocation::pluck('code', 'id')->prepend(trans('global.pleaseSelect'), '');

        $archive->load('archived_by', 'storage_location');

        return view('admin.archives.edit', compact('archive', 'archived_bies', 'storage_locations'));
    }

    public function update(UpdateArchiveRequest $request, Archive $archive)
    {
        $archive->update($request->all());

        return redirect()->route('admin.archives.index');
    }

    public function show(Archive $archive)
    {
        abort_if(Gate::denies('archive_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $archive->load('archived_by', 'storage_location');

        return view('admin.archives.show', compact('archive'));
    }

    public function destroy(Archive $archive)
    {
        abort_if(Gate::denies('archive_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $archive->delete();

        return back();
    }

    public function massDestroy(MassDestroyArchiveRequest $request)
    {
        $archives = Archive::find(request('ids'));

        foreach ($archives as $archive) {
            $archive->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
