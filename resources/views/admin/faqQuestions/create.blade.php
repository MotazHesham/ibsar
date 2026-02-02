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
                'title' => trans('global.create') . ' ' . trans('cruds.faqQuestion.title_singular'),
                'url' => '#',
            ],
        ];
    @endphp
    @include('partials.breadcrumb')
    <div class="card">
        <div class="card-header p-3">
            <h6 class="card-title">
                {{ trans('global.create') }} {{ trans('cruds.faqQuestion.title_singular') }}
            </h6>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.faq-questions.store') }}" enctype="multipart/form-data">
                @csrf
                @include('utilities.form.select', [
                    'name' => 'category_id',
                    'label' => 'cruds.faqQuestion.fields.category',
                    'isRequired' => true,
                    'options' => $categories,
                ]) 
                @include('utilities.form.textarea', [
                    'name' => 'question',
                    'label' => 'cruds.faqQuestion.fields.question',
                    'isRequired' => true,
                ])
                @include('utilities.form.textarea', [   
                    'name' => 'answer',
                    'label' => 'cruds.faqQuestion.fields.answer',
                    'isRequired' => true,
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
