<form method="POST" action="{{ route('admin.tasks.store') }}" enctype="multipart/form-data"
    onsubmit="modalAjaxSubmit(event, 'add-task')">
    @csrf
    <input type="hidden" name="assigned_by_id" value="{{ auth()->user()->id }}">
    <input type="hidden" name="task_board_id" value="{{ $task_board_id }}">
    <input type="hidden" name="status_id" id="status_id" value="{{ $status_id }}">
    <div class="modal-header">
        <h6 class="modal-title">{{ trans('global.create') }} {{ trans('cruds.task.title_singular') }}</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div class="row gy-2">

            @include('utilities.form.text', [
                'name' => 'name',
                'label' => 'cruds.task.fields.name',
                'isRequired' => true,
                'grid' => 'col-md-6',
            ])
            @include('utilities.form.select-ajax', [
                'name' => 'task_priority_id',
                'label' => 'cruds.task.fields.task_priority',
                'isRequired' => true,
                'options' => \App\Models\TaskPriority::pluck('name', 'id'),
                'grid' => 'col-md-6',
            ])
            @include('utilities.form.textarea', [
                'name' => 'short_description',
                'label' => 'cruds.task.fields.short_description',
                'isRequired' => false,
                'grid' => 'col-md-6',
            ])
            @include('utilities.form.textarea', [
                'name' => 'description',
                'label' => 'cruds.task.fields.description',
                'isRequired' => false,
                'grid' => 'col-md-6',
            ])
            @include('utilities.form.dropzone-multiple-ajax', [
                'name' => 'attachment',
                'label' => 'cruds.task.fields.attachment',
                'url' => route('admin.tasks.storeMedia'),
                'isRequired' => false,
                'grid' => 'col-md-12',
            ])
            @include('utilities.form.multiselect-ajax', [
                'name' => 'assigned_tos',
                'id' => 'assigned_to_id',
                'label' => 'cruds.task.fields.assigned_to',
                'isRequired' => true,
                'options' => \App\Models\User::where('user_type', 'staff')->pluck('name', 'id'),
                'grid' => 'col-md-6',
            ])
            @include('utilities.form.date-ajax', [
                'name' => 'due_date',
                'id' => 'due_date',
                'label' => 'cruds.task.fields.due_date',
                'isRequired' => false,
                'grid' => 'col-md-6',
            ])
            @include('utilities.form.multiselect-ajax', [
                'name' => 'tags',
                'id' => 'tags',
                'label' => 'cruds.task.fields.tag',
                'isRequired' => false,
                'options' => \App\Models\TaskTag::pluck('name', 'id'),
                'grid' => 'col-md-12',
            ])

        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn m-0 me-2 btn-success-light"
            data-bs-dismiss="modal">{{ trans('global.cancel') }}</button>
        <button type="submit" class="btn m-0 btn-primary">{{ trans('global.save') }}</button>
    </div>
</form>
