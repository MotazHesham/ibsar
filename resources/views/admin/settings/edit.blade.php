@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.setting.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.settings.update", [$setting->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label class="form-label" for="name">{{ trans('cruds.setting.fields.name') }}</label>
                <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name" id="name" value="{{ old('name', $setting->name) }}">
                @if($errors->has('name'))
                    <div class="invalid-feedback">
                        {{ $errors->first('name') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.setting.fields.name_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="form-label required" for="key">{{ trans('cruds.setting.fields.key') }}</label>
                <input class="form-control {{ $errors->has('key') ? 'is-invalid' : '' }}" type="text" name="key" id="key" value="{{ old('key', $setting->key) }}" required>
                @if($errors->has('key'))
                    <div class="invalid-feedback">
                        {{ $errors->first('key') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.setting.fields.key_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="form-label" for="options">{{ trans('cruds.setting.fields.options') }}</label>
                <textarea class="form-control {{ $errors->has('options') ? 'is-invalid' : '' }}" name="options" id="options">{{ old('options', $setting->options) }}</textarea>
                @if($errors->has('options'))
                    <div class="invalid-feedback">
                        {{ $errors->first('options') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.setting.fields.options_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="form-label" for="value">{{ trans('cruds.setting.fields.value') }}</label>
                <textarea class="form-control {{ $errors->has('value') ? 'is-invalid' : '' }}" name="value" id="value">{{ old('value', $setting->value) }}</textarea>
                @if($errors->has('value'))
                    <div class="invalid-feedback">
                        {{ $errors->first('value') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.setting.fields.value_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="form-label" for="lang">{{ trans('cruds.setting.fields.lang') }}</label>
                <input class="form-control {{ $errors->has('lang') ? 'is-invalid' : '' }}" type="text" name="lang" id="lang" value="{{ old('lang', $setting->lang) }}">
                @if($errors->has('lang'))
                    <div class="invalid-feedback">
                        {{ $errors->first('lang') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.setting.fields.lang_helper') }}</span>
            </div>
            <div class="form-group">
                <label>{{ trans('cruds.setting.fields.type') }}</label>
                <select class="form-control {{ $errors->has('type') ? 'is-invalid' : '' }}" name="type" id="type">
                    <option value disabled {{ old('type', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach(App\Models\Setting::TYPE_SELECT as $key => $label)
                        <option value="{{ $key }}" {{ old('type', $setting->type) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @if($errors->has('type'))
                    <div class="invalid-feedback">
                        {{ $errors->first('type') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.setting.fields.type_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="form-label" for="file">{{ trans('cruds.setting.fields.file') }}</label>
                <div class="needsclick dropzone {{ $errors->has('file') ? 'is-invalid' : '' }}" id="file-dropzone">
                </div>
                @if($errors->has('file'))
                    <div class="invalid-feedback">
                        {{ $errors->first('file') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.setting.fields.file_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="form-label" for="group_name">{{ trans('cruds.setting.fields.group_name') }}</label>
                <input class="form-control {{ $errors->has('group_name') ? 'is-invalid' : '' }}" type="text" name="group_name" id="group_name" value="{{ old('group_name', $setting->group_name) }}">
                @if($errors->has('group_name'))
                    <div class="invalid-feedback">
                        {{ $errors->first('group_name') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.setting.fields.group_name_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="form-label" for="order_level">{{ trans('cruds.setting.fields.order_level') }}</label>
                <input class="form-control {{ $errors->has('order_level') ? 'is-invalid' : '' }}" type="number" name="order_level" id="order_level" value="{{ old('order_level', $setting->order_level) }}" step="1">
                @if($errors->has('order_level'))
                    <div class="invalid-feedback">
                        {{ $errors->first('order_level') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.setting.fields.order_level_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="form-label" for="grid_col">{{ trans('cruds.setting.fields.grid_col') }}</label>
                <input class="form-control {{ $errors->has('grid_col') ? 'is-invalid' : '' }}" type="text" name="grid_col" id="grid_col" value="{{ old('grid_col', $setting->grid_col) }}">
                @if($errors->has('grid_col'))
                    <div class="invalid-feedback">
                        {{ $errors->first('grid_col') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.setting.fields.grid_col_helper') }}</span>
            </div>
            <div class="form-group">
                <button class="btn btn-danger" type="submit">
                    {{ trans('global.save') }}
                </button>
            </div>
        </form>
    </div>
</div>



@endsection

@section('scripts')
<script>
    Dropzone.options.fileDropzone = {
    url: '{{ route('admin.settings.storeMedia') }}',
    maxFilesize: 4, // MB
    maxFiles: 1,
    addRemoveLinks: true,
    headers: {
      'X-CSRF-TOKEN': "{{ csrf_token() }}"
    },
    params: {
      size: 4
    },
    success: function (file, response) {
      $('form').find('input[name="file"]').remove()
      $('form').append('<input type="hidden" name="file" value="' + response.name + '">')
    },
    removedfile: function (file) {
      file.previewElement.remove()
      if (file.status !== 'error') {
        $('form').find('input[name="file"]').remove()
        this.options.maxFiles = this.options.maxFiles + 1
      }
    },
    init: function () {
@if(isset($setting) && $setting->file)
      var file = {!! json_encode($setting->file) !!}
          this.options.addedfile.call(this, file)
      file.previewElement.classList.add('dz-complete')
      $('form').append('<input type="hidden" name="file" value="' + file.file_name + '">')
      this.options.maxFiles = this.options.maxFiles - 1
@endif
    },
     error: function (file, response) {
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