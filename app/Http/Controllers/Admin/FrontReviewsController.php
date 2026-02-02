<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\Admin\MassDestroyFrontReviewRequest;
use App\Http\Requests\Admin\StoreFrontReviewRequest;
use App\Http\Requests\Admin\UpdateFrontReviewRequest;
use App\Models\FrontReview;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class FrontReviewsController extends Controller
{
    use MediaUploadingTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('front_review_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = FrontReview::query()->select(sprintf('%s.*', (new FrontReview)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'front_review_show';
                $editGate      = 'front_review_edit';
                $deleteGate    = 'front_review_delete';
                $crudRoutePart = 'front-reviews';

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
            $table->editColumn('photo', function ($row) {
                if ($photo = $row->photo) {
                    return sprintf(
                        '<a href="%s" target="_blank"><img src="%s" width="50px" height="50px"></a>',
                        $photo->url,
                        $photo->thumbnail
                    );
                }

                return '';
            });
            $table->editColumn('name', function ($row) {
                return $row->name ? $row->name : '';
            });
            $table->editColumn('email', function ($row) {
                return $row->email ? $row->email : '';
            });
            $table->editColumn('review', function ($row) {
                return $row->review ? $row->review : '';
            });
            $table->editColumn('rate', function ($row) {
                return $row->rate ? $row->rate : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'photo']);

            return $table->make(true);
        }

        return view('admin.frontReviews.index');
    }

    public function create()
    {
        abort_if(Gate::denies('front_review_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.frontReviews.create');
    }

    public function store(StoreFrontReviewRequest $request)
    {
        $frontReview = FrontReview::create($request->all());

        if ($request->input('photo', false)) {
            $frontReview->addMedia(storage_path('tmp/uploads/' . basename($request->input('photo'))))->toMediaCollection('photo');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $frontReview->id]);
        }

        return redirect()->route('admin.front-reviews.index');
    }

    public function edit(FrontReview $frontReview)
    {
        abort_if(Gate::denies('front_review_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.frontReviews.edit', compact('frontReview'));
    }

    public function update(UpdateFrontReviewRequest $request, FrontReview $frontReview)
    {
        $frontReview->update($request->all());

        if ($request->input('photo', false)) {
            if (! $frontReview->photo || $request->input('photo') !== $frontReview->photo->file_name) {
                if ($frontReview->photo) {
                    $frontReview->photo->delete();
                }
                $frontReview->addMedia(storage_path('tmp/uploads/' . basename($request->input('photo'))))->toMediaCollection('photo');
            }
        } elseif ($frontReview->photo) {
            $frontReview->photo->delete();
        }

        return redirect()->route('admin.front-reviews.index');
    }

    public function show(FrontReview $frontReview)
    {
        abort_if(Gate::denies('front_review_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.frontReviews.show', compact('frontReview'));
    }

    public function destroy(FrontReview $frontReview)
    {
        abort_if(Gate::denies('front_review_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $frontReview->delete();

        return back();
    }

    public function massDestroy(MassDestroyFrontReviewRequest $request)
    {
        $frontReviews = FrontReview::find(request('ids'));

        foreach ($frontReviews as $frontReview) {
            $frontReview->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('front_review_create') && Gate::denies('front_review_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new FrontReview();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
}
