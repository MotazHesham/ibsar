<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\Admin\MassDestroyFrontProjectRequest;
use App\Http\Requests\Admin\StoreFrontProjectRequest;
use App\Http\Requests\Admin\UpdateFrontProjectRequest;
use App\Models\FrontProject;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class FrontProjectsController extends Controller
{
    use MediaUploadingTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('front_project_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = FrontProject::query()->select(sprintf('%s.*', (new FrontProject)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'front_project_show';
                $editGate      = 'front_project_edit';
                $deleteGate    = 'front_project_delete';
                $crudRoutePart = 'front-projects';

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
            $table->editColumn('title', function ($row) {
                return $row->title ? $row->title : '';
            });

            $table->rawColumns(['actions', 'placeholder']);

            return $table->make(true);
        }

        return view('admin.frontProjects.index');
    }

    public function create()
    {
        abort_if(Gate::denies('front_project_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.frontProjects.create');
    }

    public function store(StoreFrontProjectRequest $request)
    {
        $frontProject = FrontProject::create($request->all());

        if ($request->input('image', false)) {
            $frontProject->addMedia(storage_path('tmp/uploads/' . basename($request->input('image'))))->toMediaCollection('image');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $frontProject->id]);
        }

        return redirect()->route('admin.front-projects.index');
    }

    public function edit(FrontProject $frontProject)
    {
        abort_if(Gate::denies('front_project_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.frontProjects.edit', compact('frontProject'));
    }

    public function update(UpdateFrontProjectRequest $request, FrontProject $frontProject)
    {
        $frontProject->update($request->all());

        if ($request->input('image', false)) {
            if (! $frontProject->image || $request->input('image') !== $frontProject->image->file_name) {
                if ($frontProject->image) {
                    $frontProject->image->delete();
                }
                $frontProject->addMedia(storage_path('tmp/uploads/' . basename($request->input('image'))))->toMediaCollection('image');
            }
        } elseif ($frontProject->image) {
            $frontProject->image->delete();
        }

        return redirect()->route('admin.front-projects.index');
    }

    public function show(FrontProject $frontProject)
    {
        abort_if(Gate::denies('front_project_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.frontProjects.show', compact('frontProject'));
    }

    public function destroy(FrontProject $frontProject)
    {
        abort_if(Gate::denies('front_project_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $frontProject->delete();

        return back();
    }

    public function massDestroy(MassDestroyFrontProjectRequest $request)
    {
        $frontProjects = FrontProject::find(request('ids'));

        foreach ($frontProjects as $frontProject) {
            $frontProject->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('front_project_create') && Gate::denies('front_project_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new FrontProject();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
}
