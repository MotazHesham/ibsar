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
                'title' => trans('global.edit') . ' ' . trans('cruds.faqQuestion.title_singular'),
                'url' => '#',
            ],
        ];
    @endphp
    @include('partials.breadcrumb')
    <div class="card">
        <div class="card-header p-3">
            <h6 class="card-title">
                {{ trans('global.edit') }} {{ trans('cruds.faqQuestion.title_singular') }}
            </h6>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.faq-questions.update', [$faqQuestion->id]) }}"
                enctype="multipart/form-data">
                @method('PUT')
                @csrf
                @include('utilities.form.select', [
                    'name' => 'category_id',
                    'label' => 'cruds.faqQuestion.fields.category',
                    'isRequired' => true,
                    'options' => $categories,
                    'value' => $faqQuestion->category_id,
                ])
                @include('utilities.form.textarea', [
                    'name' => 'question',
                    'label' => 'cruds.faqQuestion.fields.question',
                    'isRequired' => true,
                    'value' => $faqQuestion->question,
                ])
                @include('utilities.form.textarea', [
                    'name' => 'answer',
                    'label' => 'cruds.faqQuestion.fields.answer',
                    'isRequired' => true,
                    'value' => $faqQuestion->answer,
                ])
                <div class="form-group">
                    <button class="btn btn-primary-light rounded-pill btn-wave" type="submit">
                        {{ trans('global.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
