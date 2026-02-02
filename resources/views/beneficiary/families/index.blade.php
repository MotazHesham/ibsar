<table class=" table table-bordered table-striped table-hover w-100">
    <thead>
        <tr> 
            <th>
                {{ trans('cruds.beneficiaryFamily.fields.id') }}
            </th>
            <th>
                {{ trans('cruds.beneficiaryFamily.fields.name') }}
            </th>
            <th>
                {{ trans('cruds.beneficiaryFamily.fields.identity_num') }}
            </th>
            <th>
                {{ trans('cruds.beneficiaryFamily.fields.phone') }}
            </th>
            <th>
                {{ trans('cruds.beneficiaryFamily.fields.email') }}
            </th>
            <th>
                &nbsp;
            </th>
        </tr>
    </thead>
    <tbody>
        @if ($beneficiaryFamilies && $beneficiaryFamilies->count() > 0)
            @foreach ($beneficiaryFamilies as $family)
                <tr id="tr-beneficiary-family-{{ $family->id }}"> 
                    <td>{{ $family->id }}</td>
                    <td> 
                        <div class="d-flex align-items-center gap-2">
                            <div class="lh-1">
                                @include('utilities.user-avatar', ['user' => $family])
                            </div>
                            <div>
                                <a href="javascript:void(0)" onclick="showAjaxOffcanvas('{{ route('beneficiary.beneficiary-families.show') }}',{id: {{ $family->id }}})">
                                    <span class="d-block fw-medium">
                                        {{ $family->name }}
                                    </span>
                                </a>
                                <span class="d-block text-muted fs-11" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-primary" title="Last Contacted">
                                    <i class="ri-account-circle-line me-1 fs-13 align-middle"></i>{{ $family->created_at->format('M, d Y - H:i A') }}</span>
                            </div>
                        </div>
                        
                    </td>
                    <td>{{ $family->identity_num }}</td>
                    <td><i class="ri-phone-line me-2 align-middle fs-14 text-muted"></i>{{ $family->phone }}</td>
                    <td><i class="ri-mail-line me-2 align-middle fs-14 text-muted"></i> {{ $family->email }}</td>
                    <td>   
                        <a class="btn btn-sm btn-icon btn-primary-light" href="javascript:void(0)" onclick="showAjaxOffcanvas('{{ route('beneficiary.beneficiary-families.show') }}',{id: {{ $family->id }}})">
                            <i class="ri-eye-line"></i>
                        </a> 
                        <a class="btn btn-sm btn-icon btn-info-light" href="javascript:void(0)" onclick="showAjaxModal('{{ route('beneficiary.beneficiary-families.edit') }}',{id: {{ $family->id }}})">
                            <i class="ri-edit-line"></i>
                        </a> 
                        <button type="button" class="btn btn-sm btn-icon btn-danger-light" onclick="ajaxDeleteFromTable('{{ route('beneficiary.beneficiary-families.destroy') }}', '{{ $family->id }}', 'tr-beneficiary-family-{{ $family->id }}')">
                            <i class="ri-delete-bin-line"></i>
                        </button>  
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="7" class="text-center">{{ trans('global.no_data_found') }}</td>
            </tr>
        @endif
    </tbody>
</table>
