@extends('layouts.master')

@section('title', 'Beneficiary Field Visibility Settings')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ trans('cruds.beneficiaryFieldVisibility.title') }}</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-primary" onclick="saveAllSettings()">
                                <i class="fas fa-save"></i> {{ trans('global.save_all') }}
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <form id="bulkUpdateForm">
                            @csrf
                            @method('PUT')

                            @foreach ($fieldGroups as $groupName => $fields)
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0 text-capitalize">{{ str_replace('_', ' ', $groupName) }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th width="5%">#</th>
                                                        <th width="25%">Field Name</th>
                                                        <th width="15%">Label</th>
                                                        <th width="10%">Visible</th>
                                                        <th width="10%">Required</th>
                                                        <th width="20%">Description</th>
                                                        <th width="15%">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($fields as $field)
                                                        <tr>
                                                            <td>{{ $field->sort_order }}</td>
                                                            <td>
                                                                <code>{{ $field->field_name }}</code>
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control form-control-sm"
                                                                    value="{{ $field->field_label }}"
                                                                    data-field-id="{{ $field->id }}"
                                                                    data-field="field_label" disabled>
                                                            </td>
                                                            <td>
                                                                <div class="form-check form-switch">
                                                                    <input class="form-check-input visibility-toggle"
                                                                        type="checkbox" data-field-id="{{ $field->id }}"
                                                                        {{ $field->is_visible ? 'checked' : '' }}>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="form-check form-switch">
                                                                    <input class="form-check-input required-toggle"
                                                                        type="checkbox" data-field-id="{{ $field->id }}"
                                                                        {{ $field->is_required ? 'checked' : '' }}>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <textarea class="form-control form-control-sm" rows="2" data-field-id="{{ $field->id }}"
                                                                    data-field="description">{{ $field->description }}</textarea>
                                                            </td>
                                                            <td>
                                                                <div class="btn-group btn-group-sm">
                                                                    <button type="button" class="btn btn-outline-primary"
                                                                        onclick="editField({{ $field->id }})"
                                                                        title="Edit">
                                                                        <i class="fas fa-edit"></i>
                                                                    </button>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Field Modal -->
    <div class="modal fade" id="editFieldModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Field Settings</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editFieldForm">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <input type="hidden" id="edit_field_id" name="field_id">
                        <div class="mb-3">
                            <label for="edit_field_label" class="form-label">Field Label</label>
                            <input type="text" class="form-control" id="edit_field_label" name="field_label" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_description" class="form-label">Description</label>
                            <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="edit_sort_order" class="form-label">Sort Order</label>
                            <input type="number" class="form-control" id="edit_sort_order" name="sort_order"
                                min="0">
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="edit_is_visible" name="is_visible">
                                    <label class="form-check-label" for="edit_is_visible">Visible</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="edit_is_required"
                                        name="is_required">
                                    <label class="form-check-label" for="edit_is_required">Required</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @parent
    <script>
        function editField(fieldId) {
            // Get field data from the table row
            const row = document.querySelector(`[data-field-id="${fieldId}"]`).closest('tr');
            const fieldLabel = row.querySelector('[data-field="field_label"]').value;
            const description = row.querySelector('[data-field="description"]').value;
            const isVisible = row.querySelector('.visibility-toggle').checked;
            const isRequired = row.querySelector('.required-toggle').checked;

            // Populate modal
            document.getElementById('edit_field_id').value = fieldId;
            document.getElementById('edit_field_label').value = fieldLabel;
            document.getElementById('edit_description').value = description;
            document.getElementById('edit_is_visible').checked = isVisible;
            document.getElementById('edit_is_required').checked = isRequired;

            // Show modal
            new bootstrap.Modal(document.getElementById('editFieldModal')).show();
        }

        function toggleVisibility(fieldId) {
            const checkbox = document.querySelector(`[data-field-id="${fieldId}"].visibility-toggle`);
            const newValue = !checkbox.checked;

            fetch(`{{ route('admin.beneficiary-field-visibility.toggle-visibility', '') }}/${fieldId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        checkbox.checked = data.is_visible;
                        showAlert('success', data.message);
                    } else {
                        showAlert('error', 'Failed to toggle visibility');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('error', 'An error occurred');
                });
        }

        function toggleRequired(fieldId) {
            const checkbox = document.querySelector(`[data-field-id="${fieldId}"].required-toggle`);

            fetch(`{{ route('admin.beneficiary-field-visibility.toggle-required', '') }}/${fieldId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        checkbox.checked = data.is_required;
                        showAlert('success', data.message);
                    } else {
                        showAlert('error', 'Failed to toggle requirement');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('error', 'An error occurred');
                });
        }

        function saveAllSettings() {
            const fields = [];
            const form = document.getElementById('bulkUpdateForm');

            // Collect all field data
            document.querySelectorAll('[data-field-id]').forEach(input => {
                const fieldId = input.getAttribute('data-field-id');
                const fieldType = input.getAttribute('data-field');

                if (fieldType) {
                    let fieldData = fields.find(f => f.id == fieldId);
                    if (!fieldData) {
                        fieldData = {
                            id: fieldId
                        };
                        fields.push(fieldData);
                    }
                    fieldData[fieldType] = input.value;
                }
            });

            // Add visibility and required states
            document.querySelectorAll('.visibility-toggle, .required-toggle').forEach(checkbox => {
                const fieldId = checkbox.getAttribute('data-field-id');
                const fieldData = fields.find(f => f.id == fieldId);
                if (fieldData) {
                    if (checkbox.classList.contains('visibility-toggle')) {
                        fieldData.is_visible = checkbox.checked;
                    } else if (checkbox.classList.contains('required-toggle')) {
                        fieldData.is_required = checkbox.checked;
                    }
                }
            });

            // Send bulk update request
            fetch('{{ route('admin.beneficiary-field-visibility.bulk-update') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        fields: fields
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert('success', data.message);
                    } else {
                        showAlert('error', 'Failed to save settings');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('error', 'An error occurred');
                });
        }

        function showAlert(type, message) {
            // You can implement your preferred alert system here
            alert(message);
        }

        // Handle edit form submission
        document.getElementById('editFieldForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const fieldId = formData.get('field_id');

            fetch(`{{ route('admin.beneficiary-field-visibility.update', '') }}/${fieldId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update the table row
                        const row = document.querySelector(`[data-field-id="${fieldId}"]`).closest('tr');
                        row.querySelector('[data-field="field_label"]').value = formData.get('field_label');
                        row.querySelector('[data-field="description"]').value = formData.get('description');

                        // Close modal
                        bootstrap.Modal.getInstance(document.getElementById('editFieldModal')).hide();
                        showAlert('success', data.message);
                    } else {
                        showAlert('error', 'Failed to update field');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('error', 'An error occurred');
                });
        });
    </script>
@endsection
