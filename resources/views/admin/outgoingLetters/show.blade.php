@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.lettersManagment.title'), 'url' => '#'],
            [
                'title' => trans('global.list') . ' ' . trans('cruds.outgoingLetter.title'),
                'url' => route('admin.outgoing-letters.index'),
            ],
            ['title' => trans('global.show') . ' ' . trans('cruds.outgoingLetter.title_singular'), 'url' => '#'],
        ];
    @endphp
    @include('partials.breadcrumb')

    <div class="card">
        <div class="card-header p-3">
            <h6 class="card-title">
                {{ trans('global.show') }} {{ trans('cruds.outgoingLetter.title') }}
            </h6>
        </div>

        <div class="card-body">
            <div class="form-group">
                <div class="form-group">
                    <a class="btn btn-light mt-3 mb-3" href="{{ route('admin.outgoing-letters.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
                <table class="table table-bordered table-striped">
                    <tbody>
                        <tr>
                            <th>
                                {{ trans('cruds.outgoingLetter.fields.id') }}
                            </th>
                            <td>
                                {{ $outgoingLetter->id }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.outgoingLetter.fields.letter_number') }}
                            </th>
                            <td>
                                {{ $outgoingLetter->letter_number }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.outgoingLetter.fields.letter_date') }}
                            </th>
                            <td>
                                {{ $outgoingLetter->letter_date }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.outgoingLetter.fields.delivered_date') }}
                            </th>
                            <td>
                                {{ $outgoingLetter->delivered_date }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.outgoingLetter.fields.recevier') }}
                            </th>
                            <td>
                                {{ $outgoingLetter->recevier->name ?? '' }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.outgoingLetter.fields.subject') }}
                            </th>
                            <td>
                                {{ $outgoingLetter->subject }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.outgoingLetter.fields.letter') }}
                            </th>
                            <td>
                                {!! $outgoingLetter->letter !!}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.outgoingLetter.fields.priority') }}
                            </th>
                            <td>
                                {{ App\Models\OutgoingLetter::PRIORITY_SELECT[$outgoingLetter->priority] ?? '' }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.outgoingLetter.fields.outgoing_type') }}
                            </th>
                            <td>
                                {{ App\Models\OutgoingLetter::OUTGOING_TYPE_SELECT[$outgoingLetter->outgoing_type] ?? '' }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.outgoingLetter.fields.attachments') }}
                            </th>
                            <td>
                                @foreach ($outgoingLetter->attachments as $key => $media)
                                    <a href="{{ $media->getUrl() }}" class="btn btn-primary btn-sm" target="_blank">
                                        {{ trans('global.view_file') }}
                                    </a>
                                @endforeach
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.outgoingLetter.fields.note') }}
                            </th>
                            <td>
                                {{ $outgoingLetter->note }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.outgoingLetter.fields.incoming_letter') }}
                            </th>
                            <td>
                                {{ $outgoingLetter->incoming_letter->letter_number ?? '' }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.outgoingLetter.fields.is_archived') }}
                            </th>
                            <td>
                                <input type="checkbox" disabled="disabled"
                                    {{ $outgoingLetter->is_archived ? 'checked' : '' }}>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.outgoingLetter.fields.created_by') }}
                            </th>
                            <td>
                                {{ $outgoingLetter->created_by->name ?? '' }}
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="form-group">
                    <a class="btn btn-light mt-3 mb-3" href="{{ route('admin.outgoing-letters.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
