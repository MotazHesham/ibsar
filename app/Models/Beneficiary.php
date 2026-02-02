<?php

namespace App\Models;

use App\Helpers\ActivityLogHelper;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Utils\LogsModelActivity;
use App\Models\BeneficiaryOrder;
use App\Models\BeneficiaryFamily;
use App\Models\BeneficiaryFile;
use Spatie\Activitylog\Models\Activity;

class Beneficiary extends Model
{
    use SoftDeletes, HasFactory;
    use LogsModelActivity;

    public $table = 'beneficiaries';

    public const CAN_WORK_SELECT = [
        'yes' => 'نعم',
        'no'  => 'لا',
    ];
    public const HOUSING_QUALITY_SELECT = [
        'good' => 'بحالة ممتازة',
        'mid' => 'بحالة متوسطة',
        'poor' => 'متهالك',
    ];
    public const FURNITURE_QUALITY_SELECT = [
        'very_good' => 'ممتاز',
        'good' => 'جيد',
        'need_to_change' => 'يحتاج تغيير',
    ];
    public const ELECTRICAL_DEVICES_QUALITY_SELECT = [
        'very_good' => 'ممتاز',
        'good' => 'جيد',
        'need_to_change' => 'يحتاج تغيير',
    ];

    public const CHARACTERISTIC_OF_NATIONALITY_SELECT = [
        'saudi' => 'سعودي',
        'other' => 'اخري',
        'son_of_citizen' => 'ابن مواطنة',
        'husband_of_citizen' => 'زوج مواطنة',
        'mother_of_citizen' => 'ام مواطن',
        'wife_of_citizen' => 'زوجة مواطن',
    ];

    public const PROFILE_STATUS_SELECT = [
        'uncompleted' => 'غير مكتمل',
        'request_join' => 'طلب الانضمام',
        'in_review' => 'قيد المراجعة',
        'approved' => 'فعال',
        'rejected' => 'مرفوض',
    ];

    public const FORM_STEPS = [
        'login_information' => 'بيانات التسجيل',
        'basic_information' => 'بيانات الأساسية',
        'work_information' => 'بيانات العمل',
        'family_information' => 'بيانات الأسرة',
        'economic_information' => 'بيانات الاقتصادية',
        'documents' => 'المستندات',
    ];

