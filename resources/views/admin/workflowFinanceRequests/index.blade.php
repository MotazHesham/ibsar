@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.donationManagement.title'), 'url' => '#'],
            ['title' => trans('cruds.workflowFinanceRequest.title'), 'url' => '#'],
        ];
        $currentStatus = request()->has('status') ? request('status') : 'unposted';
    @endphp
    @include('partials.breadcrumb')

    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a class="nav-link {{ $currentStatus === 'unposted' ? 'active' : '' }}"
                href="{{ route('admin.workflow-finance-requests.index', ['status' => 'unposted']) }}">
                {{ trans('cruds.workflowFinanceRequest.status.unposted') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $currentStatus === 'posted' ? 'active' : '' }}"
                href="{{ route('admin.workflow-finance-requests.index', ['status' => 'posted']) }}">
                {{ trans('cruds.workflowFinanceRequest.status.posted') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->has('status') && request('status') === '' ? 'active' : '' }}"
                href="{{ route('admin.workflow-finance-requests.index', ['status' => '']) }}">
                {{ trans('global.all') }}
            </a>
        </li>
    </ul>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-end">
                <input type="hidden" name="status" value="{{ $currentStatus }}">
                <div class="col-md-4">
                    <label class="form-label">{{ trans('cruds.workflowFinanceRequest.fields.workflow_category') }}</label>
                    <select name="workflow_category" class="form-select">
                        <option value="">{{ trans('global.all') }}</option>
                        @foreach ($categories as $key => $label)
                            <option value="{{ $key }}" @selected(request('workflow_category') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary">تصفية</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-striped table-hover ajaxTable w-100 datatable-WorkflowFinanceRequest">
                <thead>
                    <tr>
                        <th width="10"></th>
                        <th>#</th>
                        <th>{{ trans('cruds.workflowFinanceRequest.fields.beneficiary') }}</th>
                        <th>{{ trans('cruds.beneficiaryOrder.title_singular') }}</th>
                        <th>{{ trans('cruds.workflowFinanceRequest.fields.title') }}</th>
                        <th>{{ trans('cruds.workflowFinanceRequest.fields.workflow_category') }}</th>
                        <th>{{ trans('cruds.workflowFinanceRequest.fields.amount') }}</th>
                        <th>{{ trans('cruds.workflowFinanceRequest.fields.status') }}</th>
                        <th>{{ trans('cruds.workflowFinanceRequest.fields.created_at') }}</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@section('scripts')
    @parent
    <script>
        $(function() {
            let dtOverrideGlobals = {
                processing: true,
                serverSide: true,
                retrieve: true,
                aaSorting: [],
                ajax: {
                    url: "{{ route('admin.workflow-finance-requests.index') }}",
                    data: function(d) {
                        d.status = @json($currentStatus);
                        d.workflow_category = @json(request('workflow_category'));
                    }
                },
                columns: [
                    { data: 'placeholder', name: 'placeholder' },
                    { data: 'id', name: 'id' },
                    { data: 'beneficiary_name', name: 'beneficiaryOrder.beneficiary.user.name' },
                    { data: 'beneficiary_order_id', name: 'beneficiary_order_id' },
                    { data: 'title', name: 'title' },
                    { data: 'workflow_category_label', name: 'workflow_category' },
                    { data: 'amount', name: 'amount' },
                    { data: 'status', name: 'status' },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'actions', name: '{{ trans('global.actions') }}' }
                ],
                order: [[1, 'desc']],
                pageLength: 25,
            };

            $('.datatable-WorkflowFinanceRequest').DataTable(dtOverrideGlobals);
        });
    </script>
@endsection
