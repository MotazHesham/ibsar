@extends(auth()->user()->user_type == 'staff' ? 'layouts.master' : 'layouts.master-beneficiary')
@section('styles')
    <!-- GLightbox CSS -->
    <link rel="stylesheet" href="{{ asset('assets/libs/glightbox/css/glightbox.min.css') }}">
@endsection

@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.chat.title'), 'url' => '#'],
            [
                'title' => trans('global.list') . ' ' . trans('cruds.chat.title'),
                'url' => route('admin.chats.index'),
            ],
        ];
    @endphp
    @include('partials.breadcrumb')

    <div class="main-chart-wrapper gap-lg-2 gap-0 mb-2 d-lg-flex">
        <div class="chat-info border">
            <div class="chat-search p-3 border-bottom">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Search Chat" aria-describedby="button-addon01">
                    <button aria-label="button" class="btn btn-primary" type="button" id="button-addon01">
                        <i class="ri-search-line"></i>
                    </button>
                </div>
            </div>
            <!-- Start Chat Button -->
            @if (auth()->user()->user_type == 'staff')
                <div class="p-3 border-bottom">
                    <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal"
                        data-bs-target="#startChatModal">
                        <i class="ri-add-line me-2"></i>
                        {{ trans('cruds.chat.extra.start_chat') }}
                    </button>
                </div>
            @endif
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane show active border-0 chat-users-tab" id="users-tab-pane" role="tabpanel"
                    aria-labelledby="users-tab" tabindex="0">
                    <ul class="list-unstyled mb-0 mt-2 chat-users-tab" id="chat-msg-scroll">
                        @include('admin.chats.contacts')
                    </ul>
                </div>
            </div>
        </div>
        <div class="main-chat-area border" id="main-chat-area">
            <div id="chat-content" class="d-flex align-items-center justify-content-center h-100">
                <div class="text-center">
                    <i class="ri-message-3-line fs-1 text-muted mb-3"></i>
                    <h5 class="text-muted">{{ trans('cruds.chat.extra.select_a_chat_to_start_conversation') }}</h5>
                </div>
            </div>
            <div id="chat-loading" class="d-none d-flex align-items-center justify-content-center h-100">
                <div class="text-center">
                    <div class="spinner-border text-primary mb-3" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <h6 class="text-muted">Loading conversation...</h6>
                </div>
            </div>
        </div>

    </div>

    <div class="offcanvas offcanvas-bottom" tabindex="-1" id="offcanvasAttachment"
        aria-labelledby="offcanvasAttachmentLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasAttachmentLabel">

            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body small">
            @include('utilities.form.dropzone-multiple', [
                'name' => 'attachments',
                'id' => 'attachments',
                'label' => 'cruds.chat.fields.attachment',
                'isRequired' => false,
                'url' => route('admin.chats.storeMedia'),
            ])
        </div>
    </div>

    <!-- Start Chat Modal -->
    <div class="modal fade" id="startChatModal" tabindex="-1" aria-labelledby="startChatModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('admin.chats.start-chat') }}" method="post">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="startChatModalLabel">{{ trans('cruds.chat.extra.start_chat') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="chatRecipient"
                                class="form-label required">{{ trans('cruds.chat.extra.select_recipient') }}</label>
                            <select class="form-select" name="user_id" id="chatRecipient" required>
                                <option value="">{{ trans('cruds.chat.extra.choose_someone_to_chat_with') }}</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="initialMessage"
                                class="form-label required">{{ trans('cruds.chat.extra.initial_message') }}</label>
                            <textarea class="form-control" name="message" id="initialMessage" rows="3" required
                                placeholder="{{ trans('cruds.chat.extra.type_your_first_message') }}"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">{{ trans('cruds.chat.extra.cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ trans('cruds.chat.extra.start_chat') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- Emojji Picker JS -->
    <script src="{{ asset('assets/libs/fg-emoji-picker/fgEmojiPicker.js') }}"></script>

    <!-- Gallery JS -->
    <script src="{{ asset('assets/libs/glightbox/js/glightbox.min.js') }}"></script>

    <!-- Chat JS -->
    <script src="{{ asset('assets/js/chat.js') }}"></script>

    <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
    <script>
        var pusher = new Pusher('{{ getSetting('pusher_key') }}', {
            cluster: '{{ getSetting('pusher_cluster') }}'
        });
    </script>
    <script>
        @foreach ($conversations as $conversation)
            var channel = pusher.subscribe('conversation.{{ $conversation->id }}');
            channel.bind('App\\Events\\ConversationEvent', function(data) {
                // Update the conversation list to show new message
                updateConversationList(data.conversation_id, data.message, data.time, data.user_id);

                // If this conversation is currently active, add the message to the chat
                if (data.user_id != {{ auth()->id() }}) {
                    if (activeConversationId == data.conversation_id) {
                        addMessageToChat(data.message_html);
                    } else {
                        // Show notification
                        showMessageNotification(data);
                    }
                }
            });

            var channelSeen = pusher.subscribe('conversation-seen.{{ $conversation->id }}');
            channelSeen.bind('App\\Events\\ConversationSeenEvent', function(data) {
                var conversationItem = document.querySelector(
                    `[data-conversation-id="${data.conversation_id}"]`);
                if (activeConversationId == data.conversation_id && {{ auth()->id() }} != data.user_id) {
                    // Update conversation list item
                    if (conversationItem) {
                        conversationItem.classList.remove('chat-msg-unread');
                    }

                    // Update read marks in the active chat messages
                    var mainChatContent = document.getElementById('main-chat-content');
                    if (mainChatContent) {
                        var readMarkIcons = mainChatContent.querySelectorAll('.seen-mark.text-muted');
                        readMarkIcons.forEach(icon => {
                            icon.classList.remove('text-muted');
                        });
                    }
                }
                if ({{ auth()->id() }} != data.user_id) {

                    var markIcon = conversationItem.querySelector('.chat-read-icon');
                    var markIconText = markIcon.querySelector('.ri-check-double-fill');
                    if (markIconText) {
                        markIconText.classList.remove('text-muted');
                    }
                }
            });
        @endforeach
    </script>

    <script>
        function offCanvasAttachment() {
            document.getElementById('offcanvasAttachment').modal('show');
        }

        function markAsRead(conversationId) {
            const conversationItem = document.querySelector(`[data-conversation-id="${conversationId}"]`);
            const badge = conversationItem.querySelector('.unread-count');
            if (badge) {
                badge.remove();
                fetch(`{{ route('admin.chats.mark-as-read') }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        conversation_id: conversationId
                    })
                });
            }
            if (conversationItem.classList.contains('chat-msg-unread')) {
                conversationItem.classList.remove('chat-msg-unread');
            }
        }
    </script>

    <script>
        function sendMessage(conversationId) {
            var attachments = uploadedAttachmentsMap;
            var message = document.getElementById('message-input').value;
            if (message.trim() === '' && attachments.length === 0) {
                return;
            }

            // Reset the message input
            var messageInput = document.getElementById('message-input');
            messageInput.value = '';
            fetch(`{{ route('admin.chats.send-message') }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        conversation_id: conversationId,
                        message: message,
                        attachments: attachments
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        addMessageToChat(data.html);
                    } else {
                        console.error('Error sending message:', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }
    </script>

    <script>
        var activeConversationId = null;

        function loadChat(conversationId) {
            document.querySelector(".main-chart-wrapper").classList.add("responsive-chat-open")
            if (activeConversationId == conversationId) {
                return;
            }
            const mainChatArea = document.getElementById('main-chat-area');
            const conversationItem = document.querySelector(`[data-conversation-id="${conversationId}"]`);
            const badge = conversationItem.querySelector('.unread-count');

            document.querySelectorAll(".checkforactive").forEach((ele) => {
                ele.classList.remove("active")
            })
            conversationItem.classList.add('active');
            // Show loading spinner by replacing the entire content
            mainChatArea.innerHTML = `
                <div class="d-flex align-items-center justify-content-center h-100">
                    <div class="text-center">
                        <div class="spinner-border text-primary mb-3" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <h6 class="text-muted">Loading conversation...</h6>
                    </div>
                </div>
            `;

            // Make AJAX call to load conversation
            fetch(`{{ route('admin.chats.load-conversation') }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        conversation_id: conversationId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Replace entire main-chat-area content with the loaded conversation
                        mainChatArea.innerHTML = data.html;
                        activeConversationId = conversationId;
                        // Initialize SimpleBar if main-chat-content exists
                        const mainChatContent = document.getElementById('main-chat-content');
                        if (mainChatContent) {
                            const simpleBar = new SimpleBar(mainChatContent, {
                                autoHide: true
                            });
                            simpleBar.getScrollElement().scrollTop = simpleBar.getScrollElement().scrollHeight;
                        }
                        if (badge) {
                            badge.remove();
                        }
                        if (conversationItem.classList.contains('chat-msg-unread')) {
                            conversationItem.classList.remove('chat-msg-unread');
                        }
                        new FgEmojiPicker({
                            trigger: [".emoji-picker"],
                            insertInto: document.querySelector(".chat-message-space"),
                            closeButton: true,
                            position: ['top', 'right'],
                            preFetch: true,
                            dir: "/../assets/libs/fg-emoji-picker/"
                        });

                        // Initialize infinite scrolling
                        initializeInfiniteScroll(conversationId);
                    } else {
                        // Show error message
                        mainChatArea.innerHTML = `
                        <div class="d-flex align-items-center justify-content-center h-100">
                            <div class="text-center">
                                <i class="ri-error-warning-line fs-1 text-danger mb-3"></i>
                                <h5 class="text-danger">Error loading conversation</h5>
                                <p class="text-muted">${data.message || 'Something went wrong'}</p>
                            </div>
                        </div>
                    `;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    // Show error message
                    mainChatArea.innerHTML = `
                    <div class="d-flex align-items-center justify-content-center h-100">
                        <div class="text-center">
                            <i class="ri-error-warning-line fs-1 text-danger mb-3"></i>
                            <h5 class="text-danger">Error loading conversation</h5>
                            <p class="text-muted">Network error occurred</p>
                        </div>
                    </div>
                `;
                });
        }
    </script>

    <script>
        // Helper function to update conversation list with new message
        function updateConversationList(conversationId, message, time, userId) {
            // Find the conversation item in the list
            const conversationItem = document.querySelector(`[data-conversation-id="${conversationId}"]`);
            if (conversationItem) {
                // Update the last message text
                const messageText = conversationItem.querySelector('.chat-msg');
                if (messageText) {
                    messageText.textContent = message;
                }

                // Update the time
                const timeElement = conversationItem.querySelector('.chat-msg-time');
                if (timeElement) {
                    timeElement.textContent = time;
                }

                if (userId != {{ auth()->id() }}) {
                    // Add unread badge if not already present
                    const badge = conversationItem.querySelector('.unread-count');
                    if (!badge) {
                        const messageContainer = conversationItem.querySelector('.chat-msg-container');
                        if (messageContainer) {
                            const newBadge = document.createElement('span');
                            newBadge.className = 'badge bg-info rounded-pill float-end unread-count';
                            newBadge.textContent = '1';
                            messageContainer.appendChild(newBadge);
                        }
                    } else {
                        // Increment existing badge
                        const currentCount = parseInt(badge.textContent) || 0;
                        badge.textContent = currentCount + 1;
                    }
                    conversationItem.classList.add('chat-msg-unread');

                    const markIcon = conversationItem.querySelector('.chat-read-icon');
                    const markIconText = markIcon.querySelector('.ri-check-double-fill');
                    if (markIconText) {
                        markIconText.remove();
                    }
                } else {
                    var markIcon = `<span class="chat-read-icon float-end align-middle">
                                <i class="ri-check-double-fill text-muted"></i>
                            </span>`;
                    conversationItem.querySelector('.chat-read-icon').innerHTML = markIcon;
                }
                // Move the conversation to the top of the list 
                $('#chat-msg-scroll .simplebar-content').prepend(conversationItem);
            }
        }

        // Helper function to add message to active chat
        function addMessageToChat(message_html) {
            const chatContent = document.getElementById('main-chat-content');
            if (chatContent) {
                const messageList = chatContent.querySelector('ul');
                if (messageList) {

                    // Add to the end of the message list
                    messageList.insertAdjacentHTML('beforeend', message_html);

                    // Scroll to bottom
                    const simpleBar = SimpleBar.instances.get(chatContent);
                    if (simpleBar) {
                        simpleBar.getScrollElement().scrollTop = simpleBar.getScrollElement().scrollHeight;
                    }
                }
            }
        }

        // Helper function to show notification
        function showMessageNotification(data) {
            showToast('{{ trans('cruds.chat.extra.new_message') }}'.replace(':user_name', data.user_name), 'info',
                'bottom');
        }
    </script>

    <script>
        // Infinite scrolling variables
        let isLoadingMore = false;
        let hasMorePages = true;
        let currentPage = 1;
        let currentConversationId = null;

        // Function to initialize infinite scrolling for a conversation
        function initializeInfiniteScroll(conversationId) {
            currentConversationId = conversationId;
            currentPage = 1;
            hasMorePages = true;
            isLoadingMore = false;

            const mainChatContent = document.getElementById('main-chat-content');
            if (mainChatContent) {
                const simpleBar = SimpleBar.instances.get(mainChatContent);
                if (simpleBar) {
                    const scrollElement = simpleBar.getScrollElement();

                    // Remove existing scroll listener
                    scrollElement.removeEventListener('scroll', handleScroll);

                    // Add new scroll listener
                    scrollElement.addEventListener('scroll', handleScroll);
                }
            }
        }

        // Handle scroll event for infinite scrolling
        function handleScroll(event) {
            if (isLoadingMore || !hasMorePages || !currentConversationId) return;

            const scrollTop = event.target.scrollTop;

            // Check if scrolled to top (with 100px threshold for better UX)
            if (scrollTop <= 100) {
                loadMoreMessages();
            }
        }

        // Function to load more messages
        function loadMoreMessages() {
            if (isLoadingMore || !hasMorePages || !currentConversationId) return;

            isLoadingMore = true;
            currentPage++;

            // Show loading indicator
            const loadingIndicator = document.getElementById('loading-indicator');
            if (loadingIndicator) {
                loadingIndicator.classList.remove('d-none');
            }

            // Make AJAX call to load more messages
            fetch(`{{ route('admin.chats.load-more-messages') }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        conversation_id: currentConversationId,
                        page: currentPage
                    })
                })
                .then(response => response.json())
                .then(data => {
                    // Hide loading indicator
                    if (loadingIndicator) {
                        loadingIndicator.classList.add('d-none');
                    }

                    if (data.success && data.html && data.html.trim() !== '') {
                        // Insert new messages at the beginning of the list
                        const messagesList = document.getElementById('messages-list');
                        if (messagesList) {
                            messagesList.insertAdjacentHTML('afterbegin', data.html);
                        }

                        // Update pagination state
                        hasMorePages = data.hasMorePages;
                        currentPage = data.currentPage;

                        // Maintain scroll position
                        const mainChatContent = document.getElementById('main-chat-content');
                        if (mainChatContent) {
                            const simpleBar = SimpleBar.instances.get(mainChatContent);
                            if (simpleBar) {
                                const scrollElement = simpleBar.getScrollElement();
                                const newScrollHeight = scrollElement.scrollHeight;
                                const oldScrollHeight = scrollElement.scrollHeight - (data.html.match(/<li/g) || [])
                                    .length * 100; // Approximate height per message
                                scrollElement.scrollTop = newScrollHeight - oldScrollHeight;
                            }
                        }
                    } else {
                        // No more messages
                        hasMorePages = false;

                        // Show end message
                        const messagesList = document.getElementById('messages-list');
                        if (messagesList) {
                            const endMessage = `
                            <li class="text-center py-2">
                                <small class="text-muted">{{ trans('cruds.chat.extra.no_more_messages') }}</small>
                            </li>
                        `;
                            messagesList.insertAdjacentHTML('afterbegin', endMessage);
                        }
                    }
                })
                .catch(error => {
                    console.error('Error loading more messages:', error);

                    // Hide loading indicator
                    if (loadingIndicator) {
                        loadingIndicator.classList.add('d-none');
                    }

                    // Show error message
                    const messagesList = document.getElementById('messages-list');
                    if (messagesList) {
                        const errorHtml = `
                        <li class="text-center py-3">
                            <i class="ri-error-warning-line me-2"></i>
                            <small class="text-danger">Error loading messages. Please try again.</small>
                            <br><button class="btn btn-sm btn-outline-primary mt-2" onclick="loadMoreMessages()">Retry</button>
                        </li>
                    `;
                        messagesList.insertAdjacentHTML('afterbegin', errorHtml);
                    }

                    // Reset page counter
                    currentPage--;
                })
                .finally(() => {
                    isLoadingMore = false;
                });
        }
    </script>
@endsection
