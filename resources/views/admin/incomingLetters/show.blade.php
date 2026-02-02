@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.lettersManagment.title'), 'url' => '#'],
            [
                'title' => trans('global.list') . ' ' . trans('cruds.incomingLetter.title'),
                'url' => route('admin.incoming-letters.index'),
            ],
            ['title' => trans('global.show') . ' ' . trans('cruds.incomingLetter.title_singular'), 'url' => '#'],
        ];
    @endphp
    @include('partials.breadcrumb')
    <div class="card">
        <div class="card-header p-3">
            <h6 class="card-title">
                {{ trans('global.show') }} {{ trans('cruds.incomingLetter.title') }}
            </h6>
        </div>

        <div class="card-body">
            <div class="form-group">
                <div class="form-group">
                    <a class="btn btn-light mt-3 mb-3" href="{{ route('admin.incoming-letters.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
                <table class="table table-bordered table-striped">
                    <tbody>
                        <tr>
                            <th>
                                {{ trans('cruds.incomingLetter.fields.id') }}
                            </th>
                            <td>
                                {{ $incomingLetter->id }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.incomingLetter.fields.letter_number') }}
                            </th>
                            <td>
                                {{ $incomingLetter->letter_number }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.incomingLetter.fields.external_number') }}
                            </th>
                            <td>
                                {{ $incomingLetter->external_number }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.incomingLetter.fields.letter_date') }}
                            </th>
                            <td>
                                {{ $incomingLetter->letter_date }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.incomingLetter.fields.received_date') }}
                            </th>
                            <td>
                                {{ $incomingLetter->received_date }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.incomingLetter.fields.recevier') }}
                            </th>
                            <td>
                                {{ $incomingLetter->recevier->name ?? '' }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.incomingLetter.fields.subject') }}
                            </th>
                            <td>
                                {{ $incomingLetter->subject }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.incomingLetter.fields.letter') }}
                            </th>
                            <td>
                                {!! $incomingLetter->letter !!}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.incomingLetter.fields.priority') }}
                            </th>
                            <td>
                                {{ App\Models\IncomingLetter::PRIORITY_SELECT[$incomingLetter->priority] ?? '' }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.incomingLetter.fields.incoming_type') }}
                            </th>
                            <td>
                                {{ App\Models\IncomingLetter::INCOMING_TYPE_SELECT[$incomingLetter->incoming_type] ?? '' }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.incomingLetter.fields.letter_organization') }}
                            </th>
                            <td>
                                {{ $incomingLetter->letter_organization->name ?? '' }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.incomingLetter.fields.attachments') }}
                            </th>
                            <td>
                                @foreach ($incomingLetter->attachments as $key => $media)
                                    <a href="{{ $media->getUrl() }}" class="btn btn-primary btn-sm" target="_blank">
                                        {{ trans('global.view_file') }}
                                    </a>
                                @endforeach
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.incomingLetter.fields.note') }}
                            </th>
                            <td>
                                {{ $incomingLetter->note }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.incomingLetter.fields.outgoing_letter') }}
                            </th>
                            <td>
                                {{ $incomingLetter->outgoing_letter->letter_number ?? '' }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.incomingLetter.fields.is_archived') }}
                            </th>
                            <td>
                                <input type="checkbox" disabled="disabled"
                                    {{ $incomingLetter->is_archived ? 'checked' : '' }}>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.incomingLetter.fields.created_by') }}
                            </th>
                            <td>
                                {{ $incomingLetter->created_by->name ?? '' }}
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="form-group">
                    <a class="btn btn-light mt-3 mb-3" href="{{ route('admin.incoming-letters.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
