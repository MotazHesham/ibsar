<div class="form-group mb-3 text-center {{ $grid ?? '' }}">
    <input type="file" class="single-fileupload" id="{{ $id }}-filepond">
    <label class="form-label mt-4 {{ $isRequired ? 'required' : '' }}">{{ trans($label) }}</label>
    <span class="help-block">{{ trans($label . '_helper') }}</span>
    @if ($errors->has($name))
        <div class="invalid-feedback">
            {{ $errors->first($name) }}
        </div>
    @endif
</div>

@section('scripts')
    @parent
    @php
        $preview = $model->{$name}->preview ?? null;
        $file_name = $model->{$name}->file_name ?? null;
    @endphp
    <script>
        

        // If visible on load
        if (document.getElementById('{{ $id }}-filepond').offsetParent !== null) {
            initFilePond('{{ $id }}','{{ $url }}','{{ $name }}','{{ $preview }}','{{ $file_name }}', true);
        }

        // Or in your tab event handler
        document.querySelectorAll('[data-bs-toggle="tab"]').forEach(function(tab) {
            tab.addEventListener('shown.bs.tab', function () {
                if (document.getElementById('{{ $id }}-filepond').offsetParent !== null) {
                    initFilePond('{{ $id }}','{{ $url }}','{{ $name }}','{{ $preview }}','{{ $file_name }}', true);
                }
            });
        });
    </script>
@endsection
