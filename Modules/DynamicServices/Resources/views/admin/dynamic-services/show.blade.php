@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.dynamicServicesManagement.title'), 'url' => '#'],
            [
                'title' => trans('global.list') . ' ' . trans('cruds.dynamicService.title'),
                'url' => route('admin.dynamic-services.index'),
            ],
            ['title' => trans('global.show') . ' ' . trans('cruds.dynamicService.title_singular'), 'url' => '#'],
        ];
        $pageTitle =
            trans('global.show') . ' ' . trans('cruds.dynamicService.title_singular') . ' #' . $dynamicService->id;
    @endphp
    @include('partials.breadcrumb')

    <div class="row">
        <div class="col-xxl-8">
            <div class="card custom-card">
                <div class="card-header">
                    <h6 class="card-title">{{ trans('cruds.dynamicService.fields.basic_info') }}</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label
                                    class="form-label fw-semibold">{{ trans('cruds.dynamicService.fields.title') }}</label>
                                <p class="form-control-static">{{ $dynamicService->title }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label
                                    class="form-label fw-semibold">{{ trans('cruds.dynamicService.fields.slug') }}</label>
                                <p class="form-control-static">{{ $dynamicService->slug }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label
                                    class="form-label fw-semibold">{{ trans('cruds.dynamicService.fields.status') }}</label>
                                <p class="form-control-static">
                                    <span
                                        class="badge bg-{{ $dynamicService->status == 'active' ? 'success' : 'danger' }}-transparent">
                                        {{ trans('global.' . $dynamicService->status) }}
                                    </span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label
                                    class="form-label fw-semibold">{{ trans('cruds.dynamicService.fields.icon') }}</label>
                                <p class="form-control-static">
                                    @if ($dynamicService->icon)
                                        <i class="{{ $dynamicService->icon }}"></i> {{ $dynamicService->icon }} 
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label
                                    class="form-label fw-semibold">{{ trans('cruds.dynamicService.fields.description') }}</label>
                                <p class="form-control-static">
                                    {{ $dynamicService->description ?? '' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Beneficiaries Requests Section -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card custom-card">
                <div class="card-header">
                    <h6 class="card-title">
                        {{ trans('cruds.dynamicService.fields.beneficiaries_requests') ?? 'طلبات المستفيدين' }}</h6>
                </div>
                <div class="card-body">
                    @if ($dynamicServiceOrders && $dynamicServiceOrders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ trans('cruds.beneficiaryOrder.fields.id') }}</th>
                                        <th>{{ trans('cruds.beneficiaryOrder.fields.beneficiary') }}</th>
                                        <th>{{ trans('cruds.beneficiaryOrder.fields.title') }}</th>
                                        <th>{{ trans('cruds.beneficiaryOrder.fields.status') }}</th>
                                        <th>{{ trans('cruds.beneficiaryOrder.fields.accept_status') }}</th>
                                        <th>{{ trans('cruds.beneficiaryOrder.fields.specialist') }}</th>
                                        <th>{{ trans('cruds.beneficiaryOrder.fields.created_at') }}</th>
                                        <th>&nbsp;</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($dynamicServiceOrders as $index => $dynamicServiceOrder)
                                        @php
                                            $beneficiaryOrder = $dynamicServiceOrder->beneficiaryOrder;
                                        @endphp
                                        @if ($beneficiaryOrder)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $beneficiaryOrder->id }}</td>
                                                <td>
                                                    @if ($beneficiaryOrder->beneficiary && $beneficiaryOrder->beneficiary->user)
                                                        {{ $beneficiaryOrder->beneficiary->user->name }}
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>{{ $beneficiaryOrder->title ?? '-' }}</td>
                                                <td>
                                                    @if ($beneficiaryOrder->status)
                                                        <span
                                                            class="badge bg-{{ $beneficiaryOrder->status->badge_class ?? 'primary' }}-transparent">
                                                            {{ $beneficiaryOrder->status->name }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($beneficiaryOrder->accept_status)
                                                        <span
                                                            class="badge bg-{{ $beneficiaryOrder->accept_status == 'yes' ? 'success' : 'danger' }}-transparent">
                                                            {{ \App\Models\BeneficiaryOrder::ACCEPT_STATUS_RADIO[$beneficiaryOrder->accept_status] ?? $beneficiaryOrder->accept_status }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($beneficiaryOrder->specialist)
                                                        {{ $beneficiaryOrder->specialist->name }}
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>{{ $beneficiaryOrder->created_at ? $beneficiaryOrder->created_at->format('Y-m-d H:i') : '-' }}
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.beneficiary-orders.show', $beneficiaryOrder->id) }}"
                                                        class="btn btn-sm btn-primary">
                                                        <i class="ri-eye-line"></i> {{ trans('global.view') }}
                                                    </a>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            {{ trans('cruds.dynamicService.fields.no_beneficiaries_requests') ?? 'لا توجد طلبات من المستفيدين لهذه الخدمة' }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Program Meetings Section (for training category) -->
    @if ($dynamicService->category === 'training')
        <div class="row mt-4">
            <div class="col-12">
                <div class="card custom-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="card-title mb-0">جدول اللقاءات</h6>
                        <button type="button" class="btn btn-sm btn-primary" onclick="toggleProgramMeetingsForm()">
                            <i class="ri-edit-line"></i> {{ $dynamicService->program_meetings ? 'تعديل' : 'إضافة' }}
                        </button>
                    </div>
                    <div class="card-body">
                        @if ($dynamicService->program_meetings && count($dynamicService->program_meetings) > 0)
                            <div id="program_meetings_display">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>تاريخ ووقت الاجتماع</th>
                                                <th>عنوان الاجتماع</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($dynamicService->program_meetings as $index => $meeting)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>
                                                        @if (!empty($meeting['date']))
                                                            {{ \Carbon\Carbon::parse($meeting['date'])->format('Y-m-d H:i') }}
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $meeting['title'] ?? '-' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @else
                            <div id="program_meetings_display">
                                <div class="alert alert-info">لا توجد اجتماعات محددة</div>
                            </div>
                        @endif

                        <div id="program_meetings_form" style="display: none;">
                            <form id="programMeetingsForm" method="POST" action="{{ route('admin.dynamic-services.update-program-meetings', $dynamicService->id) }}">
                                @csrf
                                @method('PUT')
                                <div id="program_meetings_container">
                                    @if ($dynamicService->program_meetings && count($dynamicService->program_meetings) > 0)
                                        @foreach ($dynamicService->program_meetings as $index => $meeting)
                                            <div class="meeting-item mb-2 border rounded p-3">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <h6 class="mb-0">اجتماع {{ $index + 1 }}</h6>
                                                    <button type="button" class="btn btn-sm btn-danger" onclick="removeProgramMeeting(this)">
                                                        <i class="ri-delete-bin-line"></i> حذف
                                                    </button>
                                                </div>
                                                <input type="datetime-local" name="program_meetings[{{ $index }}][date]" 
                                                    class="form-control mb-2 program-meeting-date"
                                                    placeholder="تاريخ ووقت الاجتماع"
                                                    value="{{ !empty($meeting['date']) ? \Carbon\Carbon::parse($meeting['date'])->format('Y-m-d\TH:i') : '' }}">
                                                <input type="text" name="program_meetings[{{ $index }}][title]" 
                                                    class="form-control"
                                                    placeholder="عنوان الاجتماع"
                                                    value="{{ $meeting['title'] ?? '' }}">
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="meeting-item mb-2 border rounded p-3">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="mb-0">اجتماع 1</h6>
                                                <button type="button" class="btn btn-sm btn-danger" onclick="removeProgramMeeting(this)">
                                                    <i class="ri-delete-bin-line"></i> حذف
                                                </button>
                                            </div>
                                            <input type="datetime-local" name="program_meetings[0][date]" 
                                                class="form-control mb-2 program-meeting-date"
                                                placeholder="تاريخ ووقت الاجتماع">
                                            <input type="text" name="program_meetings[0][title]" 
                                                class="form-control"
                                                placeholder="عنوان الاجتماع">
                                        </div>
                                    @endif
                                </div>
                                <div class="d-flex gap-2 mt-3">
                                    <button type="button" class="btn btn-sm btn-secondary" onclick="addProgramMeeting()">
                                        <i class="ri-add-line"></i> إضافة اجتماع
                                    </button>
                                    <button type="submit" class="btn btn-sm btn-primary">
                                        <i class="ri-save-line"></i> حفظ
                                    </button>
                                    <button type="button" class="btn btn-sm btn-secondary" onclick="cancelProgramMeetingsForm()">
                                        <i class="ri-close-line"></i> إلغاء
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Form Fields Section -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card custom-card">
                <div class="card-header">
                    <h6 class="card-title">{{ trans('cruds.dynamicService.fields.form_fields') }}</h6>
                </div>
                <div class="card-body">
                    @if ($dynamicService->form_fields)
                        @php
                            $formFields = json_decode($dynamicService->form_fields, true);
                        @endphp
                        <div class="row">
                            @foreach ($formFields as $field)
                                <div class="col-md-6 mb-3">
                                    <div class="border rounded p-3">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="mb-0">{{ $field['label'] }}</h6>
                                            <span class="badge bg-primary-transparent">{{ $field['type'] }}</span>
                                        </div>
                                        <div class="row">
                                            <div class="col-6">
                                                <small
                                                    class="text-muted">{{ trans('cruds.dynamicService.fields.field_grid') }}:</small>
                                                <p class="mb-1">{{ $field['grid'] }}</p>
                                            </div>
                                            <div class="col-6">
                                                <small
                                                    class="text-muted">{{ trans('cruds.dynamicService.fields.field_required') }}:</small>
                                                <p class="mb-1">
                                                    <span
                                                        class="badge bg-{{ $field['required'] ? 'success' : 'secondary' }}-transparent">
                                                        {{ $field['required'] ? trans('global.yes') : trans('global.no') }}
                                                    </span>
                                                </p>
                                            </div>
                                            @if (!empty($field['options']))
                                                <div class="col-12">
                                                    <small
                                                        class="text-muted">{{ trans('cruds.dynamicService.fields.field_options') }}:</small>
                                                    <ul class="list-unstyled mb-0">
                                                        @foreach ($field['options'] as $option)
                                                            <li><i
                                                                    class="fas fa-check text-success me-2"></i>{{ $option }}
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info">
                            {{ trans('cruds.dynamicService.fields.no_form_fields') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@if ($dynamicService->category === 'training')
    @section('scripts')
        @parent
        <script>
            function toggleProgramMeetingsForm() {
                const display = document.getElementById('program_meetings_display');
                const form = document.getElementById('program_meetings_form');
                
                if (form.style.display === 'none') {
                    display.style.display = 'none';
                    form.style.display = 'block';
                } else {
                    display.style.display = 'block';
                    form.style.display = 'none';
                }
            }

            function cancelProgramMeetingsForm() {
                const display = document.getElementById('program_meetings_display');
                const form = document.getElementById('program_meetings_form');
                
                display.style.display = 'block';
                form.style.display = 'none';
            }

            function addProgramMeeting() {
                const container = document.getElementById('program_meetings_container');
                const meetingCount = container.querySelectorAll('.meeting-item').length;
                const newMeeting = document.createElement('div');
                newMeeting.className = 'meeting-item mb-2 border rounded p-3';
                newMeeting.innerHTML = `
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h6 class="mb-0">اجتماع ${meetingCount + 1}</h6>
                        <button type="button" class="btn btn-sm btn-danger" onclick="removeProgramMeeting(this)">
                            <i class="ri-delete-bin-line"></i> حذف
                        </button>
                    </div>
                    <input type="datetime-local" name="program_meetings[${meetingCount}][date]" 
                        class="form-control mb-2 program-meeting-date"
                        placeholder="تاريخ ووقت الاجتماع">
                    <input type="text" name="program_meetings[${meetingCount}][title]" 
                        class="form-control"
                        placeholder="عنوان الاجتماع">
                `;
                container.appendChild(newMeeting);
            }

            function removeProgramMeeting(button) {
                const container = document.getElementById('program_meetings_container');
                const items = container.querySelectorAll('.meeting-item');
                
                // Don't allow removing if it's the last item
                if (items.length > 1) {
                    button.closest('.meeting-item').remove();
                    // Renumber the remaining items
                    container.querySelectorAll('.meeting-item').forEach((item, index) => {
                        item.querySelector('h6').textContent = `اجتماع ${index + 1}`;
                        const dateInput = item.querySelector('input[type="datetime-local"]');
                        const titleInput = item.querySelector('input[type="text"]');
                        dateInput.name = `program_meetings[${index}][date]`;
                        titleInput.name = `program_meetings[${index}][title]`;
                    });
                } else {
                    alert('يجب أن يكون هناك اجتماع واحد على الأقل');
                }
            }

            // Handle form submission
            document.getElementById('programMeetingsForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                
                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message || 'حدث خطأ أثناء الحفظ');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('حدث خطأ أثناء الحفظ');
                });
            });
        </script>
    @endsection
@endif
