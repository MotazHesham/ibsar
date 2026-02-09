@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => 'سير العمل (المحرك الجديد)', 'url' => route('admin.workflow-instances.index')],
            ['title' => 'حالة #' . $workflowInstance->id, 'url' => '#'],
        ];
        $pageTitle = 'سير العمل - ' . ($workflowInstance->workflow->name ?? '') . ' #' . $workflowInstance->id;
    @endphp
    @include('partials.breadcrumb')

    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0">معلومات الحالة</h6>
                    <span class="badge
                        @if($workflowInstance->status === 'running') bg-success
                        @elseif($workflowInstance->status === 'on_hold') bg-warning
                        @elseif($workflowInstance->status === 'completed') bg-secondary
                        @elseif($workflowInstance->status === 'cancelled') bg-danger
                        @else bg-light text-dark
                        @endif
                    ">
                        {{ $workflowInstance->status === 'running' ? 'قيد التشغيل' : ($workflowInstance->status === 'on_hold' ? 'في الانتظار' : ($workflowInstance->status === 'completed' ? 'مكتمل' : ($workflowInstance->status === 'cancelled' ? 'ملغى' : $workflowInstance->status))) }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>السير:</strong> {{ $workflowInstance->workflow->name ?? '-' }} ({{ $workflowInstance->workflow->key ?? '' }})</p>
                            <p class="mb-1"><strong>الجهة:</strong> {{ class_basename($workflowInstance->entity_type) }} #{{ $workflowInstance->entity_id }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>الخطوة الحالية:</strong> {{ $workflowInstance->currentStep->name ?? '-' }} ({{ $workflowInstance->currentStep->key ?? '-' }})</p>
                            <p class="mb-1"><strong>نوع الخطوة:</strong> {{ $workflowInstance->currentStep->type ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($workflowInstance->currentStep && in_array($workflowInstance->currentStep->type, ['human', 'decision']) && $workflowInstance->status === 'running')
        <div class="row mt-3">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title">تنفيذ الخطوة الحالية</h6>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.workflow-instances.execute', $workflowInstance) }}">
                            @csrf
                            <div class="mb-3">
                                <label for="condition_key" class="form-label">الإجراء (مفتاح الشرط)</label>
                                <select name="condition_key" id="condition_key" class="form-select">
                                    <option value="">-- اختر إن وُجد --</option>
                                    @foreach($outgoingTransitions as $t)
                                        @if($t->condition_key)
                                            <option value="{{ $t->condition_key }}">{{ $t->name ?? $t->condition_key }} → {{ $t->toStep->name ?? '' }}</option>
                                        @endif
                                    @endforeach
                                </select>
                                <small class="text-muted">مطلوب إذا كانت هناك أكثر من نتيجة (مثل: موافقة / رفض)</small>
                            </div>
                            <div class="mb-3">
                                <label for="notes" class="form-label">ملاحظات</label>
                                <textarea name="notes" id="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                            </div>
                            @error('error')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                            <button type="submit" class="btn btn-primary">تنفيذ</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="row mt-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title">سجل الأحداث (الجدول الزمني)</h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        @foreach($workflowInstance->logs as $log)
                            <div class="timeline-item">
                                <div class="timeline-marker"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">{{ $log->action }}</h6>
                                    <p class="text-muted mb-0 small">{{ $log->created_at?->format('Y-m-d H:i') }} @if($log->performer_role) | {{ $log->performer_role }} @endif</p>
                                    @if($log->notes)
                                        <p class="mb-0 mt-1">{{ $log->notes }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @if($workflowInstance->logs->isEmpty())
                        <p class="text-muted mb-0">لا توجد أحداث مسجلة.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('admin.workflow-instances.index') }}" class="btn btn-secondary">العودة للقائمة</a>
    </div>
@endsection

@section('styles')
<style>
    .timeline { position: relative; padding-left: 30px; }
    .timeline-item { position: relative; padding-bottom: 20px; }
    .timeline-marker {
        position: absolute;
        left: -8px;
        top: 5px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #007bff;
        border: 2px solid #fff;
    }
    .timeline-content {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
    }
</style>
@endsection
