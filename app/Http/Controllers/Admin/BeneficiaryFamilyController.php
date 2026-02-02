<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\Admin\MassDestroyBeneficiaryFamilyRequest;
use App\Http\Requests\Admin\StoreBeneficiaryFamilyRequest;
use App\Http\Requests\Admin\UpdateBeneficiaryFamilyRequest;
use App\Models\Beneficiary;
use App\Models\BeneficiaryFamily;
use App\Models\DisabilityType;
use App\Models\FamilyRelationship;
use App\Models\HealthCondition;
use App\Models\MaritalStatus;
use App\Models\EducationalQualification;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class BeneficiaryFamilyController extends Controller
{
    use MediaUploadingTrait; 

    public function show(Request $request)
    {
        $beneficiaryFamily = BeneficiaryFamily::find($request->id);
        
        $html = view('admin.beneficiaryFamilies.show', compact('beneficiaryFamily'))->render();

        return response()->json([
            'html' => $html,  
        ]);
    }

    public function create(Request $request)
    {
        abort_if(Gate::denies('beneficiary_family_create'), Response::HTTP_FORBIDDEN, '403 Forbidden'); 

        $beneficiary = Beneficiary::find($request->beneficiary_id);

        $family_relationships = FamilyRelationship::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $marital_statuses = MaritalStatus::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $health_conditions = HealthCondition::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $disability_types = DisabilityType::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $educational_qualifications = EducationalQualification::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $html = view('admin.beneficiaryFamilies.create', compact('beneficiary', 'disability_types', 'family_relationships', 'health_conditions', 'marital_statuses', 'educational_qualifications'))->render();

        return response()->json([
            'html' => $html,  
        ]);
    }

    public function store(StoreBeneficiaryFamilyRequest $request)
    { 
        $beneficiaryFamily = BeneficiaryFamily::create($request->all());

        if ($request->input('photo', false)) {
            $beneficiaryFamily->addMedia(storage_path('tmp/uploads/' . basename($request->input('photo'))))->toMediaCollection('photo');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $beneficiaryFamily->id]);
        }
        $beneficiaryFamilies = BeneficiaryFamily::where('beneficiary_id', $request->beneficiary_id)->orderBy('id', 'desc')->get();
        $html = view('admin.beneficiaryFamilies.index', compact('beneficiaryFamilies'))->render();

        return response()->json([
            'html' => $html,
            'wrapper' => '#wrapper-family-information',
            'message' => trans('global.flash.created', ['title' => trans('cruds.beneficiaryFamily.title_singular')])
        ]);
    }

    public function edit(Request $request)
    {
        abort_if(Gate::denies('beneficiary_family_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden'); 

        $beneficiaryFamily = BeneficiaryFamily::find($request->id);

        $family_relationships = FamilyRelationship::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $marital_statuses = MaritalStatus::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $health_conditions = HealthCondition::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $disability_types = DisabilityType::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $educational_qualifications = EducationalQualification::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $beneficiaryFamily->load('beneficiary', 'family_relationship', 'marital_status', 'health_condition', 'disability_type', 'educational_qualification');

        $html = view('admin.beneficiaryFamilies.edit', compact( 'beneficiaryFamily', 'disability_types', 'family_relationships', 'health_conditions', 'marital_statuses', 'educational_qualifications'))->render();

        return response()->json([
            'html' => $html,  
        ]);
    }

    public function update(UpdateBeneficiaryFamilyRequest $request, BeneficiaryFamily $beneficiaryFamily)
    {
        $beneficiaryFamily->update($request->all());

        if ($request->input('photo', false)) {
            if (! $beneficiaryFamily->photo || $request->input('photo') !== $beneficiaryFamily->photo->file_name) {
                if ($beneficiaryFamily->photo) {
                    $beneficiaryFamily->photo->delete();
                }
                $beneficiaryFamily->addMedia(storage_path('tmp/uploads/' . basename($request->input('photo'))))->toMediaCollection('photo');
            }
        } elseif ($beneficiaryFamily->photo) {
            $beneficiaryFamily->photo->delete();
        }

        $beneficiaryFamilies = BeneficiaryFamily::where('beneficiary_id', $beneficiaryFamily->beneficiary_id)->orderBy('id', 'desc')->get();
        $html = view('admin.beneficiaryFamilies.index', compact('beneficiaryFamilies'))->render();

        return response()->json([
            'html' => $html,  
            'wrapper' => '#wrapper-family-information',
            'message' => trans('global.flash.updated', ['title' => trans('cruds.beneficiaryFamily.title_singular')])
        ]);
    } 

    public function destroy(Request $request)
    {
        abort_if(Gate::denies('beneficiary_family_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $beneficiaryFamily = BeneficiaryFamily::find($request->id);

        $beneficiaryFamily->delete();

        $beneficiaryFamilies = BeneficiaryFamily::where('beneficiary_id', $beneficiaryFamily->beneficiary_id)->orderBy('id', 'desc')->get();
        $html = view('admin.beneficiaryFamilies.index', compact('beneficiaryFamilies'))->render();

        return response()->json([ 
            'message' => trans('global.flash.deleted', ['title' => trans('cruds.beneficiaryFamily.title_singular')])
        ]);
    } 

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('beneficiary_family_create') && Gate::denies('beneficiary_family_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new BeneficiaryFamily();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
}
