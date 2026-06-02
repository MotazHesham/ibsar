@php
    $targetCount = $programWorkflow['targetCount'] ?? 0;
    $registeredCount = $programWorkflow['registeredCount'] ?? 0;
    $pendingApproval = $programWorkflow['pendingApproval'] ?? collect();
    $canSendDetails = $programWorkflow['canSendDetails'] ?? false;
    $isProgramCompleted = $programWorkflow['isProgramCompleted'] ?? false;
    $programDetails = $programWorkflow['programDetails'] ?? [];
@endphp

<div class="row mt-4">
    <div class="col-12">
        <div class="card custom-card">
            <div class="card-header">
                <h6 class="card-title mb-0">مسار البرنامج الاجتماعي</h6>
            </div>
            <div class="card-body">
                <div class="alert alert-info py-2 mb-3">
                    <small>
                        <strong>العدد المستهدف:</strong> {{ $targetCount ?: 'غير محدد' }}
                        | <strong>المعتمدون:</strong> {{ $registeredCount }}
                        @if ($targetCount > 0)
                            ({{ $registeredCount }}/{{ $targetCount }})
                        @endif
                    </small>
                </div>

                @if ($isProgramCompleted && !empty($programDetails['message']))
                    <div class="alert alert-success">
                        <strong>تم إرسال تفاصيل البرنامج للمستفيدين</strong>
                        <div class="mt-2 small">{{ $programDetails['message'] }}</div>
                    </div>
                @endif

                @if ($pendingApproval->isNotEmpty())
                    <h6 class="mb-3">طلبات بانتظار الاعتماد</h6>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>المستفيد</th>
                                    <th>تاريخ الطلب</th>
                                    <th>الإجراء</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pendingApproval as $index => $dynamicServiceOrder)
                                    @php $beneficiaryOrder = $dynamicServiceOrder->beneficiaryOrder; @endphp
                                    @if ($beneficiaryOrder)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $beneficiaryOrder->beneficiary?->user?->name ?? '—' }}</td>
                                            <td>{{ $beneficiaryOrder->created_at?->format('Y-m-d H:i') ?? '—' }}</td>
                                            <td>
                                                <form action="{{ route('admin.dynamic-services.process-workflow', $dynamicService) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf @method('PUT')
                                                    <input type="hidden" name="beneficiary_order_id" value="{{ $beneficiaryOrder->id }}">
                                                    <input type="hidden" name="workflow_action" value="approve_projects">
                                                    <button type="submit" class="btn btn-sm btn-success">يعتمد</button>
                                                </form>
                                                <button type="button" class="btn btn-sm btn-danger"
                                                    data-bs-toggle="collapse"
                                                    data-bs-target="#reject-form-{{ $beneficiaryOrder->id }}">
                                                    لا يعتمد
                                                </button>
                                                <div class="collapse mt-2" id="reject-form-{{ $beneficiaryOrder->id }}">
                                                    <form action="{{ route('admin.dynamic-services.process-workflow', $dynamicService) }}"
                                                        method="POST">
                                                        @csrf @method('PUT')
                                                        <input type="hidden" name="beneficiary_order_id" value="{{ $beneficiaryOrder->id }}">
                                                        <input type="hidden" name="workflow_action" value="reject">
                                                        @include('utilities.form.textarea', [
                                                            'name' => 'reason',
                                                            'label' => 'سبب الرفض',
                                                            'isRequired' => true,
                                                            'grid' => 'col-12',
                                                        ])
                                                        <button type="submit" class="btn btn-sm btn-danger mt-2">تأكيد الرفض</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                @if ($canSendDetails)
                    <div class="card border-primary">
                        <div class="card-header py-2 bg-primary-transparent">
                            <h6 class="mb-0">إرسال تفاصيل البرنامج — العدد مكتمل</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.dynamic-services.process-workflow', $dynamicService) }}" method="POST">
                                @csrf @method('PUT')
                                <input type="hidden" name="workflow_action" value="send_program_details">
                                @include('utilities.form.textarea', [
                                    'name' => 'program_details_message',
                                    'label' => 'تفاصيل البرنامج للمستفيدين',
                                    'isRequired' => true,
                                    'grid' => 'col-12',
                                    'value' => $programDetails['message'] ?? '',
                                ])
                                <button type="submit" class="btn btn-primary mt-2">إرسال تفاصيل البرنامج للمستفيدين</button>
                            </form>
                        </div>
                    </div>
                @elseif ($targetCount > 0 && $registeredCount < $targetCount && ($programWorkflow['waitingForDetails'] ?? 0) > 0)
                    <div class="alert alert-warning mb-0">
                        بانتظار اكتمال العدد المستهدف ({{ $registeredCount }}/{{ $targetCount }}) قبل إرسال تفاصيل البرنامج.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
