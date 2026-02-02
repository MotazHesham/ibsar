@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.userManagement.title'), 'url' => '#'],
            ['title' => trans('global.list') . ' ' . trans('cruds.role.title'), 'url' => route('admin.roles.index')],
            ['title' => trans('global.edit') . ' ' . trans('cruds.role.title'), 'url' => '#'],
        ];
    @endphp
    @include('partials.breadcrumb')
    <form method="POST" action="{{ route('admin.roles.update', [$role->id]) }}" enctype="multipart/form-data">
        @method('PUT')
        @csrf
        <div class="card">
            <div class="card-header">
                {{ __('global.edit') }} {{ __('cruds.role.title_singular') }}
            </div>

            <div class="card-body">
                <div class="form-group">
                    <label class="form-label required" for="title">{{ __('cruds.role.fields.title') }}</label>
                    <input class="form-control {{ $errors->has('title') ? 'is-invalid' : '' }}" type="text" name="title"
                        id="title" value="{{ old('title', $role->title) }}" required>
                    @if ($errors->has('title'))
                        <div class="invalid-feedback">
                            {{ $errors->first('title') }}
                        </div>
                    @endif
                    <span class="help-block">{{ __('cruds.role.fields.title_helper') }}</span>
                </div>
            </div>
        </div>

        <div class="card" style="background: #ffffff61;">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-2">
                        <ul class="nav nav-tabs" role="tablist" id="relationship-tabs">
                            <li class="nav-item">
                                @foreach (\App\Models\Permission::where('parent', 1)->get() as $parent)
                                    <a class="nav-link @if ($loop->first) active @endif"
                                        href="#{{ $parent->id }}" role="tab" data-bs-toggle="tab">
                                        {{ __('permissions.' . $parent->title) }}
                                    </a>
                                @endforeach
                                <a class="nav-link" href="#general" role="tab" data-bs-toggle="tab">
                                    {{ __('permissions.other') }}
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-10">
                        <div class="tab-content">
                            @foreach (\App\Models\Permission::where('parent', 1)->get() as $parent)
                                <div class="tab-pane @if ($loop->first) active @endif" role="tabpanel"
                                    id="{{ $parent->id }}">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center flex-wrap mb-4">
                                                <div class="">
                                                    <p class="text-muted m-0">{{ __('permissions.' . $parent->title) }}</p>
                                                </div>
                                                <div class="custom-toggle-switch ms-2">
                                                    <input id="p{{ $parent->id }}" name="permissions[]"
                                                        value="{{ $parent->id }}" type="checkbox"
                                                        {{ in_array($parent->id, old('permissions', [])) || $role->permissions->contains($parent->id) ? 'checked' : '' }}>
                                                    <label for="p{{ $parent->id }}" class="label-success mb-1"></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        @foreach (\App\Models\Permission::whereIn('type', explode('.', $parent->type))->get()->groupBy('type') as $key => $array)
                                            <div class="col-md-3">
                                                <div class="card">
                                                    <div class="card-header">
                                                        <div style="display: flex;justify-content:space-between">
                                                            <div>{{ __('permissions.type.' . $key) }}</div>
                                                            <div>
                                                                <button type="button"
                                                                    class="btn btn-success btn-sm btn-pill"
                                                                    onclick="check({{ $array }},true)">Check
                                                                    all</button>
                                                                <button type="button"
                                                                    class="btn btn-outline-warning btn-sm btn-pill"
                                                                    onclick="check({{ $array }},false)">UnCheck
                                                                    all</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="card-body">
                                                        @foreach ($array as $raw)
                                                            <div class="d-flex align-items-center flex-wrap mb-4">
                                                                <div class="">
                                                                    <p class="text-muted m-0">
                                                                        {{ __('permissions.' . $raw->title) }}</p>
                                                                </div>
                                                                <div class="custom-toggle-switch ms-2">
                                                                    <input id="{{ $raw->id }}" name="permissions[]"
                                                                        value="{{ $raw->id }}"
                                                                        data-parent="{{ $parent->id }}"
                                                                        data-permission_type="{{ $raw->type }}"
                                                                        data-permission="{{ $raw->title }}"
                                                                        onchange="checkedPermission('{{ $raw->id }}')"
                                                                        type="checkbox"
                                                                        {{ in_array($raw->id, old('permissions', [])) || $role->permissions->contains($raw->id) ? 'checked' : '' }}>
                                                                    <label for="{{ $raw->id }}"
                                                                        class="label-success mb-1"></label>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                            <div class="tab-pane" role="tabpanel" id="general">
                                <div class="row">
                                    @foreach (\App\Models\Permission::where('parent', 2)->get()->groupBy('type') as $key => $array)
                                        <div class="col-md-3">
                                            <div class="card">
                                                <div class="card-header">
                                                    <div style="display: flex;justify-content:space-between">
                                                        <div>{{ __('permissions.type.' . $key) }}</div>
                                                        <div>
                                                            <button type="button" class="btn btn-success btn-sm btn-pill"
                                                                onclick="check({{ $array }},true)">Check
                                                                all</button>
                                                            <button type="button"
                                                                class="btn btn-outline-warning btn-sm btn-pill"
                                                                onclick="check({{ $array }},false)">UnCheck
                                                                all</button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    @foreach ($array as $raw)
                                                        <div class="d-flex align-items-center flex-wrap mb-4">
                                                            <div class="">
                                                                <p class="text-muted m-0">
                                                                    {{ __('permissions.' . $raw->title) }}</p>
                                                            </div>
                                                            <div class="custom-toggle-switch ms-2">
                                                                <input id="{{ $raw->id }}" name="permissions[]"
                                                                    value="{{ $raw->id }}"
                                                                    data-parent="{{ $parent->id }}"
                                                                    data-permission_type="{{ $raw->type }}"
                                                                    data-permission="{{ $raw->title }}"
                                                                    onchange="checkedPermission('{{ $raw->id }}')"
                                                                    type="checkbox"
                                                                    {{ in_array($raw->id, old('permissions', [])) || $role->permissions->contains($raw->id) ? 'checked' : '' }}>
                                                                <label for="{{ $raw->id }}"
                                                                    class="label-success mb-1"></label>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <button class="btn btn-danger" type="submit">
                {{ __('global.save') }}
            </button>
        </div>
    </form>
@endsection

@section('scripts')
    @parent
    <script>
        function checkedPermission(permissionId) {

            // Get the checkbox element by ID
            const checkbox = document.getElementById(permissionId);

            // Check if the checkbox is checked
            const isChecked = checkbox.checked;

            // Retrieve the values of data attributes
            const permissionType = checkbox.getAttribute('data-permission_type');
            const permissionTitle = checkbox.getAttribute('data-permission');
            const permissionParent = checkbox.getAttribute('data-parent');

            if (isChecked) {
                // Find the access permission checkbox
                const accessPermission = document.querySelector(`input[data-permission="${permissionType}_access"]`);
                if (accessPermission) {
                    accessPermission.checked = true;
                } else {
                    console.log(`Parent permission checkbox not found for: ${permissionType}_access`);
                }

                // Find the parent checkbox using a safer method
                const parent = document.getElementById(`p${permissionParent}`);
                if (parent) {
                    parent.checked = true;
                } else {
                    console.log(`Parent permission checkbox not found for: p${permissionParent}`);
                }
            }
        }

        function check(array, status) {
            array.forEach(item => {
                const checkbox = document.getElementById(item.id);
                if (checkbox) {
                    checkbox.checked = status;
                } else {
                    console.log(`Checkbox not found for ID: ${item.id}`);
                }
                checkedPermission(item.id);
            });
        }
    </script>
@endsection
