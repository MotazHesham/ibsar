@if ($user->photo)
    <span class="avatar avatar-md avatar-rounded @isset($class) {{ $class }} @endisset">
        <img src="{{ $user->photo->getUrl('thumb') }}" alt="img" data-bs-toggle="tooltip" data-bs-placement="bottom"
            title="{{ $user->name }}">
    </span>
@else
    <span
        class="avatar avatar-md badge {{ getRandomColor($user->name) }} avatar-rounded profile-timeline-avatar @isset($class) {{ $class }} @endisset"
        data-bs-toggle="tooltip" data-bs-placement="bottom" title="{{ $user->name }}">
        {{ getEnglishEquivalent($user->name) }}
    </span>
@endif
