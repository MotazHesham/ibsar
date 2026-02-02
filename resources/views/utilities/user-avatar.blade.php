<a href="{{ route('admin.users.show', $user->id) }}" class="avatar avatar-sm avatar-rounded @isset($class) {{ $class }} @endisset">
    @if ($user->photo)
        <img src="{{ $user->photo->getUrl('thumb') }}" alt="img" data-bs-toggle="tooltip" data-bs-placement="bottom"
            title="{{ $user->name }}">
    @else
        <span class="avatar avatar-sm badge {{ getRandomColor($user->name) }} avatar-rounded profile-timeline-avatar @isset($class) {{ $class }} @endisset"
            data-bs-toggle="tooltip" data-bs-placement="bottom" title="{{ $user->name }}">
            {{ getEnglishEquivalent($user->name) }}
        </span>
    @endif
</a>
