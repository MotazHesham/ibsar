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
        --vol-primary-light: #dbeafe;
        --vol-success: #059669;
        --vol-success-light: #d1fae5;
        --vol-warning: #d97706;
        --vol-warning-light: #fef3c7;
        --vol-muted: #64748b;
        --vol-border: #e2e8f0;
        --vol-shadow: 0 1px 3px rgba(0,0,0,.06);
        --vol-shadow-lg: 0 10px 40px -10px rgba(0,0,0,.12);
    }
    body.volunteer-dashboard { background: var(--vol-bg); min-height: 100vh; }
    .vol-header {
        background: var(--vol-card);
        box-shadow: var(--vol-shadow);
        padding: 1rem 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1rem;
    }
    .vol-header-brand { display: flex; align-items: center; gap: .75rem; text-decoration: none; color: #1e293b; font-weight: 600; }
    .vol-header-brand img { height: 40px; }
    .vol-header-user { display: flex; align-items: center; gap: 1rem; }
    .vol-header-user .name { color: var(--vol-muted); font-size: .9rem; }
    .vol-btn-logout {
        padding: .4rem .9rem;
        border-radius: 8px;
        border: 1px solid var(--vol-border);
        background: var(--vol-card);
        color: var(--vol-muted);
        font-size: .875rem;
        transition: all .2s;
    }
    .vol-btn-logout:hover { background: #f8fafc; color: #475569; }
    .vol-main { max-width: 1200px; margin: 0 auto; padding: 1.5rem; }
    .vol-page-title { font-size: 1.5rem; font-weight: 700; color: #1e293b; margin-bottom: 1.5rem; }
    .vol-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 1rem; margin-bottom: 1.5rem; }
    .vol-stat-card {
        background: var(--vol-card);
        border-radius: 12px;
        padding: 1rem 1.25rem;
        box-shadow: var(--vol-shadow);
        border: 1px solid var(--vol-border);
        transition: transform .2s, box-shadow .2s;
    }
    .vol-stat-card:hover { transform: translateY(-2px); box-shadow: var(--vol-shadow-lg); }
    .vol-stat-card .number { font-size: 1.5rem; font-weight: 700; color: #1e293b; line-height: 1.2; }
    .vol-stat-card .label { font-size: .8rem; color: var(--vol-muted); margin-top: .25rem; }
    .vol-stat-card.stat-approved .number { color: var(--vol-primary); }
    .vol-stat-card.stat-progress .number { color: var(--vol-warning); }
    .vol-stat-card.stat-completed .number { color: var(--vol-success); }
    .vol-alert { border-radius: 10px; border: none; padding: .875rem 1rem; }
    .vol-tasks-grid { display: grid; gap: 1.25rem; }
    @media (min-width: 768px) { .vol-tasks-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (min-width: 992px) { .vol-tasks-grid { grid-template-columns: repeat(3, 1fr); } }
    .vol-task-card {
        background: var(--vol-card);
        border-radius: 14px;
        overflow: hidden;
        box-shadow: var(--vol-shadow);
        border: 1px solid var(--vol-border);
        transition: transform .2s, box-shadow .2s;
        display: flex;
        flex-direction: column;
    }
    .vol-task-card:hover { transform: translateY(-2px); box-shadow: var(--vol-shadow-lg); }
    .vol-task-card .card-accent { height: 4px; }
    .vol-task-card .card-accent.status-approved { background: linear-gradient(90deg, var(--vol-primary), #60a5fa); }
    .vol-task-card .card-accent.status-in_progress { background: linear-gradient(90deg, var(--vol-warning), #fbbf24); }
    .vol-task-card .card-accent.status-completed { background: linear-gradient(90deg, var(--vol-success), #34d399); }
    .vol-task-card .card-accent.status-pending { background: linear-gradient(90deg, var(--vol-muted), #94a3b8); }
    .vol-task-card .card-body { padding: 1.25rem; flex: 1; display: flex; flex-direction: column; }
    .vol-task-card .card-title {
        font-size: 1.05rem; font-weight: 600; color: #1e293b; margin-bottom: .5rem;
        display: flex; align-items: flex-start; justify-content: space-between; gap: .5rem;
    }
    .vol-task-card .card-title .title-text { flex: 1; }
    .vol-task-badge {
        font-size: .7rem; font-weight: 600; padding: .25rem .5rem; border-radius: 6px;
        white-space: nowrap;
    }
    .vol-task-badge.approved { background: var(--vol-primary-light); color: var(--vol-primary); }
    .vol-task-badge.in_progress { background: var(--vol-warning-light); color: #b45309; }
    .vol-task-badge.completed { background: var(--vol-success-light); color: var(--vol-success); }
    .vol-task-badge.pending { background: #f1f5f9; color: var(--vol-muted); }
    .vol-task-meta { font-size: .85rem; color: var(--vol-muted); margin-bottom: .5rem; display: flex; align-items: center; gap: .35rem; }
    .vol-task-meta i { font-size: 1rem; opacity: .9; }
    .vol-task-address { font-size: .8rem; color: #64748b; margin-bottom: 1rem; display: flex; align-items: flex-start; gap: .35rem; }
    .vol-task-address i { margin-top: 2px; flex-shrink: 0; }
    .vol-task-actions { margin-top: auto; display: flex; flex-wrap: wrap; gap: .5rem; }
    .vol-task-actions .btn {
        font-size: .8rem; padding: .4rem .65rem; border-radius: 8px;
        font-weight: 500; display: inline-flex; align-items: center; gap: .3rem;
    }
    .vol-empty {
        background: var(--vol-card);
        border-radius: 14px;
        padding: 3rem 2rem;
        text-align: center;
        border: 1px dashed var(--vol-border);
    }
    .vol-empty-icon { font-size: 4rem; color: var(--vol-border); margin-bottom: 1rem; }
    .vol-empty-title { font-weight: 600; color: #1e293b; margin-bottom: .35rem; }
    .vol-empty-text { color: var(--vol-muted); font-size: .9rem; }
    .vol-modal .modal-content { border-radius: 14px; border: none; box-shadow: var(--vol-shadow-lg); }
    .vol-modal .modal-header { border-bottom: 1px solid var(--vol-border); padding: 1.25rem 1.5rem; }
    .vol-modal .modal-title { font-weight: 600; color: #1e293b; }
    .vol-modal .modal-body { padding: 1.5rem; }
    .vol-modal .modal-footer { border-top: 1px solid var(--vol-border); padding: 1rem 1.5rem; gap: .5rem; }
    .vol-modal .form-label { font-weight: 500; color: #334155; }
    .vol-modal .form-control, .vol-modal .form-select { border-radius: 8px; border-color: var(--vol-border); }
    .vol-qr-wrap { background: #f8fafc; border-radius: 12px; padding: 1.5rem; display: inline-block; }
    .vol-qr-wrap img { display: block; border-radius: 8px; }
</style>
@endsection

@section('content')
    <header class="vol-header">
        <a href="{{ route('volunteer.dashboard') }}" class="vol-header-brand">
            @if(getSetting('site_logo'))
                <img src="{{ getSetting('site_logo') }}" alt="{{ getSetting('site_name') }}">
            @endif
            <span>{{ getSetting('site_name') }}</span>
        </a>
        <div class="vol-header-user">
            <span class="name">{{ auth('volunteer')->user()->name ?? '' }}</span>
            <form method="POST" action="{{ route('volunteer.logout') }}" class="d-inline">
                @csrf
                <button type="submit" class="vol-btn-logout">
                    <i class="ri-logout-box-r-line"></i> {{ trans('global.logout') }}
                </button>
            </form>
        </div>
    </header>

    <main class="vol-main">
        <h1 class="vol-page-title">{{ trans('frontend.volunteer.dashboard') }}</h1>

        @if (session('success'))
            <div class="alert alert-success vol-alert d-flex align-items-center gap-2">
                <i class="ri-checkbox-circle-fill"></i> {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger vol-alert d-flex align-items-center gap-2">
                <i class="ri-error-warning-fill"></i> {{ session('error') }}
            </div>
        @endif

        @if($tasks->isEmpty())
            <div class="vol-empty">
                <div class="vol-empty-icon"><i class="ri-task-line"></i></div>
                <div class="vol-empty-title">{{ trans('frontend.volunteer.no_tasks') }}</div>
                <div class="vol-empty-text">{{ app()->getLocale() == 'ar' ? 'سيتم إضافة المهام هنا بعد تعيينها لك.' : 'Tasks will appear here once assigned to you.' }}</div>
            </div>
        @else
            @php
                $countApproved = $tasks->where('status', 'approved')->count();
                $countProgress = $tasks->where('status', 'in_progress')->count();
                $countCompleted = $tasks->where('status', 'completed')->count();
            @endphp
            <div class="vol-stats">
                <div class="vol-stat-card stat-approved">
                    <div class="number">{{ $countApproved }}</div>
                    <div class="label">{{ \App\Models\VolunteerTask::STATUS_SELECT['approved'] ?? 'Approved' }}</div>
                </div>
                <div class="vol-stat-card stat-progress">
                    <div class="number">{{ $countProgress }}</div>
                    <div class="label">{{ \App\Models\VolunteerTask::STATUS_SELECT['in_progress'] ?? 'In progress' }}</div>
                </div>
                <div class="vol-stat-card stat-completed">
                    <div class="number">{{ $countCompleted }}</div>
                    <div class="label">{{ \App\Models\VolunteerTask::STATUS_SELECT['completed'] ?? 'Completed' }}</div>
                </div>
            </div>

            <div class="vol-tasks-grid">
                @foreach($tasks as $task)
                    <article class="vol-task-card">
                        <div class="card-accent status-{{ $task->status }}"></div>
                        <div class="card-body">
                            <h2 class="card-title">
                                <span class="title-text">{{ $task->name }}</span>
                                <span class="vol-task-badge {{ $task->status }}">{{ \App\Models\VolunteerTask::STATUS_SELECT[$task->status] ?? $task->status }}</span>
                            </h2>
                            <p class="vol-task-meta">
                                <i class="ri-calendar-line"></i>
                                {{ $task->date }} @if($task->arrive_time) · {{ \Carbon\Carbon::parse($task->arrive_time)->format('H:i') }} @endif
                            </p>
                            @if($task->address)
                                <p class="vol-task-address"><i class="ri-map-pin-line"></i> {{ Str::limit($task->address, 55) }}</p>
                            @endif
                            <div class="vol-task-actions">
                                <a href="{{ route('volunteer.tasks.show', $task) }}" class="btn btn-outline-primary">
                                    <i class="ri-eye-line"></i> {{ trans('global.view') }}
                                </a>
                                @if(in_array($task->status, ['approved', 'in_progress']))
                                    <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#qrModal-{{ $task->id }}">
                                        <i class="ri-qr-code-line"></i> {{ trans('frontend.volunteer.view_qr') }}
                                    </button>
                                    @if($task->status === 'approved')
                                        <form action="{{ route('volunteer.tasks.start', $task) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-success">
                                                <i class="ri-play-circle-line"></i> {{ trans('frontend.volunteer.start_task') }}
                                            </button>
                                        </form>
                                    @endif
                                    @if($task->status === 'in_progress')
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#finishModal-{{ $task->id }}">
                                            <i class="ri-check-double-line"></i> {{ trans('frontend.volunteer.finish_task') }}
                                        </button>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </article>

                    <div class="modal fade vol-modal" id="qrModal-{{ $task->id }}" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title"><i class="ri-qr-code-line me-2"></i>{{ trans('frontend.volunteer.view_qr') }} — {{ $task->name }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body text-center">
                                    @php
                                        $verifyUrl = URL::temporarySignedRoute('volunteer.task-verify', now()->addDays(7), ['task' => $task]);
                                    @endphp
                                    <div class="vol-qr-wrap">
                                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=220x220&data={{ urlencode($verifyUrl) }}" alt="QR" width="220" height="220">
                                    </div>
                                    <p class="small text-muted mt-3 mb-0">{{ trans('frontend.volunteer.task') }} #{{ $task->id }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade vol-modal" id="finishModal-{{ $task->id }}" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <form action="{{ route('volunteer.tasks.finish', $task) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="modal-header">
                                        <h5 class="modal-title"><i class="ri-check-double-line me-2"></i>{{ trans('frontend.volunteer.finish_task') }} — {{ $task->name }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                @endforeach
            </div>
        @endif
    </main>
@endsection
