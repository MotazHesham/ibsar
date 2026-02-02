<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\Admin\MassDestroyBeneficiaryOrderFollowupRequest;
use App\Http\Requests\Admin\StoreBeneficiaryOrderFollowupRequest;
use App\Http\Requests\Admin\UpdateBeneficiaryOrderFollowupRequest;
use App\Models\BeneficiaryOrder;
use App\Models\BeneficiaryOrderFollowup;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class BeneficiaryOrderFollowupsController extends Controller
{
    use MediaUploadingTrait; 

    public function create(Request $request)
    {
        abort_if(Gate::denies('beneficiary_order_followup_create'), Response::HTTP_FORBIDDEN, '403 Forbidden'); 

        $beneficiaryOrder = BeneficiaryOrder::find($request->beneficiary_order_id);

        $html = view('admin.beneficiaryOrderFollowups.create', compact('beneficiaryOrder'))->render();

        return response()->json([
            'html' => $html,  
        ]); 
    }

    public function store(StoreBeneficiaryOrderFollowupRequest $request)
    {
        $validatedRequest = $request->validated();
        $validatedRequest['user_id'] = auth()->user()->id;
        $beneficiaryOrderFollowup = BeneficiaryOrderFollowup::create($validatedRequest);

        foreach ($request->input('attachments', []) as $file) {
            $beneficiaryOrderFollowup->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('attachments');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $beneficiaryOrderFollowup->id]);
        }
        $beneficiaryOrderFollowups = BeneficiaryOrderFollowup::where('beneficiary_order_id', $request->beneficiary_order_id)->get();
        $html = view('admin.beneficiaryOrderFollowups.index', compact('beneficiaryOrderFollowups'))->render();

        return response()->json([
            'html' => $html,
            'wrapper' => '#wrapper-order-followups',
            'message' => trans('global.flash.created', ['title' => trans('cruds.beneficiaryOrderFollowup.title_singular')])
        ]);
    }

    public function edit(Request $request)
    {
        abort_if(Gate::denies('beneficiary_order_followup_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden'); 

        $beneficiaryOrderFollowup = BeneficiaryOrderFollowup::find($request->id);

        $html = view('admin.beneficiaryOrderFollowups.edit', compact('beneficiaryOrderFollowup'))->render();

        return response()->json([
            'html' => $html,  
        ]);
    }

    public function update(UpdateBeneficiaryOrderFollowupRequest $request, BeneficiaryOrderFollowup $beneficiaryOrderFollowup)
    {
        $beneficiaryOrderFollowup->update($request->all());

        if (count($beneficiaryOrderFollowup->attachments) > 0) {
            foreach ($beneficiaryOrderFollowup->attachments as $media) {
                if (! in_array($media->file_name, $request->input('attachments', []))) {
                    $media->delete();
                }
            }
        }
        $media = $beneficiaryOrderFollowup->attachments->pluck('file_name')->toArray();
        foreach ($request->input('attachments', []) as $file) {
            if (count($media) === 0 || ! in_array($file, $media)) {
                $beneficiaryOrderFollowup->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('attachments');
            }
        }

        $beneficiaryOrderFollowups = BeneficiaryOrderFollowup::where('beneficiary_order_id', $beneficiaryOrderFollowup->beneficiary_order_id)->get();
        $html = view('admin.beneficiaryOrderFollowups.index', compact('beneficiaryOrderFollowups'))->render();

        return response()->json([
            'html' => $html,
            'wrapper' => '#wrapper-order-followups',
            'message' => trans('global.flash.updated', ['title' => trans('cruds.beneficiaryOrderFollowup.title_singular')])
        ]);
    } 

    public function destroy(Request $request)
    {
        abort_if(Gate::denies('beneficiary_order_followup_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $beneficiaryOrderFollowup = BeneficiaryOrderFollowup::find($request->id);

        $beneficiaryOrderFollowup->delete();

        $beneficiaryOrderFollowups = BeneficiaryOrderFollowup::where('beneficiary_order_id', $beneficiaryOrderFollowup->beneficiary_order_id)->get();
        $html = view('admin.beneficiaryOrderFollowups.index', compact('beneficiaryOrderFollowups'))->render();

        return response()->json([
            'html' => $html,
            'wrapper' => '#wrapper-order-followups',
            'message' => trans('global.flash.deleted', ['title' => trans('cruds.beneficiaryOrderFollowup.title_singular')])
        ]);
    } 

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('beneficiary_order_followup_create') && Gate::denies('beneficiary_order_followup_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new BeneficiaryOrderFollowup();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
}
