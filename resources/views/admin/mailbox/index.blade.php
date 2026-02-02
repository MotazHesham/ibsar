@extends('layouts.master')
@section('styles')
    <style>
        /* Mail Replies Styling */
        .mail-replies {
            margin-top: 1rem;
        }
        
        .mail-reply-item {
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }
        
        .mail-reply-item:hover {
            transform: translateX(2px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        
        .mail-reply-item.bg-light-primary {
            border-left-color: var(--primary-color);
            background: linear-gradient(135deg, rgba(var(--primary-rgb), 0.05) 0%, rgba(var(--primary-rgb), 0.02) 100%);
        }
        
        .mail-reply-item.bg-light {
            border-left-color: var(--secondary-color);
            background: linear-gradient(135deg, rgba(var(--secondary-rgb), 0.05) 0%, rgba(var(--secondary-rgb), 0.02) 100%);
        }
        
        .mail-reply-content {
            line-height: 1.6;
            color: var(--text-color);
        }
        
        .mail-reply-content p {
            margin-bottom: 0.5rem;
        }
        
        .mail-reply-content p:last-child {
            margin-bottom: 0;
        }
        
        .mail-reply-item .avatar-sm {
            width: 2rem;
            height: 2rem;
            font-size: 0.75rem;
        }
        
        .mail-reply-item .badge {
            font-size: 0.625rem;
            padding: 0.25rem 0.5rem;
        }
        
        /* Dark mode adjustments */
        [data-theme-mode="dark"] .mail-reply-item.bg-light-primary {
            background: linear-gradient(135deg, rgba(var(--primary-rgb), 0.15) 0%, rgba(var(--primary-rgb), 0.08) 100%);
        }
        
        [data-theme-mode="dark"] .mail-reply-item.bg-light {
            background: linear-gradient(135deg, rgba(var(--secondary-rgb), 0.15) 0%, rgba(var(--secondary-rgb), 0.08) 100%);
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .mail-reply-item .d-flex {
                flex-direction: column;
                gap: 1rem;
            }
            
            .mail-reply-item .text-end {
                text-align: left !important;
            }
        }
    </style>
@endsection
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.mailbox.title'), 'url' => '#'],
            [
                'title' => trans('global.list') . ' ' . trans('cruds.mailbox.title'),
                'url' => route('admin.mailbox.index'),
            ],
        ];
    @endphp
    @include('partials.breadcrumb')

    <div class="main-mail-container mb-2 gap-2 d-flex">
        <div class="mail-navigation border">
            <div class="d-grid align-items-top p-3 border-bottom border-block-end-dashed">
                <button class="btn btn-primary d-flex align-items-center justify-content-center" data-bs-toggle="modal"
                    data-bs-target="#mail-Compose">
                    <i class="ri-add-circle-line fs-16 align-middle me-1"></i>{{ trans('cruds.mailbox.extra.new_compose') }}
                </button>
            </div>
            <div>
                <ul class="list-unstyled mail-main-nav" id="mail-main-nav">
                    <li class="px-0 pt-0">
                        <span class="fs-11 text-muted op-7 fw-medium">{{ trans('cruds.mailbox.side_bar.mails') }}</span>
                    </li>
                    <li class="mail-type @if (request('type') == 'inbox') active @endif">
                        <a href="{{ route('admin.mailbox.index', ['type' => 'inbox']) }}">
                            <div class="d-flex align-items-center">
                                <span class="me-2 lh-1">
                                    <i class="ti ti-inbox align-middle fs-16"></i>
                                </span>
                                <span class="flex-fill text-nowrap">
                                    {{ trans('cruds.mailbox.side_bar.inbox') }}
                                </span>
                                @if ($totalMessages->inbox > 0)
                                    <span class="badge bg-success rounded-pill">{{ $totalMessages->inbox }}</span>
                                @endif
                            </div>
                        </a>
                    </li>
                    <li class="mail-type @if (request('type') == 'sent') active @endif">
                        <a href="{{ route('admin.mailbox.index', ['type' => 'sent']) }}">
                            <div class="d-flex align-items-center">
                                <span class="me-2 lh-1">
                                    <i class="ti ti-send align-middle fs-16"></i>
                                </span>
                                <span class="flex-fill text-nowrap">
                                    {{ trans('cruds.mailbox.side_bar.sent') }}
                                </span>
                                @if ($totalMessages->sent > 0)
                                    <span class="badge bg-light text-dark rounded-pill">{{ $totalMessages->sent }}</span>
                                @endif
                            </div>
                        </a>
                    </li>
                    <li class="mail-type @if (request('type') == 'important') active @endif">
                        <a href="{{ route('admin.mailbox.index', ['type' => 'important']) }}">
                            <div class="d-flex align-items-center">
                                <span class="me-2 lh-1">
                                    <i class="ti ti-bookmark align-middle fs-16"></i>
                                </span>
                                <span class="flex-fill text-nowrap">
                                    {{ trans('cruds.mailbox.side_bar.important') }}
                                </span>
                                @if ($totalMessages->important > 0)
                                    <span class="badge bg-secondary rounded-pill">{{ $totalMessages->important }}</span>
                                @endif
                            </div>
                        </a>
                    </li>
                    <li class="mail-type @if (request('type') == 'starred') active @endif">
                        <a href="{{ route('admin.mailbox.index', ['type' => 'starred']) }}">
                            <div class="d-flex align-items-center">
                                <span class="me-2 lh-1">
                                    <i class="ti ti-star align-middle fs-16"></i>
                                </span>
                                <span class="flex-fill text-nowrap">
                                    {{ trans('cruds.mailbox.side_bar.starred') }}
                                </span>
                                @if ($totalMessages->starred > 0)
                                    <span class="badge bg-warning rounded-pill">{{ $totalMessages->starred }}</span>
                                @endif
                            </div>
                        </a>
                    </li>
                    <li class="mail-type @if (request('type') == 'trash') active @endif">
                        <a href="{{ route('admin.mailbox.index', ['type' => 'trash']) }}">
                            <div class="d-flex align-items-center">
                                <span class="me-2 lh-1">
                                    <i class="ti ti-trash align-middle fs-16"></i>
                                </span>
                                <span class="flex-fill text-nowrap">
                                    {{ trans('cruds.mailbox.side_bar.trash') }}
                                </span>
                                @if ($totalMessages->trash > 0)
                                    <span class="badge bg-danger rounded-pill">{{ $totalMessages->trash }}</span>
                                @endif
                            </div>
                        </a>
                    </li>
                    <li class="mail-type @if (request('type') == 'archive') active @endif">
                        <a href="{{ route('admin.mailbox.index', ['type' => 'archive']) }}">
                            <div class="d-flex align-items-center">
                                <span class="me-2 lh-1">
                                    <i class="ti ti-archive align-middle fs-16"></i>
                                </span>
                                <span class="flex-fill text-nowrap">
                                    {{ trans('cruds.mailbox.side_bar.archive') }}
                                </span>
                                @if ($totalMessages->archive > 0)
                                    <span class="badge bg-info rounded-pill">{{ $totalMessages->archive }}</span>
                                @endif
                            </div>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="total-mails border">
            <button class="btn btn-icon btn-dark me-1 d-lg-none d-block total-mails-close btn-sm">
                <i class="ri-bar-chart-horizontal-line"></i>
            </button>
            <form action="{{ route('admin.mailbox.index') }}" method="get">
                <input type="hidden" name="type" value="{{ request('type') }}">
                <div class="p-3 d-flex align-items-center border-bottom border-block-end-dashed flex-wrap gap-2">
                    <div class="input-group">
                        <input type="text" class="form-control shadow-none" placeholder="{{ trans('global.search') }}"
                            aria-describedby="button-addon" name="search" value="{{ request('search') }}">
                        <button class="btn btn-primary" type="submit" id="button-addon"><i class="ri-search-line me-1"></i>
                            {{ trans('global.search') }}
                        </button>
                    </div>
                </div>
            </form>
            <div class="mail-messages" id="mail-messages">
                <ul class="list-unstyled mb-0 mail-messages-container" id="mail-messages-container">
                    @include('admin.mailbox.partials.messages', ['messages' => $messages])
                </ul>
            </div>
        </div>
    </div>

    <!-- Start::mail modal -->
    <div class="modal modal-lg fade" id="mail-Compose" tabindex="-1" aria-labelledby="mail-ComposeLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.mailbox.store') }}" method="post">
                    @csrf
                    <div class="modal-header">
                        <h6 class="modal-title" id="mail-ComposeLabel">{{ trans('cruds.mailbox.extra.new_compose') }}
                        </h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            @include('utilities.form.multiselect', [
                                'name' => 'receivers',
                                'id' => 'receivers',
                                'isRequired' => true,
                                'grid' => 'col-xl-12',
                                'label' => 'cruds.mailbox.fields.to_beneficiaries',
                                'options' => getBeneficiaryUsers()->pluck('name', 'id'),
                            ])
                            @include('utilities.form.text', [
                                'name' => 'subject',
                                'id' => 'subject',
                                'isRequired' => true,
                                'grid' => 'col-xl-12',
                                'label' => 'cruds.mailbox.fields.subject',
                            ])
                            @include('utilities.form.textarea', [
                                'name' => 'content',
                                'id' => 'content',
                                'isRequired' => true,
                                'grid' => 'col-xl-12',
                                'label' => 'cruds.mailbox.fields.content',
                                'editor' => true,
                            ])
                            @include('utilities.form.dropzone-multiple', [
                                'name' => 'attachments',
                                'id' => 'attachments',
                                'isRequired' => false,
                                'grid' => 'col-xl-12',
                                'url' => route('admin.mailbox.storeMedia'),
                                'label' => 'cruds.mailbox.fields.attachments',
                            ])
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light"
                            data-bs-dismiss="modal">{{ trans('global.cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ trans('global.send') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- End::mail modal -->
@endsection

@section('scripts')
    <script>
        let currentPage = 1;
        let isLoading = false;
        let hasMorePages = {{ $messages->hasMorePages() ? 'true' : 'false' }};
        let currentType = '{{ request('type', 'inbox') }}';
        let currentSearch = '{{ request('search', '') }}';
        let simpleBar = null;

        (function() {
            "use strict";
            let i = true;
            var myElement11 = document.getElementById("mail-main-nav");
            new SimpleBar(myElement11, {
                autoHide: true
            });

            let closeButton = document.querySelectorAll(
                ".responsive-mail-action-icons > button"
            )[0];

            if (closeButton) {
                closeButton.onclick = () => {
                    document.querySelector(".total-mails").classList.remove("d-none");
                    i = true;
                };
            }

            window.addEventListener("resize", () => {
                if (window.innerWidth > 1399) {
                    document.querySelector(".total-mails").classList.remove("d-none");
                }

                if (window.innerWidth < 1399 && i == false) {
                    document.querySelector(".total-mails").classList.add("d-none");
                } else {
                    document.querySelector(".total-mails").classList.remove("d-none");
                }

                if (window.innerWidth > 991) {
                    document.querySelector(".mail-navigation").style.display = "block";
                    document.querySelector(".total-mails").style.display = "block";
                } else {
                    if (
                        document.querySelector(".total-mails").style.display == "block" &&
                        i == false
                    ) {
                        document.querySelector(".mail-navigation").style.display = "none";
                    }
                    if ((document.querySelector(".mail-navigation").style.display = "none")) {}
                }
            });
            document.addEventListener("DOMContentLoaded", (event) => {
                console.log(window.innerWidth);
                if (window.innerWidth > 1399) {
                    document.querySelector(".total-mails").classList.remove("d-none");
                } else {}

                if (window.innerWidth < 1399 && i == false) {
                    document.querySelector(".total-mails").classList.add("d-none");
                } else {
                    document.querySelector(".total-mails").classList.remove("d-none");
                }
                if (window.innerWidth > 991) {
                    document.querySelector(".mail-navigation").style.display = "block";
                    document.querySelector(".total-mails").style.display = "block";
                } else {
                    if (
                        document.querySelector(".total-mails").style.display == "block" && i == false) {
                        document.querySelector(".mail-navigation").style.display = "none";
                    }
                    if ((document.querySelector(".mail-navigation").style.display = "none")) {}
                }
            });

            document.querySelector(".total-mails-close").onclick = () => {
                if (window.innerWidth < 992) {
                    document.querySelector(".mail-navigation").style.display = "block";
                    document.querySelector(".total-mails").classList.add("d-none");
                    i = true;
                }
            };

        })();
        $(document).ready(function() {
            // Initialize click handlers for existing messages
            initializeMessageHandlers();

            // Initialize SimpleBar for the mail messages container
            simpleBar = new SimpleBar(document.getElementById('mail-messages'), {
                autoHide: true
            });

            // Add scroll event listener to SimpleBar content
            const simpleBarContent = simpleBar.getScrollElement();
            simpleBarContent.addEventListener('scroll', handleScroll);

            // Handle search form submission to reset pagination
            $('form[action="{{ route('admin.mailbox.index') }}"]').on('submit', function() {
                resetPagination();
                currentSearch = $(this).find('input[name="search"]').val();
                currentType = $(this).find('input[name="type"]').val();
            });

            // Handle navigation links to reset pagination
            $('.mail-type a').on('click', function() {
                resetPagination();
            });
        });

        function initializeMessageHandlers() {
            $('#mail-messages-container li').off('click').on('click', function() {
                $(this).removeClass('active');
            });
        }

        function handleScroll() {
            if (isLoading || !hasMorePages) return;

            const simpleBarContent = simpleBar.getScrollElement();
            const scrollTop = simpleBarContent.scrollTop;
            const scrollHeight = simpleBarContent.scrollHeight;
            const clientHeight = simpleBarContent.clientHeight;

            // Check if scrolled to bottom (with 100px threshold for better UX)
            if (scrollTop + clientHeight >= scrollHeight - 100) {
                loadMoreMessages();
            }
        }

        function resetPagination() {
            currentPage = 1;
            hasMorePages = true;
            isLoading = false;
        }

        function loadMoreMessages() {
            if (isLoading || !hasMorePages) return;

            isLoading = true;
            currentPage++;

            // Show loading indicator
            const loadingHtml =
                '<li class="loading-indicator"><div class="d-flex justify-content-center align-items-center py-3"><div class="spinner-border spinner-border-sm me-2" role="status"><span class="visually-hidden">Loading...</span></div><span class="text-muted">Loading more messages...</span></div></li>';
            $('#mail-messages-container').append(loadingHtml);

            // Scroll to show loading indicator
            setTimeout(() => {
                const simpleBarContent = simpleBar.getScrollElement();
                simpleBarContent.scrollTop = simpleBarContent.scrollHeight;
            }, 100);

            $.ajax({
                url: "{{ route('admin.mailbox.loadMore') }}",
                type: "POST",
                data: {
                    page: currentPage,
                    type: currentType,
                    search: currentSearch,
                    _token: "{{ csrf_token() }}"
                },
                timeout: 30000, // 30 second timeout
                success: function(response) {
                    // Remove loading indicator
                    $('.loading-indicator').remove();

                    if (response.html && response.html.trim() !== '') {
                        // Append new messages
                        $('#mail-messages-container').append(response.html);

                        // Update pagination state
                        hasMorePages = response.hasMorePages;
                        currentPage = response.currentPage;

                        // Reinitialize click handlers for new messages
                        initializeMessageHandlers();

                        // Show success message if no more pages
                        if (!response.hasMorePages) {
                            const endMessage =
                                '<li class="text-center py-2"><small class="text-muted">No more messages to load</small></li>';
                            $('#mail-messages-container').append(endMessage);
                        }
                    } else {
                        // No more messages
                        hasMorePages = false;
                        const endMessage =
                            '<li class="text-center py-2"><small class="text-muted">No more messages to load</small></li>';
                        $('#mail-messages-container').append(endMessage);
                    }
                },
                error: function(xhr, status, error) {
                    // Remove loading indicator
                    $('.loading-indicator').remove();

                    // Show error message
                    let errorMessage = 'Error loading messages. Please try again.';
                    if (status === 'timeout') {
                        errorMessage = 'Request timed out. Please try again.';
                    } else if (xhr.status === 429) {
                        errorMessage = 'Too many requests. Please wait a moment and try again.';
                    }

                    const errorHtml =
                        '<li class="error-message"><div class="text-center py-3"><i class="ri-error-warning-line me-2"></i><small class="text-danger">' +
                        errorMessage +
                        '</small><br><button class="btn btn-sm btn-outline-primary mt-2" onclick="loadMoreMessages()">Retry</button></div></li>';
                    $('#mail-messages-container').append(errorHtml);

                    // Reset page counter
                    currentPage--;
                },
                complete: function() {
                    isLoading = false;
                }
            });
        }

        function starMessage(messageId, button, type = null) {
            $.ajax({
                url: "{{ route('admin.mailbox.star') }}",
                type: "POST",
                data: {
                    message_id: messageId,
                    _token: "{{ csrf_token() }}",
                },
                success: function(response) {
                    if (!type) {
                        if (button.classList.contains('true')) {
                            button.classList.remove('true');
                        } else {
                            button.classList.add('true');
                        }
                    } else {
                        if (button.querySelector('i').classList.contains('ri-star-fill')) {
                            button.querySelector('i').classList.remove('ri-star-fill');
                            button.querySelector('i').classList.add('ri-star-line');
                            button.querySelector('i').classList.remove('text-warning');
                        } else {
                            button.querySelector('i').classList.remove('ri-star-line');
                            button.querySelector('i').classList.add('ri-star-fill');
                            button.querySelector('i').classList.add('text-warning');
                        }
                    }
                },
                error: function() {
                    // Show error toast or notification
                    console.error('Failed to star message');
                }
            });
        }

        function importantMessage(messageId, button) {
            $.ajax({
                url: "{{ route('admin.mailbox.important') }}",
                type: "POST",
                data: {
                    message_id: messageId,
                    _token: "{{ csrf_token() }}",
                },
                success: function(response) {
                    if (button.querySelector('i').classList.contains('ri-bookmark-fill')) {
                        button.querySelector('i').classList.remove('ri-bookmark-fill');
                        button.querySelector('i').classList.add('ri-bookmark-line');
                        button.querySelector('i').classList.remove('text-secondary');
                    } else {
                        button.querySelector('i').classList.remove('ri-bookmark-line');
                        button.querySelector('i').classList.add('ri-bookmark-fill');
                        button.querySelector('i').classList.add('text-secondary');
                    }
                },
                error: function() {
                    // Show error toast or notification
                    console.error('Failed to mark message as important');
                }
            });
        }
        function archiveMessage(messageId, button) {
            $.ajax({
                url: "{{ route('admin.mailbox.archive') }}",
                type: "POST",
                data: {
                    message_id: messageId,
                    _token: "{{ csrf_token() }}",
                },
                success: function(response) {
                    if (button.querySelector('i').classList.contains('ri-inbox-archive-fill')) {
                        button.querySelector('i').classList.remove('ri-inbox-archive-fill');
                        button.querySelector('i').classList.add('ri-inbox-archive-line');
                        button.querySelector('i').classList.remove('text-success');
                    } else {
                        button.querySelector('i').classList.remove('ri-inbox-archive-line');
                        button.querySelector('i').classList.add('ri-inbox-archive-fill');
                        button.querySelector('i').classList.add('text-success');
                    }
                },
                error: function() {
                    // Show error toast or notification
                    console.error('Failed to archive message');
                }
            });
        }
    </script>
@endsection
