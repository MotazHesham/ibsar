@isset($prependButtons)
    @foreach ($prependButtons as $button) 
        <a class="btn btn-sm btn-icon btn-{{ $button['color'] ?? 'primary' }}-light" title="{{ $button['title'] }}"
            href="{{ $button['url'] }}" {!! $button['attributes'] ?? '' !!}>
            <i class="{{ $button['icon'] ?? '' }}"></i>
        </a>  
    @endforeach
@endisset

@if($viewGate)
    <a class="btn btn-sm btn-icon btn-primary-light" title="{{ trans('global.view') }}"
        href="{{ route($crudRoutePart . '.show', $row->id) }}">
        <i class="ri-eye-line"></i>
    </a>
@endif
@if($editGate)
    <a class="btn btn-sm btn-icon btn-info-light" title="{{ trans('global.edit') }}"
        href="{{ route($crudRoutePart . '.edit', $row->id) }}">
        <i class="ri-edit-line"></i>
    </a>
@endif
@if($deleteGate)
    <button class="btn btn-sm btn-icon btn-danger-light" title="{{ trans('global.delete') }}"
        onclick="deleteRecord('{{ route($crudRoutePart . '.destroy', $row->id) }}', {{ $row->id }})">
        <i class="ri-delete-bin-line"></i>
    </button>
@endif

@isset($appendButtons)
    @foreach ($appendButtons as $button) 
        <a class="btn btn-sm btn-icon btn-{{ $button['color'] ?? 'primary' }}-light" title="{{ $button['title'] }}"
            href="{{ $button['url'] }}" {{ $button['attributes'] ?? '' }}>
            <i class="{{ $button['icon'] ?? '' }}"></i>
        </a>  
    @endforeach
@endisset