@extends('layouts.master-beneficiary')
@section('content')
    @php
        $page_title = 'الإشعارات';
    @endphp

    <div class="card custom-card">
        <div class="card-header">
            <div class="card-title mb-0">إشعاراتي</div>
        </div>
        <div class="card-body p-0">
            @if ($alerts->isEmpty())
                <div class="p-4 text-center text-muted">لا توجد إشعارات</div>
            @else
                <ul class="list-group list-group-flush">
                    @foreach ($alerts as $alert)
                        <li class="list-group-item">
                            <p class="mb-1">{{ $alert->alert_text }}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted small">{{ $alert->created_at?->format('Y-m-d H:i') }}</span>
                                @if ($alert->alert_link)
                                    <a href="{{ $alert->alert_link }}" class="btn btn-sm btn-primary">عرض الطلب</a>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
@endsection
