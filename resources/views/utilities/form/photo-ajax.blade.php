<div class="form-group mb-3 text-center">
    <input type="file" class="single-fileupload" accept="image/png, image/jpeg, image/gif"
        id="{{ $id }}-filepond">
    <label class="form-label mt-4 {{ $isRequired ? 'required' : '' }}">{{ trans($label) }}</label>
    <span class="help-block">{{ trans($label . '_helper') }}</span>
    @if ($errors->has($name))
        <div class="invalid-feedback">
            {{ $errors->first($name) }}
        </div>
    @endif
</div>



@php
    $preview = $model->{$name}->preview ?? null;
    $file_name = $model->{$name}->file_name ?? null;
@endphp

<script>
    initFilePond('{{ $id }}','{{ $url }}','{{ $name }}','{{ $preview }}','{{ $file_name }}'); 
</script>
