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
        var form = $('#{{ $id ?? $name }}-dropzone').closest('form')[0];
        var dropzoneConfig = {
            url: '{{ $url }}',
            maxFilesize: 5, // MB 
            maxFiles: 1,
            addRemoveLinks: true,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            params: {
                size: 5,
                width: 4096,
                height: 4096
            },
            success: function(file, response) { 
                var existingInput = form.querySelector(`input[name="{{ $name }}"]`);
                if (existingInput) {
                    existingInput.remove();
                }
                var input = document.createElement('input');
                input.type = 'hidden';
                input.name = '{{ $name }}';
                input.value = response.name;
                form.appendChild(input);
            },
            removedfile: function(file) {
                var previewElement = file.previewElement;
                if (previewElement) {
                    previewElement.remove();
                }
                
                if (file.status !== 'error') { 
                    var input = form.querySelector(`input[name="{{ $name }}"]`);
                    if (input) {
                        input.remove();
                    }
                    this.options.maxFiles = this.options.maxFiles + 1;
                }
            },
            init: function() {
                @if (isset($model) && $model->{$collectionName ?? $name})
                    var file = {!! json_encode($model->{$collectionName ?? $name}) !!};
                    this.displayExistingFile(file, file.preview ?? file.preview_url); 
                    var input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = '{{ $name }}';
                    input.value = file.file_name;
                    form.appendChild(input);
                    this.options.maxFiles = this.options.maxFiles - 1;   
                @endif 
            },
            error: function(file, response) {
                let message = '';
                if (typeof response === 'string') {
                    message = response;
                } else if (response && response.errors && response.errors.file) {
                    message = response.errors.file;
                } else {
                    message = 'An error occurred while uploading the file.';
                }

                var previewElement = file.previewElement;
                if (previewElement) {
                    previewElement.classList.add('dz-error');
                    var errorElements = previewElement.querySelectorAll('[data-dz-errormessage]');
                    errorElements.forEach(element => {
                        element.textContent = message;
                    });
                }
            }
        };

        // Initialize Dropzone
        new Dropzone(`#{{ $id ?? $name }}-dropzone`, dropzoneConfig);
    </script>
@endsection
