@extends('layouts.master')
@section('content')

<div class="card mt-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>{{ trans('cruds.beneficiaryOrdersReport.title') }}</span>
        @if(!empty($orders) && $orders->total() > 0)
            <a href="{{ route('admin.beneficiary-orders-reports.export', request()->all()) }}" class="btn btn-success btn-sm">
                {{ trans('global.export') }}
            </a>
        @endif
    </div>

    <div class="card-body">
        <form method="GET" action="{{ route('admin.beneficiary-orders-reports.index') }}" class="mb-4">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label">بحث عام (اسم/ايميل/عنوان/رقم)</label>
                    <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" class="form-control" placeholder="مثال: 42 أو أحمد" />
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">الخدمة</label>
                    <select name="service_id" class="form-control select2">
                        <option value="">الكل</option>
                        @foreach($services as $id => $name)
                            <option value="{{ $id }}" {{ (string)($filters['service_id'] ?? '') === (string)$id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">حالة الطلب</label>
                    <select name="status_id" class="form-control select2">
                        <option value="">الكل</option>
                        @foreach($statuses as $id => $name)
                            <option value="{{ $id }}" {{ (string)($filters['status_id'] ?? '') === (string)$id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">القبول</label>
                    <select name="accept_status" class="form-control select2">
                        <option value="">الكل</option>
                        @foreach($acceptOptions as $key => $label)
                            <option value="{{ $key }}" {{ ($filters['accept_status'] ?? '') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label">منتهي؟</label>
                    <select name="done" class="form-control select2">
                        <option value="">الكل</option>
                        <option value="1" {{ ($filters['done'] ?? '') === '1' ? 'selected' : '' }}>نعم</option>
                        <option value="0" {{ ($filters['done'] ?? '') === '0' ? 'selected' : '' }}>لا</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">مؤرشف؟</label>
                    <select name="is_archived" class="form-control select2">
                        <option value="">الكل</option>
                        <option value="1" {{ ($filters['is_archived'] ?? '') === '1' ? 'selected' : '' }}>نعم</option>
                        <option value="0" {{ ($filters['is_archived'] ?? '') === '0' ? 'selected' : '' }}>لا</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">الأخصائي</label>
                    <select name="specialist_id" class="form-control select2">
                        <option value="">الكل</option>
                        @foreach($specialists as $id => $name)
                            <option value="{{ $id }}" {{ (string)($filters['specialist_id'] ?? '') === (string)$id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">من تاريخ الإنشاء</label>
                    <input type="date" name="created_from" value="{{ $filters['created_from'] ?? '' }}" class="form-control" />
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">إلى تاريخ الإنشاء</label>
                    <input type="date" name="created_to" value="{{ $filters['created_to'] ?? '' }}" class="form-control" />
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">بحث</button>
                <a href="{{ route('admin.beneficiary-orders-reports.index') }}" class="btn btn-secondary">تفريغ</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @isset($orders)
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover datatable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>المستفيد</th>
                            <th>الخدمة</th>
                            <th>الحالة</th>
                            <th>القبول</th>
                            <th>الأخصائي</th>
                            <th>العنوان</th>
                            <th>منتهي</th>
                            <th>مؤرشف</th>
                            <th>تاريخ الإنشاء</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr>
                                <td>{{ $order->id }}</td>
                                <td>{{ $order->beneficiary->user->name ?? '-' }}</td>
                                <td>{{ $order->service->title ?? '-' }}</td>
                                <td>{{ $order->status->name ?? '-' }}</td>
                                <td>{{ \App\Models\BeneficiaryOrder::ACCEPT_STATUS_RADIO[$order->accept_status] ?? '-' }}</td>
                                <td>{{ $order->specialist->name ?? '-' }}</td>
                                <td>{{ $order->title }}</td>
                                <td>{{ $order->done ? 'نعم' : 'لا' }}</td>
                                <td>{{ $order->is_archived ? 'نعم' : 'لا' }}</td>
                                <td>{{ $order->created_at?->format('Y-m-d') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center">لا توجد نتائج</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div>
                {{ $orders->links() }}
            </div>
        @endisset
    </div>
</div>



@endsection