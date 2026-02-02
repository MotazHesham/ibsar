@php
    $sender = $message->sender;
    $receiver = $message->receiver();
    $isSender = $message->sender_id == auth()->id();
    $participant = $message->participants->firstWhere('user_id', auth()->id());
@endphp
<!-- Start::mail information offcanvas -->
<div class="mail-info-header d-flex flex-wrap gap-2 align-items-center offcanvas-header border-bottom">
    @if ($isSender)
        @if ($receiver)
            @include('utilities.user-avatar', ['user' => $receiver, 'class' => 'avatar-md'])
        @else
            <span class="avatar avatar-md me-2 avatar-rounded mail-msg-avatar">
                <img src="{{ asset('assets/images/faces/12.jpg') }}" alt="">
            </span>
        @endif
    @else
        @if ($sender)
            @include('utilities.user-avatar', ['user' => $sender, 'class' => 'avatar-md'])
        @else
            <span class="avatar avatar-md me-2 avatar-rounded mail-msg-avatar">
                <img src="{{ asset('assets/images/faces/12.jpg') }}" alt="">
            </span>
        @endif
    @endif
    <div class="flex-fill">
        <h6 class="mb-0 fw-medium">{{ $isSender ? $receiver->name : $sender->name }}</h6>
        <span class="text-muted fs-11">{{ $isSender ? $receiver->email : $sender->email }}</span>
    </div>
    <div class="mail-action-icons">
        <button class="btn btn-icon btn-outline-light border" onclick="starMessage({{ $message->id }},this,'ajax')"
            onmouseover="initTooltip(this)" title="Starred">
            <i class="lh-1 {{ $participant->is_starred ? 'ri-star-fill text-warning' : 'ri-star-line' }}"></i>
        </button>
        <button class="btn btn-icon btn-outline-light border" onclick="importantMessage({{ $message->id }},this)"
            onmouseover="initTooltip(this)" title="Important">
            <i
                class="lh-1 {{ $participant->is_important ? 'ri-bookmark-fill text-secondary' : 'ri-bookmark-line' }}"></i>
        </button>
        <button class="btn btn-icon btn-outline-light border ms-1" onmouseover="initTooltip(this)"
            onclick="addToArchive('{{ $participant->id }}', 'MessageParticipant')" title="Archive">
            <i
                class="lh-1 {{ $participant->is_archived ? 'ri-inbox-archive-fill text-success' : 'ri-inbox-archive-line' }}"></i>
        </button>
        <button class="btn btn-icon btn-outline-light border ms-1" onmouseover="initTooltip(this)" title="Delete"
            onclick="deleteRecord('{{ route('admin.mailbox.destroy', $message->id) }}', {{ $message->id }})">
            <i
                class="lh-1 {{ $participant->is_deleted ? 'ri-delete-bin-fill text-danger' : 'ri-delete-bin-line' }}"></i>
        </button>
        <button type="button" class="btn btn-icon btn-outline-light border" data-bs-dismiss="offcanvas"
            aria-label="Close"> <i class="ri-close-line lh-1"></i></button>
    </div>
    <div class="responsive-mail-action-icons">
        <div class="dropdown">
            <button class="btn btn-icon btn-light btn-wave waves-light waves-effect waves-light" type="button"
                data-bs-toggle="dropdown" aria-expanded="false">
                <i class="ti ti-dots-vertical"></i>
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="javascript:void(0);"
                        onclick="starMessage({{ $message->id }},this,'ajax')"><i
                            class="lh-1 {{ $participant->is_starred ? 'ri-star-fill text-warning' : 'ri-star-line' }}"></i>Starred</a>
                </li>
                <li><a class="dropdown-item" href="javascript:void(0);"
                        onclick="importantMessage({{ $message->id }},this)"><i
                            class="lh-1 {{ $participant->is_important ? 'ri-bookmark-fill text-secondary' : 'ri-bookmark-line' }}"></i>Important</a>
                </li>
                <li><a class="dropdown-item" href="javascript:void(0);"
                        onclick="archiveMessage({{ $message->id }},this)"><i
                            class="lh-1 {{ $participant->is_archived ? 'ri-inbox-archive-fill text-success' : 'ri-inbox-archive-line' }}"></i>Archive</a>
                </li>
                <li><a class="dropdown-item" href="javascript:void(0);"
                        onclick="deleteRecord('{{ route('admin.mailbox.destroy', $message->id) }}', {{ $message->id }})"><i
                            class="lh-1 {{ $participant->is_deleted ? 'ri-delete-bin-fill text-danger' : 'ri-delete-bin-line' }}"></i>Delete</a>
                </li>
            </ul>
        </div>
        <button class="btn btn-icon btn-light ms-1 close-button" data-bs-dismiss="offcanvas" aria-label="Close">
            <i class="ri-close-line"></i>
        </button>
    </div>
