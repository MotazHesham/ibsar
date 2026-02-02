<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DynamicServiceWorkflowTraining extends Model
{
    use HasFactory;

    public $table = 'dynamic_service_workflow_training';

    protected $dates = [
        'appointment_date',
        'program_start_date',
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'workflow_id',
        'service_type',
        'appointment_date',
        'appointment_attended',
        'specialist_report',
        'training_department_approved',
        'program_start_date',
        'attendance_data',
        'accounting_entries',
        'test_passed',
        'test_result',
        'alternatives_offered',
        'satisfaction_assessment',
        'device_delivered',
        'device_item_id',
        'payment_url',
        'is_paid_program',
        'in_waiting_list',
        'group_position',
        'group_size',
        'group_completed',
        'meeting_schedule',
        'certificate_issued',
        'certificate_test_passed',
        'certificate_message_sent',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'appointment_date' => 'datetime',
        'program_start_date' => 'datetime',
        'appointment_attended' => 'boolean',
        'training_department_approved' => 'boolean',
        'is_paid_program' => 'boolean',
        'in_waiting_list' => 'boolean',
        'group_completed' => 'boolean',
        'meeting_schedule' => 'array',
        'attendance_data' => 'array',
        'accounting_entries' => 'array',
        'test_passed' => 'boolean',
        'satisfaction_assessment' => 'array',
        'device_delivered' => 'boolean',
        'certificate_issued' => 'boolean',
        'certificate_test_passed' => 'boolean',
    ];

    // Training workflow statuses
    public const STATUS_APPOINTMENT_SET = 'appointment_set';
    public const STATUS_APPOINTMENT_RESCHEDULED = 'appointment_rescheduled';
    public const STATUS_RATED = 'rated';
    public const STATUS_SENT_TO_TRAINING = 'sent_to_training';
    public const STATUS_TRAINING_APPROVED = 'training_approved';
    public const STATUS_TRAINING_REJECTED = 'training_rejected';
    public const STATUS_APPOINTMENTS_SCHEDULED = 'appointments_scheduled';
    public const STATUS_PROGRAM_STARTED = 'program_started';
    public const STATUS_ATTENDANCE_TRACKING = 'attendance_tracking';
    public const STATUS_ACCOUNTING_ENTRIES = 'accounting_entries';
    public const STATUS_TEST_SENT = 'test_sent';
    public const STATUS_TEST_FAILED = 'test_failed';
    public const STATUS_TEST_PASSED = 'test_passed';
    public const STATUS_DEVICE_DELIVERY = 'device_delivery';
    
    // Group-specific statuses
    public const STATUS_APPROVED_WITH_PAYMENT = 'approved_with_payment';
    public const STATUS_IN_WAITING_LIST = 'in_waiting_list';
    public const STATUS_MEETING_SCHEDULE_SENT = 'meeting_schedule_sent';
    public const STATUS_CERTIFICATE_COMPLETED = 'certificate_completed';
    public const STATUS_CERTIFICATE_ISSUED = 'certificate_issued';

    public const STATUS_LABELS = [
        DynamicServiceWorkflow::STATUS_PENDING_REVIEW => 'قيد المراجعة',
        DynamicServiceWorkflow::STATUS_REFUSED => 'مرفوض',
        self::STATUS_APPOINTMENT_SET => 'تم تحديد موعد',
        self::STATUS_APPOINTMENT_RESCHEDULED => 'تم إعادة جدولة الموعد',
        self::STATUS_RATED => 'تم التقييم',
        self::STATUS_SENT_TO_TRAINING => 'تم الإرسال لقسم التدريب',
        self::STATUS_TRAINING_APPROVED => 'موافق عليه من قسم التدريب',
        self::STATUS_TRAINING_REJECTED => 'مرفوض من قسم التدريب',
        self::STATUS_APPOINTMENTS_SCHEDULED => 'تم جدولة المواعيد',
        self::STATUS_PROGRAM_STARTED => 'تم بدء البرنامج',
        self::STATUS_ATTENDANCE_TRACKING => 'تتبع الحضور',
        self::STATUS_ACCOUNTING_ENTRIES => 'القيد المحاسبي',
        self::STATUS_TEST_SENT => 'تم إرسال الاختبار',
        self::STATUS_TEST_FAILED => 'فشل الاختبار',
        self::STATUS_TEST_PASSED => 'نجح الاختبار',
        self::STATUS_DEVICE_DELIVERY => 'تسليم الجهاز',
        DynamicServiceWorkflow::STATUS_COMPLETED => 'مكتمل',
        // Group-specific labels
        self::STATUS_APPROVED_WITH_PAYMENT => 'موافق عليه مع رابط الدفع',
        self::STATUS_IN_WAITING_LIST => 'في قائمة الانتظار',
        self::STATUS_MEETING_SCHEDULE_SENT => 'تم إرسال جدول اللقاءات',
        self::STATUS_CERTIFICATE_COMPLETED => 'اكتمال الشهادة',
        self::STATUS_CERTIFICATE_ISSUED => 'تم إصدار الشهادة',
    ];

    public function workflow()
    {
        return $this->belongsTo(DynamicServiceWorkflow::class, 'workflow_id');
    }

    public function canTransitionTo($status)
    {
        $currentStatus = $this->workflow->current_status;
        
        if ($this->service_type === 'group') {
            return $this->canTransitionToGroup($currentStatus, $status);
        } else {
            return $this->canTransitionToIndividual($currentStatus, $status);
        }
    }

    private function canTransitionToIndividual($currentStatus, $status)
    {
        $validTransitions = [
            DynamicServiceWorkflow::STATUS_PENDING_REVIEW => [DynamicServiceWorkflow::STATUS_REFUSED, self::STATUS_APPOINTMENT_SET],
            self::STATUS_APPOINTMENT_SET => [self::STATUS_APPOINTMENT_RESCHEDULED, self::STATUS_RATED],
            self::STATUS_APPOINTMENT_RESCHEDULED => [self::STATUS_APPOINTMENT_SET, self::STATUS_RATED],
            self::STATUS_RATED => [self::STATUS_SENT_TO_TRAINING],
            self::STATUS_SENT_TO_TRAINING => [self::STATUS_TRAINING_APPROVED, self::STATUS_TRAINING_REJECTED],
            self::STATUS_TRAINING_APPROVED => [self::STATUS_APPOINTMENTS_SCHEDULED],
            self::STATUS_APPOINTMENTS_SCHEDULED => [self::STATUS_PROGRAM_STARTED],
            self::STATUS_PROGRAM_STARTED => [self::STATUS_ATTENDANCE_TRACKING],
            self::STATUS_ATTENDANCE_TRACKING => [self::STATUS_ACCOUNTING_ENTRIES],
            self::STATUS_ACCOUNTING_ENTRIES => [self::STATUS_TEST_SENT],
            self::STATUS_TEST_SENT => [self::STATUS_TEST_FAILED, self::STATUS_TEST_PASSED],
            self::STATUS_TEST_FAILED => [DynamicServiceWorkflow::STATUS_COMPLETED],
            self::STATUS_TEST_PASSED => [self::STATUS_DEVICE_DELIVERY],
            self::STATUS_DEVICE_DELIVERY => [DynamicServiceWorkflow::STATUS_COMPLETED],
        ];

        $allowedStatuses = $validTransitions[$currentStatus] ?? [];
        return in_array($status, $allowedStatuses);
    }

    private function canTransitionToGroup($currentStatus, $status)
    {
        $validTransitions = [
            DynamicServiceWorkflow::STATUS_PENDING_REVIEW => [DynamicServiceWorkflow::STATUS_REFUSED, self::STATUS_APPROVED_WITH_PAYMENT],
            self::STATUS_APPROVED_WITH_PAYMENT => [self::STATUS_IN_WAITING_LIST],
            self::STATUS_IN_WAITING_LIST => [self::STATUS_MEETING_SCHEDULE_SENT],
            self::STATUS_MEETING_SCHEDULE_SENT => [self::STATUS_PROGRAM_STARTED],
            self::STATUS_PROGRAM_STARTED => [self::STATUS_ATTENDANCE_TRACKING],
            self::STATUS_ATTENDANCE_TRACKING => [self::STATUS_ACCOUNTING_ENTRIES],
            self::STATUS_ACCOUNTING_ENTRIES => [self::STATUS_CERTIFICATE_COMPLETED],
            self::STATUS_CERTIFICATE_COMPLETED => [self::STATUS_CERTIFICATE_ISSUED, self::STATUS_DEVICE_DELIVERY, DynamicServiceWorkflow::STATUS_COMPLETED],
            self::STATUS_DEVICE_DELIVERY => [self::STATUS_CERTIFICATE_ISSUED, DynamicServiceWorkflow::STATUS_COMPLETED],
            self::STATUS_CERTIFICATE_ISSUED => [DynamicServiceWorkflow::STATUS_COMPLETED],
        ];

        $allowedStatuses = $validTransitions[$currentStatus] ?? [];
        return in_array($status, $allowedStatuses);
    }
}

