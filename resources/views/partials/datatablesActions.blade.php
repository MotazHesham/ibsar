@isset($prependButtons)
    @foreach ($prependButtons as $button)
        @can($button['gate'])
            <a class="btn btn-sm btn-icon btn-{{ $button['color'] ?? 'primary' }}-light" title="{{ $button['title'] }}"
                href="{{ $button['url'] }}" {!! $button['attributes'] ?? '' !!}>
                <i class="{{ $button['icon'] ?? '' }}"></i>
            </a> 
        @endcan
    @endforeach
@endisset

@can($viewGate)
    <a class="btn btn-sm btn-icon btn-primary-light" title="{{ trans('global.view') }}"
        href="{{ route('admin.' . $crudRoutePart . '.show', $row->id) }}">
        <i class="ri-eye-line"></i>
    </a>
@endcan
@can($editGate)
    <a class="btn btn-sm btn-icon btn-info-light" title="{{ trans('global.edit') }}"
        href="{{ route('admin.' . $crudRoutePart . '.edit', $row->id) }}">
        <i class="ri-edit-line"></i>
    </a>
@endcan
@can($deleteGate)
    <button class="btn btn-sm btn-icon btn-danger-light" title="{{ trans('global.delete') }}"
        onclick="deleteRecord('{{ route('admin.' . $crudRoutePart . '.destroy', $row->id) }}', {{ $row->id }})">
        <i class="ri-delete-bin-line"></i>
    </button>
@endcan

@isset($appendButtons)
    @foreach ($appendButtons as $button)
        @can($button['gate'])
            <a class="btn btn-sm btn-icon btn-{{ $button['color'] ?? 'primary' }}-light" title="{{ $button['title'] }}"
                href="{{ $button['url'] }}" {{ $button['attributes'] ?? '' }}>
                <i class="{{ $button['icon'] ?? '' }}"></i>
            </a> 
        @endcan
    @endforeach
@endisset