</div>
<div class="offcanvas-body p-0">
    <div class="mails-information">
        <div class="mail-info-body p-3" id="mail-info-body">
            <div class="d-sm-flex d-block align-items-center justify-content-between mb-3">
                <div>
                    <p class="fs-20 fw-medium mb-0">{{ $message->subject }}</p>
                </div>
                <div class="float-end">
                    <span class="fs-12 text-muted">{{ $message->created_at->format('M-d-Y,h:iA') }}</span>
                </div>
            </div>
            <div class="main-mail-content mb-3">
                {!! $message->content !!}
            </div>
            @if ($message->attachments->count() > 0)
                <div class="mail-attachments mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="mb-2">
                            <span class="fs-14 fw-medium"><i class="ri-attachment-2 me-1 align-middle"></i>
                                {{ trans('cruds.mailbox.fields.attachments') }}:
                            </span>
                        </div>
                    </div>
                    <div class="mt-2 d-flex flex-wrap gap-3">
                        @foreach ($message->attachments as $attachment)
                            <div class="d-flex align-items-center flex-wrap gap-3 border p-2 rounded-2">
                                <div class="me-1 lh-1">
                                    <span class="avatar avatar-md bg-primary">
                                        <i class="ri-file-pdf-2-line fs-18"></i>
                                    </span>
                                </div>
                                <div class="flex-fill">
                                    <a href="javascript:void(0);">
                                        <p class="mb-1 fs-12 fw-medium">
                                            {{ $attachment->name }}
                                        </p>
                                    </a>
                                    <div class="fs-12 text-muted text-wrap">{{ formatFileSize($attachment->size) }}
                                    </div>
                                </div>
                                <div class="ms-auto btn btn-sm btn-success-light rounded-circle btn-icon">
                                    <i class="ri-download-line"></i>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
            <div class="mb-3">
                <span class="fs-14 fw-medium"><i
                        class="ri-reply-all-line me-1 align-middle d-inline-block"></i>{{ trans('cruds.mailbox.fields.replies') }}:</span>
            </div>
            <div class="mail-replies">
                @foreach ($replies as $reply)
                    @php
                        $replySender = $reply->sender;
                        $isReplyFromMe = $reply->sender_id == auth()->id();
                    @endphp
                    <div class="mail-reply-item mb-3 p-3 border rounded-3 {{ $isReplyFromMe ? 'bg-light-primary' : 'bg-light' }}">
                        <div class="d-flex align-items-start gap-3">
                            <div class="flex-shrink-0">
                                @if ($replySender)
                                    @include('utilities.user-avatar', ['user' => $replySender, 'class' => 'avatar-sm'])
                                @else
                                    <span class="avatar avatar-sm avatar-rounded">
                                        <img src="{{ asset('assets/images/faces/12.jpg') }}" alt="">
                                    </span>
                                @endif
                            </div>
                            <div class="flex-fill">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="mb-0 fw-medium fs-14">
                                            {{ $replySender ? $replySender->name : 'Unknown User' }}
                                        </h6>
                                        <span class="text-muted fs-12">
                                            {{ $replySender ? $replySender->email : 'No email' }}
                                        </span>
                                    </div>
                                    <div class="text-end"> 
                                        <div class="text-muted fs-11 mt-1">
                                            {{ $reply->created_at->format('M d, Y h:i A') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="mail-reply-content">
                                    {!! $reply->content !!}
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mail-reply mb-3">
                <form action="{{ route(auth()->user()->is_beneficiary ? 'beneficiary.mailbox.reply' : 'admin.mailbox.reply') }}" method="POST">
                    @csrf
                    <input type="hidden" name="message_id" value="{{ $message->id }}">
                    <div class="row">
                        @include('utilities.form.textarea-ajax', [
                            'name' => 'content',
                            'id' => 'replycontent',
                            'label' => 'cruds.mailbox.fields.reply',
                            'isRequired' => true,
                            'editor' => true,
                            'grid' => 'col-md-12',
                        ])
                    </div>
                    <button type="submit" class="btn btn-primary mt-2 mb-3">{{ trans('global.send') }}</button>
                </form>
            </div>
        </div> 
    </div>
</div>
<!-- End::mail information offcanvas -->
