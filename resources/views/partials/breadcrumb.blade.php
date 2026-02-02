<!-- Page Header -->
<div class="d-flex align-items-center justify-content-between page-header-breadcrumb flex-wrap gap-2">
    <div>
        <nav>
            <ol class="breadcrumb mb-1">
                @if (isset($breadcrumbs))
                    @foreach ($breadcrumbs as $breadcrumb)
                        <li class="breadcrumb-item {{ $loop->last ? 'active' : '' }}">
                            @if (!$loop->last && isset($breadcrumb['url']))
                                <a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['title'] }}</a>
                            @else
                                {{ $breadcrumb['title'] }}
                            @endif
                        </li>
                    @endforeach
                @endif
            </ol>
        </nav>
        <h1 class="page-title fw-medium fs-18 mb-0">{{ $pageTitle ?? '' }}</h1>
    </div>
    @if (isset($buttons))
        <div class="btn-list">
            @foreach ($buttons as $button)
                @if (isset($button['permission']))
                    @can($button['permission'])
                        <a href="{{ $button['url'] }}" @isset($button['onclick']) onclick="{{ $button['onclick'] }}" @endisset class="btn {{ $button['class'] ?? 'btn-primary' }} rounded-pill btn-wave">
                            {{ $button['title'] }}
                        </a>
                    @endcan
                @else
                    <a href="{{ $button['url'] }}" @isset($button['onclick']) onclick="{{ $button['onclick'] }}" @endisset class="btn {{ $button['class'] ?? 'btn-primary' }} rounded-pill btn-wave">
                        {{ $button['title'] }}
                    </a>
                @endif
            @endforeach
        </div>
    @endif
</div>
<!-- Page Header Close -->
