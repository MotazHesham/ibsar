@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.donationManagement.title'), 'url' => route('admin.workflow-finance-requests.index')],
            ['title' => trans('cruds.workflowFinanceRequest.title_singular') . ' #' . $workflowFinanceRequest->id, 'url' => '#'],
        ];
        $beneficiaryOrder = $workflowFinanceRequest->beneficiaryOrder;
        $isUnposted = $workflowFinanceRequest->isUnposted();
    @endphp
    @include('partials.breadcrumb')

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row">
        <div class="col-lg-7">
            <div class="card custom-card">
                <div class="card-header">
                    <h6 class="card-title mb-0">{{ trans('cruds.workflowFinanceRequest.title_singular') }}</h6>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th>{{ trans('cruds.workflowFinanceRequest.fields.status') }}</th>
                                <td>
                                    <span class="badge bg-{{ $isUnposted ? 'warning' : 'success' }}-transparent">
                                        {{ \App\Models\WorkflowFinanceRequest::STATUS_SELECT[$workflowFinanceRequest->status] ?? $workflowFinanceRequest->status }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>{{ trans('cruds.workflowFinanceRequest.fields.title') }}</th>
                                <td>{{ $workflowFinanceRequest->title }}</td>
                            </tr>
                            <tr>
                                <th>{{ trans('cruds.workflowFinanceRequest.fields.workflow_category') }}</th>
                                <td>{{ $workflowFinanceRequest->workflow_category_label }}</td>
                            </tr>
                            <tr>
                                <th>{{ trans('cruds.workflowFinanceRequest.fields.beneficiary') }}</th>
                                <td>{{ $beneficiaryOrder?->beneficiary?->user?->name ?? '—' }}</td>
                            </tr>
                            <tr>
                                <th>{{ trans('cruds.beneficiaryOrder.title_singular') }}</th>
                                <td>
                                    @if ($beneficiaryOrder)
                                        <a href="{{ route('admin.beneficiary-orders.show', $beneficiaryOrder) }}">#{{ $beneficiaryOrder->id }}</a>
                                    @else
                                        —
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>{{ trans('cruds.workflowFinanceRequest.fields.amount') }}</th>
                                <td>{{ $workflowFinanceRequest->amount !== null ? number_format((float) $workflowFinanceRequest->amount, 2) : '—' }}</td>
                            </tr>
                            @if ($workflowFinanceRequest->journal_reference)
                                <tr>
                                    <th>{{ trans('cruds.workflowFinanceRequest.fields.journal_reference') }}</th>
                                    <td>{{ $workflowFinanceRequest->journal_reference }}</td>
                                </tr>
                            @endif
                            @if ($workflowFinanceRequest->notes)
                                <tr>
                                    <th>{{ trans('cruds.workflowFinanceRequest.fields.notes') }}</th>
                                    <td>{{ $workflowFinanceRequest->notes }}</td>
                                </tr>
                            @endif
                            @if ($workflowFinanceRequest->processed_at)
                                <tr>
                                    <th>{{ trans('cruds.workflowFinanceRequest.fields.processed_at') }}</th>
                                    <td>{{ $workflowFinanceRequest->processed_at->format('Y-m-d H:i') }}</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @if ($isUnposted)
            @can('workflow_finance_request_edit')
                <div class="col-lg-5">
                    <div class="card custom-card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">تحديد التكلفة وترحيل القيد</h6>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('admin.workflow-finance-requests.update', $workflowFinanceRequest) }}">
                                @csrf @method('PUT')
                                @include('utilities.form.text', [
                                    'name' => 'amount',
                                    'label' => 'cruds.workflowFinanceRequest.fields.amount',
                                    'isRequired' => true,
                                    'grid' => 'col-12',
                                    'value' => old('amount', $workflowFinanceRequest->amount),
                                    'attributes' => 'type="number" step="0.01" min="0.01"',
                                ])
                                @include('utilities.form.text', [
                                    'name' => 'journal_reference',
                                    'label' => 'cruds.workflowFinanceRequest.fields.journal_reference',
                                    'isRequired' => false,
                                    'grid' => 'col-12',
                                    'value' => old('journal_reference', $workflowFinanceRequest->journal_reference),
                                ])
                                @include('utilities.form.textarea', [
                                    'name' => 'notes',
                                    'label' => 'cruds.workflowFinanceRequest.fields.notes',
                                    'isRequired' => false,
                                    'grid' => 'col-12',
                                    'value' => old('notes', $workflowFinanceRequest->notes),
                                ])
                                <div class="d-flex flex-wrap gap-2 mt-3">
                                    <button type="submit" name="action" value="save" class="btn btn-primary">
                                        {{ trans('global.save') }}
                                    </button>
                                    <button type="submit" name="action" value="post" class="btn btn-success"
                                        onclick="return confirm('تأكيد ترحيل القيد؟')">
                                        ترحيل القيد
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endcan
        @endif
    </div>
@endsection
