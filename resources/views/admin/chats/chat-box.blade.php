@if (isset($conversation) && isset($otherParticipant))
    <div class="d-flex align-items-center border-bottom main-chat-head flex-wrap">
        @include('utilities.user-avatar-2', ['user' => $otherParticipant, 'class' => 'me-2 chatstatusperson lh-1']) 
        <div class="flex-fill">
            <p class="mb-0 fw-medium fs-14 lh-1">
                <a href="javascript:void(0);" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight"
                    aria-controls="offcanvasRight" class="chatnameperson responsive-userinfo-open">
                    {{ $otherParticipant->name }}
                </a>
            </p>
            <p class="text-muted mb-0 chatpersonstatus">{{ $otherParticipant->status ?? '' }}</p>
        </div> 
        <div class="d-flex flex-wrap rightIcons gap-2"> 
            <button aria-label="button" type="button" class="btn btn-icon btn-primary-light my-0 responsive-chat-close btn-sm" 
                onclick="document.querySelector('.main-chart-wrapper').classList.remove('responsive-chat-open')">
                <i class="ri-close-line"></i>
            </button>
        </div>
    </div>
    <div class="chat-content" id="main-chat-content">
        <div id="loading-indicator" class="d-none text-center py-3">
            <div class="spinner-border spinner-border-sm me-2" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <span class="text-muted">Loading more messages...</span>
        </div>
        <ul class="list-unstyled" id="messages-list"> 
            @foreach($conversation->messages->reverse() as $message)
                @include('admin.chats.message', ['message' => $message, 'otherParticipant' => $otherParticipant, 'authUser' => auth()->user()])
            @endforeach
        </ul>
    </div>
    
    <div class="chat-footer">
        <a aria-label="anchor" class="btn btn-success-light me-2 btn-icon btn-send" href="javascript:void(0)" data-bs-toggle="offcanvas"
        data-bs-target="#offcanvasAttachment">
            <i class="ri-attachment-2"></i>
        </a>
        <a aria-label="anchor" class="btn btn-icon me-2 btn-info emoji-picker" href="javascript:void(0)">
            <i class="ri-emotion-line"></i>
        </a>
        <input class="form-control chat-message-space" placeholder="{{ trans('cruds.chat.extra.type_your_message') }}" type="text" id="message-input" 
        onkeydown="if(event.key === 'Enter') sendMessage({{ $conversation->id }})" onclick="markAsRead({{ $conversation->id }})">
        <a aria-label="anchor" class="btn btn-primary ms-2 btn-icon btn-send" href="javascript:void(0)" onclick="sendMessage({{ $conversation->id }})">
            <i class="ri-send-plane-2-line"></i>
        </a>
    </div>
@endif
