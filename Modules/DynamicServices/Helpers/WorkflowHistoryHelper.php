<?php

namespace Modules\DynamicServices\Helpers;

use App\Models\User;
use Modules\DynamicServices\Workflows\AbstractWorkflowHandler;
use Modules\DynamicServices\Workflows\AssistanceWorkflowHandler;
use Modules\DynamicServices\Workflows\DetectionCenterWorkflowHandler;
use Modules\DynamicServices\Workflows\SocialProgramsWorkflowHandler;
use Modules\DynamicServices\Workflows\SurgicalProceduresWorkflowHandler;
use Modules\DynamicServices\Workflows\TrainingWorkflowHandler;

class WorkflowHistoryHelper
{
    public const ACTION_LABELS = [
        AbstractWorkflowHandler::ACTION_APPROVE => 'اعتماد',
        AbstractWorkflowHandler::ACTION_REJECT => 'رفض',
        AbstractWorkflowHandler::ACTION_MARK_ATTENDED => 'حضر',
        AbstractWorkflowHandler::ACTION_MARK_NOT_ATTENDED => 'لم يحضر',
        AbstractWorkflowHandler::ACTION_ADVANCE => 'متابعة',
        AbstractWorkflowHandler::ACTION_COMPLETE => 'إنهاء الطلب',

        SocialProgramsWorkflowHandler::ACTION_APPROVE_PROJECTS => 'اعتماد (المشاريع)',
        SocialProgramsWorkflowHandler::ACTION_SEND_DETAILS => 'إرسال تفاصيل البرنامج',

        AssistanceWorkflowHandler::ACTION_APPROVE_RESEARCHER => 'اعتماد الباحث الاجتماعي',
        AssistanceWorkflowHandler::ACTION_APPROVE_PROJECTS => 'اعتماد قسم المشاريع',
        AssistanceWorkflowHandler::ACTION_DISBURSE_FINANCE => 'صرف المالية',
        AssistanceWorkflowHandler::ACTION_VERIFY_OTP => 'تأكيد الاستلام (OTP)',
        AssistanceWorkflowHandler::ACTION_REQUEST_INCOMPLETE => 'طلب استكمال الوثائق',
        AssistanceWorkflowHandler::ACTION_RETURN_RESEARCHER => 'إعادة للباحث الاجتماعي',

        TrainingWorkflowHandler::ACTION_SUBMIT_EVALUATION_SCHEDULE => 'جدولة التقييم',
        TrainingWorkflowHandler::ACTION_SUBMIT_EVALUATION => 'تقييم',
        TrainingWorkflowHandler::ACTION_RESCHEDULE => 'إعادة جدولة',
        TrainingWorkflowHandler::ACTION_APPROVE_FINANCIAL => 'اعتماد المالية',
        TrainingWorkflowHandler::ACTION_REJECT_FINANCIAL => 'رفض المساهمة المالية',
        TrainingWorkflowHandler::ACTION_CONFIRM_DONATION => 'تأكيد التبرع',
        TrainingWorkflowHandler::ACTION_SCHEDULE_SESSION => 'جدولة جلسة',
        TrainingWorkflowHandler::ACTION_MARK_SESSION_ATTENDED => 'تسجيل حضور الجلسة',
        TrainingWorkflowHandler::ACTION_SUBMIT_TEST => 'اختبار',
        TrainingWorkflowHandler::ACTION_COMPLETE_SATISFACTION => 'استبيان الرضا',
        TrainingWorkflowHandler::ACTION_SUBMIT_GROUP_SCHEDULE => 'جدول البرنامج الجماعي',
        TrainingWorkflowHandler::ACTION_MARK_PROGRAM_ATTENDANCE => 'حضور البرنامج',

        SurgicalProceduresWorkflowHandler::ACTION_APPROVE_RECEPTION => 'اعتماد الاستقبال',
        SurgicalProceduresWorkflowHandler::ACTION_TRANSFER_CLINIC => 'تحويل للعيادة',
        SurgicalProceduresWorkflowHandler::ACTION_CLOSE_CASE => 'إغلاق الحالة',
        SurgicalProceduresWorkflowHandler::ACTION_SUBMIT_CLINIC_REPORT => 'تقرير العيادة',
        SurgicalProceduresWorkflowHandler::ACTION_REJECT_CLINIC => 'رفض العيادة',
        SurgicalProceduresWorkflowHandler::ACTION_SUBMIT_CONTRIBUTION => 'تحديد المساهمة',
        SurgicalProceduresWorkflowHandler::ACTION_APPROVE_FINANCIAL => 'اعتماد المالية',
        SurgicalProceduresWorkflowHandler::ACTION_REJECT_FINANCIAL => 'رفض المساهمة المالية',
        SurgicalProceduresWorkflowHandler::ACTION_SUBMIT_OPERATION => 'تسجيل العملية',

        DetectionCenterWorkflowHandler::ACTION_SCHEDULE_EXAM => 'جدولة موعد الكشف',
        DetectionCenterWorkflowHandler::ACTION_SUBMIT_EVALUATION => 'تقييم الدكتور',
        DetectionCenterWorkflowHandler::ACTION_APPROVE_FINANCIAL => 'اعتماد المالية',
        DetectionCenterWorkflowHandler::ACTION_REJECT_FINANCIAL => 'رفض المساهمة المالية',
        DetectionCenterWorkflowHandler::ACTION_VERIFY_OTP => 'تأكيد الاستلام (OTP)',
        DetectionCenterWorkflowHandler::ACTION_ALLOCATE_DEVICE => 'تخصيص من المخزون',

        'auto_stock_reject' => 'اعتذار تلقائي (عدم توفر المخزون)',
        'beneficiary_pickup_schedule' => 'تأكيد موعد الاستلام (المستفيد)',
        'beneficiary_receipt' => 'رفع إيصال السداد (المستفيد)',
        'beneficiary_docs_completed' => 'استكمال الوثائق (المستفيد)',
        'beneficiary_satisfaction' => 'إكمال استبيان الرضا (المستفيد)',
    ];

