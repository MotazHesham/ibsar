<div class="form-group mb-3 {{ $grid ?? '' }}">
    <div class="mail-compose">
        <label class="form-label" for="{{ $name }}">
            {{ trans($label) }}
            @if ($isRequired)
                <span class="text-danger">*</span>
            @endif
        </label>

        @if (isset($editor) && $editor)
            {{-- Hidden input to store content for submission --}}
            <input type="hidden" name="{{ $name }}" id="input-{{ $id ?? $name }}"
                value="{{ old($name, isset($value) ? $value : '') }}">

            {{-- Quill editor div --}}
            <div id="editor-{{ $id ?? $name }}" style="min-height: 200px;"
                class="form-control p-2 {{ $errors->has($name) ? 'is-invalid' : '' }}"></div>
        @else
            <textarea name="{{ $name }}" id="{{ $id ?? $name }}" rows="7"
                class="form-control {{ $errors->has($name) ? 'is-invalid' : '' }}" rows="3">{{ old($name, isset($value) ? $value : '') }}</textarea>
        @endif

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
</div>

@if (isset($editor) && $editor)
    <script>
        var toolbarOptions_{{ $id ?? $name }} = [
            [{
                header: [1, 2, 3, 4, 5, 6, false]
            }],
            [{
                font: []
            }],
            ["bold", "italic", "underline", "strike"],
            ["blockquote", "code-block"],
            [{
                list: "ordered"
            }, {
                list: "bullet"
            }],
            [{
                color: []
            }, {
                background: []
            }],
            [{
                align: []
            }], 
            ["clean"]
        ];

        var quill_{{ $id ?? $name }} = new Quill("#editor-{{ $id ?? $name }}", {
            modules: {
                toolbar: toolbarOptions_{{ $id ?? $name }}
            },
            theme: "snow",
        });

        // Set initial content
        var hiddenInput_{{ $id ?? $name }} = document.getElementById("input-{{ $id ?? $name }}");
        quill_{{ $id ?? $name }}.root.innerHTML = hiddenInput_{{ $id ?? $name }}.value;

        // Sync on change
        quill_{{ $id ?? $name }}.on('text-change', function() {
            hiddenInput_{{ $id ?? $name }}.value = quill_{{ $id ?? $name }}.root.innerHTML;
        });
    </script>
@endif
