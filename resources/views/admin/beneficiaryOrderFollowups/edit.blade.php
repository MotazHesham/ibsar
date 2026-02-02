<form method="POST" action="{{ route('admin.beneficiary-order-followups.update', $beneficiaryOrderFollowup->id) }}"
    enctype="multipart/form-data" onsubmit="modalAjaxSubmit(event)">
    @csrf
    @method('PUT')
    <div class="modal-header">
        <h5 class="modal-title" id="addFamilyModalLabel">
            {{ trans('global.edit') }} {{ trans('cruds.beneficiaryOrderFollowup.title_singular') }}
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">

        <div class="row gy-2">
            @include('utilities.form.date-ajax', [
                'name' => 'date',
                'id' => 'dateFollowup',
                'label' => 'cruds.beneficiaryOrderFollowup.fields.date',
                'isRequired' => true,
                'grid' => 'col-md-12',
                'value' => $beneficiaryOrderFollowup->date,
            ])
            @include('utilities.form.textarea', [
                'name' => 'comment',
                'label' => 'cruds.beneficiaryOrderFollowup.fields.comment',
                'isRequired' => true,
                'grid' => 'col-md-12',
                'value' => $beneficiaryOrderFollowup->comment,
            ])
            @include('utilities.form.dropzone-multiple-ajax', [
                'name' => 'attachments',
                'id' => 'attachmentsFollowup',
                'label' => 'cruds.beneficiaryOrderFollowup.fields.attachments',
                'isRequired' => false,
                'grid' => 'col-md-12',
                'url' => route('admin.beneficiary-order-followups.storeMedia'),
                'model' => $beneficiaryOrderFollowup,
            ])

        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            {{ trans('global.cancel') }}
        </button>
        <button type="submit" class="btn btn-primary">
            {{ trans('global.save') }}
        </button>
    </div>

</form>
