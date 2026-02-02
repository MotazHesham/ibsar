<div class="form-group mb-3 {{ $grid ?? '' }}">
    <label class="form-label" for="{{ $name }}">
        {{ trans($label) }}
        @if ($isRequired)
            <span class="text-danger">*</span>
        @endif
    </label>
    <input class="form-control {{ $errors->has($name) ? 'is-invalid' : '' }}"
        type="{{ $type ?? 'text' }}" 
        name="{{ $name }}" id="{{ $id ?? $name }}"
        placeholder="{{ trans('global.enter') }} {{ trans($label) }}"
        value="{{ old($name, isset($value) ? $value : '') }}" 
        @if ($isRequired) required @endif @isset($attributes) {{ $attributes }} @endif>
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
