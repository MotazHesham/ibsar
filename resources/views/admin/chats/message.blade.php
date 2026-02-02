@if ($message->user_id == $otherParticipant->id)
    <li class="chat-item-start">
        <div class="chat-list-inner">
            <div class="chat-user-profile">
                @include('utilities.user-avatar-2', [
                    'user' => $otherParticipant,
                    'class' => 'chatstatusperson',
                ])
            </div>
            <div class="ms-3">
                <div class="main-chat-msg">
                    <div>
                        <p class="mb-0">{!! $message->message !!}</p>

                        <!-- File Attachments -->
                        @if ($message->attachments && $message->attachments->count() > 0)
                            <div class="chat-attachments mt-2">
                                @foreach ($message->attachments as $attachment)
                                    <div class="attachment-item d-inline-block me-2 mb-1">
                                        @if (in_array(strtolower($attachment->mime_type), ['image/jpg', 'image/jpeg', 'image/png', 'image/gif', 'image/webp']))
                                            <!-- Image Preview -->
                                            <a href="{{ $attachment->getUrl() }}" target="_blank" class="attachment-link">
                                                <img src="{{ $attachment->getUrl('preview') }}"
                                                    alt="{{ $attachment->name }}" class="attachment-preview"
                                                    style="max-width: 100px; max-height: 100px; border-radius: 4px;">
                                            </a>
                                        @else
                                            <!-- File Icon -->
                                            <a href="{{ $attachment->getUrl() }}" target="_blank"
                                                class="attachment-link">
                                                <div class="attachment-file d-flex align-items-center p-2 bg-light rounded"
                                                    style="min-width: 200px;">
                                                    <i class="ri-file-line me-2 fs-4 text-muted"></i>
                                                    <div class="flex-fill">
                                                        <div class="fw-medium text-truncate">{{ $attachment->name }}
                                                        </div>
                                                        <small
                                                            class="text-muted">{{ number_format($attachment->size / 1024, 1) }}
                                                            KB</small>
                                                    </div>
                                                    <i class="ri-download-line ms-2 text-primary"></i>
                                                </div>
                                            </a>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
                <span class="chatting-user-info">
                    <span class="chatnameperson">{{ $otherParticipant->name }}</span>
                    <span
                        class="msg-sent-time">{{ $message->created_at ? $message->created_at->format('h:i a') : '' }}</span>
                </span>
            </div>
        </div>
    </li>
@else
    <li class="chat-item-end">
        <div class="chat-list-inner">
            <div class="me-3">
                <div class="main-chat-msg">
                    <div>
                        <p class="mb-0">{!! $message->message !!}</p>

                        <!-- File Attachments -->
                        @if ($message->attachments && $message->attachments->count() > 0)
                            <div class="chat-attachments mt-2">
                                @foreach ($message->attachments as $attachment)
                                    <div class="attachment-item d-inline-block me-2 mb-1">
                                        @if (in_array(strtolower($attachment->mime_type), ['image/jpg', 'image/jpeg', 'image/png', 'image/gif', 'image/webp']))
                                            <!-- Image Preview -->
                                            <a href="{{ $attachment->getUrl() }}" target="_blank"
                                                class="attachment-link">
                                                <img src="{{ $attachment->getUrl('preview') }}"
                                                    alt="{{ $attachment->name }}" class="attachment-preview"
                                                    style="max-width: 100px; max-height: 100px; border-radius: 4px;">
                                            </a>
                                        @else
                                            <!-- File Icon -->
                                            <a href="{{ $attachment->getUrl() }}" target="_blank"
                                                class="attachment-link">
                                                <div class="attachment-file d-flex align-items-center p-2 bg-light rounded"
                                                    style="min-width: 200px;">
                                                    <i class="ri-file-line me-2 fs-4 text-muted"></i>
                                                    <div class="flex-fill">
                                                        <div class="fw-medium text-truncate">{{ $attachment->name }}
                                                        </div>
                                                        <small
                                                            class="text-muted">{{ number_format($attachment->size / 1024, 1) }}
                                                            KB</small>
                                                    </div>
                                                    <i class="ri-download-line ms-2 text-primary"></i>
                                                </div>
                                            </a>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
                <span class="chatting-user-info">
                    <span class="msg-sent-time">
                        <span class="chat-read-mark align-middle d-inline-flex">
                            <i class="ri-check-double-line seen-mark @if(!$message->is_seen) text-muted @endif" @if($message->is_seen) title="seen at: {{ $message->seen_at }}" @endif></i>
                        </span>{{ $message->created_at ? $message->created_at->format('h:i a') : '' }}
                    </span>
                    {{ trans('cruds.chat.extra.you') }}
                </span>
            </div>
            <div class="chat-user-profile">
                @include('utilities.user-avatar-2', ['user' => $authUser, 'class' => 'chatstatusperson'])
            </div>
        </div>
    </li>
@endif