    protected $dates = [
        'dob',
        'martial_status_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'handle',
        'user_id',
        'nationality_id',
        'marital_status_id',
        'beneficiary_category_id',
        'accommodation_type_id',
        'accommodation_entity_id',
        'job_type_id',
        'job_details',
        'educational_qualification_id',
        'profile_status',
        'rejection_reason',
        'form_step',
        'characteristic_of_nationality',
        'dob',
        'martial_status_date',
        'address',
        'latitude',
        'longitude',
        'region_id',
        'city_id',
        'district_id',
        'street',
        'building_number',
        'floor_number',
        'building_additional_number',
        'postal_code',
        'health_condition_id',
        'custom_health_condition',
        'disability_type_id',
        'custom_disability_type',
        'can_work',
        'incomes',
        'total_incomes',
        'expenses',
        'total_expenses',
        'case_study',
        'is_archived',
        'specialist_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function beneficiaryBeneficiaryOrders()
    {
        return $this->hasMany(BeneficiaryOrder::class, 'beneficiary_id', 'id');
    }

    public function beneficiaryFamilies()
    {
        return $this->hasMany(BeneficiaryFamily::class, 'beneficiary_id', 'id');
    }

    public function beneficiaryFiles()
    {
        return $this->hasMany(BeneficiaryFile::class, 'beneficiary_id', 'id');
    }

    public function accommodation_type()
    {
        return $this->belongsTo(AccommodationType::class, 'accommodation_type_id');
    }

    public function accommodation_entity()
    {
        return $this->belongsTo(AccommodationEntity::class, 'accommodation_entity_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function nationality()
    {
        return $this->belongsTo(Nationality::class, 'nationality_id');
    }

    public function marital_status()
    {
        return $this->belongsTo(MaritalStatus::class, 'marital_status_id');
    }

    public function beneficiary_category()
    {
        return $this->belongsTo(BeneficiaryCategory::class, 'beneficiary_category_id');
    }

    public function job_type()
    {
        return $this->belongsTo(JobType::class, 'job_type_id');
    }

    public function educational_qualification()
    {
        return $this->belongsTo(EducationalQualification::class, 'educational_qualification_id');
    }

    public function getDobAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(config('panel.date_format')) : null;
    }

    public function getMartialStatusDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(config('panel.date_format')) : null;
    }

    public function setMartialStatusDateAttribute($value)
    {
        $this->attributes['martial_status_date'] = $value ? Carbon::createFromFormat(config('panel.date_format'), $value)->format('Y-m-d') : null;
    }

    public function setDobAttribute($value)
    {
        $this->attributes['dob'] = $value ? Carbon::createFromFormat(config('panel.date_format'), $value)->format('Y-m-d') : null;
    }

    public function canRequestOrder($flash = false)
    { 
        if(getSetting('auto_accept_beneficiary') == 'no'){ 
            if($this->profile_status != 'approved'){
                if($flash){
                    session()->flash('errorMessage', 'لا يمكن الأضافة حتي يتم قبول حسابك');
                }
                return false;
            }
        }else{
            $beneficiary_form_steps = Beneficiary::FORM_STEPS;
            $required_step = getSetting('enable_request_order_after_beneficiary_status');
            
            $current_step_index = array_search($this->form_step, array_keys($beneficiary_form_steps));
            $required_step_index = array_search($required_step, array_keys($beneficiary_form_steps));
            
            if($current_step_index < $required_step_index){
                if($flash){
                    session()->flash('errorMessage', 'لا يمكن الطلب حتي تستكمل بياناتك'); 
                }
                return false;
            }
        } 
        return true;
    }

    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    public function health_condition()
    {
        return $this->belongsTo(HealthCondition::class, 'health_condition_id');
    }

    public function disability_type()
    {
        return $this->belongsTo(DisabilityType::class, 'disability_type_id');
    }

    public function specialist()
    {
        return $this->belongsTo(User::class, 'specialist_id');
    }

    public function economicCategory()
    {
        if ($this->total_incomes < 5000) {
            return 'أ';
        } elseif ($this->total_incomes >= 5000 && $this->total_incomes < 8000) {
            return 'ب';
        } elseif ($this->total_incomes >= 8000 && $this->total_incomes < 12000) {
            return 'ج';
        } elseif ($this->total_incomes > 12000) {
            return 'د';
        }
    }

    public function getLogAttributes()
    {
        return [
            'profile_status',
            'dob',
            'martial_status_date',
            'address',
            'latitude',
            'longitude',
            'street',
            'building_number',
            'floor_number',
            'custom_health_condition',
            'custom_disability_type',
            'can_work',
            'incomes',
            'expenses',
            'case_study',
            'job_details',
            'is_archived',
            'rejection_reason',

            'region->id',
            'region->name',
            'city->id',
            'city->name',
            'district->id',
            'district->name',
            'nationality->id',
            'nationality->name',
            'marital_status->id',
            'marital_status->name',
            'accommodation_type->id',
            'accommodation_type->name',
            'accommodation_entity->id',
            'accommodation_entity->name',
            'job_type->id',
            'job_type->name',
            'educational_qualification->id',
            'educational_qualification->name',
            'health_condition->id',
            'health_condition->name',
            'disability_type->id',
            'disability_type->name',
            'specialist->id',
            'specialist->name',
        ];
    }

    public function getActivityDescriptionForEvent($eventName)
    {
        if ($eventName == 'created') {
            return "تم فتح ملف جديد للمستفيد";
        } elseif ($eventName == 'updated') {
            return "تم تحديث بيانات المستفيد";
        } elseif ($eventName == 'deleted') {
            return 'تم حذف ملف المستفيد';
        }
    }
    public function getLogNameToUse(): ?string
    {
        return 'beneficiary_activity-' . $this->id;
    }

    public function getCustomAttributes(Activity $activity)
    {
        $properties = $activity->properties ?? [];

        $transformData = function ($data, &$properties) use ($activity) {
            $oldAttributes = $properties['old'] ?? [];
            $currentAttributes = $properties['attributes'] ?? [];

            if (isset($data['profile_status']) && isset(self::PROFILE_STATUS_SELECT[$data['profile_status']])) {
                $data['profile_status'] = self::PROFILE_STATUS_SELECT[$data['profile_status']];
            }
            if (isset($data['can_work']) && isset(self::CAN_WORK_SELECT[$data['can_work']])) {
                $data['can_work'] = self::CAN_WORK_SELECT[$data['can_work']];
            }
            if (isset($data['is_archived'])) {
                if ($activity->event != 'created') {
                    $data['is_archived'] = $data['is_archived'] == 1 ? 'مؤرشف' : 'غير مؤرشف';
                }
            }

            // Handle special cases for incomes and expenses and case_study and job_details
            if (isset($currentAttributes['incomes'])) {
                if (isset($oldAttributes['incomes'])) {
                    $changes = compareJsonValues($oldAttributes['incomes'], $currentAttributes['incomes'])['changed'];
                } else {
                    $changes = json_decode($currentAttributes['incomes']);
                }
                foreach ($changes as $key => $change) {
                    $income = EconomicStatus::find($key);
                    if ($income) {
                        $data[$income->getTranslation('name', 'ar')] = $change['new'] ?? $change;
                    }
                }
                unset($data['incomes']);
                $properties['skipped_attributes'] = [
                    'old' => $oldAttributes['incomes'],
                    'new' => $currentAttributes['incomes'],
                ];
            }

            if (isset($currentAttributes['expenses'])) {
                if (isset($oldAttributes['expenses'])) {
                    $changes = compareJsonValues($oldAttributes['expenses'], $currentAttributes['expenses'])['changed'];
                } else {
                    $changes = json_decode($currentAttributes['expenses']);
                }
                foreach ($changes as $key => $change) {
                    if($key == 'accommodation_rent' || $key == 'accommodation_rent_late'){
                        $data[$key] = $change['new'] ?? $change;
                    }else{ 
                        $expense = EconomicStatus::find($key);
                        if ($expense) {
                            $data[$expense->getTranslation('name', 'ar')] = $change['new'] ?? $change;
                        }
                    }
                }

                unset($data['expenses']);
                $properties['skipped_attributes'] = [
                    'old' => $oldAttributes['expenses'],
                    'new' => $currentAttributes['expenses'],
                ];
            }

            if (isset($currentAttributes['case_study'])) {
                if (isset($oldAttributes['case_study'])) {
                    $changes = compareJsonValues($oldAttributes['case_study'], $currentAttributes['case_study'])['changed'];
                } else {
                    $changes = json_decode($currentAttributes['case_study']);
                }
                foreach ($changes as $key => $change) {
                    $data[$key] = $change['new'] ?? $change;
                }

                unset($data['case_study']);
                $properties['skipped_attributes'] = [
                    'old' => $oldAttributes['case_study'],
                    'new' => $currentAttributes['case_study'],
                ];
            }

            if (isset($currentAttributes['job_details'])) {
                if (isset($oldAttributes['job_details'])) {
                    $changes = compareJsonValues($oldAttributes['job_details'], $currentAttributes['job_details'])['changed'];
                } else {
                    $changes = json_decode($currentAttributes['job_details']);
                }
                foreach ($changes as $key => $change) {
                    $data[$key] = $change['new'] ?? $change;
                }

                unset($data['job_details']);
                $properties['skipped_attributes'] = [
                    'old' => $oldAttributes['job_details'],
                    'new' => $currentAttributes['job_details'],
                ];
            }

            return $data;
        };

        if (isset($properties['attributes'])) {
            $properties['attributes'] = $transformData($properties['attributes'], $properties);
        }

        if (isset($properties['old'])) {
            $properties['old'] = $transformData($properties['old'], $properties);
        }

        return $properties;
    }
}
