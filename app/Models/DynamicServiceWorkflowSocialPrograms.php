<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DynamicServiceWorkflowSocialPrograms extends Model
{
    use HasFactory;

    public $table = 'dynamic_service_workflow_social_programs';

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'workflow_id',
        'program_accepted',
        'program_completed',
        'waiting_list_position',
        'document_prepared',
        'document_data',
        'executive_decision',
        'executive_decision_notes',
        'program_proceeded',
        'program_proceeded_date',
        'review_notes',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'program_accepted' => 'boolean',
        'program_completed' => 'boolean',
        'document_prepared' => 'boolean',
        'program_proceeded' => 'boolean',
        'document_data' => 'array',
        'program_proceeded_date' => 'datetime',
    ];

    // Social programs workflow statuses
    public const STATUS_ACCEPTED = 'accepted';
    public const STATUS_PROGRAM_COMPLETED_CHECK = 'program_completed_check';
    public const STATUS_PROGRAM_COMPLETED = 'program_completed';
    public const STATUS_PROGRAM_NOT_COMPLETED = 'program_not_completed';
    public const STATUS_ON_WAITING_LIST = 'on_waiting_list';
    public const STATUS_DOCUMENT_PREPARED = 'document_prepared';
    public const STATUS_SENT_TO_EXECUTIVE_MANAGEMENT = 'sent_to_executive_management';
    public const STATUS_EXECUTIVE_ACCEPTED = 'executive_accepted';
    public const STATUS_EXECUTIVE_REFUSED = 'executive_refused';
    public const STATUS_PROGRAM_PROCEEDED = 'program_proceeded';
    public const STATUS_BACK_TO_PROGRAM_MANAGEMENT_REVIEW = 'back_to_program_management_review';

    public const STATUS_LABELS = [
        DynamicServiceWorkflow::STATUS_PENDING_REVIEW => 'قيد المراجعة',
        DynamicServiceWorkflow::STATUS_REFUSED => 'مرفوض',
        self::STATUS_ACCEPTED => 'مقبول',
        self::STATUS_PROGRAM_COMPLETED_CHECK => 'التحقق من اكتمال البرنامج',
        self::STATUS_PROGRAM_COMPLETED => 'البرنامج مكتمل',
        self::STATUS_PROGRAM_NOT_COMPLETED => 'البرنامج غير مكتمل',
        self::STATUS_ON_WAITING_LIST => 'في قائمة الانتظار',
        self::STATUS_DOCUMENT_PREPARED => 'تم إعداد الوثائق',
        self::STATUS_SENT_TO_EXECUTIVE_MANAGEMENT => 'تم الإرسال للإدارة التنفيذية',
        self::STATUS_EXECUTIVE_ACCEPTED => 'قرار الإدارة التنفيذية: مقبول',
        self::STATUS_EXECUTIVE_REFUSED => 'قرار الإدارة التنفيذية: مرفوض',
        self::STATUS_PROGRAM_PROCEEDED => 'تم المضي قدماً في البرنامج',
        self::STATUS_BACK_TO_PROGRAM_MANAGEMENT_REVIEW => 'العودة لإدارة البرنامج للمراجعة',
        DynamicServiceWorkflow::STATUS_COMPLETED => 'مكتمل',
    ];

    public function workflow()
    {
        return $this->belongsTo(DynamicServiceWorkflow::class, 'workflow_id');
    }

    public function canTransitionTo($status)
    {
        $currentStatus = $this->workflow->current_status;

        $validTransitions = [
            DynamicServiceWorkflow::STATUS_PENDING_REVIEW => [
                DynamicServiceWorkflow::STATUS_REFUSED,
                self::STATUS_ACCEPTED
            ],
            self::STATUS_ACCEPTED => [self::STATUS_PROGRAM_COMPLETED_CHECK],
            self::STATUS_PROGRAM_COMPLETED_CHECK => [
                self::STATUS_PROGRAM_COMPLETED,
                self::STATUS_PROGRAM_NOT_COMPLETED
            ],
            self::STATUS_PROGRAM_NOT_COMPLETED => [self::STATUS_ON_WAITING_LIST],
            self::STATUS_ON_WAITING_LIST => [self::STATUS_PROGRAM_COMPLETED],
            self::STATUS_PROGRAM_COMPLETED => [self::STATUS_DOCUMENT_PREPARED],
            self::STATUS_DOCUMENT_PREPARED => [self::STATUS_SENT_TO_EXECUTIVE_MANAGEMENT],
            self::STATUS_SENT_TO_EXECUTIVE_MANAGEMENT => [
                self::STATUS_EXECUTIVE_ACCEPTED,
                self::STATUS_EXECUTIVE_REFUSED
            ],
            self::STATUS_EXECUTIVE_ACCEPTED => [self::STATUS_PROGRAM_PROCEEDED],
            self::STATUS_EXECUTIVE_REFUSED => [self::STATUS_BACK_TO_PROGRAM_MANAGEMENT_REVIEW],
            self::STATUS_BACK_TO_PROGRAM_MANAGEMENT_REVIEW => [
                self::STATUS_DOCUMENT_PREPARED, // Loop back to prepare document again
                DynamicServiceWorkflow::STATUS_COMPLETED // Or complete if review is done
            ],
            self::STATUS_PROGRAM_PROCEEDED => [DynamicServiceWorkflow::STATUS_COMPLETED],
        ];

        $allowedStatuses = $validTransitions[$currentStatus] ?? [];
        return in_array($status, $allowedStatuses);
    }
}

