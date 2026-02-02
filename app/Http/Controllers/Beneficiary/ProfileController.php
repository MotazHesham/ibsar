<?php

namespace App\Http\Controllers\Beneficiary;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Models\Beneficiary;
use App\Http\Requests\Beneficiary\UpdateProfileBeneficiaryRequest;
use App\Models\City;
use App\Models\Nationality;
use App\Models\MaritalStatus;
use App\Models\BeneficiaryCategory;
use App\Models\AccommodationType;
use App\Models\AccommodationEntity;
use App\Models\JobType;
use App\Models\EducationalQualification;
use App\Models\District;
use App\Models\HealthCondition;
use App\Models\DisabilityType;
use App\Models\EconomicStatus;
use App\Models\Region;
use App\Models\RequiredDocument;
use Illuminate\Http\Request;
use App\Services\BeneficiaryService;

class ProfileController extends Controller
{
    use MediaUploadingTrait;
    public function __construct(
        protected BeneficiaryService $beneficiaryService
    ) {}
    public function show()
    { 
        $user = auth()->user();
        $beneficiary = $user->beneficiary;

        $nationalities = Nationality::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $marital_statuses = MaritalStatus::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $beneficiary_categories = BeneficiaryCategory::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $accommodation_types = AccommodationType::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $accommodation_entities_charity = AccommodationEntity::where('type', 'charity')->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $accommodation_entities_social = AccommodationEntity::where('type', 'social')->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $job_types = JobType::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $job_types_need_job_details = JobType::where('required_job_details', 1)->pluck('id');

        $educational_qualifications = EducationalQualification::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $region = Region::find($beneficiary->region_id);
        if ($region) {
            $cities = $region->cities()->get()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        } else {
            $cities = City::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        }

        $districts = District::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $health_conditions = HealthCondition::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $disability_types = DisabilityType::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        
        $requiredDocuments = RequiredDocument::all();

        $incomes = EconomicStatus::where('type', 'income')->orderBy('order_level', 'desc')->get();
        $expenses = EconomicStatus::where('type', 'expense')->orderBy('order_level', 'desc')->get();

        return view(
            $beneficiary->profile_status == 'uncompleted' ? 'beneficiary.profile.edit' : 'beneficiary.profile.show',
            compact(
                'beneficiary',
                'user',
                'nationalities',
                'marital_statuses',
                'beneficiary_categories',
                'accommodation_types',
                'job_types',
                'educational_qualifications',
                'districts',
                'health_conditions',
                'disability_types',
                'incomes',
                'expenses',
                'requiredDocuments',
                'cities',
                'job_types_need_job_details',
                'accommodation_types',
                'accommodation_entities_charity',
                'accommodation_entities_social'
            )
        );
    }
    public function update(UpdateProfileBeneficiaryRequest $request, $id)
    {
        $beneficiary = Beneficiary::findOrFail($id);
        $this->beneficiaryService->updateBeneficiary($beneficiary, $request);

        if($request->has('redirect_to') && $request->redirect_to == 'request_order'){
            return redirect()->route('beneficiary.beneficiary-orders.create', ['service_type' => request()->get('service_type')]);
        }
        return redirect()->route('beneficiary.profile.show');
    }
}
