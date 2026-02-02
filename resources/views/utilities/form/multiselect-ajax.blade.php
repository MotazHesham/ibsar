<div class="form-group mb-3 {{ $grid ?? '' }}">
    <label class="mb-2" for="{{ $name }}">
        {{ trans($label) }}
        @if ($isRequired)
            <span class="text-danger">*</span>
        @endif
    </label>
    <select class="form-control" name="{{ $name }}[]" id="{{ $id ?? $name }}"
        @if ($isRequired) required @endif multiple>
        @foreach ($options as $rawId => $entry)
            <option value="{{ $rawId }}"
                {{ in_array($rawId, old($name) ? old($name, []) : (isset($value) ? $value : [])) ? 'selected' : '' }}>
                {{ $entry }}</option>
        @endforeach
    </select>
    @if ($errors->has($name))
        <span class="text-danger">
            {{ $errors->first($name) }}
        </span>
    @endif
    <span class="help-block">{{ trans($label . '_helper') }}</span>
</div>


<script>
    var multipleCancelButton = new Choices(
        '#{{ $id }}', {
            allowHTML: true,
            removeItemButton: true,
        }
    );
</script>
