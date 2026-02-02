{{-- Tab Navigation --}}
<ul class="nav nav-tabs" id="dynamicFormTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="order-info-tab" data-bs-toggle="tab" data-bs-target="#order-info" type="button"
            role="tab" aria-controls="order-info" aria-selected="true">
            {{ trans('cruds.service.extra.order_info') }}
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="dynamic-fields-tab" data-bs-toggle="tab" data-bs-target="#dynamic-fields"
            type="button" role="tab" aria-controls="dynamic-fields" aria-selected="false">
            {{ trans('cruds.service.extra.dynamic_fields') }}
        </button>
    </li>
</ul>

{{-- Tab Content --}}
<div class="tab-content" id="dynamicFormTabsContent">
    <div class="tab-pane fade show active" id="order-info" role="tabpanel" aria-labelledby="order-info-tab">
        @include('partials.beneficiaryOrderForm.basic-data')
    </div>
    <div class="tab-pane fade" id="dynamic-fields" role="tabpanel" aria-labelledby="dynamic-fields-tab">
        <div class="row">
            @if($dynamicService->form_fields)
                @foreach(json_decode($dynamicService->form_fields, true) ?? [] as $field)
                    @php
                        $fieldName = 'dynamic_field_' . $field['id'];
                        $fieldLabel = $field['label'] ?? '';
                        $fieldType = $field['type'] ?? 'text';
                        $fieldRequired = $field['required'] ?? false;
                        $fieldOptions = $field['options'] ?? [];
                        $fieldGrid = $field['grid'] ?? 'col-md-6';
                        $fieldValidation = $field['validation'] ?? '';
                        $fieldAttributes = $field['attributes'] ?? '';
                    @endphp

                    @switch($fieldType)
                        @case('text')
                            @include('utilities.form.text', [
                                'name' => $fieldName,
                                'label' => $fieldLabel,
                                'isRequired' => $fieldRequired,
                                'grid' => $fieldGrid,
                                'attributes' => $fieldAttributes,
                                'helperBlock' => '',
                            ])
                            @break

                        @case('textarea')
                            @include('utilities.form.textarea', [
                                'name' => $fieldName,
                                'label' => $fieldLabel,
                                'isRequired' => $fieldRequired,
                                'grid' => $fieldGrid,
                                'editor' => false,
                                'attributes' => $fieldAttributes,
                                'helperBlock' => '',
                            ])
                            @break

                        @case('select')
                            @include('utilities.form.select', [
                                'name' => $fieldName,
                                'label' => $fieldLabel,
                                'isRequired' => $fieldRequired,
                                'grid' => $fieldGrid,
                                'options' => $fieldOptions,
                                'search' => false,
                                'attributes' => $fieldAttributes,
                                'helperBlock' => '',
                            ])
                            @break

                        @case('radio')
                            @include('utilities.form.radio', [
                                'name' => $fieldName,
                                'label' => $fieldLabel,
                                'isRequired' => $fieldRequired,
                                'grid' => $fieldGrid,
                                'options' => $fieldOptions,
                                'attributes' => $fieldAttributes,
                                'value' => null,
                                'helperBlock' => '',
                            ])
                            @break

                        @case('checkbox')
                            @include('utilities.form.checkbox', [
                                'name' => $fieldName,
                                'label' => $fieldLabel,
                                'isRequired' => $fieldRequired,
                                'grid' => $fieldGrid,
                                'options' => $fieldOptions,
                                'attributes' => $fieldAttributes,
                                'helperBlock' => '',
                            ])
                            @break

                        @case('date')
                            @include('utilities.form.date', [
                                'name' => $fieldName,
                                'id' => $fieldName,
                                'label' => $fieldLabel,
                                'isRequired' => $fieldRequired,
                                'grid' => $fieldGrid,
                                'attributes' => $fieldAttributes,
                                'helperBlock' => '',
                            ])
                            @break

                        @case('time')
                            @include('utilities.form.time', [
                                'name' => $fieldName,
                                'id' => $fieldName,
                                'label' => $fieldLabel,
                                'isRequired' => $fieldRequired,
                                'grid' => $fieldGrid,
                                'attributes' => $fieldAttributes,
                                'helperBlock' => '',
                            ])
                            @break

                        @case('number')
                            @include('utilities.form.text', [
                                'name' => $fieldName,
                                'label' => $fieldLabel,
                                'isRequired' => $fieldRequired,
                                'grid' => $fieldGrid,
                                'type' => 'number',
                                'attributes' => $fieldAttributes,
                                'helperBlock' => '',
                            ])
                            @break

                        @default
                            @include('utilities.form.text', [
                                'name' => $fieldName,
                                'label' => $fieldLabel,
                                'isRequired' => $fieldRequired,
                                'grid' => $fieldGrid,
                                'attributes' => $fieldAttributes,
                                'helperBlock' => '',
                            ])
                    @endswitch
                @endforeach
            @else
                <div class="col-12">
                    <div class="alert alert-info">
                        {{ trans('cruds.service.extra.no_dynamic_fields') }}
                    </div>
                </div>
            @endif
        </div>

        <div class="d-grid gap-2 col-6 mx-auto">
            <button class="btn btn-primary rounded-pill btn-wave" type="submit" id="submitBtn">
                {{ trans('global.save') }}
            </button>
        </div>
    </div>
</div>

<style>
    .tab-pane {
        transition: opacity 0.3s ease-in-out;
    }

    .tab-pane.fade {
        opacity: 0;
    }

    .tab-pane.fade.show {
        opacity: 1;
    }
</style>
