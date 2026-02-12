@extends('layouts.custom-master')

@php
    $bodyClass = 'volunteer-dashboard';
@endphp

@section('styles')
<link href="{{ asset('assets/icon-fonts/icons.css') }}" rel="stylesheet">
<style>
    :root {
        --vol-bg: #f0f4f8;
        --vol-card: #ffffff;
        --vol-primary: #2563eb;
        --vol-success: #059669;
        --vol-warning: #d97706;
        --vol-muted: #64748b;
        --vol-border: #e2e8f0;
        --vol-shadow: 0 1px 3px rgba(0,0,0,.06);
        --vol-shadow-lg: 0 10px 40px -10px rgba(0,0,0,.12);
    }
    body.volunteer-dashboard { background: var(--vol-bg); min-height: 100vh; }
    .vol-header {
        background: var(--vol-card); box-shadow: var(--vol-shadow);
        padding: 1rem 1.5rem;
        display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;
    }
    .vol-header-brand { display: flex; align-items: center; gap: .75rem; text-decoration: none; color: #1e293b; font-weight: 600; }
    .vol-header-brand img { height: 40px; }
    .vol-btn-logout {
        padding: .4rem .9rem; border-radius: 8px; border: 1px solid var(--vol-border);
        background: var(--vol-card); color: var(--vol-muted); font-size: .875rem;
    }
    .vol-btn-logout:hover { background: #f8fafc; color: #475569; }
    .vol-main { max-width: 800px; margin: 0 auto; padding: 1.5rem; }
    .vol-back { display: inline-flex; align-items: center; gap: .35rem; color: var(--vol-muted); text-decoration: none; font-size: .9rem; margin-bottom: 1.25rem; }
    .vol-back:hover { color: var(--vol-primary); }
    .vol-detail-card {
        background: var(--vol-card); border-radius: 14px; overflow: hidden;
        box-shadow: var(--vol-shadow); border: 1px solid var(--vol-border); margin-bottom: 1.5rem;
    }
    .vol-detail-card .card-header {
        padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--vol-border);
        display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: .75rem;
    }
    .vol-detail-card .card-header h1 { font-size: 1.25rem; font-weight: 600; margin: 0; color: #1e293b; }
    .vol-task-badge {
        font-size: .75rem; font-weight: 600; padding: .3rem .6rem; border-radius: 8px;
    }
    .vol-task-badge.approved { background: #dbeafe; color: var(--vol-primary); }
    .vol-task-badge.in_progress { background: #fef3c7; color: #b45309; }
    .vol-task-badge.completed { background: #d1fae5; color: var(--vol-success); }
    .vol-detail-card .card-body { padding: 1.5rem; }
    .vol-detail-row { display: flex; padding: .6rem 0; border-bottom: 1px solid #f1f5f9; }
    .vol-detail-row:last-child { border-bottom: none; }
    .vol-detail-dt { width: 140px; flex-shrink: 0; font-size: .875rem; color: var(--vol-muted); }
    .vol-detail-dd { font-size: .875rem; color: #334155; margin: 0; }
    .vol-detail-report { background: #f8fafc; border-radius: 10px; padding: 1rem; margin-top: .5rem; white-space: pre-wrap; font-size: .9rem; }
    .vol-files-list { list-style: none; padding: 0; margin: .5rem 0 0; }
    .vol-files-list li { padding: .4rem 0; }
    .vol-files-list a { color: var(--vol-primary); text-decoration: none; display: inline-flex; align-items: center; gap: .35rem; }
    .vol-files-list a:hover { text-decoration: underline; }
    .vol-actions { display: flex; flex-wrap: wrap; gap: .5rem; }
    .vol-actions .btn { font-size: .875rem; padding: .5rem .85rem; border-radius: 8px; display: inline-flex; align-items: center; gap: .35rem; }
    .vol-modal .modal-content { border-radius: 14px; border: none; box-shadow: var(--vol-shadow-lg); }
    .vol-modal .modal-header { border-bottom: 1px solid var(--vol-border); padding: 1.25rem 1.5rem; }
    .vol-modal .modal-body { padding: 1.5rem; }
    .vol-modal .modal-footer { border-top: 1px solid var(--vol-border); padding: 1rem 1.5rem; }
    .vol-modal .form-label { font-weight: 500; }
    .vol-modal .form-control { border-radius: 8px; border-color: var(--vol-border); }
    .vol-qr-wrap { background: #f8fafc; border-radius: 12px; padding: 1.5rem; display: inline-block; }
</style>
@endsection

@section('content')
    <header class="vol-header">
        <a href="{{ route('volunteer.dashboard') }}" class="vol-header-brand">
            @if(getSetting('site_logo')) <img src="{{ getSetting('site_logo') }}" alt="{{ getSetting('site_name') }}"> @endif
            <span>{{ getSetting('site_name') }}</span>
        </a>
        <form method="POST" action="{{ route('volunteer.logout') }}" class="d-inline">
            @csrf
            <button type="submit" class="vol-btn-logout"><i class="ri-logout-box-r-line"></i> {{ trans('global.logout') }}</button>
        </form>
    </header>

    <main class="vol-main">
        <a href="{{ route('volunteer.dashboard') }}" class="vol-back">
            <i class="ri-arrow-left-s-line"></i> {{ trans('frontend.volunteer.dashboard') }}
        </a>

        @if (session('success'))
            <div class="alert alert-success d-flex align-items-center gap-2 mb-3" style="border-radius:10px;border:none;">
                <i class="ri-checkbox-circle-fill"></i> {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger d-flex align-items-center gap-2 mb-3" style="border-radius:10px;border:none;">
                <i class="ri-error-warning-fill"></i> {{ session('error') }}
            </div>
        @endif

        <div class="vol-detail-card">
            <div class="card-header">
                <h1>{{ $task->name }}</h1>
                <span class="vol-task-badge {{ $task->status }}">{{ \App\Models\VolunteerTask::STATUS_SELECT[$task->status] ?? $task->status }}</span>
            </div>
            <div class="card-body">
                <div class="vol-detail-row">
                    <dt class="vol-detail-dt">{{ trans('frontend.volunteer.task') }}</dt>
                    <dd class="vol-detail-dd">#{{ $task->id }}</dd>
                </div>
                <div class="vol-detail-row">
                    <dt class="vol-detail-dt">{{ trans('global.date') ?? 'Date' }}</dt>
                    <dd class="vol-detail-dd">{{ $task->date }}</dd>
                </div>
                @if($task->arrive_time)
                    <div class="vol-detail-row">
                        <dt class="vol-detail-dt">{{ trans('frontend.volunteer.started_at') }}</dt>
                        <dd class="vol-detail-dd">{{ \Carbon\Carbon::parse($task->arrive_time)->format('H:i') }}</dd>
                    </div>
                @endif
                @if($task->leave_time)
                    <div class="vol-detail-row">
                        <dt class="vol-detail-dt">{{ trans('frontend.volunteer.finished_at') }}</dt>
                        <dd class="vol-detail-dd">{{ \Carbon\Carbon::parse($task->leave_time)->format('H:i') }}</dd>
                    </div>
                @endif
                <div class="vol-detail-row">
                    <dt class="vol-detail-dt">{{ trans('global.address') }}</dt>
                    <dd class="vol-detail-dd">{{ $task->address }}</dd>
                </div>
                <div class="vol-detail-row">
                    <dt class="vol-detail-dt">{{ trans('global.phone') }}</dt>
                    <dd class="vol-detail-dd">{{ $task->phone }}</dd>
                </div>
                @if($task->visit_type)
                    <div class="vol-detail-row">
                        <dt class="vol-detail-dt">{{ trans('global.visit_type') }}</dt>
                        <dd class="vol-detail-dd">{{ $task->visit_type }}</dd>
                    </div>
                @endif
                @if($task->details)
                    <div class="vol-detail-row">
                        <dt class="vol-detail-dt">{{ trans('global.details') ?? 'Details' }}</dt>
                        <dd class="vol-detail-dd">{{ $task->details }}</dd>
                    </div>
                @endif
                @if($task->started_at)
                    <div class="vol-detail-row">
                        <dt class="vol-detail-dt">{{ trans('frontend.volunteer.started_at') }}</dt>
                        <dd class="vol-detail-dd">{{ \Carbon\Carbon::parse($task->started_at)->format('Y-m-d H:i') }}</dd>
                    </div>
                @endif
                @if($task->finished_at)
                    <div class="vol-detail-row">
                        <dt class="vol-detail-dt">{{ trans('frontend.volunteer.finished_at') }}</dt>
                        <dd class="vol-detail-dd">{{ \Carbon\Carbon::parse($task->finished_at)->format('Y-m-d H:i') }}</dd>
                    </div>
                @endif
                @if($task->report)
                    <div class="vol-detail-row">
                        <dt class="vol-detail-dt">{{ trans('frontend.volunteer.report') }}</dt>
                        <dd class="vol-detail-dd"><div class="vol-detail-report">{{ $task->report }}</div></dd>
                    </div>
                @endif
                @if($task->getMedia('report_files')->count() > 0)
                    <div class="vol-detail-row">
                        <dt class="vol-detail-dt">{{ trans('frontend.volunteer.attach_files') }}</dt>
                        <dd class="vol-detail-dd">
                            <ul class="vol-files-list">
                                @foreach($task->getMedia('report_files') as $media)
                                    <li><a href="{{ $media->getUrl() }}" target="_blank"><i class="ri-file-line"></i> {{ $media->file_name }}</a></li>
                                @endforeach
                            </ul>
                        </dd>
                    </div>
                @endif
            </div>
        </div>

        <div class="vol-actions">
            <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#qrModal">
                <i class="ri-qr-code-line"></i> {{ trans('frontend.volunteer.view_qr') }}
            </button>
            @if($task->status === 'pending')
                <form action="{{ route('volunteer.tasks.start', $task) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success"><i class="ri-play-circle-line"></i> {{ trans('frontend.volunteer.start_task') }}</button>
                </form>
            @endif
            @if($task->status === 'in_progress')
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#finishModal">
                    <i class="ri-check-double-line"></i> {{ trans('frontend.volunteer.finish_task') }}
                </button>
            @endif
        </div>
    </main>

    <div class="modal fade vol-modal" id="qrModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="ri-qr-code-line me-2"></i>{{ trans('frontend.volunteer.view_qr') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    @php $verifyUrl = URL::temporarySignedRoute('volunteer.task-verify', now()->addDays(7), ['task' => $task]); @endphp
                    <div class="vol-qr-wrap">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=220x220&data={{ urlencode($verifyUrl) }}" alt="QR" width="220" height="220">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade vol-modal" id="finishModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('volunteer.tasks.finish', $task) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="ri-check-double-line me-2"></i>{{ trans('frontend.volunteer.finish_task') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-4">
                            <label class="form-label">{{ trans('frontend.volunteer.report') }}</label>
                            <textarea name="report" class="form-control" rows="4" placeholder="{{ trans('frontend.volunteer.report_placeholder') }}">{{ old('report') }}</textarea>
                        </div>
                        <div>
                            <label class="form-label">{{ trans('frontend.volunteer.attach_files') }}</label>
                            <input type="file" name="report_files[]" class="form-control" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ trans('global.cancel') }}</button>
                        <button type="submit" class="btn btn-primary"><i class="ri-send-plane-line me-1"></i>{{ trans('frontend.volunteer.finish_task') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
@endsection
