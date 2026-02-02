@foreach ($conversations as $conversation)
    @php
        $lastMessage = $conversation->lastMessage;
        $otherParticipant = $conversation->other_user;
    @endphp
    <li class="@if ($conversation->pivot->unread_count > 0) chat-msg-unread @endif checkforactive"
        data-conversation-id="{{ $conversation->id }}">
        <a href="javascript:void(0);" onclick="loadChat({{ $conversation->id }})">
            <div class="d-flex align-items-top">
                <div class="me-1 lh-1">
                    @include('utilities.user-avatar-2', ['user' => $otherParticipant, 'class' => 'me-2'])
                </div>
                <div class="flex-fill">
                    <p class="mb-0 fw-medium">
                        {{ $otherParticipant->name }}
                        <span class="float-end text-muted fw-normal fs-11 chat-msg-time">
                            {{ $lastMessage->created_at ? $lastMessage->created_at->format('h:i a') : '' }}
                        </span>
                    </p>
                    <p class="fs-12 mb-0 chat-msg-container">
                        <span class="chat-msg text-truncate">{{ $lastMessage->message }}</span>
                        @if ($conversation->pivot->unread_count > 0)
                            <span
                                class="badge bg-info rounded-pill float-end unread-count">{{ $conversation->pivot->unread_count }}</span>
                        @endif
                        <span class="chat-read-icon float-end align-middle">
                            @if ($lastMessage->user_id == auth()->id())
                                <i class="ri-check-double-fill @if (!$lastMessage->is_seen) text-muted @endif"></i>
                            @endif
                        </span>
                    </p>
                </div>
            </div>
        </a>
    </li>
@endforeach
