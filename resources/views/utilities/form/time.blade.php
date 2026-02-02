<div class="form-group mb-3 {{ $grid ?? '' }}">
    <label class="form-label" for="{{ $name }}">
        {{ trans($label) }}
        @if ($isRequired)
            <span class="text-danger">*</span>
        @endif
    </label>
    <div class="input-group">
        <div class="input-group-text text-muted">
            <i class="ri-time-line"></i>
        </div>
        <input type="text" class="form-control timepicker {{ $errors->has($name) ? 'is-invalid' : '' }}"
            id="{{ $id ?? $name }}" name="{{ $name }}" placeholder="{{ trans($label) }}"
            @if ($isRequired) required @endif value="{{ old($name, isset($value) ? $value : '') }}">
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
        $(document).ready(function() {
            flatpickr('.timepicker',{
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true
            });
        });
    </script>
@endsection 