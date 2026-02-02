<div class="form-group mb-3 {{ $grid ?? '' }}">
    <div class="form-check {{ $errors->has($name) ? 'is-invalid' : '' }}">
        <input class="form-check-input" type="checkbox" id="{{ $id ?? $name }}"
            name="{{ $name }}" value="1"
            {{ old($name, isset($value) ? $value : false) ? 'checked' : '' }}
            @if ($isRequired) required @endif>
        <label class="form-check-label" for="{{ $id ?? $name }}">
            {{ trans($label) }}
            @if ($isRequired)
                <span class="text-danger">*</span>
            @endif
        </label>
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