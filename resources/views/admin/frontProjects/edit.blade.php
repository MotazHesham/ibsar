@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.frontendSetting.title'), 'url' => '#'],
            [
                'title' => trans('global.list') . ' ' . trans('cruds.frontProject.title'),
                'url' => route('admin.front-projects.index'),
            ],
            ['title' => trans('global.edit') . ' ' . trans('cruds.frontProject.title_singular'), 'url' => '#'],
        ];
    @endphp
    @include('partials.breadcrumb')
    <div class="card">
        <div class="card-header">
            {{ trans('global.edit') }} {{ trans('cruds.frontProject.title_singular') }}
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.front-projects.update', [$frontProject->id]) }}"
                enctype="multipart/form-data">
                @method('PUT')
                @csrf
                @include('utilities.form.dropzone', [
                    'name' => 'image',
                    'id' => 'image',
                    'label' => 'cruds.frontProject.fields.image',
                    'url' => route('admin.sliders.storeMedia'),
                    'isRequired' => true,
                    'grid' => 'col-md-12',
                    'helperBlock' => '',
                    'model' => $frontProject,
                ])
                @include('utilities.form.text', [
                    'name' => 'title',
                    'label' => 'cruds.frontProject.fields.title',
                    'isRequired' => true,
                    'grid' => 'col-md-12',
                    'value' => $frontProject->title,
                ])
                @include('utilities.form.textarea', [
                    'name' => 'description',
                    'label' => 'cruds.frontProject.fields.description',
                    'isRequired' => true,
                    'grid' => 'col-md-12',
                    'value' => $frontProject->description,
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

@section('scripts')
    <script>
        Dropzone.options.imageDropzone = {
            url: '{{ route('admin.front-projects.storeMedia') }}',
            maxFilesize: 40, // MB
            acceptedFiles: '.jpeg,.jpg,.png,.gif',
            maxFiles: 1,
            addRemoveLinks: true,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            params: {
                size: 40,
                width: 4096,
                height: 4096
            },
            success: function(file, response) {
                $('form').find('input[name="image"]').remove()
                $('form').append('<input type="hidden" name="image" value="' + response.name + '">')
            },
            removedfile: function(file) {
                file.previewElement.remove()
                if (file.status !== 'error') {
                    $('form').find('input[name="image"]').remove()
                    this.options.maxFiles = this.options.maxFiles + 1
                }
            },
            init: function() {
                @if (isset($frontProject) && $frontProject->image)
                    var file = {!! json_encode($frontProject->image) !!}
                    this.options.addedfile.call(this, file)
                    this.options.thumbnail.call(this, file, file.preview ?? file.preview_url)
                    file.previewElement.classList.add('dz-complete')
                    $('form').append('<input type="hidden" name="image" value="' + file.file_name + '">')
                    this.options.maxFiles = this.options.maxFiles - 1
                @endif
            },
            error: function(file, response) {
                if ($.type(response) === 'string') {
                    var message = response //dropzone sends it's own error messages in string
                } else {
                    var message = response.errors.file
                }
                file.previewElement.classList.add('dz-error')
                _ref = file.previewElement.querySelectorAll('[data-dz-errormessage]')
                _results = []
                for (_i = 0, _len = _ref.length; _i < _len; _i++) {
                    node = _ref[_i]
                    _results.push(node.textContent = message)
                }

                return _results
            }
        }
    </script>
@endsection
