<aside class="app-sidebar sticky" id="sidebar">

    <!-- Start::main-sidebar-header -->
    <div class="main-sidebar-header">
        <a href="#" class="header-logo">
            <img src="{{ getSetting('site_logo') }}" alt="logo" class="img-fluid">
        </a>
    </div>
    <!-- End::main-sidebar-header -->

    <!-- Start::main-sidebar -->
    <div class="main-sidebar" id="sidebar-scroll">

        <!-- Start::nav -->
        <nav class="main-menu-container nav nav-pills flex-column sub-open">
            <div class="slide-left" id="slide-left">
                <svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24"
                    viewBox="0 0 24 24">
                    <path d="M13.293 6.293 7.586 12l5.707 5.707 1.414-1.414L10.414 12l4.293-4.293z"></path>
                </svg>
            </div>
            <ul class="main-menu">
                <!-- Start::slide -->
                <li class="slide">
                    <a href="{{ route('admin.home') }}" class="side-menu__item">
                        <i class="bi bi-house-door"></i>
                        <span class="side-menu__label">{{ trans('global.dashboard') }}</span>
                    </a>
                </li>
                <!-- End::slide -->
                <!-- Start::slide__category -->
                <li class="slide__category"><span
                        class="category-name">{{ trans('cruds.beneficiariesManagment.title') }}</span></li>
                <!-- End::slide__category -->

                @can('beneficiaries_managment_access')
                    @php
                        $beneficiaries_managment_routes = request()->is('admin/beneficiaries/*') ? true : false;
                    @endphp
                    <li class="slide has-sub {{ $beneficiaries_managment_routes ? 'open' : '' }}">
                        <a href="javascript:void(0);"
                            class="side-menu__item {{ $beneficiaries_managment_routes ? 'active' : '' }}">
                            <i class="ri-arrow-right-s-line side-menu__angle"></i>
                            <i class="bi bi-person-hearts"></i>
                            <span class="side-menu__label">{{ trans('cruds.beneficiariesManagment.title') }}</span>
                        </a>
                        <ul class="slide-menu child1">
                            <li class="slide side-menu__label1">
                                <a href="javascript:void(0)">{{ trans('cruds.beneficiariesManagment.title') }}</a>
                            </li>
                            @can('beneficiary_all_list_access')
                                <li class="slide">
                                    <a href="{{ route('admin.beneficiaries.index') }}?status=all"
                                        class="side-menu__item {{ request()->is('admin/beneficiaries/*') ? 'active' : '' }}">
                                        {{ trans('cruds.beneficiary.extra.all') }}
                                    </a>
                                </li>
                            @endcan
                            @can('beneficiary_access')
                                <li class="slide">
                                    <a href="{{ route('admin.beneficiaries.index') }}"
                                        class="side-menu__item {{ request()->is('admin/beneficiaries/*') ? 'active' : '' }}">
                                        {{ trans('cruds.beneficiary.extra.approved') }}
                                    </a>
                                </li>
                            @endcan
                            @can('beneficiary_un_completed_access')
                                <li class="slide">
                                    <a href="{{ route('admin.beneficiaries.index') }}?status=uncompleted"
                                        class="side-menu__item {{ request()->is('admin/beneficiaries/*') ? 'active' : '' }}">
                                        {{ trans('cruds.beneficiary.extra.uncompleted') }}
                                    </a>
                                </li>
                            @endcan
                            @can('beneficiary_refused_access')
                                <li class="slide">
                                    <a href="{{ route('admin.beneficiaries.index') }}?status=rejected"
                                        class="side-menu__item {{ request()->is('admin/beneficiaries/*') ? 'active' : '' }}">
                                        {{ trans('cruds.beneficiary.extra.rejected') }}
                                    </a>
                                </li>
                            @endcan
                            @can('beneficiary_pending_access')
                                <li class="slide">
                                    <a href="{{ route('admin.beneficiaries.index') }}?status=pending"
                                        class="side-menu__item {{ request()->is('admin/beneficiaries/*') ? 'active' : '' }}">
                                        {{ trans('cruds.beneficiary.extra.pending') }}
                                    </a>
                                </li>
                            @endcan
                            @can('beneficiary_archive_access')
                                <li class="slide">
                                    <a href="{{ route('admin.beneficiaries.index') }}?status=archived"
                                        class="side-menu__item {{ request()->is('admin/beneficiaries/*') ? 'active' : '' }}">
                                        {{ trans('cruds.beneficiary.extra.archived') }}
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>

                @endcan
                @can('beneficiary_orders_management_access')
                    @php
                        $beneficiary_orders_managment_routes = request()->is('admin/beneficiary-orders/*')
                            ? true
                            : false;
                    @endphp
                    <li class="slide has-sub {{ $beneficiary_orders_managment_routes ? 'open' : '' }}">
                        <a href="javascript:void(0);"
                            class="side-menu__item {{ $beneficiary_orders_managment_routes ? 'active' : '' }}">
                            <i class="ri-arrow-right-s-line side-menu__angle"></i>
                            <i class="bi bi-file-earmark-text"></i>
                            <span class="side-menu__label">{{ trans('cruds.beneficiaryOrder.title') }}</span>
                        </a>
                        <ul class="slide-menu child1">
                            <li class="slide side-menu__label1">
                                <a href="javascript:void(0)">{{ trans('cruds.beneficiaryOrder.title') }}</a>
                            </li>
                            @can('beneficiary_orders_all_list_access')
                                <li class="slide">
                                    <a href="{{ route('admin.beneficiary-orders.index') }}?status=all"
                                        class="side-menu__item {{ request()->is('admin/beneficiary-orders/*') ? 'active' : '' }}">
                                        {{ trans('cruds.beneficiaryOrder.extra.all') }}
                                    </a>
                                </li>
                            @endcan
                            @can('beneficiary_order_access')
                                <li class="slide">
                                    <a href="{{ route('admin.beneficiary-orders.index') }}?status=current"
                                        class="side-menu__item {{ request()->is('admin/beneficiary-orders/*') ? 'active' : '' }}">
                                        {{ trans('cruds.beneficiaryOrder.extra.current') }}
                                    </a>
                                </li>
                            @endcan
                            @can('beneficiary_orders_done_access')
                                <li class="slide">
                                    <a href="{{ route('admin.beneficiary-orders.index') }}?status=finished"
                                        class="side-menu__item {{ request()->is('admin/beneficiary-orders/*') ? 'active' : '' }}">
                                        {{ trans('cruds.beneficiaryOrder.extra.finished') }}
                                    </a>
                                </li>
                            @endcan
                            @can('beneficiary_orders_rejected_access')
                                <li class="slide">
                                    <a href="{{ route('admin.beneficiary-orders.index') }}?status=rejected"
                                        class="side-menu__item {{ request()->is('admin/beneficiary-orders/*') ? 'active' : '' }}">
                                        {{ trans('cruds.beneficiaryOrder.extra.rejected') }}
                                    </a>
                                </li>
                            @endcan
                            @can('beneficiary_orders_archive_access')
                                <li class="slide">
                                    <a href="{{ route('admin.beneficiary-orders.index') }}?status=archived"
                                        class="side-menu__item {{ request()->is('admin/beneficiary-orders/*') ? 'active' : '' }}">
                                        {{ trans('cruds.beneficiaryOrder.extra.archived') }}
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>

                @endcan


                @can('services_managment_access')
                    @php
                        $services_managment_routes =
                            request()->is('admin/services') ||
                            request()->is('admin/services/*') ||
                            request()->is('admin/courses/*') ||
                            request()->is('admin/consultant-schedules/*') ||
                            request()->is('admin/consultants/*') ||
                            request()->is('admin/buildings/*') ||
                            request()->is('admin/loans/*')
                                ? true
                                : false;
                    @endphp
                    <li class="slide has-sub {{ $services_managment_routes ? 'open' : '' }}">
                        <a href="javascript:void(0);"
                            class="side-menu__item {{ $services_managment_routes ? 'active' : '' }}">
                            <i class="ri-arrow-right-s-line side-menu__angle"></i>
                            <i class="bi bi-hdd-stack"></i>
                            <span class="side-menu__label">{{ trans('cruds.servicesManagment.title') }}</span>
                        </a>
                        <ul class="slide-menu child1">
                            <li class="slide side-menu__label1">
                                <a href="javascript:void(0)">{{ trans('cruds.servicesManagment.title') }}</a>
                            </li>
                            @can('service_access')
                                <li class="slide">
                                    <a href="{{ route('admin.services.list') }}"
                                        class="side-menu__item {{ request()->is('admin/services/*') || request()->is('admin/services') ? 'active' : '' }}">{{ trans('cruds.service.title') }}</a>
                                </li>
                            @endcan
                            @can('course_access')
                                <li class="slide">
                                    <a href="{{ route('admin.courses.index') }}"
                                        class="side-menu__item {{ request()->is('admin/courses/*') ? 'active' : '' }}">{{ trans('cruds.course.title') }}</a>
                                </li>
                            @endcan
                            @can('consultant_access')
                                <li class="slide">
                                    <a href="{{ route('admin.consultants.index') }}"
                                        class="side-menu__item {{ request()->is('admin/consultants/*') ? 'active' : '' }}">{{ trans('cruds.consultant.title') }}</a>
                                </li>
                            @endcan
                            @can('consultant_schedule_access')
                                <li class="slide">
                                    <a href="{{ route('admin.consultant-schedules.index') }}"
                                        class="side-menu__item {{ request()->is('admin/consultant-schedules/*') ? 'active' : '' }}">
                                        {{ trans('cruds.consultantSchedule.title') }}
                                    </a>
                                </li>
                            @endcan
                            @can('building_access')
                                {{-- <li class="slide">
                                <a href="{{ route('admin.buildings.index') }}"
                                    class="side-menu__item {{ request()->is('admin/buildings/*') ? 'active' : '' }}">{{ trans('cruds.building.title') }}</a>
                            </li> --}}
                            @endcan
                            @can('loan_access')
                                <li class="slide">
                                    <a href="{{ route('admin.loans.index') }}"
                                        class="side-menu__item {{ request()->is('admin/loans/*') ? 'active' : '' }}">{{ trans('cruds.loan.title') }}</a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan

                <!-- Start::slide__category -->
                <li class="slide__category"><span
                        class="category-name">{{ trans('cruds.userManagement.title') }}</span></li>
                <!-- End::slide__category -->


                @can('user_management_access')
                    @php
                        $user_managment_routes =
                            request()->is('admin/departments/*') ||
                            request()->is('admin/sections/*') ||
                            request()->is('admin/roles/*') ||
                            request()->is('admin/users/*')
                                ? true
                                : false;
                    @endphp
                    <li class="slide has-sub {{ $user_managment_routes ? 'open' : '' }}">
                        <a href="javascript:void(0);"
                            class="side-menu__item {{ $user_managment_routes ? 'active' : '' }}">
                            <i class="ri-arrow-right-s-line side-menu__angle"></i>
                            <i class="bi bi-people"></i>
                            <span class="side-menu__label">{{ trans('cruds.userManagement.title') }}</span>
                        </a>
                        <ul class="slide-menu child1">
                            <li class="slide side-menu__label1">
                                <a href="javascript:void(0)">{{ trans('cruds.userManagement.title') }}</a>
                            </li>
                            @can('department_access')
                                <li class="slide">
                                    <a href="{{ route('admin.departments.index') }}"
                                        class="side-menu__item {{ request()->is('admin/departments/*') ? 'active' : '' }}">{{ trans('cruds.department.title') }}</a>
                                </li>
                            @endcan
                            @can('section_access')
                                <li class="slide">
                                    <a href="{{ route('admin.sections.index') }}"
                                        class="side-menu__item {{ request()->is('admin/sections/*') ? 'active' : '' }}">{{ trans('cruds.section.title') }}</a>
                                </li>
                            @endcan
                            @can('role_access')
                                <li class="slide">
                                    <a href="{{ route('admin.roles.index') }}"
                                        class="side-menu__item {{ request()->is('admin/roles/*') ? 'active' : '' }}">{{ trans('cruds.role.title') }}</a>
                                </li>
                            @endcan
                            @can('user_access')
                                <li class="slide">
                                    <a href="{{ route('admin.users.index') }}"
                                        class="side-menu__item {{ request()->is('admin/users/*') ? 'active' : '' }}">{{ trans('cruds.user.title') }}</a>
                                </li>
                            @endcan
                        </ul>
                    </li>

                @endcan

                @can('task_management_access')
                    @php
                        $task_managment_routes =
                            request()->is('admin/task-boards/*') ||
                            request()->is('admin/tasks/*') ||
                            request()->is('admin/task-statuses/*') ||
                            request()->is('admin/task-tags/*') ||
                            request()->is('admin/task-priorities/*') ||
                            request()->is('admin/tasks-calendars/*')
                                ? true
                                : false;
                    @endphp
                    <li class="slide has-sub {{ $task_managment_routes ? 'open' : '' }}">
                        <a href="javascript:void(0);"
                            class="side-menu__item {{ $task_managment_routes ? 'active' : '' }}">
                            <i class="ri-arrow-right-s-line side-menu__angle"></i>
                            <i class="bi bi-list-task"></i>
                            <span class="side-menu__label">{{ trans('cruds.taskManagement.title') }}</span>
                        </a>
                        <ul class="slide-menu child1">
                            <li class="slide side-menu__label1">
                                <a href="javascript:void(0)">{{ trans('cruds.taskManagement.title') }}</a>
                            </li>
                            @can('task_board_access')
                                <li class="slide has-sub">
                                    <a href="javascript:void(0);"
                                        class="side-menu__item  {{ request()->is('admin/task-boards/*') ? 'active' : '' }}">{{ trans('cruds.taskBoard.title') }}
                                        <i class="ri-arrow-right-s-line side-menu__angle"></i>
                                    </a>
                                    <ul class="slide-menu child2">
                                        @foreach (\App\Models\TaskBoard::orderBy('id', 'desc')->take(8)->get() as $taskBoard)
                                            <li class="slide">
                                                <a href="{{ route('admin.task-boards.show', $taskBoard->id) }}"
                                                    class="side-menu__item">{{ $taskBoard->name }}</a>
                                            </li>
                                        @endforeach
                                        <li class="slide">
                                            <a href="{{ route('admin.task-boards.index') }}"
                                                class="side-menu__item text-secondary {{ request()->is('admin/task-boards') ? 'active' : '' }}">
                                                {{ trans('global.view_list') }}
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            @endcan
                            @can('task_access')
                                <li class="slide">
                                    <a href="{{ route('admin.tasks.index') }}"
                                        class="side-menu__item {{ request()->is('admin/tasks/*') ? 'active' : '' }}">
                                        {{ trans('cruds.task.title') }}
                                    </a>
                                </li>
                            @endcan
                            @can('task_status_access')
                                <li class="slide">
                                    <a href="{{ route('admin.task-statuses.index') }}"
                                        class="side-menu__item {{ request()->is('admin/task-statuses/*') ? 'active' : '' }}">
                                        {{ trans('cruds.taskStatus.title') }}
                                    </a>
                                </li>
                            @endcan
                            @can('task_tag_access')
                                <li class="slide">
                                    <a href="{{ route('admin.task-tags.index') }}"
                                        class="side-menu__item {{ request()->is('admin/task-tags/*') ? 'active' : '' }}">
                                        {{ trans('cruds.taskTag.title') }}
                                    </a>
                                </li>
                            @endcan
                            @can('task_priority_access')
                                <li class="slide">
                                    <a href="{{ route('admin.task-priorities.index') }}"
                                        class="side-menu__item {{ request()->is('admin/task-priorities/*') ? 'active' : '' }}">
                                        {{ trans('cruds.taskPriority.title') }}
                                    </a>
                                </li>
                            @endcan
                            @can('tasks_calendar_access')
                                <li class="slide">
                                    <a href="{{ route('admin.tasks-calendars.index') }}"
                                        class="side-menu__item {{ request()->is('admin/tasks-calendars/*') ? 'active' : '' }}">
                                        {{ trans('cruds.tasksCalendar.title') }}
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan

                @can('letters_managment_access')
                    @php
                        $letters_managment_routes =
                            request()->is('admin/letters-organizations/*') ||
                            request()->is('admin/incoming-letters/*') ||
                            request()->is('admin/outgoing-letters/*') ||
                            request()->is('admin/letter-archives/*')
                                ? true
                                : false;
                    @endphp
                    <li class="slide has-sub {{ $letters_managment_routes ? 'open' : '' }}">
                        <a href="javascript:void(0);"
                            class="side-menu__item {{ $letters_managment_routes ? 'active' : '' }}">
                            <i class="ri-arrow-right-s-line side-menu__angle"></i>
                            <i class="bi bi-chat-left-text"></i>
                            <span class="side-menu__label">{{ trans('cruds.lettersManagment.title') }}</span>
                        </a>
                        <ul class="slide-menu child1">
                            <li class="slide side-menu__label1">
                                <a href="javascript:void(0)">{{ trans('cruds.lettersManagment.title') }}</a>
                            </li>
                            @can('letters_organization_access')
                                <li class="slide">
                                    <a href="{{ route('admin.letters-organizations.index') }}"
                                        class="side-menu__item {{ request()->is('admin/letters-organizations/*') ? 'active' : '' }}">
                                        {{ trans('cruds.lettersOrganization.title') }}
                                    </a>
                                </li>
                            @endcan
                            @can('incoming_letter_access')
                                <li class="slide">
                                    <a href="{{ route('admin.incoming-letters.index') }}"
                                        class="side-menu__item {{ request()->is('admin/incoming-letters/*') ? 'active' : '' }}">
                                        {{ trans('cruds.incomingLetter.title') }}
                                    </a>
                                </li>
                            @endcan
                            @can('outgoing_letter_access')
                                <li class="slide">
                                    <a href="{{ route('admin.outgoing-letters.index') }}"
                                        class="side-menu__item {{ request()->is('admin/outgoing-letters/*') ? 'active' : '' }}">
                                        {{ trans('cruds.outgoingLetter.title') }}
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan

                <!-- Start::slide -->
                {{-- <li class="slide">
                        <a href="{{ route('admin.mailbox.index', ['type' => 'inbox']) }}" class="side-menu__item">
                            <i class="bi bi-envelope"></i>
                            <span class="side-menu__label">{{ trans('cruds.mailbox.title') }}</span>
                        </a>
                    </li> --}}
                <li class="slide">
                    <a href="{{ route('admin.chats.index') }}" class="side-menu__item">
                        <i class="bi bi-chat-left-text"></i>
                        <span class="side-menu__label">{{ trans('cruds.chat.title') }}</span>
                    </a>
                </li>
                <!-- End::slide -->


                @can('report_managment_access')
                    <!-- Start::slide__category -->
                    <li class="slide__category"><span
                            class="category-name">{{ trans('cruds.reportManagment.title') }}</span></li>
                    <!-- End::slide__category -->

                    @php
                        $report_managment_routes =
                            request()->is('admin/beneficiary-reports/*') ||
                            request()->is('admin/beneficiary-orders-reports/*') ||
                            request()->is('admin/task-reports/*')
                                ? true
                                : false;
                    @endphp
                    <li class="slide has-sub {{ $report_managment_routes ? 'open' : '' }}">
                        <a href="javascript:void(0);"
                            class="side-menu__item {{ $report_managment_routes ? 'active' : '' }}">
                            <i class="ri-arrow-right-s-line side-menu__angle"></i>
                            <i class="bi bi-clipboard2-data"></i>
                            <span class="side-menu__label">{{ trans('cruds.reportManagment.title') }}</span>
                        </a>
                        <ul class="slide-menu child1">
                            <li class="slide side-menu__label1">
                                <a href="javascript:void(0)">{{ trans('cruds.reportManagment.title') }}</a>
                            </li>
                            @can('beneficiary_report_access')
                                <li class="slide">
                                    <a href="{{ route('admin.beneficiary-reports.index') }}"
                                        class="side-menu__item {{ request()->is('admin/beneficiary-reports/*') ? 'active' : '' }}">
                                        {{ trans('cruds.beneficiaryReport.title') }}
                                    </a>
                                </li>
                            @endcan
                            @can('beneficiary_orders_report_access')
                                <li class="slide">
                                    <a href="{{ route('admin.beneficiary-orders-reports.index') }}"
                                        class="side-menu__item {{ request()->is('admin/beneficiary-orders-reports/*') ? 'active' : '' }}">
                                        {{ trans('cruds.beneficiaryOrdersReport.title') }}
                                    </a>
                                </li>

                                <li class="slide">
                                    <a href="{{ route('admin.service-loans-reports.index') }}"
                                        class="side-menu__item {{ request()->is('admin/service-loans-reports/*') ? 'active' : '' }}">
                                        {{ trans('cruds.serviceLoansReport.title') }}
                                    </a>
                                </li>
                            @endcan
                            @can('task_report_access')
                                <li class="slide">
                                    <a href="{{ route('admin.task-reports.index') }}"
                                        class="side-menu__item {{ request()->is('admin/task-reports/*') ? 'active' : '' }}">
                                        {{ trans('cruds.taskReport.title') }}
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan

                @can('archive_management_access')
                    @can('archive_access')
                        <!-- Start::slide -->
                        <li class="slide">
                            <a href="{{ route('admin.archives.index') }}" class="side-menu__item">
                                <i class="bi bi-archive"></i>
                                <span class="side-menu__label">{{ trans('cruds.archive.title') }}</span>
                            </a>
                        </li>
                        <!-- End::slide -->
                    @endcan
                    @can('storage_location_access')
                        <!-- Start::slide -->
                        <li class="slide">
                            <a href="{{ route('admin.storage-locations.index') }}" class="side-menu__item">
                                <i class="bi bi-hdd"></i>
                                <span class="side-menu__label">{{ trans('cruds.storageLocation.title') }}</span>
                            </a>
                        </li>
                    @endcan
                    <!-- End::slide -->
                @endcan

                <!-- Start::slide__category -->
                <li class="slide__category"><span
                        class="category-name">{{ trans('cruds.generalSetting.title') }}</span></li>
                <!-- End::slide__category -->



                @can('user_alert_access')
                    <!-- Start::slide -->
                    <li class="slide">
                        <a href="{{ route('admin.user-alerts.index') }}" class="side-menu__item">
                            <i class="bi bi-bell"></i>
                            <span class="side-menu__label">{{ trans('cruds.userAlert.title') }}</span>
                        </a>
                    </li>
                    <!-- End::slide -->
                @endcan
                @can('frontend_setting_access')
                    @php
                        $frontend_setting_routes =
                            request()->is('admin/sliders/*') ||
                            request()->is('admin/front-projects/*') ||
                            request()->is('admin/front-achievements/*') ||
                            request()->is('admin/front-partners/*') ||
                            request()->is('admin/front-reviews/*') ||
                            request()->is('admin/front-links/*') ||
                            request()->is('admin/subscriptions/*')
                                ? true
                                : false;
                    @endphp
                    <li class="slide has-sub {{ $frontend_setting_routes ? 'open' : '' }}">
                        <a href="javascript:void(0);"
                            class="side-menu__item {{ $frontend_setting_routes ? 'active' : '' }}">
                            <i class="ri-arrow-right-s-line side-menu__angle"></i>
                            <i class="bi bi-easel"></i>
                            <span class="side-menu__label">{{ trans('cruds.frontendSetting.title') }}</span>
                        </a>
                        <ul class="slide-menu child1">
                            <li class="slide side-menu__label1">
                                <a href="javascript:void(0)">{{ trans('cruds.frontendSetting.title') }}</a>
                            </li>
                            @can('slider_access')
                                <li class="slide">
                                    <a href="{{ route('admin.sliders.index') }}"
                                        class="side-menu__item {{ request()->is('admin/sliders/*') ? 'active' : '' }}">
                                        {{ trans('cruds.slider.title') }}
                                    </a>
                                </li>
                            @endcan
                            @can('front_project_access')
                                <li class="slide">
                                    <a href="{{ route('admin.front-projects.index') }}"
                                        class="side-menu__item {{ request()->is('admin/front-projects/*') ? 'active' : '' }}">
                                        {{ trans('cruds.frontProject.title') }}
                                    </a>
                                </li>
                            @endcan
                            @can('front_achievement_access')
                                <li class="slide">
                                    <a href="{{ route('admin.front-achievements.index') }}"
                                        class="side-menu__item {{ request()->is('admin/front-achievements/*') ? 'active' : '' }}">
                                        {{ trans('cruds.frontAchievement.title') }}
                                    </a>
                                </li>
                            @endcan
                            @can('front_partner_access')
                                <li class="slide">
                                    <a href="{{ route('admin.front-partners.index') }}"
                                        class="side-menu__item {{ request()->is('admin/front-partners/*') ? 'active' : '' }}">
                                        {{ trans('cruds.frontPartner.title') }}
                                    </a>
                                </li>
                            @endcan
                            @can('front_review_access')
                                <li class="slide">
                                    <a href="{{ route('admin.front-reviews.index') }}"
                                        class="side-menu__item {{ request()->is('admin/front-reviews/*') ? 'active' : '' }}">
                                        {{ trans('cruds.frontReview.title') }}
                                    </a>
                                </li>
                            @endcan
                            @can('front_link_access')
                                <li class="slide">
                                    <a href="{{ route('admin.front-links.index') }}"
                                        class="side-menu__item {{ request()->is('admin/front-links/*') ? 'active' : '' }}">
                                        {{ trans('cruds.frontLink.title') }}
                                    </a>
                                </li>
                            @endcan
                            @can('subscription_access')
                                <li class="slide">
                                    <a href="{{ route('admin.subscriptions.index') }}"
                                        class="side-menu__item {{ request()->is('admin/subscriptions/*') ? 'active' : '' }}">
                                        {{ trans('cruds.subscription.title') }}
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan

                @can('faq_management_access')
                    @php
                        $faq_management_routes =
                            request()->is('admin/faq-categories/*') ||
                            request()->is('admin/faq-questions/*') ||
                            request()->is('admin/user-queries/*')
                                ? true
                                : false;
                    @endphp
                    <li class="slide has-sub {{ $faq_management_routes ? 'open' : '' }}">
                        <a href="javascript:void(0);"
                            class="side-menu__item {{ $faq_management_routes ? 'active' : '' }}">
                            <i class="ri-arrow-right-s-line side-menu__angle"></i>
                            <i class="bi bi-question-circle"></i>
                            <span class="side-menu__label">{{ trans('cruds.faqManagement.title') }}</span>
                        </a>
                        <ul class="slide-menu child1">
                            <li class="slide side-menu__label1">
                                <a href="javascript:void(0)">{{ trans('cruds.faqManagement.title') }}</a>
                            </li>
                            @can('faq_category_access')
                                <li class="slide">
                                    <a href="{{ route('admin.faq-categories.index') }}"
                                        class="side-menu__item {{ request()->is('admin/faq-categories/*') ? 'active' : '' }}">
                                        {{ trans('cruds.faqCategory.title') }}
                                    </a>
                                </li>
                            @endcan
                            @can('faq_question_access')
                                <li class="slide">
                                    <a href="{{ route('admin.faq-questions.index') }}"
                                        class="side-menu__item {{ request()->is('admin/faq-questions/*') ? 'active' : '' }}">
                                        {{ trans('cruds.faqQuestion.title') }}
                                    </a>
                                </li>
                            @endcan
                            @can('user_query_access')
                                <li class="slide">
                                    <a href="{{ route('admin.user-queries.index') }}"
                                        class="side-menu__item {{ request()->is('admin/user-queries/*') ? 'active' : '' }}">
                                        {{ trans('cruds.userQuery.title') }}
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan
                @can('general_setting_access')
                    @php
                        $general_setting_routes =
                            request()->is('admin/nationalities/*') ||
                            request()->is('admin/marital-statuses/*') ||
                            request()->is('admin/beneficiary-categories/*') ||
                            request()->is('admin/accommodation-entities/*') ||
                            request()->is('admin/family-relationships/*') ||
                            request()->is('admin/service-statuses/*') ||
                            request()->is('admin/health-conditions/*') ||
                            request()->is('admin/educational-qualifications/*') ||
                            request()->is('admin/job-types/*') ||
                            request()->is('admin/required-documents/*') ||
                            request()->is('admin/disability-types/*') ||
                            request()->is('admin/economic-statuses/*') ||
                            request()->is('admin/consultant-types/*') ||
                            request()->is('admin/settings/*')
                                ? true
                                : false;
                    @endphp
                    <li class="slide has-sub {{ $general_setting_routes ? 'open' : '' }}">
                        <a href="javascript:void(0);"
                            class="side-menu__item {{ $general_setting_routes ? 'active' : '' }}">
                            <i class="ri-arrow-right-s-line side-menu__angle"></i>
                            <i class="bi bi-gear"></i>
                            <span class="side-menu__label">{{ trans('cruds.generalSetting.title') }}</span>
                        </a>
                        <ul class="slide-menu child1">
                            <li class="slide side-menu__label1">
                                <a href="javascript:void(0)">{{ trans('cruds.generalSetting.title') }}</a>
                            </li>


                            @can('country_managment_access')
                                @php
                                    $country_managment_routes =
                                        request()->is('admin/regions/*') ||
                                        request()->is('admin/cities/*') ||
                                        request()->is('admin/districts/*')
                                            ? true
                                            : false;
                                @endphp
                                <li class="slide has-sub {{ $country_managment_routes ? 'open' : '' }}">
                                    <a href="javascript:void(0);"
                                        class="side-menu__item {{ $country_managment_routes ? 'active' : '' }}">
                                        <i class="ri-arrow-right-s-line side-menu__angle"></i>
                                        <i class="bi bi-globe-americas"></i>
                                        <span class="side-menu__label">{{ trans('cruds.countryManagment.title') }}</span>
                                    </a>
                                    <ul class="slide-menu child1">
                                        <li class="slide side-menu__label1">
                                            <a href="javascript:void(0)">{{ trans('cruds.countryManagment.title') }}</a>
                                        </li>
                                        @can('region_access')
                                            <li class="slide">
                                                <a href="{{ route('admin.regions.index') }}"
                                                    class="side-menu__item {{ request()->is('admin/regions/*') ? 'active' : '' }}">
                                                    {{ trans('cruds.region.title') }}
                                                </a>
                                            </li>
                                        @endcan
                                        @can('city_access')
                                            <li class="slide">
                                                <a href="{{ route('admin.cities.index') }}"
                                                    class="side-menu__item {{ request()->is('admin/cities/*') ? 'active' : '' }}">
                                                    {{ trans('cruds.city.title') }}
                                                </a>
                                            </li>
                                        @endcan
                                        @can('district_access')
                                            <li class="slide">
                                                <a href="{{ route('admin.districts.index') }}"
                                                    class="side-menu__item {{ request()->is('admin/districts/*') ? 'active' : '' }}">
                                                    {{ trans('cruds.district.title') }}
                                                </a>
                                            </li>
                                        @endcan
                                    </ul>
                                </li>
                            @endcan
                            @can('nationality_access')
                                <li class="slide">
                                    <a href="{{ route('admin.nationalities.index') }}"
                                        class="side-menu__item {{ request()->is('admin/nationalities/*') ? 'active' : '' }}">
                                        {{ trans('cruds.nationality.title') }}
                                    </a>
                                </li>
                            @endcan
                            @can('marital_status_access')
                                <li class="slide">
                                    <a href="{{ route('admin.marital-statuses.index') }}"
                                        class="side-menu__item {{ request()->is('admin/marital-statuses/*') ? 'active' : '' }}">
                                        {{ trans('cruds.maritalStatus.title') }}
                                    </a>
                                </li>
                            @endcan
                            @can('beneficiary_category_access')
                                <li class="slide">
                                    <a href="{{ route('admin.beneficiary-categories.index') }}"
                                        class="side-menu__item {{ request()->is('admin/beneficiary-categories/*') ? 'active' : '' }}">
                                        {{ trans('cruds.beneficiaryCategory.title') }}
                                    </a>
                                </li>
                            @endcan
                            @can('accommodation_entity_access')
                                <li class="slide">
                                    <a href="{{ route('admin.accommodation-entities.index') }}"
                                        class="side-menu__item {{ request()->is('admin/accommodation-entities/*') ? 'active' : '' }}">
                                        {{ trans('cruds.accommodationEntity.title') }}
                                    </a>
                                </li>
                            @endcan
                            @can('family_relationship_access')
                                <li class="slide">
                                    <a href="{{ route('admin.family-relationships.index') }}"
                                        class="side-menu__item {{ request()->is('admin/family-relationships/*') ? 'active' : '' }}">
                                        {{ trans('cruds.familyRelationship.title') }}
                                    </a>
                                </li>
                            @endcan
                            @can('service_status_access')
                                <li class="slide">
                                    <a href="{{ route('admin.service-statuses.index') }}"
                                        class="side-menu__item {{ request()->is('admin/service-statuses/*') ? 'active' : '' }}">
                                        {{ trans('cruds.serviceStatus.title') }}
                                    </a>
                                </li>
                            @endcan
                            @can('health_condition_access')
                                <li class="slide">
                                    <a href="{{ route('admin.health-conditions.index') }}"
                                        class="side-menu__item {{ request()->is('admin/health-conditions/*') ? 'active' : '' }}">
                                        {{ trans('cruds.healthCondition.title') }}
                                    </a>
                                </li>
                            @endcan
                            @can('educational_qualification_access')
                                <li class="slide">
                                    <a href="{{ route('admin.educational-qualifications.index') }}"
                                        class="side-menu__item {{ request()->is('admin/educational-qualifications/*') ? 'active' : '' }}">
                                        {{ trans('cruds.educationalQualification.title') }}
                                    </a>
                                </li>
                            @endcan
                            @can('job_type_access')
                                <li class="slide">
                                    <a href="{{ route('admin.job-types.index') }}"
                                        class="side-menu__item {{ request()->is('admin/job-types/*') ? 'active' : '' }}">
                                        {{ trans('cruds.jobType.title') }}
                                    </a>
                                </li>
                            @endcan
                            @can('required_document_access')
                                <li class="slide">
                                    <a href="{{ route('admin.required-documents.index') }}"
                                        class="side-menu__item {{ request()->is('admin/required-documents/*') ? 'active' : '' }}">
                                        {{ trans('cruds.requiredDocument.title') }}
                                    </a>
                                </li>
                            @endcan
                            @can('disability_type_access')
                                <li class="slide">
                                    <a href="{{ route('admin.disability-types.index') }}"
                                        class="side-menu__item {{ request()->is('admin/disability-types/*') ? 'active' : '' }}">
                                        {{ trans('cruds.disabilityType.title') }}
                                    </a>
                                </li>
                            @endcan
                            @can('economic_status_access')
                                <li class="slide">
                                    <a href="{{ route('admin.economic-statuses.index') }}"
                                        class="side-menu__item {{ request()->is('admin/economic-statuses/*') ? 'active' : '' }}">
                                        {{ trans('cruds.economicStatus.title') }}
                                    </a>
                                </li>
                            @endcan
                            @can('consultation_type_access')
                                <li class="slide">
                                    <a href="{{ route('admin.consultation-types.index') }}"
                                        class="side-menu__item {{ request()->is('admin/consultation-types/*') ? 'active' : '' }}">{{ trans('cruds.consultationType.title') }}</a>
                                </li>
                            @endcan
                            @can('setting_access')
                                <li class="slide">
                                    <a href="{{ route('admin.settings.index') }}"
                                        class="side-menu__item {{ request()->is('admin/settings/*') ? 'active' : '' }}">
                                        {{ trans('cruds.setting.title') }}
                                    </a>
                                </li>
                                <li class="slide">
                                    <a href="{{ route('admin.beneficiary-field-visibility.index') }}"
                                        class="side-menu__item {{ request()->is('admin/beneficiary-field-visibility/*') ? 'active' : '' }}">
                                        <i class="bi bi-eye-slash"></i>
                                        {{ trans('cruds.beneficiaryFieldVisibility.title') }}
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan
            </ul>
            <div class="slide-right" id="slide-right"><svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191"
                    width="24" height="24" viewBox="0 0 24 24">
                    <path d="M10.707 17.707 16.414 12l-5.707-5.707-1.414 1.414L13.586 12l-4.293 4.293z"></path>
                </svg></div>
        </nav>
        <!-- End::nav -->

    </div>
    <!-- End::main-sidebar -->

</aside>
