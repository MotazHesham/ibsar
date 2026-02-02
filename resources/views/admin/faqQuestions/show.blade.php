@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.generalSetting.title'), 'url' => '#'],
            [
                'title' => trans('global.list') . ' ' . trans('cruds.faqQuestion.title'),
                'url' => route('admin.faq-questions.index'),
            ],
            [
                'title' => trans('global.show') . ' ' . trans('cruds.faqQuestion.title_singular'),
                'url' => '#',
            ],
        ];
    @endphp
    @include('partials.breadcrumb')
    <div class="card">
        <div class="card-header p-3">
            <h6 class="card-title">
                {{ trans('global.show') }} {{ trans('cruds.faqQuestion.title') }}
            </h6>
        </div>

        <div class="card-body">
            <div class="form-group">
                <div class="form-group">
                    <a class="btn btn-light mt-3 mb-3" href="{{ route('admin.faq-questions.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
                <table class="table table-bordered table-striped">
                    <tbody>
                        <tr>
                            <th>
                                {{ trans('cruds.faqQuestion.fields.id') }}
                            </th>
                            <td>
                                {{ $faqQuestion->id }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.faqQuestion.fields.category') }}
                            </th>
                            <td>
                                {{ $faqQuestion->category->category ?? '' }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.faqQuestion.fields.question') }}
                            </th>
                            <td>
                                {{ $faqQuestion->question }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.faqQuestion.fields.answer') }}
                            </th>
                            <td>
                                {{ $faqQuestion->answer }}
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="form-group">
                    <a class="btn btn-light mt-3 mb-3" href="{{ route('admin.faq-questions.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
