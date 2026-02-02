@if($messages->count() > 0)
    @foreach ($messages as $message)
        @php
            $sender = $message->sender;
            $receiver = $message->receiver();
            $isSender = $message->sender_id == auth()->id();
            $participant = $message->participants->firstWhere('user_id', auth()->id());
        @endphp
        <li class="@if ($participant->is_read == false) active @endif">
            <div class="d-flex align-items-top">
                <div class="me-1 lh-1">
                    @if ($isSender)
                        @if ($receiver)
                            @include('utilities.user-avatar', [
                                'user' => $receiver,
                                'class' => 'avatar-md',
                            ])
                        @else
                            <span class="avatar avatar-md me-2 avatar-rounded mail-msg-avatar">
                                <img src="{{ asset('assets/images/faces/12.jpg') }}" alt="">
                            </span>
                        @endif
                    @else
                        @if ($sender)
                            @include('utilities.user-avatar', [
                                'user' => $sender,
                                'class' => 'avatar-md',
                            ])
                        @else
                            <span class="avatar avatar-md me-2 avatar-rounded mail-msg-avatar">
                                <img src="{{ asset('assets/images/faces/12.jpg') }}" alt="">
                            </span>
                        @endif
                    @endif
                </div>
                <div class="flex-fill text-truncate">
                    <a href="javascript:void(0)"
                        onclick="showAjaxOffcanvas('{{ route('admin.mailbox.show') }}',{id: {{ $message->id }}}, 'right', 'mail-show-offcanvas')">
                        <p class="mb-1 fs-12 fw-medium p-2">
                            @if ($isSender)
                                {{ $receiver->name ?? 'Unknown' }}
                            @else
                                {{ $sender->name ?? 'Unknown' }}
                            @endif
                            <span class="float-end text-muted fw-normal fs-11">
                                <span class="me-2"><i
                                        class="ri-attachment-2 align-middle fs-12"></i></span>
                                <span data-bs-toggle="tooltip"
                                    title="{{ $message->updated_at->format('Y-m-d H:i:s') }}">
                                    {{ $message->updated_at->diffForHumans() }}
                                </span>
                            </span>
                        </p>
                    </a>
                    <div class="mail-msg mb-0">
                        <span
                            class="d-block mb-0 fw-medium text-truncate w-75">{{ $message->subject }}</span>
                        <button
                            class="btn p-0 lh-1 mail-starred @if ($participant->is_starred) true @endif border-0 float-end"
                            onclick="starMessage({{ $message->id }},this)">
                            <i class="ri-star-fill fs-14"></i>
                        </button>
                        <div class="fs-12 text-muted w-75 text-truncate mail-msg-content">
                            {{ Str::limit(strip_tags($message->content), 100) }}
                        </div>
                    </div>
                </div>
            </div>
        </li>
    @endforeach
@else
    <li class="text-center py-5">
        <div class="text-muted">
            <i class="ri-inbox-line fs-48 mb-3 d-block"></i>
            <h6 class="mb-2">{{ trans('cruds.mailbox.no_messages') }}</h6>
            <p class="mb-0 fs-12">{{ trans('cruds.mailbox.no_messages_description') }}</p>
        </div>
    </li>
@endif 