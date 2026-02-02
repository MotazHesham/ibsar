@extends('layouts.master')
@section('content')

<div class="card mt-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>{{ trans('cruds.serviceLoansReport.title') }}</span>
        @if(!empty($loans) && $loans->total() > 0)
            <a href="{{ route('admin.service-loans-reports.export', request()->all()) }}" class="btn btn-success btn-sm">
                {{ trans('global.export') }}
            </a>
        @endif
    </div>

    <div class="card-body">
        <form method="GET" action="{{ route('admin.service-loans-reports.index') }}" class="mb-4">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label">بحث عام (اسم/ايميل/رقم هوية/اسم مجموعة/رقم)</label>
                    <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" class="form-control" placeholder="مثال: 42 أو أحمد" />
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">حالة القرض</label>
                    <select name="status" class="form-control select2">
                        <option value="">الكل</option>
                        @foreach($statusOptions as $key => $label)
                            <option value="{{ $key }}" {{ ($filters['status'] ?? '') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">فلتر المدفوعات</label>
                    <select name="payment_filter" class="form-control select2">
                        <option value="">الكل</option>
                        @foreach($paymentFilterOptions as $key => $label)
                            <option value="{{ $key }}" {{ ($filters['payment_filter'] ?? '') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">من تاريخ الأقساط</label>
                    <input type="date" name="installment_date_from" value="{{ $filters['installment_date_from'] ?? '' }}" class="form-control" />
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">إلى تاريخ الأقساط</label>
                    <input type="date" name="installment_date_to" value="{{ $filters['installment_date_to'] ?? '' }}" class="form-control" />
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
                <a href="{{ route('admin.service-loans-reports.index') }}" class="btn btn-secondary">تفريغ</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @isset($loans)
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover datatable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>اسم المجموعة</th>
                            <th>المستفيد</th>
                            <th>رقم الهوية</th>
                            <th>مبلغ القرض</th>
                            <th>القسط الشهري</th>
                            <th>عدد الأشهر</th>
                            <th>المبلغ المدفوع</th>
                            <th>المبلغ المتبقي</th>
                            <th>أقساط متأخرة</th>
                            <th>أقساط غير مدفوعة</th>
                            <th>حالة القرض</th>
                            <th>تاريخ الإنشاء</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($loans as $loan)
                            @php
                                $beneficiary = $loan->beneficiary_order->beneficiary ?? null;
                                $user = $beneficiary->user ?? null;
                                $totalPaid = $loan->payments->where('payment_status', 'paid')->sum('amount');
                                $remainingAmount = $loan->amount - $totalPaid;
                                
                                $overdueCount = $loan->installments->filter(function ($installment) {
                                    // Get raw date from database (format should be Y-m-d)
                                    $installmentDate = $installment->getAttributes()['installment_date'] ?? null;
                                    if (!$installmentDate) {
                                        return false;
                                    }
                                    // Parse the date - it should already be in Y-m-d format from database
                                    try {
                                        $date = \Carbon\Carbon::parse($installmentDate)->format('Y-m-d');
                                        return $date < now()->toDateString() && $installment->payment_status !== 'paid';
                                    } catch (\Exception $e) {
                                        return false;
                                    }
                                })->count();
                                
                                $unpaidCount = $loan->installments->where('payment_status', 'pending')->count();
                            @endphp
                            <tr>
                                <td>{{ $loan->id }}</td>
                                <td>{{ $loan->group_name ?? '-' }}</td>
                                <td>{{ $user->name ?? '-' }}</td>
                                <td>{{ $user->identity_num ?? '-' }}</td>
                                <td>{{ number_format($loan->amount ?? 0, 2) }}</td>
                                <td>{{ number_format($loan->installment ?? 0, 2) }}</td>
                                <td>{{ $loan->months ?? 0 }}</td>
                                <td>{{ number_format($totalPaid, 2) }}</td>
                                <td>{{ number_format($remainingAmount, 2) }}</td>
                                <td>
                                    <span class="badge bg-danger">{{ $overdueCount }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-warning">{{ $unpaidCount }}</span>
                                </td>
                                <td>{{ \App\Models\ServiceLoan::STATUS_SELECT[$loan->status] ?? $loan->status }}</td>
                                <td>{{ $loan->created_at?->format('Y-m-d') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="13" class="text-center">لا توجد نتائج</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div>
                {{ $loans->links() }}
            </div>
        @endisset
    </div>
</div>

@endsection

