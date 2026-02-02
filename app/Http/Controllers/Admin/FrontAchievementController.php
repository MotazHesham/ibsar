<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\Admin\MassDestroyFrontAchievementRequest;
use App\Http\Requests\Admin\StoreFrontAchievementRequest;
use App\Http\Requests\Admin\UpdateFrontAchievementRequest;
use App\Models\FrontAchievement;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class FrontAchievementController extends Controller
{
    use MediaUploadingTrait;
    public function index(Request $request)
    {
        abort_if(Gate::denies('front_achievement_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = FrontAchievement::query()->select(sprintf('%s.*', (new FrontAchievement)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'front_achievement_show';
                $editGate      = 'front_achievement_edit';
                $deleteGate    = 'front_achievement_delete';
                $crudRoutePart = 'front-achievements';

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
            $table->editColumn('icon', function ($row) {
                if ($photo = $row->icon) {
                    return sprintf(
                        '<a href="%s" target="_blank"><img src="%s" width="50px" height="50px"></a>',
                        $photo->url,
                        $photo->thumbnail
                    );
                } 
                return '';
            });
            $table->editColumn('title', function ($row) {
                return $row->title ? $row->title : '';
            });
            $table->editColumn('achievement', function ($row) {
                return $row->achievement ? $row->achievement : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'icon']);

            return $table->make(true);
        }

        return view('admin.frontAchievements.index');
    }

    public function create()
    {
        abort_if(Gate::denies('front_achievement_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.frontAchievements.create');
    }

    public function store(StoreFrontAchievementRequest $request)
    {
        $frontAchievement = FrontAchievement::create($request->all());

        if ($request->input('icon', false)) {
            $frontAchievement->addMedia(storage_path('tmp/uploads/' . basename($request->input('icon'))))->toMediaCollection('icon');
        }
        return redirect()->route('admin.front-achievements.index');
    }

    public function edit(FrontAchievement $frontAchievement)
    {
        abort_if(Gate::denies('front_achievement_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.frontAchievements.edit', compact('frontAchievement'));
    }

    public function update(UpdateFrontAchievementRequest $request, FrontAchievement $frontAchievement)
    {
        $frontAchievement->update($request->all());

        if ($request->input('icon', false)) {
            if (! $frontAchievement->icon || $request->input('icon') !== $frontAchievement->icon->file_name) {
                if ($frontAchievement->icon) {
                    $frontAchievement->icon->delete();
                }
                $frontAchievement->addMedia(storage_path('tmp/uploads/' . basename($request->input('icon'))))->toMediaCollection('icon');
            }
        } elseif ($frontAchievement->icon) {
            $frontAchievement->icon->delete();
        }
        return redirect()->route('admin.front-achievements.index');
    }

    public function show(FrontAchievement $frontAchievement)
    {
        abort_if(Gate::denies('front_achievement_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.frontAchievements.show', compact('frontAchievement'));
    }

    public function destroy(FrontAchievement $frontAchievement)
    {
        abort_if(Gate::denies('front_achievement_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $frontAchievement->delete();

        return back();
    }

    public function massDestroy(MassDestroyFrontAchievementRequest $request)
    {
        $frontAchievements = FrontAchievement::find(request('ids'));

        foreach ($frontAchievements as $frontAchievement) {
            $frontAchievement->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
