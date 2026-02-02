<?php

namespace App\Services;

use App\Models\Beneficiary;
use App\Models\BeneficiaryOrder;
use App\Models\BeneficiaryOrderAppointment;
use App\Models\ConsultantSchedule;
use App\Models\CourseStudent;
use App\Models\ServiceLoan;
use App\Models\ServiceLoanMember;
use App\Models\Loan;
use App\Models\DynamicServiceOrder;
use App\Models\DynamicServiceWorkflow;
use Carbon\Carbon;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class BeneficiaryOrderService
{
    public function createBeneficiaryOrder($request)
    {
        $beneficiary = Beneficiary::find($request->beneficiary_id);
        $user = $beneficiary->user;

        $commonData = [
            'beneficiary_id' => $request->beneficiary_id,
            'beneficiary_family_id' => $request->beneficiary_family_id ?? null,
            'service_type' => $request->service_type,
            'title' => $request->title,
            'description' => $request->description,
        ];

        if ($request->service_type == 'consultant') {
            $date = Carbon::createFromFormat(config('panel.date_format'), $request->appointment_date)->format('Y-m-d');
            $dayOfWeek = date('l', strtotime($date));
            $schedule = ConsultantSchedule::where('consultant_id', $request->consultant_id)
                ->where('day', $dayOfWeek)
                ->where('attendance_type', $request->attendance_type)
                ->where('is_active', true)
                ->first();
            if (!$schedule) {
                throw new \Exception('لا يوجد مواعيد متاحة في هذا اليوم.');
            }
            $beneficiaryOrder = BeneficiaryOrder::create($commonData);
            BeneficiaryOrderAppointment::create([
                'beneficiary_order_id' => $beneficiaryOrder->id,
                'consultation_type_id' => $request->consultation_type_id,
                'beneficiary_id' => $request->beneficiary_id,
                'consultant_id' => $request->consultant_id,
                'day' => $dayOfWeek,
                'date' => $request->appointment_date,
                'time' => $request->appointment_time,
                'duration' => $schedule->slot_duration,
                'attendance_type' => $request->attendance_type,
            ]);
        } elseif ($request->service_type == 'financial' || $request->service_type == 'social') {
            $commonData['service_id'] = $request->service_id;
            $beneficiaryOrder = BeneficiaryOrder::create($commonData);
        } elseif ($request->service_type == 'courses') {
            $commonData['course_id'] = $request->course_id;
            $beneficiaryOrder = BeneficiaryOrder::create($commonData);
            $prevCourses = null;
            if ($request->prev_courses == 1) {
                $prevCourses = json_encode([
                    'name' => $request->prev_course_name,
                    'trainer' => $request->prev_course_trainer,
                ], JSON_UNESCAPED_UNICODE);
            }
            CourseStudent::create([
                'beneficiary_order_id' => $beneficiaryOrder->id,
                'course_id' => $request->course_id,
                'beneficiary_id' => $request->beneficiary_id,
                'certificate' => $request->certificate,
                'transportation' => $request->transportation,
                'prev_experience' => $request->prev_experience,
                'prev_courses' => $prevCourses,
                'attend_same_course_before' => $request->prev_courses,
                'note' => $request->note,
            ]);
        }elseif($request->service_type == 'loan'){
            $commonData['service_id'] = $request->service_id;
            $beneficiaryOrder = BeneficiaryOrder::create($commonData);

            if($request->has('contacts')){
                $contacts = json_encode($request->contacts, JSON_UNESCAPED_UNICODE);
            }else{
                $contacts = null;
            }

            $loan = Loan::find($request->loan_id);

            $serviceLoan = ServiceLoan::create([
                'group_name' => $request->group_name,
                'beneficiary_order_id' => $beneficiaryOrder->id, 
                'kafil_name' => $request->kafil_name,
                'kafil_identity_num' => $request->kafil_identity_num,
                'accommodation_type_id' => $request->accommodation_type_id,
                'marital_status_id' => $request->marital_status_id,
                'educational_qualification_id' => $request->educational_qualification_id,
                'job_type_id' => $request->job_type_id,
                'kafil_district_id' => $request->kafil_district_id,
                'kafil_street' => $request->kafil_street,
                'kafil_nearby_address' => $request->kafil_nearby_address,
                'kafil_phone' => $request->kafil_phone,
                'kafil_phone2' => $request->kafil_phone2,
                'kafil_work_phone' => $request->kafil_work_phone,
                'kafil_work_address' => $request->kafil_work_address,
                'kafil_email' => $request->kafil_email,
                'kafil_work_name' => $request->kafil_work_name,
                'kafil_mail_box' => $request->kafil_mail_box,
                'kafil_postal_code' => $request->kafil_postal_code,
                
                'amount' => $loan->amount,
                'installment' => $loan->installment,
                'months' => $loan->months,

                'contacts' => $contacts,
            ]);
            
            ServiceLoanMember::create([
                'service_loan_id' => $serviceLoan->id, 
                'beneficiary_id' => $beneficiary->id,
                'name' => $user->name,
                'identity_number' => $user->identity_num, 
                'member_position' => 'responsible',
                'status' => 'approved',
                'project_type' => $request->project_type,
                'project_location' => $request->project_location,
                'district_id' => $request->district_id,
                'street' => $request->street,
                'project_start_date' => $request->project_start_date,
                'project_years_of_experience' => $request->project_years_of_experience,
                'project_short_description' => $request->project_short_description,
                'project_financial_source' => $request->project_financial_source,
                'purpose_of_loan' => $request->purpose_of_loan,
                'has_previous_loan' => $request->has_previous_loan,
                'previous_loan_number' => $request->previous_loan_number,
                'amount' => $loan->amount,
                'installment' => $loan->installment,
                'months' => $loan->months,
                'loan_id' => $request->loan_id,
            ]);

            if($request->has('members')){
                foreach($request->members as $member){
                    ServiceLoanMember::create([
                        'service_loan_id' => $serviceLoan->id, 
                        'name' => $member['name'],
                        'identity_number' => $member['identity_number'], 
                        'member_position' => 'member',
                    ]);
                }
            }
        } elseif (str_starts_with($request->service_type, 'dynamic_')) {
            // Handle dynamic service
            $dynamicServiceId = \App\Helpers\DynamicServiceHelper::extractDynamicServiceId($request->service_type);
            $beneficiaryOrder = BeneficiaryOrder::create($commonData);
            
            // Get dynamic service to access form fields metadata
            $dynamicService = \App\Models\DynamicService::find($dynamicServiceId);
            
            // Collect dynamic field data with metadata
            $fieldData = [];
            if ($dynamicService && $dynamicService->form_fields) {
                $formFields = json_decode($dynamicService->form_fields, true);
                
                foreach ($formFields as $field) {
                    $fieldId = $field['id'];
                    $fieldKey = 'dynamic_field_' . $fieldId;
                    
                    if ($request->has($fieldKey)) {
                        $fieldData[] = [
                            'id' => $fieldId,
                            'label' => $field['label'] ?? '',
                            'type' => $field['type'] ?? 'text',
                            'required' => $field['required'] ?? false,
                            'value' => $request->input($fieldKey),
                            'options' => $field['options'] ?? null,
                            'validation' => $field['validation'] ?? null,
                            'grid' => $field['grid'] ?? 'col-md-6',
                            'attributes' => $field['attributes'] ?? null,
                        ];
                    }
                }
            }
            
            // Create dynamic service order record
            $dynamicServiceOrder = DynamicServiceOrder::create([
                'beneficiary_order_id' => $beneficiaryOrder->id,
                'dynamic_service_id' => $dynamicServiceId,
                'field_data' => $fieldData,
            ]);

            // Create workflow for training category
            if ($dynamicService && $dynamicService->category === 'training') {
                $workflow = DynamicServiceWorkflow::create([
                    'dynamic_service_order_id' => $dynamicServiceOrder->id,
                    'category' => 'training',
                    'current_status' => DynamicServiceWorkflow::STATUS_PENDING_REVIEW,
                ]);
                
                // Create training-specific data
                \App\Models\DynamicServiceWorkflowTraining::create([
                    'workflow_id' => $workflow->id,
                    'service_type' => $dynamicService->service_type, // 'individual' or 'group'
                ]);
            }
            
            // Create workflow for assistance category
            if ($dynamicService && $dynamicService->category === 'assistance') {
                $workflow = DynamicServiceWorkflow::create([
                    'dynamic_service_order_id' => $dynamicServiceOrder->id,
                    'category' => 'assistance',
                    'current_status' => DynamicServiceWorkflow::STATUS_PENDING_REVIEW,
                ]);
                
                // Create assistance-specific data
                \App\Models\DynamicServiceWorkflowAssistance::create([
                    'workflow_id' => $workflow->id,
                ]);
            }
            
            // Create workflow for social_programs category
            if ($dynamicService && $dynamicService->category === 'social_programs') {
                $workflow = DynamicServiceWorkflow::create([
                    'dynamic_service_order_id' => $dynamicServiceOrder->id,
                    'category' => 'social_programs',
                    'current_status' => DynamicServiceWorkflow::STATUS_PENDING_REVIEW,
                ]);
                
                // Create social_programs-specific data
                \App\Models\DynamicServiceWorkflowSocialPrograms::create([
                    'workflow_id' => $workflow->id,
                ]);
            }
        }

        if ($request->input('attachment', false)) {
            $beneficiaryOrder->addMedia(storage_path('tmp/uploads/' . basename($request->input('attachment'))))->toMediaCollection('attachment');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $beneficiaryOrder->id]);
        }
        return $beneficiaryOrder;
    }
}
