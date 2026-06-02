<?php

namespace Modules\DynamicServices\Helpers;

use App\Models\User;
use Modules\DynamicServices\Workflows\AssistanceWorkflowHandler;

class AssistanceWorkflowHistoryHelper
{
    public const ACTION_LABELS = [
        AssistanceWorkflowHandler::ACTION_APPROVE_RESEARCHER => 'اعتماد الباحث الاجتماعي',
        AssistanceWorkflowHandler::ACTION_APPROVE_PROJECTS => 'اعتماد قسم المشاريع',
        AssistanceWorkflowHandler::ACTION_DISBURSE_FINANCE => 'صرف المالية',
        AssistanceWorkflowHandler::ACTION_VERIFY_OTP => 'تأكيد الاستلام (OTP)',
        AssistanceWorkflowHandler::ACTION_REQUEST_INCOMPLETE => 'طلب استكمال الوثائق',
        'approve' => 'اعتماد',
        'reject' => 'رفض',
        'complete' => 'إنهاء الطلب',
        'return_researcher' => 'إعادة للباحث الاجتماعي',
        'auto_stock_reject' => 'اعتذار تلقائي (عدم توفر المخزون)',
        'beneficiary_pickup_schedule' => 'تأكيد موعد الاستلام (المستفيد)',
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

        if (! empty($entry['reason'])) {
            $details[] = 'السبب: ' . $entry['reason'];
        }

        if (! empty($entry['item_name'])) {
            $details[] = 'الصنف: ' . $entry['item_name'];
        }

        if (! empty($entry['quantity'])) {
            $details[] = 'الكمية: ' . $entry['quantity'];
        }

        if (! empty($entry['allocated_amount'])) {
            $details[] = 'المبلغ المرصود: ' . $entry['allocated_amount'];
        }

        if (! empty($entry['amount'])) {
            $details[] = 'المبلغ المعتمد: ' . $entry['amount'];
        }

        if (isset($entry['requires_training'])) {
            $details[] = 'يحتاج تدريب: ' . ($entry['requires_training'] ? 'نعم' : 'لا');
            if (! empty($entry['training_type'])) {
                $details[] = 'نوع التدريب: ' . ($entry['training_type'] === 'group' ? 'جماعي' : 'فردي');
            }
        }

        if (! empty($entry['date']) && ! empty($entry['time'])) {
            $details[] = 'موعد الاستلام: ' . $entry['date'] . ' — ' . $entry['time'];
        }

        if (! empty($entry['disbursement_reference'])) {
            $details[] = 'مرجع الصرف: ' . $entry['disbursement_reference'];
        }

        if (! empty($entry['message'])) {
            $details[] = 'ملاحظة: ' . $entry['message'];
        }

        if (! empty($entry['notes'])) {
            $details[] = 'ملاحظات: ' . $entry['notes'];
        }

        if (! empty($entry['rating'])) {
            $details[] = 'تقييم الرضا: ' . $entry['rating'] . '/5';
            if (! empty($entry['comment'])) {
                $details[] = 'تعليق: ' . $entry['comment'];
            }
        }

        return $details;
    }
}
