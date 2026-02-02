<div class="form-group mb-3 {{ $grid ?? '' }}">
    <label class="form-label">
        {{ trans($label) }}
        @if ($isRequired)
            <span class="text-danger">*</span>
        @endif
    </label>
    <div class="needsclick dropzone {{ $errors->has($name) ? 'is-invalid' : '' }}" id="{{ $id ?? $name }}-dropzone">
    </div>
    @if ($errors->has($name))
        <div class="invalid-feedback">
            {{ $errors->first($name) }}
        </div>
    @endif

    @if (isset($helperBlock))
        <span class="help-block">{{ trans($helperBlock) }}</span>
    @else
        <span class="help-block">{{ trans($label . '_helper') }}</span>
    @endif
</div>

@section('scripts')
    @parent
    <script>
        var uploaded{{ ucfirst($name) }}Map = {}
        var form = $('#{{ $id ?? $name }}-dropzone').closest('form');
        var dropzoneConfig = {
            url: '{{ $url }}',
            maxFilesize: 5, // MB 
            addRemoveLinks: true,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            params: {
                size: 5, 
            },
            success: function(file, response) {  
                form.append('<input type="hidden" name="{{ $name }}[]" value="' + response.name + '">')
                uploaded{{ ucfirst($name) }}Map[file.name] = response.name
            },
            removedfile: function(file) { 
                
                file.previewElement.remove()
                var name = ''
                if (typeof file.file_name !== 'undefined') {
                    name = file.file_name
                } else {
                    name = uploaded{{ ucfirst($name) }}Map[file.name]
                } 
                form.find('input[name="{{ $name }}[]"][value="' + name + '"]').remove()
            },
            init: function() {
                @if (isset($model) && $model->{$collectionName ?? $name}) 
                    var files =
                        {!! json_encode($model->{$collectionName ?? $name}) !!}
                    for (var i in files) {
                        var file = files[i]
                        this.options.addedfile.call(this, file)
                        file.previewElement.classList.add('dz-complete')
                        form.append('<input type="hidden" name="{{ $name }}[]" value="' + file.file_name + '">')
                    }
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
        };

        // Initialize Dropzone
        new Dropzone(`#{{ $id ?? $name }}-dropzone`, dropzoneConfig);
    </script>
@endsection
