@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.donationManagement.title'), 'url' => '#'],
            ['title' => trans('global.edit') . ' ' . trans('cruds.project.title_singular'), 'url' => '#'],
        ];
    @endphp
    @include('partials.breadcrumb')

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.projects.update', $project->id) }}">
                @csrf
                @method('PUT')
                <div class="row">
                    @include('utilities.form.text', [
                        'name' => 'name',
                        'label' => 'cruds.project.fields.name',
                        'isRequired' => true,
                        'grid' => 'col-md-6',
                        'value' => $project->name,
                    ])

                    @include('utilities.form.text', [
                        'name' => 'target_amount',
                        'label' => 'cruds.project.fields.target_amount',
                        'type' => 'number',
                        'isRequired' => true,
                        'grid' => 'col-md-6',
                        'attributes' => 'step="0.01"',
                        'value' => $project->target_amount,
                    ])

                    @include('utilities.form.textarea', [
                        'name' => 'description',
                        'label' => 'cruds.project.fields.description',
                        'isRequired' => false,
                        'grid' => 'col-md-12',
                        'value' => $project->description,
                    ])
                </div>

                <div class="mt-3">
                    <button class="btn btn-primary" type="submit">
                        {{ trans('global.save') }}
                    </button>
                    <a class="btn btn-secondary" href="{{ route('admin.projects.index') }}">
                        {{ trans('global.cancel') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection

