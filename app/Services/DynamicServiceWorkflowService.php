<?php

namespace App\Services;

use App\Models\BeneficiaryOrder;
use App\Models\DynamicServiceOrder;
use App\Models\DynamicServiceWorkflow;
use App\Models\DynamicServiceWorkflowTraining;
use App\Models\DynamicServiceWorkflowAssistance;
use App\Models\DynamicServiceWorkflowSocialPrograms;
use App\Models\DynamicServiceWorkflowTransition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DynamicServiceWorkflowService
{
    /**
     * Load workflow data for display
     */
    public function loadWorkflowData(DynamicServiceOrder $dynamicServiceOrder)
    {
        $dynamicServiceOrder->load('beneficiaryOrder.beneficiary', 'dynamicService', 'workflow.transitions.user');
        
        $workflow = $dynamicServiceOrder->workflow;
        
        if (!$workflow) {
            abort(404, 'Workflow not found for this order');
        }

        // Load category-specific data
        $category = $workflow->category;
        $loadRelation = match($category) {
            'training' => 'training',
            'assistance' => 'assistance',
            'social_programs' => 'socialPrograms',
            default => null
        };
        
        if ($loadRelation) {
            $workflow->load($loadRelation);
        }

        return [
            'dynamicServiceOrder' => $dynamicServiceOrder,
            'workflow' => $workflow,
            'specialists' => \App\Models\User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), ''),
        ];
    }

    /**
     * Get validation rules for workflow transition
     */
    public function getValidationRules(DynamicServiceWorkflow $workflow): array
    {
        $category = $workflow->category;
        
        $validationRules = [
            'to_status' => 'required|string',
            'notes' => 'nullable|string',
            'refused_reason' => 'nullable|string|required_if:to_status,' . DynamicServiceWorkflow::STATUS_REFUSED,
        ];

        // Category-specific validation
        if ($category === 'training') {
            $validationRules = array_merge($validationRules, $this->getTrainingValidationRules($workflow));
        } elseif ($category === 'assistance') {
            $validationRules = array_merge($validationRules, $this->getAssistanceValidationRules());
        } elseif ($category === 'social_programs') {
            $validationRules = array_merge($validationRules, $this->getSocialProgramsValidationRules());
        }

        return $validationRules;
    }

    /**
     * Get training-specific validation rules
     */
    protected function getTrainingValidationRules(DynamicServiceWorkflow $workflow): array
    {
        $rules = [
            'appointment_date' => 'nullable|date|required_if:to_status,' . DynamicServiceWorkflowTraining::STATUS_APPOINTMENT_SET,
            'specialist_id' => 'nullable|exists:users,id',
            'specialist_report' => 'nullable|string',
            'assessment' => 'nullable|array',
            'training_department_approved' => 'nullable|boolean',
            'program_start_date' => 'nullable|date',
            'test_passed' => 'nullable|boolean',
            'test_result' => 'nullable|string',
            'alternatives_offered' => 'nullable|string',
            'device_delivered' => 'nullable|boolean',
            'device_item_id' => 'nullable|integer|required_if:device_delivered,1|required_if:device_delivered,true',
        ];
        
        // Group-specific validation
        if ($workflow->training && $workflow->training->service_type === 'group') {
            $rules = array_merge($rules, [
                'payment_url' => 'nullable|url|required_if:is_paid_program,1',
                'is_paid_program' => 'nullable|boolean',
                'group_size' => 'nullable|integer|min:1',
                'group_position' => 'nullable|integer|min:1',
                'meeting_schedule' => 'nullable|array',
                'meeting_schedule.*.date' => 'nullable|date',
                'meeting_schedule.*.title' => 'nullable|string',
                'certificate_type' => 'nullable|string|in:completion,passed|required_if:to_status,' . DynamicServiceWorkflowTraining::STATUS_CERTIFICATE_COMPLETED,
                'certificate_test_passed' => 'nullable|boolean|required_if:certificate_type,passed',
                'certificate_message_sent' => 'nullable|string|required_if:certificate_type,completion|required_if:certificate_type,passed',
            ]);
        }

        return $rules;
    }

    /**
     * Get assistance-specific validation rules
     */
    protected function getAssistanceValidationRules(): array
    {
        return [
            'assistance_type' => 'nullable|string|in:real_receipt,financial',
            'study_case_approved' => 'nullable|boolean',
            'study_case_notes' => 'nullable|string',
            'stock_available' => 'nullable|boolean',
            'stock_item_id' => 'nullable|integer',
            'need_training' => 'nullable|boolean',
            'receiving_form_data' => 'nullable|string',
            'training_schedule' => 'nullable|array',
            'training_schedule.*.date' => 'nullable|date',
            'training_schedule.*.title' => 'nullable|string',
            'program_start_date' => 'nullable|date',
            'training_test_passed' => 'nullable|boolean',
            'training_test_notes' => 'nullable|string',
            'machine_item_id' => 'nullable|integer',
            // Stock not available flow
            'waiting_list_position' => 'nullable|integer|min:1',
            'vendor_offers' => 'nullable|array',
            'vendor_offers.*.vendor_name' => 'nullable|string',
            'vendor_offers.*.price' => 'nullable|numeric|min:0',
            'vendor_offers.*.notes' => 'nullable|string',
            'selected_vendor_id' => 'nullable|integer',
            'management_decision_notes' => 'nullable|string',
            'payment_receipt_file' => 'nullable|string',
            // Financial assistance flow
            'study_case_rejection_reason' => 'nullable|string',
            'missing_data_info' => 'nullable|string',
            'financial_receipt_file' => 'nullable|string',
            'financial_feedback_data' => 'nullable|string',
        ];
    }

    /**
     * Get social programs-specific validation rules
     */
    protected function getSocialProgramsValidationRules(): array
    {
        return [
            'waiting_list_position' => 'nullable|integer|min:1',
            'document_data' => 'nullable|string',
            'executive_decision_notes' => 'nullable|string',
            'program_proceeded_date' => 'nullable|date',
            'review_notes' => 'nullable|string',
        ];
    }

    /**
     * Process workflow transition
     */
    public function transition(Request $request, DynamicServiceWorkflow $workflow): void
    {
        // Load category-specific relationship
        $category = $workflow->category;
        $loadRelation = match($category) {
            'training' => 'training',
            'assistance' => 'assistance',
            'social_programs' => 'socialPrograms',
            default => null
        };
        
        if ($loadRelation) {
            $workflow->load($loadRelation);
        }
        
        $fromStatus = $workflow->current_status;
        
        // Prepare update data
        $baseUpdateData = [
            'current_status' => $request->to_status,
        ];

        $categoryUpdateData = [];

        // Handle specific status updates based on category
        if ($category === 'training') {
            $categoryUpdateData = $this->processTrainingTransition($request, $workflow, $baseUpdateData);
        } elseif ($category === 'assistance') {
            $categoryUpdateData = $this->processAssistanceTransition($request, $workflow, $baseUpdateData);
        } elseif ($category === 'social_programs') {
            $categoryUpdateData = $this->processSocialProgramsTransition($request, $workflow, $baseUpdateData);
        }

        if ($request->notes) {
            $baseUpdateData['notes'] = $request->notes;
        }

        // Update base workflow
        $workflow->update($baseUpdateData);

        // Update or create category-specific data
        $categoryData = $this->updateCategoryData($workflow, $categoryUpdateData, $category);
        
        // Handle file uploads for financial receipt, payment receipt, and vendor offer quotations
        if ($category === 'assistance' && $categoryData instanceof DynamicServiceWorkflowAssistance) {
            if ($request->financial_receipt_file) {
                $this->handleFinancialReceiptUpload($request, $categoryData);
            }
            if ($request->payment_receipt_file) {
                $this->handlePaymentReceiptUpload($request, $categoryData);
            }
            if ($request->vendor_offers && $request->to_status === DynamicServiceWorkflowAssistance::STATUS_SEARCHING_VENDORS_WITH_OFFERS) {
                $this->handleVendorOfferQuotationUploads($request, $categoryData);
            }
        }

        // Create transition record
        DynamicServiceWorkflowTransition::create([
            'workflow_id' => $workflow->id,
            'from_status' => $fromStatus,
            'to_status' => $request->to_status,
            'notes' => $request->notes,
            'user_id' => Auth::id(),
            'data' => $request->except(['_token', 'to_status', 'notes']),
        ]);
    }

    /**
     * Process training workflow transition
     */
    protected function processTrainingTransition(Request $request, DynamicServiceWorkflow $workflow, array &$baseUpdateData): array
    {
        $categoryUpdateData = [];
        $serviceType = $workflow->training?->service_type ?? 'individual';
        
        if ($serviceType === 'group') {
            $categoryUpdateData = $this->processGroupTrainingTransition($request,$workflow, $baseUpdateData);
        } else {
            $categoryUpdateData = $this->processIndividualTrainingTransition($request, $workflow, $baseUpdateData);
        }

        return $categoryUpdateData;
    }

    /**
     * Process group training workflow transition
     */
    protected function processGroupTrainingTransition(Request $request, DynamicServiceWorkflow $workflow, array &$baseUpdateData): array
    {
        $categoryUpdateData = [];
        $beneficiaryOrder = $workflow->dynamicServiceOrder->beneficiaryOrder ?? null;

        switch ($request->to_status) {
            case DynamicServiceWorkflow::STATUS_REFUSED:
                $baseUpdateData['refused_reason'] = $request->refused_reason;
                break;
            case DynamicServiceWorkflowTraining::STATUS_APPROVED_WITH_PAYMENT:
                $categoryUpdateData['is_paid_program'] = $request->has('is_paid_program') ? (bool)$request->is_paid_program : false;
                if ($request->payment_url) {
                    $categoryUpdateData['payment_url'] = $request->payment_url;
                }
                break;
            case DynamicServiceWorkflowTraining::STATUS_IN_WAITING_LIST:
                $usersInSameGroup = BeneficiaryOrder::where('service_type', $beneficiaryOrder->service_type)->get();
                $groupPosition = $usersInSameGroup->count();
                $categoryUpdateData['in_waiting_list'] = true;  
                $categoryUpdateData['group_position'] = $groupPosition; 
                break;
            case DynamicServiceWorkflowTraining::STATUS_MEETING_SCHEDULE_SENT:
                if ($request->meeting_schedule) {
                    $meetings = array_filter($request->meeting_schedule, function($meeting) {
                        return !empty($meeting['date']) || !empty($meeting['title']);
                    });
                    if (!empty($meetings)) {
                        $categoryUpdateData['meeting_schedule'] = array_values($meetings);
                    }
                }
                break;
            case DynamicServiceWorkflowTraining::STATUS_PROGRAM_STARTED:
                $categoryUpdateData['program_start_date'] = $request->program_start_date ?? now();
                // Get program meetings from dynamic service
                $dynamicService = $workflow->dynamicServiceOrder->dynamicService;
                if ($dynamicService && $dynamicService->program_meetings) {
                    $categoryUpdateData['meeting_schedule'] = $dynamicService->program_meetings;
                }
                break;
            case DynamicServiceWorkflowTraining::STATUS_CERTIFICATE_COMPLETED:
                // Handle certificate type: completion or passed
                $certificateType = $request->certificate_type;
                
                if ($certificateType === 'completion') {
                    // For completion certificate, only save the message
                    $categoryUpdateData['certificate_message_sent'] = $request->certificate_message_sent;
                    $baseUpdateData['current_status'] = DynamicServiceWorkflow::STATUS_COMPLETED;
                    // Don't set certificate_test_passed for completion certificates
                } elseif ($certificateType === 'passed') {
                    // For passed certificate, save both test result and message
                    $categoryUpdateData['certificate_test_passed'] = $request->certificate_test_passed;
                    $categoryUpdateData['certificate_message_sent'] = $request->certificate_message_sent;
                }
                break;
            case DynamicServiceWorkflowTraining::STATUS_DEVICE_DELIVERY:
                // Handle device delivery for group training (after certificate passed)
                if ($request->has('device_delivered')) {
                    $categoryUpdateData['device_delivered'] = (bool)$request->device_delivered;
                    if ($request->device_delivered && $request->device_item_id) {
                        $categoryUpdateData['device_item_id'] = $request->device_item_id;
                    } else {
                        // Clear device_item_id if device_delivered is false
                        $categoryUpdateData['device_item_id'] = null;
                    }
                }
                break;
            case DynamicServiceWorkflowTraining::STATUS_CERTIFICATE_ISSUED:
                $categoryUpdateData['certificate_issued'] = true;
                break;
        }

        return $categoryUpdateData;
    }

    /**
     * Process individual training workflow transition
     */
    protected function processIndividualTrainingTransition(Request $request, DynamicServiceWorkflow $workflow, array &$baseUpdateData): array
    {
        $categoryUpdateData = [];

        switch ($request->to_status) {
            case DynamicServiceWorkflow::STATUS_REFUSED:
                $baseUpdateData['refused_reason'] = $request->refused_reason;
                break;
            case DynamicServiceWorkflowTraining::STATUS_APPOINTMENT_SET:
                $categoryUpdateData['appointment_date'] = $request->appointment_date;
                break;
            case DynamicServiceWorkflowTraining::STATUS_RATED:
                $baseUpdateData['specialist_id'] = $request->specialist_id;
                if ($request->has('assessment')) {
                    $assessmentData = $request->assessment;
                    if ($request->specialist_report) {
                        $assessmentData['notes'] = $request->specialist_report;
                    }
                    $categoryUpdateData['specialist_report'] = json_encode($assessmentData, JSON_UNESCAPED_UNICODE);
                } elseif ($request->specialist_report) {
                    $categoryUpdateData['specialist_report'] = $request->specialist_report;
                }
                break;
            case DynamicServiceWorkflowTraining::STATUS_TRAINING_APPROVED:
            case DynamicServiceWorkflowTraining::STATUS_TRAINING_REJECTED:
                $categoryUpdateData['training_department_approved'] = $request->to_status === DynamicServiceWorkflowTraining::STATUS_TRAINING_APPROVED;
                break;
            case DynamicServiceWorkflowTraining::STATUS_PROGRAM_STARTED:
                $categoryUpdateData['program_start_date'] = $request->program_start_date;
                // Get program meetings from dynamic service
                $dynamicService = $workflow->dynamicServiceOrder->dynamicService;
                if ($dynamicService && $dynamicService->program_meetings) {
                    $categoryUpdateData['meeting_schedule'] = $dynamicService->program_meetings;
                }
                break;
            case DynamicServiceWorkflowTraining::STATUS_TEST_PASSED:
            case DynamicServiceWorkflowTraining::STATUS_TEST_FAILED:
                $categoryUpdateData['test_passed'] = $request->to_status === DynamicServiceWorkflowTraining::STATUS_TEST_PASSED;
                $categoryUpdateData['test_result'] = $request->test_result;
                if ($request->to_status === DynamicServiceWorkflowTraining::STATUS_TEST_FAILED) {
                    $categoryUpdateData['alternatives_offered'] = $request->alternatives_offered;
                }
                break;
            case DynamicServiceWorkflowTraining::STATUS_DEVICE_DELIVERY:
                // Handle device delivery for individual training
                if ($request->has('device_delivered')) {
                    $categoryUpdateData['device_delivered'] = (bool)$request->device_delivered;
                    if ($request->device_delivered && $request->device_item_id) {
                        $categoryUpdateData['device_item_id'] = $request->device_item_id;
                    } else {
                        // Clear device_item_id if device_delivered is false
                        $categoryUpdateData['device_item_id'] = null;
                    }
                }
                break;
            case DynamicServiceWorkflow::STATUS_COMPLETED:
                // No additional data needed for completion
                break;
        }

        return $categoryUpdateData;
    }

    /**
     * Process assistance workflow transition
     */
    protected function processAssistanceTransition(Request $request, DynamicServiceWorkflow $workflow, array &$baseUpdateData): array
    {
        $categoryUpdateData = [];

        switch ($request->to_status) {
            case DynamicServiceWorkflow::STATUS_REFUSED:
                $baseUpdateData['refused_reason'] = $request->refused_reason;
                break;
            case DynamicServiceWorkflowAssistance::STATUS_ASSISTANCE_TYPE_SELECTED:
                $categoryUpdateData['assistance_type'] = $request->assistance_type;
                break;
            case DynamicServiceWorkflowAssistance::STATUS_STUDY_CASE_APPROVED: 
                $categoryUpdateData['study_case_approved'] = $request->to_status === DynamicServiceWorkflowAssistance::STATUS_STUDY_CASE_APPROVED;
                break; 
            case DynamicServiceWorkflowAssistance::STATUS_STOCK_AVAILABLE:
            case DynamicServiceWorkflowAssistance::STATUS_STOCK_NOT_AVAILABLE:
                $categoryUpdateData['stock_available'] = $request->to_status === DynamicServiceWorkflowAssistance::STATUS_STOCK_AVAILABLE;
                if ($request->stock_item_id) {
                    $categoryUpdateData['stock_item_id'] = $request->stock_item_id;
                }
                break;
            case DynamicServiceWorkflowAssistance::STATUS_NEED_TRAINING:
            case DynamicServiceWorkflowAssistance::STATUS_NO_TRAINING_NEEDED:
                $categoryUpdateData['need_training'] = $request->to_status === DynamicServiceWorkflowAssistance::STATUS_NEED_TRAINING;
                break;
            case DynamicServiceWorkflowAssistance::STATUS_RECEIVING_FORM_FILLED:
                if ($request->receiving_form_data) {
                    $categoryUpdateData['receiving_form_data'] = json_decode($request->receiving_form_data, true);
                }
                break;
            case DynamicServiceWorkflowAssistance::STATUS_TRAINING_DEPARTMENT_SCHEDULE_SET:
                if ($request->training_schedule) {
                    $schedule = array_filter($request->training_schedule, function($item) {
                        return !empty($item['date']) || !empty($item['title']);
                    });
                    if (!empty($schedule)) {
                        $categoryUpdateData['training_schedule'] = array_values($schedule);
                    }
                }
                break;
            case DynamicServiceWorkflowAssistance::STATUS_TRAINING_PROGRAM_STARTED:
                $categoryUpdateData['training_program_start_date'] = $request->program_start_date ?? now();
                break;
            case DynamicServiceWorkflowAssistance::STATUS_TRAINING_TEST_PASSED:
            case DynamicServiceWorkflowAssistance::STATUS_TRAINING_TEST_FAILED:
                $categoryUpdateData['training_test_passed'] = $request->to_status === DynamicServiceWorkflowAssistance::STATUS_TRAINING_TEST_PASSED;
                if ($request->training_test_notes) {
                    $categoryUpdateData['training_test_notes'] = $request->training_test_notes;
                }
                break;
            case DynamicServiceWorkflowAssistance::STATUS_MACHINE_SENT:
                if ($request->machine_item_id) {
                    $categoryUpdateData['machine_item_id'] = $request->machine_item_id;
                }
                break;
            case DynamicServiceWorkflowAssistance::STATUS_REVIEW_REQUEST_SENT:
                $categoryUpdateData['review_request_sent'] = true;
                break;
            // Stock not available flow
            case DynamicServiceWorkflowAssistance::STATUS_ON_WAITING_LIST:
                // Auto-set waiting list position for real_receipt type
                $assistanceType = $workflow->assistance->assistance_type ?? null;
                if ($assistanceType === 'real_receipt') {
                    // Calculate position automatically: count all assistance workflows with same type on waiting list
                    $waitingListCount = DynamicServiceWorkflowAssistance::whereHas('workflow', function($query) {
                        $query->where('current_status', DynamicServiceWorkflowAssistance::STATUS_ON_WAITING_LIST);
                    })
                    ->where('assistance_type', 'real_receipt')
                    ->count();
                    $categoryUpdateData['waiting_list_position'] = $waitingListCount + 1;
                } elseif ($request->waiting_list_position) {
                    $categoryUpdateData['waiting_list_position'] = $request->waiting_list_position;
                }
                break;
            case DynamicServiceWorkflowAssistance::STATUS_SEARCHING_VENDORS_WITH_OFFERS:
                if ($request->vendor_offers) {
                    $offers = array_filter($request->vendor_offers, function($offer) {
                        return !empty($offer['vendor_name']) || !empty($offer['price']);
                    });
                    if (!empty($offers)) {
                        $categoryUpdateData['vendor_offers'] = array_values($offers);
                    }
                }
                break;
            case DynamicServiceWorkflowAssistance::STATUS_MANAGEMENT_ACCEPTED_OFFER:
            case DynamicServiceWorkflowAssistance::STATUS_MANAGEMENT_REFUSED_OFFER:
                if ($request->selected_vendor_id !== null) {
                    $categoryUpdateData['selected_vendor_id'] = $request->selected_vendor_id;
                }
                if ($request->management_decision_notes) {
                    $categoryUpdateData['management_decision_notes'] = $request->management_decision_notes;
                }
                break;
            case DynamicServiceWorkflowAssistance::STATUS_PAYMENT_RECEIPT_UPLOADED:
                if ($request->payment_receipt_file) {
                    $categoryUpdateData['payment_receipt_file'] = $request->payment_receipt_file;
                }
                break;
            // Financial assistance flow
            case DynamicServiceWorkflowAssistance::STATUS_STUDY_CASE_REJECTED:
                if ($request->study_case_rejection_reason) {
                    $categoryUpdateData['study_case_rejection_reason'] = $request->study_case_rejection_reason;
                }
                if ($request->missing_data_info) {
                    $missingData = json_decode($request->missing_data_info, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $categoryUpdateData['missing_data_info'] = $missingData;
                    } else {
                        $categoryUpdateData['missing_data_info'] = ['info' => $request->missing_data_info];
                    }
                }
                break;
            case DynamicServiceWorkflowAssistance::STATUS_FINANCIAL_RECEIPT_UPLOADED:
                if ($request->financial_receipt_file) {
                    $categoryUpdateData['financial_receipt_file'] = $request->financial_receipt_file;
                }
                break;
            case DynamicServiceWorkflowAssistance::STATUS_FINANCIAL_FEEDBACK_RECEIVED:
                if ($request->financial_feedback_data) {
                    $feedback = json_decode($request->financial_feedback_data, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $categoryUpdateData['financial_feedback_data'] = $feedback;
                    } else {
                        $categoryUpdateData['financial_feedback_data'] = ['notes' => $request->financial_feedback_data];
                    }
                }
                break; 
        }

        return $categoryUpdateData;
    }

    /**
     * Process social programs workflow transition
     */
    protected function processSocialProgramsTransition(Request $request, DynamicServiceWorkflow $workflow, array &$baseUpdateData): array
    {
        $categoryUpdateData = [];

        switch ($request->to_status) {
            case DynamicServiceWorkflow::STATUS_REFUSED:
                $baseUpdateData['refused_reason'] = $request->refused_reason;
                break;
            case DynamicServiceWorkflowSocialPrograms::STATUS_ACCEPTED:
                $categoryUpdateData['program_accepted'] = true;
                break;
            case DynamicServiceWorkflowSocialPrograms::STATUS_PROGRAM_COMPLETED:
                $categoryUpdateData['program_completed'] = true;
                break;
            case DynamicServiceWorkflowSocialPrograms::STATUS_PROGRAM_NOT_COMPLETED:
                $categoryUpdateData['program_completed'] = false;
                break;
            case DynamicServiceWorkflowSocialPrograms::STATUS_ON_WAITING_LIST:
                if ($request->waiting_list_position) {
                    $categoryUpdateData['waiting_list_position'] = $request->waiting_list_position;
                }
                break;
            case DynamicServiceWorkflowSocialPrograms::STATUS_DOCUMENT_PREPARED:
                $categoryUpdateData['document_prepared'] = true;
                if ($request->document_data) {
                    $documentData = json_decode($request->document_data, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $categoryUpdateData['document_data'] = $documentData;
                    } else {
                        $categoryUpdateData['document_data'] = ['data' => $request->document_data];
                    }
                }
                break;
            case DynamicServiceWorkflowSocialPrograms::STATUS_EXECUTIVE_ACCEPTED:
                $categoryUpdateData['executive_decision'] = 'accepted';
                if ($request->executive_decision_notes) {
                    $categoryUpdateData['executive_decision_notes'] = $request->executive_decision_notes;
                }
                break;
            case DynamicServiceWorkflowSocialPrograms::STATUS_EXECUTIVE_REFUSED:
                $categoryUpdateData['executive_decision'] = 'refused';
                if ($request->executive_decision_notes) {
                    $categoryUpdateData['executive_decision_notes'] = $request->executive_decision_notes;
                }
                break;
            case DynamicServiceWorkflowSocialPrograms::STATUS_PROGRAM_PROCEEDED:
                $categoryUpdateData['program_proceeded'] = true;
                if ($request->program_proceeded_date) {
                    $categoryUpdateData['program_proceeded_date'] = $request->program_proceeded_date;
                } else {
                    $categoryUpdateData['program_proceeded_date'] = now();
                }
                break;
            case DynamicServiceWorkflowSocialPrograms::STATUS_BACK_TO_PROGRAM_MANAGEMENT_REVIEW:
                if ($request->review_notes) {
                    $categoryUpdateData['review_notes'] = $request->review_notes;
                }
                break;
        }

        return $categoryUpdateData;
    }

    /**
     * Update or create category-specific data
     */
    protected function updateCategoryData(DynamicServiceWorkflow $workflow, array $categoryUpdateData, string $category)
    {
        if (empty($categoryUpdateData)) {
            return $workflow->categoryData();
        }

        $categoryData = $workflow->categoryData();
        if ($categoryData) {
            $categoryData->update($categoryUpdateData);
        } else {
            // Create category-specific record if it doesn't exist
            $categoryUpdateData['workflow_id'] = $workflow->id;
            if ($category === 'training') {
                $categoryData = DynamicServiceWorkflowTraining::create($categoryUpdateData);
            } elseif ($category === 'assistance') {
                $categoryData = DynamicServiceWorkflowAssistance::create($categoryUpdateData);
            } elseif ($category === 'social_programs') {
                $categoryData = DynamicServiceWorkflowSocialPrograms::create($categoryUpdateData);
            }
        }
        
        return $categoryData;
    }

    /**
     * Handle financial receipt file upload
     */
    protected function handleFinancialReceiptUpload(Request $request, $categoryData): void
    {
        if ($request->financial_receipt_file && $categoryData instanceof DynamicServiceWorkflowAssistance) {
            $filePath = storage_path('tmp/uploads/' . $request->financial_receipt_file);
            
            if (file_exists($filePath)) {
                $categoryData->clearMediaCollection('financial_receipt');
                $categoryData->addMedia($filePath)
                    ->toMediaCollection('financial_receipt');
            }
        }
    }

    /**
     * Handle payment receipt file upload
     */
    protected function handlePaymentReceiptUpload(Request $request, $categoryData): void
    {
        if ($request->payment_receipt_file && $categoryData instanceof DynamicServiceWorkflowAssistance) {
            $filePath = storage_path('tmp/uploads/' . $request->payment_receipt_file);
            
            if (file_exists($filePath)) {
                $categoryData->clearMediaCollection('payment_receipt');
                $categoryData->addMedia($filePath)
                    ->toMediaCollection('payment_receipt');
            }
        }
    }

    /**
     * Handle vendor offer quotation file uploads
     */
    protected function handleVendorOfferQuotationUploads(Request $request, $categoryData): void
    {
        if (!$request->vendor_offers || !$categoryData instanceof DynamicServiceWorkflowAssistance) {
            return;
        }

        foreach ($request->vendor_offers as $index => $offer) {
            if (!empty($offer['quotation_file'])) {
                $filePath = storage_path('tmp/uploads/' . $offer['quotation_file']);
                
                if (file_exists($filePath)) {
                    $mediaCollection = "vendor_offer_quotation_{$index}";
                    $categoryData->clearMediaCollection($mediaCollection);
                    $categoryData->addMedia($filePath)
                        ->toMediaCollection($mediaCollection);
                }
            }
        }
    }

    /**
     * Update attendance data
     */
    public function updateAttendance(Request $request, DynamicServiceWorkflow $workflow): void
    {
        if ($workflow->category !== 'training') {
            throw new \InvalidArgumentException('Attendance tracking is only available for training workflows');
        }

        $training = $workflow->training;
        if (!$training) {
            throw new \InvalidArgumentException('Training workflow data not found');
        }

        $attendanceData = $training->attendance_data ?? [];
        $attendanceData[$request->session_id] = [
            'attended' => $request->attended,
            'notes' => $request->notes,
            'updated_at' => now()->toDateTimeString(),
            'updated_by' => Auth::id(),
        ];

        $training->update(['attendance_data' => $attendanceData]);
    }

    /**
     * Update accounting entries
     */
    public function updateAccounting(Request $request, DynamicServiceWorkflow $workflow): void
    {
        if ($workflow->category !== 'training') {
            throw new \InvalidArgumentException('Accounting entries are only available for training workflows');
        }

        $training = $workflow->training;
        if (!$training) {
            throw new \InvalidArgumentException('Training workflow data not found');
        }

        $training->update(['accounting_entries' => $request->entries]);
    }

    /**
     * Update satisfaction assessment
     */
    public function updateSatisfaction(Request $request, DynamicServiceWorkflow $workflow): void
    {
        if ($workflow->category !== 'training') {
            throw new \InvalidArgumentException('Satisfaction assessment is only available for training workflows');
        }

        $training = $workflow->training;
        if (!$training) {
            throw new \InvalidArgumentException('Training workflow data not found');
        }

        $training->update(['satisfaction_assessment' => $request->satisfaction_data]);
    }
}

