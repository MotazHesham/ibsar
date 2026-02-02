<form method="POST" action="{{ route('admin.tasks.update', $task->id) }}" enctype="multipart/form-data"
    onsubmit="modalAjaxSubmit(event, 'edit-task')">
    @csrf
    @method('PUT')
    <div class="modal-header">
        <h6 class="modal-title">{{ trans('global.edit') }} {{ trans('cruds.task.title_singular') }}</h6>
    </div>
    <div class="modal-body">
        <div class="row gy-2">
            @include('utilities.form.text', [
                'name' => 'name',
                'label' => 'cruds.task.fields.name',
                'isRequired' => true,
                'grid' => 'col-md-6',
                'value' => $task->name ?? null,
            ])
            @include('utilities.form.select', [
                'name' => 'task_priority_id',
                'label' => 'cruds.task.fields.task_priority',
                'isRequired' => true,
                'options' => \App\Models\TaskPriority::pluck('name', 'id'),
                'grid' => 'col-md-6',
                'value' => $task->task_priority_id ?? null,
            ])
            @include('utilities.form.textarea', [
                'name' => 'short_description',
                'label' => 'cruds.task.fields.short_description',
                'isRequired' => false,
                'grid' => 'col-md-6',
                'value' => $task->short_description ?? null,
            ])
            @include('utilities.form.textarea', [
                'name' => 'description',
                'label' => 'cruds.task.fields.description',
                'isRequired' => false,
                'grid' => 'col-md-6',
                'value' => $task->description ?? null,
            ])
            @include('utilities.form.dropzone-multiple-ajax', [
                'name' => 'attachment',
                'id' => 'attachmentedit',
                'label' => 'cruds.task.fields.attachment',
                'url' => route('admin.tasks.storeMedia'),
                'isRequired' => false,
                'grid' => 'col-md-12',
                'model' => $task ?? null,
            ])
            @include('utilities.form.multiselect-ajax', [
                'name' => 'assigned_tos',
                'id' => 'assigned_to_id-edit',
                'label' => 'cruds.task.fields.assigned_to',
                'isRequired' => true,
                'options' => \App\Models\User::where('user_type', 'staff')->pluck('name', 'id'),
                'grid' => 'col-md-6',
                'value' => $task->assigned_tos->pluck('id')->toArray() ?? null,
            ])
            @include('utilities.form.date-ajax', [
                'name' => 'due_date',
                'id' => 'due_date-edit',
                'label' => 'cruds.task.fields.due_date',
                'isRequired' => false,
                'grid' => 'col-md-6',
                'value' => $task->due_date ?? null,
            ])
            @include('utilities.form.multiselect-ajax', [
                'name' => 'tags',
                'id' => 'tags-edit',
                'label' => 'cruds.task.fields.tag',
                'isRequired' => false,
                'options' => \App\Models\TaskTag::pluck('name', 'id'),
                'grid' => 'col-md-12',
                'value' => $task->tags->pluck('id')->toArray() ?? null,
            ])

        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn m-0 me-2 btn-success-light"
            data-bs-dismiss="modal">{{ trans('global.cancel') }}</button>
        <button type="submit" class="btn m-0 btn-primary">{{ trans('global.update') }}</button>
    </div>
</form>
