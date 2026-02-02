@if ($setting->type == 'string' && ($setting->lang == null ? true : $setting->lang == currentEditingLang()))
    <div class="mt-4 col-md-{{ $setting->grid_col }}">
        <label for="{{ $setting->key }}">
            {{ trans('cruds.setting.type.' . $setting->key) }}
            @if ($setting->lang != null)
                <i class="bi bi-translate text-primary" title="Translatable field"></i>
            @endif
        </label>
        <input type="text" class="form-control" id="{{ $setting->key }}" name="{{ $setting->key }}"
            value="{{ $setting->value }}">
    </div>
@elseif($setting->type == 'file')
    <div class="mt-4 col-md-{{ $setting->grid_col }}">
        <label for="{{ $setting->key }}">
            {{ trans('cruds.setting.type.' . $setting->key) }}
        </label>
        <div class="needsclick dropzone" id="{{ $setting->key }}-dropzone">
        </div>
    </div>
    @push('stack-scripts')
        <script>
            var {{ $setting->key }}Dropzone = new Dropzone("#{{ $setting->key }}-dropzone", {
                url: '{{ route('admin.settings.storeMedia') }}',
                maxFilesize: 4,
                maxFiles: 1,
                addRemoveLinks: true,
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                params: {
                    size: 4
                },
                success: function(file, response) {
                    $('form').find('input[name="{{ $setting->key }}"]').remove()
                    $('form').append('<input type="hidden" name="{{ $setting->key }}" value="' + response.name + '">')
                },
                removedfile: function(file) {
                    file.previewElement.remove()
                    if (file.status !== 'error') {
                        $('form').find('input[name="{{ $setting->key }}"]').remove()
                        this.options.maxFiles = this.options.maxFiles + 1
                    }
                },
                init: function() {
                    @if (isset($setting) && $setting->file)
                        var file = {!! json_encode($setting->file) !!}
                        this.options.addedfile.call(this, file)
                        this.options.thumbnail.call(this, file, file.preview ?? file.preview_url)
                        file.previewElement.classList.add('dz-complete')
                        $('form').append('<input type="hidden" name="{{ $setting->key }}" value="' + file.file_name + '">')
                        this.options.maxFiles = this.options.maxFiles - 1
                    @endif
                },
                error: function(file, response) {
                    if ($.type(response) === 'string') {
                        var message = response
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
            });
        </script>
    @endpush
@elseif($setting->type == 'number' && ($setting->lang == null ? true : $setting->lang == currentEditingLang()))
    <div class="mt-4 col-md-{{ $setting->grid_col }}">
        <label for="{{ $setting->key }}">
            @if ($setting->lang != null)
                <i class="fa-solid fa-language text-info" title="Translatable field"></i>
            @endif
            {{ trans('cruds.setting.type.' . $setting->key) }}
        </label>
        <input type="number" class="form-control" id="{{ $setting->key }}" name="{{ $setting->key }}"
            value="{{ $setting->value }}">
    </div>
@elseif($setting->type == 'select' && ($setting->lang == null ? true : $setting->lang == currentEditingLang()))
    <div class="mt-4 col-md-{{ $setting->grid_col }}">
        <label for="{{ $setting->key }}">
            @if ($setting->lang != null)
                <i class="fa-solid fa-language text-info" title="Translatable field"></i>
            @endif
            {{ trans('cruds.setting.type.' . $setting->key) }}
        </label>
        <select class="form-control" id="{{ $setting->key }}" name="{{ $setting->key }}">
            @foreach (json_decode($setting->options) as $key => $option)
                <option value="{{ $key }}" {{ $setting->value == $key ? 'selected' : '' }}>
                    {{ trans('cruds.setting.options.' . $setting->key . '.' . $key) }}
                </option>
            @endforeach
        </select>
    </div>
@elseif($setting->type == 'radio' && ($setting->lang == null ? true : $setting->lang == currentEditingLang()))
    <div class="mt-4 col-md-{{ $setting->grid_col }}">
        <div class="card">
            <div class="card-body">
                <label for="{{ $setting->key }}">
                    @if ($setting->lang != null)
                        <i class="fa-solid fa-language text-info" title="Translatable field"></i>
                    @endif
                    {{ trans('cruds.setting.type.' . $setting->key) }}
                </label>
                @foreach (json_decode($setting->options) as $key => $option)
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="{{ $setting->key }}"
                            id="{{ $setting->key }}_{{ $key }}" value="{{ $key }}"
                            {{ $setting->value == $key ? 'checked' : '' }}>
                        <label class="form-check-label"
                            for="{{ $setting->key }}_{{ $key }}">{{ trans('cruds.setting.options.' . $setting->key . '.' . $key) }}</label>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@elseif($setting->type == 'textarea' && ($setting->lang == null ? true : $setting->lang == currentEditingLang()))
    <div class="mt-4 col-md-{{ $setting->grid_col }}">
        <label for="{{ $setting->key }}">
            @if ($setting->lang != null)
                <i class="fa-solid fa-language text-info" title="Translatable field"></i>
            @endif
            {{ trans('cruds.setting.type.' . $setting->key) }}
        </label>
        <textarea class="form-control" id="{{ $setting->key }}" name="{{ $setting->key }}">{{ $setting->value }}</textarea>
    </div>
@elseif($setting->type == 'textarea_html' && ($setting->lang == null ? true : $setting->lang == currentEditingLang()))
    <div class="mt-4 col-md-{{ $setting->grid_col }}">
        <label for="{{ $setting->key }}">
            @if ($setting->lang != null)
                <i class="fa-solid fa-language text-info" title="Translatable field"></i>
            @endif
            {{ trans('cruds.setting.type.' . $setting->key) }}
        </label>
        <textarea class="form-control ckeditor" id="{{ $setting->key }}" name="{{ $setting->key }}">{!! $setting->value !!}</textarea>
    </div>
@endif
