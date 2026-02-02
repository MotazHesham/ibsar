@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.lettersManagment.title'), 'url' => '#'],
            [
                'title' => trans('global.list') . ' ' . trans('cruds.outgoingLetter.title'),
                'url' => route('admin.outgoing-letters.index'),
            ],
            ['title' => trans('global.edit') . ' ' . trans('cruds.outgoingLetter.title_singular'), 'url' => '#'],
        ];
    @endphp
    @include('partials.breadcrumb')
    <form method="POST" action="{{ route('admin.outgoing-letters.update', [$outgoingLetter->id]) }}"
        enctype="multipart/form-data">
        @method('PUT')
        @csrf
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            @include('utilities.form.text', [
                                'name' => 'letter_number',
                                'label' => 'cruds.outgoingLetter.fields.letter_number',
                                'isRequired' => true,
                                'grid' => 'col-md-6',
                                'value' => $outgoingLetter->letter_number,
                            ])
                            @include('utilities.form.date', [
                                'name' => 'letter_date',
                                'id' => 'letter_date',
                                'label' => 'cruds.outgoingLetter.fields.letter_date',
                                'isRequired' => true,
                                'grid' => 'col-md-6',
                                'value' => $outgoingLetter->letter_date,
                            ])
                            @include('utilities.form.date', [
                                'name' => 'delivered_date',
                                'id' => 'delivered_date',
                                'label' => 'cruds.outgoingLetter.fields.delivered_date',
                                'isRequired' => true,
                                'grid' => 'col-md-6',
                                'value' => $outgoingLetter->delivered_date,
                            ])
                            @include('utilities.form.select', [
                                'name' => 'recevier_id',
                                'id' => 'recevier_id',
                                'label' => 'cruds.outgoingLetter.fields.recevier',
                                'isRequired' => true,
                                'options' => $receviers,
                                'grid' => 'col-md-6',
                                'search' => true,
                                'value' => $outgoingLetter->recevier_id,
                            ])
                            @include('utilities.form.select', [
                                'name' => 'priority',
                                'label' => 'cruds.outgoingLetter.fields.priority',
                                'isRequired' => true,
                                'options' => App\Models\OutgoingLetter::PRIORITY_SELECT,
                                'grid' => 'col-md-6',
                                'value' => $outgoingLetter->priority,
                            ])
                            @include('utilities.form.select', [
                                'name' => 'outgoing_type',
                                'label' => 'cruds.outgoingLetter.fields.outgoing_type',
                                'isRequired' => true,
                                'options' => App\Models\OutgoingLetter::OUTGOING_TYPE_SELECT,
                                'grid' => 'col-md-6',
                                'value' => $outgoingLetter->outgoing_type,
                            ])
                            @include('utilities.form.select', [
                                'name' => 'letter_organization_id',
                                'id' => 'letter_organization_id',
                                'label' => 'cruds.outgoingLetter.fields.letter_organization',
                                'isRequired' => false,
                                'options' => $letter_organizations,
                                'grid' => 'col-md-12',
                                'value' => $outgoingLetter->letter_organization_id,
                            ])
                            <div class="col-md-12">
                                <div class="row">
                                    @include('utilities.form.select', [
                                        'name' => 'has_incoming_letter',
                                        'label' => 'cruds.outgoingLetter.fields.has_incoming_letter',
                                        'isRequired' => true,
                                        'options' => [
                                            '' => trans('global.pleaseSelect'),
                                            '1' => trans('global.yes'),
                                            '0' => trans('global.no'),
                                        ],
                                        'grid' => 'col',
                                        'value' => $outgoingLetter->incoming_letter_id ? '1' : '0',
                                    ])
                                    <div id="incoming_letter_wrapper" style="display: none;" class="col">
                                        @include('utilities.form.select', [
                                            'name' => 'incoming_letter_id',
                                            'id' => 'incoming_letter_id',
                                            'label' => 'cruds.outgoingLetter.fields.incoming_letter',
                                            'isRequired' => false,
                                            'options' => $incoming_letters,
                                            'value' => $outgoingLetter->incoming_letter_id,
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
                                'label' => 'cruds.outgoingLetter.fields.subject',
                                'isRequired' => true,
                                'grid' => 'col-md-12',
                                'value' => $outgoingLetter->subject,
                            ])
                            @include('utilities.form.textarea', [
                                'name' => 'letter',
                                'label' => 'cruds.outgoingLetter.fields.letter',
                                'isRequired' => true,
                                'grid' => 'col-md-12',
                                'editor' => true,
                                'value' => $outgoingLetter->letter,
                            ])
                            @include('utilities.form.textarea', [
                                'name' => 'note',
                                'label' => 'cruds.outgoingLetter.fields.note',
                                'isRequired' => false,
                                'grid' => 'col-md-6',
                                'value' => $outgoingLetter->note,
                            ])
                            @include('utilities.form.dropzone-multiple', [
                                'name' => 'attachments',
                                'label' => 'cruds.outgoingLetter.fields.attachments',
                                'isRequired' => false,
                                'url' => route('admin.outgoing-letters.storeMedia'),
                                'grid' => 'col-md-6',
                                'model' => $outgoingLetter,
                            ])
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <button class="btn btn-primary-light rounded-pill btn-wave" type="submit">
                {{ trans('global.update') }}
            </button>
        </div>
    </form>
@endsection

@section('scripts')
    @parent

    <script>
        handleIncomingLetterToggle({{$outgoingLetter->incoming_letter_id ? '1' : '0'}});

        /* Health Condition Toggle */
        function handleIncomingLetterToggle(value) {
            var IncomingLetterWrapper = document.getElementById('incoming_letter_wrapper');
            var CustomIncomingLetterWrapper = document.getElementById('custom_incoming_letter_wrapper');

            if (value === '1') {
                IncomingLetterWrapper.style.display = 'block';
            } else {
                IncomingLetterWrapper.style.display = 'none';
                document.getElementById('incoming_letter_id').value = '';
            }
        }

        var HasIncomingLetterSelect = document.getElementById('has_incoming_letter');

        if (HasIncomingLetterSelect) {
            HasIncomingLetterSelect.addEventListener('change', function() {
                handleIncomingLetterToggle(this.value);
            });
        }
    </script>
@endsection