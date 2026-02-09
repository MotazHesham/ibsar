@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => 'سير العمل (المحرك الجديد)', 'url' => route('admin.workflow-instances.index')],
            ['title' => 'بدء سير عمل جديد', 'url' => '#'],
        ];
        $pageTitle = 'بدء سير عمل جديد';
    @endphp
    @include('partials.breadcrumb')

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title">اختر السير والجهة</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.workflow-instances.start') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="workflow_id" class="form-label">السير <span class="text-danger">*</span></label>
                            <select name="workflow_id" id="workflow_id" class="form-select" required>
                                <option value="">-- اختر السير --</option>
                                @foreach($workflows as $wf)
                                    <option value="{{ $wf->id }}" {{ old('workflow_id') == $wf->id ? 'selected' : '' }}>{{ $wf->name }} ({{ $wf->key }})</option>
                                @endforeach
                            </select>
                            @error('workflow_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="entity_type" class="form-label">نوع الجهة <span class="text-danger">*</span></label>
                            <input type="text" name="entity_type" id="entity_type" class="form-control" value="{{ old('entity_type', $entityType) }}" placeholder="مثال: App\Models\DynamicServiceOrder" required>
                            @error('entity_type')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="entity_id" class="form-label">معرف الجهة <span class="text-danger">*</span></label>
                            <input type="number" name="entity_id" id="entity_id" class="form-control" value="{{ old('entity_id', $entityId) }}" min="1" required>
                            @error('entity_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        @error('error')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                        <button type="submit" class="btn btn-primary">بدء السير</button>
                        <a href="{{ route('admin.workflow-instances.index') }}" class="btn btn-secondary">إلغاء</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
