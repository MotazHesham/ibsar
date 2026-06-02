<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\DynamicServices\Models\DynamicService;

class WorkflowFinanceRequest extends Model
{
    use SoftDeletes;

    public const STATUS_UNPOSTED = 'unposted';
    public const STATUS_POSTED = 'posted';

    public const STATUS_SELECT = [
        self::STATUS_UNPOSTED => 'قيد غير مرحّل',
        self::STATUS_POSTED => 'مرحّل',
    ];

    protected $fillable = [
        'beneficiary_order_id',
        'source_type',
        'source_id',
        'reference_type',
        'reference_id',
        'workflow_category',
        'workflow_step',
        'trigger_action',
        'title',
        'amount',
        'status',
        'journal_reference',
        'notes',
        'processed_by',
        'processed_at',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'processed_at' => 'datetime',
    ];

    public function beneficiaryOrder(): BelongsTo
    {
        return $this->belongsTo(BeneficiaryOrder::class);
    }

    public function source(): MorphTo
    {
        return $this->morphTo();
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getWorkflowCategoryLabelAttribute(): string
    {
        return DynamicService::CATEGORIES[$this->workflow_category] ?? $this->workflow_category;
    }

    public function isUnposted(): bool
    {
        return $this->status === self::STATUS_UNPOSTED;
    }
}
