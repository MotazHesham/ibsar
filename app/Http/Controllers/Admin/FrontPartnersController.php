<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\Admin\MassDestroyFrontPartnerRequest;
use App\Http\Requests\Admin\StoreFrontPartnerRequest;
use App\Http\Requests\Admin\UpdateFrontPartnerRequest;
use App\Models\FrontPartner;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class FrontPartnersController extends Controller
{
    use MediaUploadingTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('front_partner_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = FrontPartner::query()->select(sprintf('%s.*', (new FrontPartner)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'front_partner_show';
                $editGate      = 'front_partner_edit';
                $deleteGate    = 'front_partner_delete';
                $crudRoutePart = 'front-partners';

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
            $table->editColumn('image', function ($row) {
                if ($photo = $row->image) {
                    return sprintf(
                        '<a href="%s" target="_blank"><img src="%s" width="50px" height="50px"></a>',
                        $photo->url,
                        $photo->thumbnail
                    );
                }

                return '';
            });

            $table->rawColumns(['actions', 'placeholder', 'image']);

            return $table->make(true);
        }

        return view('admin.frontPartners.index');
    }

    public function create()
    {
        abort_if(Gate::denies('front_partner_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.frontPartners.create');
    }

    public function store(StoreFrontPartnerRequest $request)
    {
        $frontPartner = FrontPartner::create($request->all());

        if ($request->input('image', false)) {
            $frontPartner->addMedia(storage_path('tmp/uploads/' . basename($request->input('image'))))->toMediaCollection('image');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $frontPartner->id]);
        }

        return redirect()->route('admin.front-partners.index');
    }

    public function edit(FrontPartner $frontPartner)
    {
        abort_if(Gate::denies('front_partner_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.frontPartners.edit', compact('frontPartner'));
    }

    public function update(UpdateFrontPartnerRequest $request, FrontPartner $frontPartner)
    {
        $frontPartner->update($request->all());

        if ($request->input('image', false)) {
            if (! $frontPartner->image || $request->input('image') !== $frontPartner->image->file_name) {
                if ($frontPartner->image) {
                    $frontPartner->image->delete();
                }
                $frontPartner->addMedia(storage_path('tmp/uploads/' . basename($request->input('image'))))->toMediaCollection('image');
            }
        } elseif ($frontPartner->image) {
            $frontPartner->image->delete();
        }

        return redirect()->route('admin.front-partners.index');
    }

    public function destroy(FrontPartner $frontPartner)
    {
        abort_if(Gate::denies('front_partner_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $frontPartner->delete();

        return back();
    }

    public function massDestroy(MassDestroyFrontPartnerRequest $request)
    {
        $frontPartners = FrontPartner::find(request('ids'));

        foreach ($frontPartners as $frontPartner) {
            $frontPartner->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('front_partner_create') && Gate::denies('front_partner_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new FrontPartner();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
}
