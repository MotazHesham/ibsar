
<form method="POST" action="{{ route(($routeName ?? 'admin.beneficiaries.update'), $beneficiary->id) }}"
    enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <input type="hidden" name="user_id" value="{{ $user->id }}">
    <input type="hidden" name="step" value="documents">
    <div class="row gy-4">
        <div class="col-md-12"> 
            <div class="row">
                @foreach ($requiredDocuments as $document)
                    @if($document->marital_status_id != null && $document->marital_status_id != $beneficiary->marital_status_id)
                        @continue
                    @endif
                    @include('utilities.form.dropzone', [
                        'name' => 'documents['.$document->id.']',
                        'id' => 'documents_'.$document->id,
                        'label' => $document->name,
                        'url' => route(($storeMediaUrl ?? 'admin.beneficiary-files.storeMedia')),
                        'isRequired' => $document->is_required,
                        'grid' => 'col-md-2',
                        'helperBlock' => '',
                        'model' => $beneficiary->beneficiaryFiles ? $beneficiary->beneficiaryFiles->where('required_document_id', $document->id)->first() : null,
                        'collectionName' => 'file',
                    ]) 
                @endforeach
            </div>
            <button type="submit" class="btn btn-primary mt-3">
                {{ trans('global.update') }}
            </button>
            @if(getSetting('auto_accept_beneficiary') == 'yes' && auth()->user()->is_beneficiary && $beneficiary->canRequestOrder())
                <button type="submit" class="btn btn-success mt-3" name="redirect_to" value="request_order">
                    {{ trans('cruds.beneficiary.extra.update_and_request_order') }}
                </button>
            @endif
            @if(auth()->user()->is_beneficiary)
                <button type="submit" class="btn btn-success mt-3" name="redirect_to" value="request_join">
                    {{ trans('cruds.beneficiary.extra.update_and_request_join') }}
                </button>
            @endif
        </div>
    </div>
</form>