@extends('layouts.master')
@section('content')

<div class="card mt-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>{{ trans('cruds.beneficiaryReport.title') }}</span>
        @if(!empty($beneficiaries) && $beneficiaries->total() > 0)
            <a href="{{ route('admin.beneficiary-reports.export', request()->all()) }}" class="btn btn-success btn-sm">
                {{ trans('global.export') }}
            </a>
        @endif
    </div>

    <div class="card-body">
        <form method="GET" action="{{ route('admin.beneficiary-reports.index') }}" class="mb-4">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label">الاسم / البريد / اسم المستخدم</label>
                    <input type="text" name="name" value="{{ $filters['name'] ?? '' }}" class="form-control" placeholder="مثل: أحمد" />
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">رقم الهوية</label>
                    <input type="text" name="identity_num" value="{{ $filters['identity_num'] ?? '' }}" class="form-control" />
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">الهاتف</label>
                    <input type="text" name="phone" value="{{ $filters['phone'] ?? '' }}" class="form-control" />
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">حالة الملف</label>
                    <select name="profile_status" class="form-control select2">
                        <option value="">الكل</option>
                        @foreach($profileStatusOptions as $key => $label)
                            <option value="{{ $key }}" {{ ($filters['profile_status'] ?? '') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label">المنطقة</label>
                    <select name="region_id" class="form-control select2">
                        <option value="">الكل</option>
                        @foreach($regions as $id => $name)
                            <option value="{{ $id }}" {{ (string)($filters['region_id'] ?? '') === (string)$id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">المدينة</label>
                    <select name="city_id" class="form-control select2">
                        <option value="">الكل</option>
                        @foreach($cities as $id => $name)
                            <option value="{{ $id }}" {{ (string)($filters['city_id'] ?? '') === (string)$id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">الحالة الاجتماعية</label>
                    <select name="marital_status_id" class="form-control select2">
                        <option value="">الكل</option>
                        @foreach($maritalStatuses as $id => $name)
                            <option value="{{ $id }}" {{ (string)($filters['marital_status_id'] ?? '') === (string)$id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                @field('beneficiary_category_id')
                    <div class="col-md-3 mb-3">
                        <label class="form-label">فئة المستفيد</label>
                        <select name="beneficiary_category_id" class="form-control select2">
                            <option value="">الكل</option>
                            @foreach($beneficiaryCategories as $id => $name)
                                <option value="{{ $id }}" {{ (string)($filters['beneficiary_category_id'] ?? '') === (string)$id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endfield
                <div class="col-md-3 mb-3">
                    <label class="form-label">قابل للعمل</label>
                    <select name="can_work" class="form-control select2">
                        <option value="">الكل</option>
                        @foreach($canWorkOptions as $key => $label)
                            <option value="{{ $key }}" {{ ($filters['can_work'] ?? '') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label">الأخصائي</label>
                    <select name="specialist_id" class="form-control select2">
                        <option value="">الكل</option>
                        @foreach($specialists as $id => $name)
                            <option value="{{ $id }}" {{ (string)($filters['specialist_id'] ?? '') === (string)$id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">من تاريخ الإنشاء</label>
                    <input type="date" name="created_from" value="{{ $filters['created_from'] ?? '' }}" class="form-control" />
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">إلى تاريخ الإنشاء</label>
                    <input type="date" name="created_to" value="{{ $filters['created_to'] ?? '' }}" class="form-control" />
                </div>
            </div>
            
            <!-- Column Selector -->
            <div class="row mt-3">
                <div class="col-12">
                    <div class="card border-primary">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">اختر الأعمدة المراد عرضها</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @if(isset($availableColumns) && !empty($availableColumns))
                                    @foreach($availableColumns as $columnKey => $columnLabel)
                                        <div class="col-md-3 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input column-checkbox" type="checkbox" 
                                                       name="columns[]" 
                                                       value="{{ $columnKey }}" 
                                                       id="column_{{ $columnKey }}"
                                                       {{ in_array($columnKey, $selectedColumns ?? []) ? 'checked' : '' }}
                                                       {{ $columnKey === 'id' ? 'disabled' : '' }}>
                                                <label class="form-check-label {{ $columnKey === 'id' ? 'text-muted' : '' }}" for="column_{{ $columnKey }}">
                                                    {{ $columnLabel }}
                                                    @if($columnKey === 'id')
                                                        <small class="text-muted">(إجباري)</small>
                                                    @endif
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            <div class="mt-2">
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAllColumns()">تحديد الكل</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deselectAllColumns()">إلغاء تحديد الكل</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="d-flex gap-2 mt-3">
                <button type="submit" class="btn btn-primary">بحث</button>
                <a href="{{ route('admin.beneficiary-reports.index') }}" class="btn btn-secondary">تفريغ</a>
            </div>
        </form>
    </div>
</div> 

<div class="card">

    <div class="card-body">
        @isset($beneficiaries)
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover datatable">
                    <thead>
                        <tr>
                            @if(isset($selectedColumns) && !empty($selectedColumns))
                                @foreach($selectedColumns as $columnKey)
                                    @if(isset($availableColumns[$columnKey]))
                                        <th>{{ $availableColumns[$columnKey] }}</th>
                                    @endif
                                @endforeach
                            @else
                                <th colspan="1" class="text-center">لا توجد أعمدة محددة</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($beneficiaries as $beneficiary)
                            <tr>
                                @if(isset($selectedColumns) && !empty($selectedColumns))
                                    @foreach($selectedColumns as $columnKey)
                                        @if(isset($availableColumns[$columnKey]))
                                            <td>
                                            @switch($columnKey)
                                                @case('id')
                                                    {{ $beneficiary->id }}
                                                    @break
                                                @case('name')
                                                    {{ $beneficiary->user->name ?? '-' }}
                                                    @break
                                                @case('email')
                                                    {{ $beneficiary->user->email ?? '-' }}
                                                    @break
                                                @case('identity_num')
                                                    {{ $beneficiary->user->identity_num ?? '-' }}
                                                    @break
                                                @case('phone')
                                                    {{ $beneficiary->user->phone ?? '-' }}
                                                    @break
                                                @case('profile_status')
                                                    {{ \App\Models\Beneficiary::PROFILE_STATUS_SELECT[$beneficiary->profile_status] ?? $beneficiary->profile_status }}
                                                    @break
                                                @case('region')
                                                    {{ $beneficiary->region->name ?? '-' }}
                                                    @break
                                                @case('city')
                                                    {{ $beneficiary->city->name ?? '-' }}
                                                    @break
                                                @case('district')
                                                    {{ $beneficiary->district->name ?? '-' }}
                                                    @break
                                                @case('nationality')
                                                    {{ $beneficiary->nationality->name ?? '-' }}
                                                    @break
                                                @case('marital_status')
                                                    {{ $beneficiary->marital_status->name ?? '-' }}
                                                    @break
                                                @case('beneficiary_category')
                                                    {{ $beneficiary->beneficiary_category->name ?? '-' }}
                                                    @break
                                                @case('can_work')
                                                    {{ \App\Models\Beneficiary::CAN_WORK_SELECT[$beneficiary->can_work] ?? $beneficiary->can_work }}
                                                    @break
                                                @case('specialist')
                                                    {{ $beneficiary->specialist->name ?? '-' }}
                                                    @break
                                                @case('created_at')
                                                    {{ $beneficiary->created_at?->format('Y-m-d') }}
                                                    @break
                                                @default
                                                    -
                                            @endswitch
                                            </td>
                                        @endif
                                    @endforeach
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ isset($selectedColumns) && !empty($selectedColumns) ? count($selectedColumns) : 1 }}" class="text-center">لا توجد نتائج</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div>
                {{ $beneficiaries->links() }}
            </div>
        @endisset
    </div>
</div>



@endsection

@section('scripts')
<script>
    function selectAllColumns() {
        document.querySelectorAll('.column-checkbox').forEach(checkbox => {
            if (!checkbox.disabled) {
                checkbox.checked = true;
            }
        });
    }
    
    function deselectAllColumns() {
        document.querySelectorAll('.column-checkbox').forEach(checkbox => {
            if (!checkbox.disabled) {
                checkbox.checked = false;
            }
        });
    }
    
    // Ensure ID column is always included in form submission
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form[method="GET"]');
        if (form) {
            // Always add ID as hidden input since checkbox is disabled
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'columns[]';
            hiddenInput.value = 'id';
            form.appendChild(hiddenInput);
        }
    });
</script>
@endsection