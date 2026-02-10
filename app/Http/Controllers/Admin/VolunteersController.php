<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\Admin\MassDestroyVolunteerRequest;
use App\Http\Requests\Admin\StoreVolunteerRequest;
use App\Http\Requests\Admin\UpdateVolunteerRequest;
use App\Models\Volunteer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class VolunteersController extends Controller
{
    use MediaUploadingTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('volunteer_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Volunteer::query()->select(sprintf('%s.*', (new Volunteer)->table));
            $table = DataTables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate   = 'volunteer_show';
                $editGate   = 'volunteer_edit';
                $deleteGate = 'volunteer_delete';
                $crudRoutePart = 'volunteers';
                $appendButtons = [];
                if ($row->approved == 0) {
                    $appendButtons[] = [
                        'gate'   => 'volunteer_edit',
                        'title'  => trans('global.verify'),
                        'url'    => route('admin.volunteers.verify', $row->id),
                        'icon'   => 'ri-check-line',
                        'color'  => 'success',
                    ];
                }
                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row',
                    'appendButtons'
                ));
            });

            $table->editColumn('id', function ($row) {
                return $row->id ?: '';
            });
            $table->editColumn('name', function ($row) {
                return $row->name ?: '';
            });
            $table->editColumn('identity_num', function ($row) {
                return $row->identity_num ?: '';
            });
            $table->editColumn('email', function ($row) {
                return $row->email ?: '';
            });
            $table->editColumn('phone_number', function ($row) {
                return $row->phone_number ?: '';
            });
            $table->editColumn('approved', function ($row) {
                return $row->approved ? trans('global.yes') : trans('global.no');
            });

            $table->rawColumns(['actions', 'placeholder']);

            return $table->make(true);
        }

        return view('admin.volunteers.index');
    }

    public function create()
    {
        abort_if(Gate::denies('volunteer_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.volunteers.create');
    }

    public function store(StoreVolunteerRequest $request)
    {
        $volunteer = Volunteer::create($request->except('password', 'photo', 'cv'));

        if ($request->input('password')) {
            $volunteer->update(['password' => Hash::make($request->password)]);
        }

        if ($request->input('photo', false)) {
            $volunteer->addMedia(storage_path('tmp/uploads/' . basename($request->input('photo'))))->toMediaCollection('photo');
        }

        if ($request->input('cv', false)) {
            $volunteer->addMedia(storage_path('tmp/uploads/' . basename($request->input('cv'))))->toMediaCollection('cv');
        }

        return redirect()->route('admin.volunteers.index');
    }

    public function edit(Volunteer $volunteer)
    {
        abort_if(Gate::denies('volunteer_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.volunteers.edit', compact('volunteer'));
    }

    public function update(UpdateVolunteerRequest $request, Volunteer $volunteer)
    {
        $volunteer->update($request->except('password', 'photo', 'cv'));

        if ($request->input('password')) {
            $volunteer->update(['password' => Hash::make($request->password)]);
        }

        if ($request->input('photo', false)) {
            if (!$volunteer->photo || $request->input('photo') !== $volunteer->photo->file_name) {
                if ($volunteer->photo) {
                    $volunteer->photo->delete();
                }
                $volunteer->addMedia(storage_path('tmp/uploads/' . basename($request->input('photo'))))->toMediaCollection('photo');
            }
        } elseif ($volunteer->photo) {
            $volunteer->photo->delete();
        }

        if ($request->input('cv', false)) {
            if (!$volunteer->cv || $request->input('cv') !== $volunteer->cv->file_name) {
                if ($volunteer->cv) {
                    $volunteer->cv->delete();
                }
                $volunteer->addMedia(storage_path('tmp/uploads/' . basename($request->input('cv'))))->toMediaCollection('cv');
            }
        } elseif ($volunteer->cv) {
            $volunteer->cv->delete();
        }

        return redirect()->route('admin.volunteers.index');
    }

    public function show(Volunteer $volunteer)
    {
        abort_if(Gate::denies('volunteer_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $volunteer->load('volunteerTasks');

        return view('admin.volunteers.show', compact('volunteer'));
    }

    public function destroy(Volunteer $volunteer)
    {
        abort_if(Gate::denies('volunteer_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $volunteer->delete();

        return back();
    }

    public function massDestroy(MassDestroyVolunteerRequest $request)
    {
        $volunteers = Volunteer::find($request->input('ids', []));

        foreach ($volunteers as $volunteer) {
            $volunteer->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function verify($id)
    {
        abort_if(Gate::denies('volunteer_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $volunteer = Volunteer::findOrFail($id);

        return view('admin.volunteers.verify', compact('volunteer'));
    }

    public function verify_submit(Request $request)
    {
        $request->validate([
            'id'       => ['required', 'exists:volunteers,id'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $volunteer = Volunteer::findOrFail($request->id);
        $volunteer->update([
            'password' => Hash::make($request->password),
            'approved' => 1,
        ]);

        return redirect()->route('admin.volunteers.index');
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('volunteer_create') && Gate::denies('volunteer_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new Volunteer();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
}
