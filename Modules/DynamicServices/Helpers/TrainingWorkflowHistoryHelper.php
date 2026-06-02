<?php

namespace Modules\DynamicServices\Helpers;

use App\Models\User;
use Modules\DynamicServices\Services\TrainingWorkflowService;
use Modules\DynamicServices\Workflows\TrainingWorkflowHandler;

class TrainingWorkflowHistoryHelper
{
    public const ACTION_LABELS = [
        TrainingWorkflowHandler::ACTION_SUBMIT_EVALUATION_SCHEDULE => 'حفظ وإرسال موعد التقييم',
        TrainingWorkflowHandler::ACTION_SUBMIT_EVALUATION => 'حفظ نموذج التقييم',
        TrainingWorkflowHandler::ACTION_RESCHEDULE => 'إعادة جدولة الموعد',
        TrainingWorkflowHandler::ACTION_APPROVE_FINANCIAL => 'اعتماد المالية',
        TrainingWorkflowHandler::ACTION_REJECT_FINANCIAL => 'رفض المالية (لم يسدد)',
        TrainingWorkflowHandler::ACTION_CONFIRM_DONATION => 'تأكيد تخصيص التبرع',
        TrainingWorkflowHandler::ACTION_SCHEDULE_SESSION => 'جدولة موعد جلسة',
        TrainingWorkflowHandler::ACTION_MARK_SESSION_ATTENDED => 'تسجيل حضور جلسة',
        TrainingWorkflowHandler::ACTION_SUBMIT_TEST => 'حفظ درجة الاختبار',
        TrainingWorkflowHandler::ACTION_COMPLETE_SATISFACTION => 'إكمال استبيان الرضا',
        TrainingWorkflowHandler::ACTION_SUBMIT_GROUP_SCHEDULE => 'إرسال جدول اللقاءات',
        TrainingWorkflowHandler::ACTION_MARK_PROGRAM_ATTENDANCE => 'تسجيل حضور البرنامج',
        'approve' => 'اعتماد',
        'reject' => 'رفض',
        'mark_attended' => 'تسجيل حضور التقييم',
        'mark_not_attended' => 'عدم حضور التقييم',
        'advance' => 'الانتقال للمرحلة التالية',
        'complete' => 'إنهاء الطلب',
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
        $action = $entry['action'] ?? '';

        if (! empty($entry['reason'])) {
            $details[] = 'السبب: ' . $entry['reason'];
        }

        if (! empty($entry['date']) && ! empty($entry['time'])) {
            $details[] = 'الموعد: ' . $entry['date'] . ' — ' . $entry['time'];
        } elseif (! empty($entry['date'])) {
            $details[] = 'التاريخ: ' . $entry['date'];
        }

        if (! empty($entry['types']) && is_array($entry['types'])) {
            $typeLabels = array_map(
                fn ($type) => TrainingWorkflowService::EVALUATION_APPOINTMENT_TYPES[$type] ?? $type,
                $entry['types']
            );
            $details[] = 'أنواع التقييم: ' . implode('، ', $typeLabels);
        }

        if (isset($entry['session_number'])) {
            $details[] = 'الجلسة رقم: ' . $entry['session_number'];
        }

        if (! empty($entry['session_date'])) {
            $details[] = 'تاريخ الجلسة: ' . $entry['session_date'] . (! empty($entry['session_time']) ? ' — ' . $entry['session_time'] : '');
        }

        if (isset($entry['qualified'])) {
            $details[] = 'التأهل للتدريب: ' . ($entry['qualified'] ? 'مؤهل' : 'غير مؤهل');
        }

        if (! empty($entry['sessions_count'])) {
            $details[] = 'عدد الجلسات: ' . $entry['sessions_count'];
        }

        if (! empty($entry['evaluator_name'])) {
            $details[] = 'المقيّم: ' . $entry['evaluator_name'];
        } elseif (! empty($entry['visual_status'])) {
            $status = TrainingWorkflowService::VISUAL_STATUS_OPTIONS[$entry['visual_status']] ?? $entry['visual_status'];
            if (($entry['visual_status'] ?? '') === 'other' && ! empty($entry['visual_status_other'])) {
                $status .= ' (' . $entry['visual_status_other'] . ')';
            }
            $details[] = 'الحالة البصرية: ' . $status;
        }

        if (isset($entry['approved'])) {
            $details[] = 'المالية: ' . ($entry['approved'] ? 'معتمد' : 'غير معتمد');
        }

        if (isset($entry['passed'])) {
            $details[] = 'نتيجة الاختبار: ' . ($entry['passed'] ? 'اجتاز' : 'لم يجتز');
            if (isset($entry['average'])) {
                $details[] = 'المتوسط: ' . $entry['average'] . '%';
            }
            if (isset($entry['needs_device'])) {
                $details[] = 'يحتاج جهاز: ' . ($entry['needs_device'] ? 'نعم' : 'لا');
            }
        }

        if (! empty($entry['start_date']) && ! empty($entry['end_date'])) {
            $details[] = 'جدول البرنامج: من ' . $entry['start_date'] . ' إلى ' . $entry['end_date'];
            if (! empty($entry['days']) && is_array($entry['days'])) {
                $details[] = 'الأيام: ' . implode('، ', $entry['days']);
            }
            if (! empty($entry['start_time']) && ! empty($entry['end_time'])) {
                $details[] = 'الوقت: ' . $entry['start_time'] . ' — ' . $entry['end_time'];
            }
        }

        if ($action === 'beneficiary_satisfaction' && ! empty($entry['rating'])) {
            $details[] = 'تقييم الرضا: ' . $entry['rating'] . '/5';
            if (! empty($entry['comment'])) {
                $details[] = 'ملاحظات: ' . $entry['comment'];
            }
        }

        if (! empty($entry['note'])) {
            $details[] = 'ملاحظة: ' . $entry['note'];
        }

        return $details;
    }
}
