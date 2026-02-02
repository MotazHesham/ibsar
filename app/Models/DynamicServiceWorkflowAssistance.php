<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class DynamicServiceWorkflowAssistance extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    public $table = 'dynamic_service_workflow_assistance';

    protected $dates = [
        'training_program_start_date',
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'workflow_id',
        'assistance_type',
        'study_case_approved',
        'stock_available',
        'stock_item_id',
        'need_training',
        'receiving_form_data',
        'review_request_sent',
        'training_schedule',
        'training_program_start_date',
        'training_attendance_data',
        'training_financial_statements',
        'training_test_passed',
        'training_test_notes',
        'machine_item_id',
        // Stock not available flow fields
        'waiting_list_position',
        'vendor_offers',
        'selected_vendor_id',
        'management_decision_notes',
        'payment_receipt_file',
        // Financial assistance flow fields
        'study_case_rejection_reason',
        'missing_data_info',
        'financial_receipt_file',
        'financial_feedback_data', 
        'created_at',
        'updated_at',
    ];

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')->fit('crop', 50, 50);
        $this->addMediaConversion('preview')->fit('crop', 120, 120);
    }

    protected $casts = [
        'study_case_approved' => 'boolean',
        'stock_available' => 'boolean',
        'need_training' => 'boolean',
        'receiving_form_data' => 'array',
        'review_request_sent' => 'boolean',
        'training_schedule' => 'array',
        'training_program_start_date' => 'datetime',
        'training_attendance_data' => 'array',
        'training_financial_statements' => 'array',
        'training_test_passed' => 'boolean',
        'vendor_offers' => 'array',
        'feedback_data' => 'array',
        'missing_data_info' => 'array',
        'financial_feedback_data' => 'array',
    ];

    // Assistance workflow statuses
    public const STATUS_ASSISTANCE_TYPE_SELECTED = 'assistance_type_selected';
    public const STATUS_STUDY_CASE_FROM_SOCIAL_RESEARCHER = 'study_case_from_social_researcher';
    public const STATUS_STUDY_CASE_APPROVED = 'study_case_approved';
    public const STATUS_STUDY_CASE_REJECTED = 'study_case_rejected';
    public const STATUS_PROJECT_MANAGEMENT_REVIEW_AND_AUDIT = 'project_management_review_and_audit';
    public const STATUS_PROJECT_MANAGEMENT_APPROVED = 'project_management_approved';
    public const STATUS_PROJECT_MANAGEMENT_NOT_APPROVED = 'project_management_not_approved';
    // Financial assistance flow
    public const STATUS_SENT_TO_FINANCIAL_DEPARTMENT_FOR_PAYMENT = 'sent_to_financial_department_for_payment';
    public const STATUS_FINANCIAL_RECEIPT_UPLOADED = 'financial_receipt_uploaded';
    public const STATUS_FINANCIAL_FEEDBACK_RECEIVED = 'financial_feedback_received';
    // Real receipt flow
    public const STATUS_STOCK_AVAILABILITY_CHECK = 'stock_availability_check';
    public const STATUS_STOCK_AVAILABLE = 'stock_available';
    public const STATUS_STOCK_NOT_AVAILABLE = 'stock_not_available';
    // Stock not available flow
    public const STATUS_ON_WAITING_LIST = 'on_waiting_list';
    public const STATUS_SENT_TO_MARKETING_FOR_DONATION = 'sent_to_marketing_for_donation';
    public const STATUS_MARKETING_SUPPORT_COMPLETE = 'marketing_support_complete';
    public const STATUS_SENT_TO_PURCHASING_DEPARTMENT = 'sent_to_purchasing_department';
    public const STATUS_SEARCHING_VENDORS_WITH_OFFERS = 'searching_vendors_with_offers';
    public const STATUS_OFFERS_SENT_TO_MANAGEMENT = 'offers_sent_to_management';
    public const STATUS_MANAGEMENT_ACCEPTED_OFFER = 'management_accepted_offer';
    public const STATUS_MANAGEMENT_REFUSED_OFFER = 'management_refused_offer';
    public const STATUS_SENT_TO_FINANCIAL_DEPARTMENT = 'sent_to_financial_department';
    public const STATUS_PAYMENT_RECEIPT_UPLOADED = 'payment_receipt_uploaded';
    public const STATUS_FEEDBACK_RECEIVED = 'feedback_received';
    // Stock available flow
    public const STATUS_NEED_TRAINING_CHECK = 'need_training_check';
    public const STATUS_NEED_TRAINING = 'need_training';
    public const STATUS_NO_TRAINING_NEEDED = 'no_training_needed';
    public const STATUS_BENEFICIARY_NOTIFIED_AVAILABLE = 'beneficiary_notified_available';
    public const STATUS_RECEIVING_FORM_FILLED = 'receiving_form_filled';
    public const STATUS_ORDER_RECEIVED = 'order_received';
    public const STATUS_SENT_TO_FINANCIAL_FOR_STOCK_REMOVAL = 'sent_to_financial_for_stock_removal';
    public const STATUS_TRAINING_DEPARTMENT_SCHEDULE_SET = 'training_department_schedule_set';
    public const STATUS_TRAINING_PROGRAM_STARTED = 'training_program_started';
    public const STATUS_TRAINING_ATTENDANCE_REVIEWED = 'training_attendance_reviewed';
    public const STATUS_TRAINING_FINANCIAL_STATEMENTS = 'training_financial_statements';
    public const STATUS_TRAINING_TEST = 'training_test';
    public const STATUS_TRAINING_TEST_PASSED = 'training_test_passed';
    public const STATUS_TRAINING_TEST_FAILED = 'training_test_failed';
    public const STATUS_MACHINE_SENT = 'machine_sent';
    public const STATUS_REVIEW_REQUEST_SENT = 'review_request_sent';

    public const STATUS_LABELS = [
        DynamicServiceWorkflow::STATUS_PENDING_REVIEW => 'قيد المراجعة',
        DynamicServiceWorkflow::STATUS_REFUSED => 'مرفوض',
        self::STATUS_ASSISTANCE_TYPE_SELECTED => 'تم اختيار نوع المساعدة',
        self::STATUS_STUDY_CASE_FROM_SOCIAL_RESEARCHER => 'دراسة الحالة من الباحث الاجتماعي',
        self::STATUS_STUDY_CASE_APPROVED => 'موافقة على دراسة الحالة',
        self::STATUS_STUDY_CASE_REJECTED => 'رفض دراسة الحالة',
        self::STATUS_PROJECT_MANAGEMENT_REVIEW_AND_AUDIT => 'مراجعة وتدقيق ادارة المشاريع',
        self::STATUS_PROJECT_MANAGEMENT_APPROVED => 'موافقة إدارة المشاريع',
        self::STATUS_PROJECT_MANAGEMENT_NOT_APPROVED => 'عدم موافقة إدارة المشاريع',
        self::STATUS_STOCK_AVAILABILITY_CHECK => 'التحقق من توفر المخزون',
        self::STATUS_STOCK_AVAILABLE => 'المخزون متوفر',
        self::STATUS_STOCK_NOT_AVAILABLE => 'المخزون غير متوفر',
        self::STATUS_NEED_TRAINING_CHECK => 'التحقق من الحاجة للتدريب',
        self::STATUS_NEED_TRAINING => 'يحتاج تدريب',
        self::STATUS_NO_TRAINING_NEEDED => 'لا يحتاج تدريب',
        self::STATUS_BENEFICIARY_NOTIFIED_AVAILABLE => 'تم إشعار المستفيد بالتوفر',
        self::STATUS_RECEIVING_FORM_FILLED => 'تم ملء نموذج الاستلام',
        self::STATUS_ORDER_RECEIVED => 'تم استلام الطلب',
        self::STATUS_SENT_TO_FINANCIAL_FOR_STOCK_REMOVAL => 'تم الإرسال للمالية لإزالة من المخزون',
        self::STATUS_TRAINING_DEPARTMENT_SCHEDULE_SET => 'تم تحديد جدول التدريب',
        self::STATUS_TRAINING_PROGRAM_STARTED => 'تم بدء برنامج التدريب',
        self::STATUS_TRAINING_ATTENDANCE_REVIEWED => 'تم مراجعة حضور التدريب',
        self::STATUS_TRAINING_FINANCIAL_STATEMENTS => 'البيانات المالية للتدريب',
        self::STATUS_TRAINING_TEST => 'اختبار التدريب',
        self::STATUS_TRAINING_TEST_PASSED => 'نجح اختبار التدريب',
        self::STATUS_TRAINING_TEST_FAILED => 'فشل اختبار التدريب',
        self::STATUS_MACHINE_SENT => 'تم إرسال الجهاز',
        self::STATUS_REVIEW_REQUEST_SENT => 'تم إرسال طلب المراجعة',
        // Stock not available flow labels
        self::STATUS_ON_WAITING_LIST => 'في قائمة الانتظار',
        self::STATUS_SENT_TO_MARKETING_FOR_DONATION => 'تم الإرسال للتسويق للتبرع',
        self::STATUS_MARKETING_SUPPORT_COMPLETE => 'اكتمال الدعم التسويقي',
        self::STATUS_SENT_TO_PURCHASING_DEPARTMENT => 'تم الإرسال لقسم المشتريات',
        self::STATUS_SEARCHING_VENDORS_WITH_OFFERS => 'البحث عن موردين بعروض أسعار',
        self::STATUS_OFFERS_SENT_TO_MANAGEMENT => 'تم إرسال العروض لإدارة البرنامج',
        self::STATUS_MANAGEMENT_ACCEPTED_OFFER => 'قبول العرض من الإدارة',
        self::STATUS_MANAGEMENT_REFUSED_OFFER => 'رفض العرض من الإدارة',
        self::STATUS_SENT_TO_FINANCIAL_DEPARTMENT => 'تم الإرسال للقسم المالي',
        self::STATUS_PAYMENT_RECEIPT_UPLOADED => 'تم رفع إيصال الدفع',
        // Financial assistance flow labels
        self::STATUS_SENT_TO_FINANCIAL_DEPARTMENT_FOR_PAYMENT => 'تم الإرسال للقسم المالي للدفع',
        self::STATUS_FINANCIAL_RECEIPT_UPLOADED => 'تم رفع إيصال الدفع المالي',
        self::STATUS_FINANCIAL_FEEDBACK_RECEIVED => 'تم استلام الملاحظات المالية',
        DynamicServiceWorkflow::STATUS_COMPLETED => 'مكتمل',
    ];

    public function workflow()
    {
        return $this->belongsTo(DynamicServiceWorkflow::class, 'workflow_id');
    }

    public function canTransitionTo($status)
    {
        $currentStatus = $this->workflow->current_status;
        $assistanceType = $this->assistance_type;
        
        $validTransitions = [
            DynamicServiceWorkflow::STATUS_PENDING_REVIEW => [DynamicServiceWorkflow::STATUS_REFUSED, self::STATUS_ASSISTANCE_TYPE_SELECTED],
            self::STATUS_ASSISTANCE_TYPE_SELECTED => [self::STATUS_STUDY_CASE_FROM_SOCIAL_RESEARCHER],
            self::STATUS_STUDY_CASE_FROM_SOCIAL_RESEARCHER => [
                self::STATUS_STUDY_CASE_APPROVED,
                self::STATUS_STUDY_CASE_REJECTED
            ],
        ];

        // Different flows based on assistance type
        if ($assistanceType === 'financial') {
            // Financial assistance flow
            $validTransitions[self::STATUS_STUDY_CASE_APPROVED] = [self::STATUS_PROJECT_MANAGEMENT_REVIEW_AND_AUDIT];
            $validTransitions[self::STATUS_STUDY_CASE_REJECTED] = [];
            $validTransitions[self::STATUS_PROJECT_MANAGEMENT_REVIEW_AND_AUDIT] = [
                self::STATUS_PROJECT_MANAGEMENT_APPROVED,
                self::STATUS_PROJECT_MANAGEMENT_NOT_APPROVED
            ];
            $validTransitions[self::STATUS_PROJECT_MANAGEMENT_APPROVED] = [self::STATUS_SENT_TO_FINANCIAL_DEPARTMENT_FOR_PAYMENT];
            $validTransitions[self::STATUS_PROJECT_MANAGEMENT_NOT_APPROVED] = [self::STATUS_STUDY_CASE_FROM_SOCIAL_RESEARCHER];
            $validTransitions[self::STATUS_SENT_TO_FINANCIAL_DEPARTMENT_FOR_PAYMENT] = [self::STATUS_FINANCIAL_RECEIPT_UPLOADED];
            $validTransitions[self::STATUS_FINANCIAL_RECEIPT_UPLOADED] = [self::STATUS_FINANCIAL_FEEDBACK_RECEIVED];
            $validTransitions[self::STATUS_FINANCIAL_FEEDBACK_RECEIVED] = [];
        } else {
            // Real receipt flow (existing flow)
            $validTransitions[self::STATUS_STUDY_CASE_APPROVED] = [self::STATUS_PROJECT_MANAGEMENT_REVIEW_AND_AUDIT];
            $validTransitions[self::STATUS_PROJECT_MANAGEMENT_REVIEW_AND_AUDIT] = [
                self::STATUS_PROJECT_MANAGEMENT_APPROVED,
                self::STATUS_PROJECT_MANAGEMENT_NOT_APPROVED
            ];
            $validTransitions[self::STATUS_PROJECT_MANAGEMENT_APPROVED] = [self::STATUS_STOCK_AVAILABILITY_CHECK];
            $validTransitions[self::STATUS_PROJECT_MANAGEMENT_NOT_APPROVED] = [self::STATUS_STUDY_CASE_FROM_SOCIAL_RESEARCHER];
            $validTransitions[self::STATUS_STOCK_AVAILABILITY_CHECK] = [
                self::STATUS_STOCK_AVAILABLE,
                self::STATUS_STOCK_NOT_AVAILABLE
            ];
            $validTransitions[self::STATUS_STOCK_AVAILABLE] = [self::STATUS_NEED_TRAINING_CHECK];
            $validTransitions[self::STATUS_NEED_TRAINING_CHECK] = [
                self::STATUS_NEED_TRAINING,
                self::STATUS_NO_TRAINING_NEEDED
            ];
            // No training needed flow
            $validTransitions[self::STATUS_NO_TRAINING_NEEDED] = [self::STATUS_BENEFICIARY_NOTIFIED_AVAILABLE];
            $validTransitions[self::STATUS_BENEFICIARY_NOTIFIED_AVAILABLE] = [self::STATUS_RECEIVING_FORM_FILLED];
            $validTransitions[self::STATUS_RECEIVING_FORM_FILLED] = [self::STATUS_ORDER_RECEIVED];
            $validTransitions[self::STATUS_ORDER_RECEIVED] = [self::STATUS_SENT_TO_FINANCIAL_FOR_STOCK_REMOVAL];
            $validTransitions[self::STATUS_SENT_TO_FINANCIAL_FOR_STOCK_REMOVAL] = [];
            // Training needed flow
            $validTransitions[self::STATUS_NEED_TRAINING] = [self::STATUS_TRAINING_DEPARTMENT_SCHEDULE_SET];
            $validTransitions[self::STATUS_TRAINING_DEPARTMENT_SCHEDULE_SET] = [self::STATUS_TRAINING_PROGRAM_STARTED];
            $validTransitions[self::STATUS_TRAINING_PROGRAM_STARTED] = [self::STATUS_TRAINING_ATTENDANCE_REVIEWED];
            $validTransitions[self::STATUS_TRAINING_ATTENDANCE_REVIEWED] = [self::STATUS_TRAINING_FINANCIAL_STATEMENTS];
            $validTransitions[self::STATUS_TRAINING_FINANCIAL_STATEMENTS] = [self::STATUS_TRAINING_TEST];
            $validTransitions[self::STATUS_TRAINING_TEST] = [
                self::STATUS_TRAINING_TEST_PASSED,
                self::STATUS_TRAINING_TEST_FAILED
            ];
            $validTransitions[self::STATUS_TRAINING_TEST_PASSED] = [self::STATUS_MACHINE_SENT];
            $validTransitions[self::STATUS_MACHINE_SENT] = [self::STATUS_REVIEW_REQUEST_SENT];
            $validTransitions[self::STATUS_REVIEW_REQUEST_SENT] = [self::STATUS_SENT_TO_FINANCIAL_FOR_STOCK_REMOVAL];
            $validTransitions[self::STATUS_SENT_TO_FINANCIAL_FOR_STOCK_REMOVAL] = [];
            // Stock not available flow
            $validTransitions[self::STATUS_STOCK_NOT_AVAILABLE] = [self::STATUS_ON_WAITING_LIST];
            $validTransitions[self::STATUS_ON_WAITING_LIST] = [self::STATUS_SENT_TO_MARKETING_FOR_DONATION];
            $validTransitions[self::STATUS_SENT_TO_MARKETING_FOR_DONATION] = [self::STATUS_MARKETING_SUPPORT_COMPLETE];
            $validTransitions[self::STATUS_MARKETING_SUPPORT_COMPLETE] = [self::STATUS_SENT_TO_PURCHASING_DEPARTMENT];
            $validTransitions[self::STATUS_SENT_TO_PURCHASING_DEPARTMENT] = [self::STATUS_SEARCHING_VENDORS_WITH_OFFERS];
            $validTransitions[self::STATUS_SEARCHING_VENDORS_WITH_OFFERS] = [self::STATUS_OFFERS_SENT_TO_MANAGEMENT];
            $validTransitions[self::STATUS_OFFERS_SENT_TO_MANAGEMENT] = [
                self::STATUS_MANAGEMENT_ACCEPTED_OFFER,
                self::STATUS_MANAGEMENT_REFUSED_OFFER
            ];
            $validTransitions[self::STATUS_MANAGEMENT_ACCEPTED_OFFER] = [self::STATUS_SENT_TO_FINANCIAL_DEPARTMENT];
            $validTransitions[self::STATUS_SENT_TO_FINANCIAL_DEPARTMENT] = [self::STATUS_PAYMENT_RECEIPT_UPLOADED];
            $validTransitions[self::STATUS_PAYMENT_RECEIPT_UPLOADED] = [];
            $validTransitions[self::STATUS_MANAGEMENT_REFUSED_OFFER] = [self::STATUS_SEARCHING_VENDORS_WITH_OFFERS];
        }

        $allowedStatuses = $validTransitions[$currentStatus] ?? [];
        return in_array($status, $allowedStatuses);
    }
}

