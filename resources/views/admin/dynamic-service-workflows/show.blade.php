@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.beneficiaryOrdersManagement.title'), 'url' => '#'],
            [
                'title' => trans('global.list') . ' ' . trans('cruds.beneficiaryOrder.title'),
                'url' => route('admin.beneficiary-orders.index'),
            ],
            ['title' => 'إدارة سير العمل', 'url' => '#'],
        ];
        $pageTitle = 'إدارة سير العمل - طلب #' . $dynamicServiceOrder->beneficiaryOrder->id;
    @endphp
    @include('partials.breadcrumb')

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title">معلومات الطلب</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>المستفيد:</strong> {{ $dynamicServiceOrder->beneficiaryOrder->beneficiary->user->name ?? '' }}</p>
                            <p><strong>الخدمة:</strong> {{ $dynamicServiceOrder->dynamicService->title ?? '' }}</p>
                            <p><strong>نوع الخدمة:</strong> 
                                @if($workflow->category === 'training')
                                    {{ ($workflow->training->service_type ?? 'individual') === 'individual' ? 'فردي' : 'جماعي' }}
                                @elseif($workflow->category === 'assistance')
                                    {{ \App\Models\DynamicService::CATEGORIES[\App\Models\DynamicService::CATEGORY_ASSISTANCE] }}
                                @elseif($workflow->category === 'social_programs')
                                    {{ \App\Models\DynamicService::CATEGORIES[\App\Models\DynamicService::CATEGORY_SOCIAL_PROGRAMS] }}
                                @elseif($workflow->category === 'surgical_procedures')
                                    {{ \App\Models\DynamicService::CATEGORIES[\App\Models\DynamicService::CATEGORY_SURGICAL_PROCEDURES] }}
                                @elseif($workflow->category === 'detection_center')
                                    {{ \App\Models\DynamicService::CATEGORIES[\App\Models\DynamicService::CATEGORY_DETECTION_CENTER] }}
                                @else
                                    {{ $workflow->category }}
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>الحالة الحالية:</strong> 
                                <span class="badge bg-primary">{{ $workflow->status_label }}</span>
                            </p>
                            <p><strong>تاريخ الإنشاء:</strong> {{ $dynamicServiceOrder->created_at->format('Y-m-d H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title">سير العمل</h6>
                </div>
                <div class="card-body">
                    @include('admin.dynamic-service-workflows.partials.workflow-form')
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="card-title">سجل التحولات</h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        @foreach($workflow->transitions->sortByDesc('created_at') as $transition)
                            <div class="timeline-item">
                                <div class="timeline-marker"></div>
                                <div class="timeline-content">
                                    <h6>{{ $transition->user->name ?? 'غير معروف' }}</h6>
                                    <p class="text-muted">{{ $transition->created_at->format('Y-m-d H:i') }}</p>
                                    @php
                                        // Get status labels based on category
                                        $statusLabels = [];
                                        if ($workflow->category === 'training') {
                                            $statusLabels = \App\Models\DynamicServiceWorkflowTraining::STATUS_LABELS;
                                        } elseif ($workflow->category === 'assistance') {
                                            $statusLabels = \App\Models\DynamicServiceWorkflowAssistance::STATUS_LABELS;
                                        } else {
                                            $statusLabels = \App\Models\DynamicServiceWorkflow::STATUS_LABELS;
                                        }
                                    @endphp
                                    <p><strong>من:</strong> {{ $statusLabels[$transition->from_status] ?? $transition->from_status }}</p>
                                    <p><strong>إلى:</strong> {{ $statusLabels[$transition->to_status] ?? $transition->to_status }}</p>
                                    @if($transition->notes)
                                        <p><strong>ملاحظات:</strong> {{ $transition->notes }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            @include('admin.dynamic-service-workflows.partials.workflow-info')
        </div>
    </div>
@endsection

@section('styles')
<style>
    .timeline {
        position: relative;
        padding-left: 30px;
    }
    .timeline-item {
        position: relative;
        padding-bottom: 20px;
    }
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