    public static function actionLabel(string $action): string
    {
        return self::ACTION_LABELS[$action] ?? $action;
    }

    public static function stepLabel(array $steps, string $step): string
    {
        if ($step === 'rejected') {
            return 'مرفوض';
        }

        if ($step === 'completed') {
            return 'مكتمل';
        }

        return $steps[$step] ?? $step;
    }

    public static function actorName(?int $userId): string
    {
        if (! $userId) {
            return 'النظام';
        }

        return User::find($userId)?->name ?? 'موظف #' . $userId;
    }

    public static function formatDetails(array $entry): array
    {
        $details = [];

        $scalarFields = [
            'reason' => 'السبب',
            'message' => 'ملاحظة',
            'notes' => 'ملاحظات',
            'note' => 'ملاحظة',
            'item_name' => 'الصنف',
            'quantity' => 'الكمية',
            'allocated_amount' => 'المبلغ المرصود',
            'amount' => 'المبلغ',
            'contribution_amount' => 'قيمة المساهمة',
            'operation_type' => 'نوع العملية',
            'operation_name' => 'اسم العملية',
            'operation_price' => 'سعر العملية',
            'operation_summary' => 'تقرير العملية',
            'operation_date' => 'تاريخ العملية',
            'clinic_name' => 'العيادة',
            'close_reason' => 'سبب الإغلاق',
            'disbursement_reference' => 'مرجع الصرف',
            'outcome' => 'نتيجة التقييم',
            'evaluation_notes' => 'ملاحظات التقييم',
            'rating' => 'تقييم الرضا',
            'comment' => 'تعليق',
        ];

        foreach ($scalarFields as $key => $label) {
            if (! empty($entry[$key])) {
                $details[] = $label . ': ' . $entry[$key];
            }
        }

        if (! empty($entry['date']) && ! empty($entry['time'])) {
            $details[] = 'الموعد: ' . $entry['date'] . ' — ' . $entry['time'];
        } elseif (! empty($entry['date'])) {
            $details[] = 'التاريخ: ' . $entry['date'];
        }

        if (! empty($entry['exam_types']) && is_array($entry['exam_types'])) {
            $details[] = 'أنواع الكشف: ' . implode('، ', $entry['exam_types']);
        }

        if (! empty($entry['visual_aids']) && is_array($entry['visual_aids'])) {
            $details[] = 'المعينات: ' . implode('، ', $entry['visual_aids']);
        }

        if (isset($entry['requires_training'])) {
            $details[] = 'يحتاج تدريب: ' . ($entry['requires_training'] ? 'نعم' : 'لا');
            if (! empty($entry['training_type'])) {
                $details[] = 'نوع التدريب: ' . ($entry['training_type'] === 'group' ? 'جماعي' : 'فردي');
            }
        }

        if (! empty($entry['program_details']['message'])) {
            $details[] = 'تفاصيل البرنامج: ' . $entry['program_details']['message'];
        } elseif (! empty($entry['message']) && ($entry['action'] ?? '') === SocialProgramsWorkflowHandler::ACTION_SEND_DETAILS) {
            $details[] = 'تفاصيل البرنامج: ' . $entry['message'];
        }

        if (! empty($entry['approved']) && is_bool($entry['approved'])) {
            $details[] = $entry['approved'] ? 'تم الاعتماد' : 'لم يُعتمد';
        }

        return $details;
    }
}
