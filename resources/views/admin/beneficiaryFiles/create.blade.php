@extends('layouts.master')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.beneficiaryFile.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.beneficiary-files.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="beneficiary_id">{{ trans('cruds.beneficiaryFile.fields.beneficiary') }}</label>
                <select class="form-control select2 {{ $errors->has('beneficiary') ? 'is-invalid' : '' }}" name="beneficiary_id" id="beneficiary_id">
                    @foreach($beneficiaries as $id => $entry)
                        <option value="{{ $id }}" {{ old('beneficiary_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('beneficiary'))
                    <div class="invalid-feedback">
                        {{ $errors->first('beneficiary') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.beneficiaryFile.fields.beneficiary_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="name">{{ trans('cruds.beneficiaryFile.fields.name') }}</label>
                <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name" id="name" value="{{ old('name', '') }}">
                @if($errors->has('name'))
                    <div class="invalid-feedback">
                        {{ $errors->first('name') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.beneficiaryFile.fields.name_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="file">{{ trans('cruds.beneficiaryFile.fields.file') }}</label>
                <div class="needsclick dropzone {{ $errors->has('file') ? 'is-invalid' : '' }}" id="file-dropzone">
                </div>
                @if($errors->has('file'))
                    <div class="invalid-feedback">
                        {{ $errors->first('file') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.beneficiaryFile.fields.file_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="required_document_id">{{ trans('cruds.beneficiaryFile.fields.required_document') }}</label>
                <select class="form-control select2 {{ $errors->has('required_document') ? 'is-invalid' : '' }}" name="required_document_id" id="required_document_id">
                    @foreach($required_documents as $id => $entry)
                        <option value="{{ $id }}" {{ old('required_document_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('required_document'))
                    <div class="invalid-feedback">
                        {{ $errors->first('required_document') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.beneficiaryFile.fields.required_document_helper') }}</span>
            </div>
            <div class="form-group">
                <button class="btn btn-primary-light rounded-pill btn-wave" type="submit">
                    {{ trans('global.save') }}
                </button>
            </div>
        </form>
    </div>
</div>



@endsection

@section('scripts')
<script>
    Dropzone.options.fileDropzone = {
    url: '{{ route('admin.beneficiary-files.storeMedia') }}',
    maxFilesize: 4, // MB
    maxFiles: 1,
    addRemoveLinks: true,
    headers: {
      'X-CSRF-TOKEN': "{{ csrf_token() }}"
    },
    params: {
      size: 4
    },
    success: function (file, response) {
      $('form').find('input[name="file"]').remove()
      $('form').append('<input type="hidden" name="file" value="' + response.name + '">')
    },
    removedfile: function (file) {
      file.previewElement.remove()
      if (file.status !== 'error') {
        $('form').find('input[name="file"]').remove()
        this.options.maxFiles = this.options.maxFiles + 1
      }
    },
    init: function () {
@if(isset($beneficiaryFile) && $beneficiaryFile->file)
      var file = {!! json_encode($beneficiaryFile->file) !!}
          this.options.addedfile.call(this, file)
      file.previewElement.classList.add('dz-complete')
      $('form').append('<input type="hidden" name="file" value="' + file.file_name + '">')
      this.options.maxFiles = this.options.maxFiles - 1
@endif
    },
     error: function (file, response) {
         if ($.type(response) === 'string') {
             var message = response //dropzone sends it's own error messages in string
         } else {
             var message = response.errors.file
         }
         file.previewElement.classList.add('dz-error')
         _ref = file.previewElement.querySelectorAll('[data-dz-errormessage]')
         _results = []
         for (_i = 0, _len = _ref.length; _i < _len; _i++) {
             node = _ref[_i]
             _results.push(node.textContent = message)
         }

         return _results
     }
}
</script>
@endsection