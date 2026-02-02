<div class="form-group mb-3 {{ isset($grid) ? $grid : '' }}">
    <label class="mb-2 form-label" for="{{ $name }}">
        {{ trans($label) }}
        @if ($isRequired)
            <span class="text-danger">*</span>
        @endif
    </label>

    @if(!isset($hideButtons) || $hideButtons == false)
        <div style="padding-bottom: 4px">
            <span class="btn btn-primary btn-xs select-all" style="border-radius: 0">{{ trans('global.select_all') }}</span>
            <span class="btn btn-primary btn-xs deselect-all" style="border-radius: 0">{{ trans('global.deselect_all') }}</span>
        </div>
    @endif
    <select class="select2 {{ $errors->has($name) ? ' is-invalid' : '' }}" 
        name="{{ $name }}[]" id="{{ $name }}"
        @if ($isRequired) required @endif multiple> 
        @foreach ($options as $id => $entry)
            <option value="{{ $id }}"
                {{ in_array($id, (old($name) ? old($name,[]) : (isset($value) ? $value : []))) ? 'selected' : '' }}>
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

