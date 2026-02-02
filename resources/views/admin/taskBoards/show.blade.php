@extends('layouts.master')
@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/libs/dragula/dragula.min.css') }}">
    <style>
        .TASK-kanban-board .card.custom-card:last-child {
            margin-bottom: 0;
        }

        .TASK-kanban-board .kanban-tasks {
            position: relative;
            max-height: 35rem;
        }

        .TASK-kanban-board .kanban-tasks .simplebar-content {
            padding-inline-end: 1rem !important;
        }

    </style>
@endsection
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.generalSetting.title'), 'url' => '#'],
            [
                'title' => trans('global.list') . ' ' . trans('cruds.taskBoard.title'),
                'url' => route('admin.task-boards.index'),
            ],
            [
                'title' => $taskBoard->name,
                'url' => '#',
            ],
        ];
        $pageTitle = $taskBoard->name;
    @endphp
    @include('partials.breadcrumb')

    <!-- Start:: row-1 -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div class="d-flex gap-4 align-items-center flex-wrap">
                            <div class="avatar-list-stacked">
                                @php
                                    $usersOnBoard = $taskBoard->usersOnBoard()->get();
                                @endphp 

                                @foreach ($usersOnBoard->take(8) as $user)
                                    @include('utilities.user-avatar', ['user' => $user])
                                @endforeach

                                @if($usersOnBoard->count() > 8)
                                    <button class="avatar avatar-sm bg-primary avatar-rounded text-fixed-white"
                                        href="javascript:void(0);"
                                        data-bs-toggle="popover"
                                        data-bs-placement="top"
                                        data-bs-html="true"
                                        data-bs-content="
                                            <div class='d-flex flex-column gap-2'>
                                                @foreach($usersOnBoard as $user)
                                                    <div class='d-flex align-items-center gap-2'> 
                                                        <a href='{{ route('admin.users.show', $user->id) }}'>{{ $user->name }}</a>
                                                    </div>
                                                @endforeach
                                            </div>
                                        ">
                                        +{{ $usersOnBoard->count() - 8 }}
                                    </button>
                                @endif
                            </div>
                            <div class="d-flex gap-2 kanban-board flex-wrap">
                                <button class="btn btn-primary btn-w-lg" data-bs-toggle="modal"
                                    data-bs-target="#add-board"><i class="ri-add-line me-1 fw-medium align-middle"></i>
                                    {{ trans('global.create') }} {{ trans('cruds.taskBoard.title_singular') }}
                                </button>
                            </div>
                        </div> 
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End:: row-1 -->

    <!-- Start::row-2 -->
    <div class="TASK-kanban-board">
        @foreach ($taskStatuses as $taskStatus)
            @php
                $tasks = $groupedTasks[$taskStatus->id] ?? [];
                $tasks = collect($tasks);
            @endphp
            <div class="kanban-tasks-type task-status-{{ $taskStatus->id }}">
                <div class="pe-3 mb-3">
                    <div
                        class="d-flex justify-content-between align-items-center px-3 py-2 bg-{{ $taskStatus->badge_class }}-transparent border border-{{ $taskStatus->badge_class }} border-opacity-25 rounded">
                        <span class="d-block fw-medium fs-15">{{ $taskStatus->name }}</span>
                    </div>
                </div>
                <div class="kanban-tasks" id="task-status-{{ $taskStatus->id }}-tasks">
                    <div id="task-status-{{ $taskStatus->id }}-tasks-draggable"
                        data-view-btn="task-status-{{ $taskStatus->id }}-tasks" data-status-id="{{ $taskStatus->id }}">
                        @foreach ($tasks as $task)
                            @include('admin.taskBoards.partials.task-card', [
                                'task' => $task,
                                'taskStatus' => $taskStatus,
                            ])
                        @endforeach
                    </div>
                </div>
                <div class="d-flex view-more-button mt-3 gap-2 align-items-center"> 
                    <a aria-label="anchor" href="javascript:void(0);"
                        class="btn btn-{{ $taskStatus->badge_class }}-light border btn-wave flex-fill" onclick="addTask({{ $taskStatus->id }})">
                        <i class="ri-add-line align-middle me-1 fw-medium"></i>
                        {{ trans('global.create') }} {{ trans('cruds.task.title_singular') }}
                    </a>
                </div>
            </div>
        @endforeach
    </div>
    <!--End::row-2 -->

    <!-- Start::add board modal -->
    <div class="modal fade" id="add-board" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.task-boards.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h6 class="modal-title">{{ trans('global.create') }} {{ trans('cruds.taskBoard.title_singular') }}
                        </h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @include('utilities.form.text', [
                            'name' => 'name',
                            'label' => 'cruds.taskBoard.fields.name',
                            'isRequired' => true,
                        ])
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light"
                            data-bs-dismiss="modal">{{ trans('global.cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ trans('global.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- End::add board modal -->

    <!-- Start::add task modal -->
    <div class="modal fade" id="add-task" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                
            </div>
        </div>
    </div>
    <!-- End::add task modal -->

    <!-- Start::edit task modal -->
    <div class="modal fade" id="edit-task" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">

            </div>
        </div>
    </div>
    <!-- End::edit task modal -->
@endsection

@section('scripts')
    <!-- Dragula JS -->
    <script src="{{ asset('assets/libs/dragula/dragula.min.js') }}"></script>
    <script>
        function addTask(statusId) { 
            $.ajax({
                url: '{{ route('admin.tasks.create') }}',
                method: 'GET',
                data: {
                    status_id: statusId,
                    task_board_id: '{{ $taskBoard->id }}',
                },
                success: function(response) {
                    $('#add-task').modal('show');
                    $('#add-task .modal-content').html(response);
                }
            });
        }

        function editTask(taskId) {
            $.ajax({
                url: '{{ route('admin.tasks.edit', ':id') }}'.replace(':id', taskId),
                method: 'GET',
                success: function(response) {
                    $('#edit-task .modal-content').html(response);
                }
            });
            $('#edit-task').modal('show');
        }

        (function() {
            "use strict"

            const drake = dragula([
                @foreach ($taskStatuses as $taskStatus)
                    document.querySelector('#task-status-{{ $taskStatus->id }}-tasks-draggable'),
                @endforeach
            ], {
                moves: function(el, container, handle) {
                    return true; // Allow dragging from any element
                }
            });

            // Handle when a task is dropped
            drake.on('drop', function(el, target, source, sibling) {
                const taskId = el.getAttribute('data-task-id');
                const newStatusId = target.getAttribute('data-status-id');

                // Get all tasks in the new status container
                const tasks = Array.from(target.children);
                const orderData = tasks.map((task, index) => ({
                    id: task.getAttribute('data-task-id'),
                    order: tasks.length -
                        index // Reverse the order so first item has highest number
                }));

                // Make AJAX call to update task status and order
                $.ajax({
                    url: '{{ route('admin.tasks.update-status') }}',
                    method: 'POST',
                    data: {
                        task_id: taskId,
                        status_id: newStatusId,
                        order_data: orderData,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        console.log('Task status and order updated successfully');
                    },
                    error: function(xhr) {
                        showToast('Failed to update task status and order', 'error');
                        // Revert the drag if there's an error
                        drake.cancel(true);
                    }
                });
            });

            @foreach ($taskStatuses as $taskStatus)
                var myElement = document.getElementById('task-status-{{ $taskStatus->id }}-tasks');
                new SimpleBar(myElement, {
                    autoHide: true
                });
            @endforeach

            document.addEventListener("DOMContentLoaded", () => {
                setInterval(() => {
                    let i = [
                        @foreach ($taskStatuses as $taskStatus)
                            document.querySelector(
                                    '#task-status-{{ $taskStatus->id }}-tasks-draggable'),
                        @endforeach
                    ]
                    i.map((ele) => {
                        if (ele) {
                            if (ele.children.length == 0) {
                                ele.classList.add("task-Null")
                            }
                            if (ele.children.length != 0) {
                                ele.classList.remove("task-Null")
                            }
                        }
                    })
                }, 100);
            })
        })();
    </script>
@endsection
