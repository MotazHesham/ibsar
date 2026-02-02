@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.lettersManagment.title'), 'url' => '#'],
            [
                'title' => trans('global.list') . ' ' . trans('cruds.incomingLetter.title'),
                'url' => route('admin.incoming-letters.index'),
            ],
            ['title' => trans('global.edit') . ' ' . trans('cruds.incomingLetter.title_singular'), 'url' => '#'],
        ];
    @endphp
    @include('partials.breadcrumb')
    <form method="POST" action="{{ route('admin.incoming-letters.update', [$incomingLetter->id]) }}"
        enctype="multipart/form-data">
        @method('PUT')
        @csrf

        <div class="row">
            <div class="col-md-4">
                <div class="card">

                    <div class="card-body">
                        <div class="row">
                            @include('utilities.form.text', [
                                'name' => 'external_number',
                                'label' => 'cruds.incomingLetter.fields.external_number',
                                'isRequired' => false,
                                'grid' => 'col-md-6',
                                'value' => $incomingLetter->external_number,
                            ])
                            @include('utilities.form.date', [
                                'name' => 'letter_date',
                                'id' => 'letter_date',
                                'label' => 'cruds.incomingLetter.fields.letter_date',
                                'isRequired' => true,
                                'grid' => 'col-md-6',
                                'value' => $incomingLetter->letter_date,
                            ])
                            @include('utilities.form.date', [
                                'name' => 'received_date',
                                'id' => 'received_date',
                                'label' => 'cruds.incomingLetter.fields.received_date',
                                'isRequired' => true,
                                'grid' => 'col-md-6',
                                'value' => $incomingLetter->received_date,
                            ])
                            @include('utilities.form.select', [
                                'name' => 'recevier_id',
                                'id' => 'recevier_id',
                                'label' => 'cruds.incomingLetter.fields.recevier',
                                'isRequired' => true,
                                'options' => $receviers,
                                'grid' => 'col-md-6',
                                'search' => true,
                                'value' => $incomingLetter->recevier_id,
                            ])
                            @include('utilities.form.select', [
                                'name' => 'priority',
                                'id' => 'priority',
                                'label' => 'cruds.incomingLetter.fields.priority',
                                'isRequired' => true,
                                'options' => App\Models\IncomingLetter::PRIORITY_SELECT,
                                'grid' => 'col-md-6',
                                'value' => $incomingLetter->priority,
                            ])
                            @include('utilities.form.select', [
                                'name' => 'incoming_type',
                                'id' => 'incoming_type',
                                'label' => 'cruds.incomingLetter.fields.incoming_type',
                                'isRequired' => true,
                                'options' => App\Models\IncomingLetter::INCOMING_TYPE_SELECT,
                                'grid' => 'col-md-6',
                                'value' => $incomingLetter->incoming_type,
                            ])
                            @include('utilities.form.select', [
                                'name' => 'letter_organization_id',
                                'id' => 'letter_organization_id',
                                'label' => 'cruds.incomingLetter.fields.letter_organization',
                                'isRequired' => false,
                                'options' => $letter_organizations,
                                'grid' => 'col-md-12',
                                'value' => $incomingLetter->letter_organization_id,
                            ])
                            <div class="col-md-12">
                                <div class="row">
                                    @include('utilities.form.select', [
                                        'name' => 'has_outgoing_letter',
                                        'label' => 'cruds.incomingLetter.fields.has_outgoing_letter',
                                        'isRequired' => true,
                                        'options' => [
                                            '' => trans('global.pleaseSelect'),
                                            '1' => trans('global.yes'),
                                            '0' => trans('global.no'),
                                        ],
                                        'grid' => 'col',
                                        'value' => $incomingLetter->outgoing_letter_id ? 1 : 0,
                                    ])
                                    <div id="outgoing_letter_wrapper" style="display: none;" class="col">
                                        @include('utilities.form.select', [
                                            'name' => 'outgoing_letter_id',
                                            'id' => 'outgoing_letter_id',
                                            'label' => 'cruds.incomingLetter.fields.outgoing_letter',
                                            'isRequired' => false,
                                            'options' => $outgoing_letters,
                                            'value' => $incomingLetter->outgoing_letter_id,
                                        ])
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            @include('utilities.form.text', [
                                'name' => 'subject',
                                'id' => 'subject',
                                'label' => 'cruds.incomingLetter.fields.subject',
                                'isRequired' => true,
                                'grid' => 'col-md-12',
                                'value' => $incomingLetter->subject,
                            ])
                            @include('utilities.form.textarea', [
                                'name' => 'letter',
                                'id' => 'letter',
                                'label' => 'cruds.incomingLetter.fields.letter',
                                'isRequired' => true,
                                'grid' => 'col-md-12',
                                'editor' => true,
                                'value' => $incomingLetter->letter,
                            ])
                            @include('utilities.form.textarea', [
                                'name' => 'note',
                                'id' => 'note',
                                'label' => 'cruds.incomingLetter.fields.note',
                                'grid' => 'col-md-6',
                                'isRequired' => false,
                                'value' => $incomingLetter->note,
                            ])
                            @include('utilities.form.dropzone-multiple', [
                                'name' => 'attachments',
                                'id' => 'attachments',
                                'label' => 'cruds.incomingLetter.fields.attachments',
                                'grid' => 'col-md-6',
                                'isRequired' => false,
                                'url' => route('admin.incoming-letters.storeMedia'),
                                'model' => $incomingLetter,
                            ])
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="btn-list">
            <div class="d-grid gap-2 col-6 mx-auto">
                <button class="btn btn-primary rounded-pill btn-lg btn-wave" type="submit">
                    {{ trans('global.save') }}
                </button>
            </div>
        </div>
    </form>
@endsection

@section('scripts')
    @parent

    <script>
        handleOutgoingLetterToggle({{ $incomingLetter->outgoing_letter_id ? 1 : 0 }});

        /* Health Condition Toggle */
        function handleOutgoingLetterToggle(value) {
            var OutgoingLetterWrapper = document.getElementById('outgoing_letter_wrapper');
            var CustomOutgoingLetterWrapper = document.getElementById('custom_outgoing_letter_wrapper');

            if (value === '1') {
                OutgoingLetterWrapper.style.display = 'block';
            } else {
                OutgoingLetterWrapper.style.display = 'none';
                document.getElementById('outgoing_letter_id').value = '';
            }
        }

        var HasOutgoingLetterSelect = document.getElementById('has_outgoing_letter');

        if (HasOutgoingLetterSelect) {
            HasOutgoingLetterSelect.addEventListener('change', function() {
                handleOutgoingLetterToggle(this.value);
            });
        }
    </script>
@endsection
