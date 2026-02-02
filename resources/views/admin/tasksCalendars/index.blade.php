@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.taskManagement.title'), 'url' => '#'],
            [
                'title' => trans('cruds.tasksCalendar.title'),
                'url' => route('admin.tasks-calendars.index'),
            ],
        ];
    @endphp
    @include('partials.breadcrumb')

    <div class="row"> 
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">Full Calendar</div>
                </div>
                <div class="card-body">
                    <div id='calendar2'></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @parent

    <!-- Moment JS -->
    <script src="{{ asset('assets/libs/moment/moment.js') }}"></script>

    <!-- Fullcalendar JS -->
    <script src="{{ asset('assets/libs/fullcalendar/index.global.min.js') }}"></script>
    <script>
        (function() {
            "use strict";
            //_____Calendar Events Intialization

            var events = [
                { 
                    events: [
                        @foreach($events as $event)
                            @if($event->due_date)
                                {
                                    title : '{{ $event->name }}',
                                    start : '{{ \Carbon\Carbon::createFromFormat(config('panel.date_format'),$event->due_date)->format('Y-m-d') }}',
                                    url : '{{ url('admin/tasks').'/'.$event->id }}',
                                    className: "bg-{{ $event->status->badge_class ?? 'secondary' }}",
                                },
                            @endif
                        @endforeach
                    ]
                }
            ]; 

            //________ FullCalendar 
            
            var calendarEl = document.getElementById('calendar2');

            var calendar = new FullCalendar.Calendar(calendarEl, {
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
                },
                defaultView: 'month',
                navLinks: true, // can click day/week names to navigate views 
                selectMirror: true,
                droppable: true, // this allows things to be dropped onto the calendar  
                dayMaxEvents: true, // allow "more" link when too many events
                eventSources: events,

            });
            calendar.render();

            // for activity scroll
            var myElement1 = document.getElementById('full-calendar-activity');
            new SimpleBar(myElement1, {
                autoHide: true
            });

        })();
    </script>
@endsection
