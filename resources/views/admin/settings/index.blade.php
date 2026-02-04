@extends('layouts.master')
@section('content')
    <div class="card custom-card">
        <div class="card-header">
            <div class="card-title">
                {{ trans('cruds.setting.title') }}
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-xl-2">
                    <nav class="nav nav-tabs flex-column nav-style-5" role="tablist">
                        @foreach ($settings as $group_name => $raws) 
                            @php
                                $pass = true; 
                                if($group_name == 'theme_settings'){
                                    $pass = false;
                                }
                            @endphp
                            @if($pass)
                                <a class="nav-link @if ($loop->first) active @endif" data-bs-toggle="tab"
                                role="tab" aria-current="page" href="#{{ $group_name }}-vertical-link"
                                aria-selected="@if ($loop->first) true @else false @endif">{{ trans('cruds.setting.group_name.' . $group_name) }}</a>
                            @endif
                        @endforeach
                    </nav>
                </div>
                <div class="col-xl-10">
                    @include('utilities.switchlang')
                    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="lang" value="{{ currentEditingLang() }}" id="">

                        <div class="tab-content mt-2 mt-xl-0">
                            @foreach ($settings as $group_name => $raws)
                                <div class="tab-pane @if ($loop->first) show active @endif text-muted"
                                    id="{{ $group_name }}-vertical-link" role="tabpanel">
                                    <div class="row">
                                        @foreach ($raws as $raw)
                                            @include('admin.settings.inputs', ['setting' => $raw])
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <button type="submit" class="btn btn-primary mt-4">
                            {{ trans('global.save') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function() {
            function SimpleUploadAdapter(editor) {
                editor.plugins.get('FileRepository').createUploadAdapter = function(loader) {
                    return {
                        upload: function() {
                            return loader.file
                                .then(function(file) {
                                    return new Promise(function(resolve, reject) {
                                        // Init request
                                        var xhr = new XMLHttpRequest();
                                        xhr.open('POST',
                                            '{{ route('admin.settings.storeCKEditorImages') }}',
                                            true);
                                        xhr.setRequestHeader('x-csrf-token', window._token);
                                        xhr.setRequestHeader('Accept', 'application/json');
                                        xhr.responseType = 'json';

                                        // Init listeners
                                        var genericErrorText =
                                            `Couldn't upload file: ${ file.name }.`;
                                        xhr.addEventListener('error', function() {
                                            reject(genericErrorText)
                                        });
                                        xhr.addEventListener('abort', function() {
                                            reject()
                                        });
                                        xhr.addEventListener('load', function() {
                                            var response = xhr.response;

                                            if (!response || xhr.status !== 201) {
                                                return reject(response && response
                                                    .message ?
                                                    `${genericErrorText}\n${xhr.status} ${response.message}` :
                                                    `${genericErrorText}\n ${xhr.status} ${xhr.statusText}`
                                                );
                                            }

                                            $('form').append(
                                                '<input type="hidden" name="ck-media[]" value="' +
                                                response.id + '">');

                                            resolve({
                                                default: response.url
                                            });
                                        });

                                        if (xhr.upload) {
                                            xhr.upload.addEventListener('progress', function(
                                                e) {
                                                if (e.lengthComputable) {
                                                    loader.uploadTotal = e.total;
                                                    loader.uploaded = e.loaded;
                                                }
                                            });
                                        }

                                        // Send request
                                        var data = new FormData();
                                        data.append('upload', file);
                                        data.append('crud_id', '0');
                                        xhr.send(data);
                                    });
                                })
                        }
                    };
                }
            }

            var allEditors = document.querySelectorAll('.ckeditor');
            for (var i = 0; i < allEditors.length; ++i) {
                ClassicEditor.create(
                    allEditors[i], {
                        extraPlugins: [SimpleUploadAdapter]
                    }
                );
            }
        });
    </script>
@endsection
