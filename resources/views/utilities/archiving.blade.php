<div class="modal fade" id="archiveModal" tabindex="-1"  style="z-index: 1100"
aria-labelledby="archiveModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.archives.store') }}" method="POST">
                @csrf
                <input type="hidden" name="archiveable_id" id="archiveable_id">
                <input type="hidden" name="archiveable_model" id="archiveable_model">
                <div class="modal-header">
                    <h5 class="modal-title" id="archiveModalLabel">
                        {{ trans('global.archive') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @include('utilities.form.textarea', [
                        'name' => 'archive_reason',
                        'label' => 'cruds.archive.fields.archive_reason',
                        'isRequired' => false,
                        'value' => '',
                    ])
                    @include('utilities.form.select', [
                        'name' => 'storage_location_id',
                        'label' => 'cruds.archive.fields.storage_location',
                        'options' => \App\Models\StorageLocation::pluck('code', 'id')->prepend(
                            trans('global.pleaseSelect'),
                            ''),
                        'isRequired' => false,
                    ])

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ trans('global.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ trans('global.archive') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function addToArchive(id, model) {
        $('#archiveable_id').val(id);
        $('#archiveable_model').val(model);
        $('#archiveModal').modal('show');
    }
</script>
