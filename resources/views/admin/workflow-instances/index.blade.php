@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => 'سير العمل (المحرك الجديد)', 'url' => '#'],
            ['title' => trans('global.list'), 'url' => '#'],
        ];
        $pageTitle = 'سير العمل - قائمة الحالات';
    @endphp
    @include('partials.breadcrumb', ['buttons' => [
        ['title' => 'بدء سير عمل جديد', 'url' => route('admin.workflow-instances.create'), 'class' => 'btn-primary'],
    ]])

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>السير</th>
                                    <th>الجهة المرتبطة</th>
                                    <th>الخطوة الحالية</th>
                                    <th>الحالة</th>
                                    <th>تاريخ البدء</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($instances as $instance)
                                    <tr>
                                        <td>{{ $instance->id }}</td>
                                        <td>{{ $instance->workflow->name ?? '-' }}</td>
                                        <td>{{ class_basename($instance->entity_type) }} #{{ $instance->entity_id }}</td>
                                        <td>{{ $instance->currentStep->name ?? '-' }}</td>
                                        <td>
                                            @if($instance->status === 'running')
                                                <span class="badge bg-success">قيد التشغيل</span>
                                            @elseif($instance->status === 'on_hold')
                                                <span class="badge bg-warning">في الانتظار</span>
                                            @elseif($instance->status === 'completed')
                                                <span class="badge bg-secondary">مكتمل</span>
                                            @elseif($instance->status === 'cancelled')
                                                <span class="badge bg-danger">ملغى</span>
                                            @else
                                                <span class="badge bg-light text-dark">{{ $instance->status }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $instance->created_at?->format('Y-m-d H:i') }}</td>
                                        <td>
                                            <a href="{{ route('admin.workflow-instances.show', $instance) }}" class="btn btn-sm btn-outline-primary">عرض</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">لا توجد حالات.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $instances->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
