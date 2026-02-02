<table class=" table table-bordered table-striped table-hover w-100">
    <thead>
        <tr>
            <th>
                {{ trans('cruds.beneficiaryOrderFollowup.fields.id') }}
            </th>
            <th>
                {{ trans('cruds.beneficiaryOrderFollowup.fields.date') }}
            </th>
            <th>
                {{ trans('cruds.beneficiaryOrderFollowup.fields.user') }}
            </th>
            <th>
                {{ trans('cruds.beneficiaryOrderFollowup.fields.comment') }}
            </th>
            <th>
                {{ trans('cruds.beneficiaryOrderFollowup.fields.attachments') }}
            </th>
            <th>
                &nbsp;
            </th>
        </tr>
    </thead>
    <tbody>
        @if ($beneficiaryOrderFollowups && $beneficiaryOrderFollowups->count() > 0)
            @foreach ($beneficiaryOrderFollowups as $followup)
                <tr id="tr-beneficiary-order-followup-{{ $followup->id }}">
                    <td>{{ $followup->id }}</td> 
                    <td>{{ $followup->date }}</td>
                    <td>{{ $followup->user->name ?? '' }}</td>
                    <td> 
                        {{ $followup->comment }}
                    </td>
                    <td> 
                        @foreach ($followup->attachments as $attachment)
                            <a href="{{ $attachment->getUrl() }}" class="btn btn-link btn-sm" target="_blank">
                                <i class="ri-file-line"></i> {{ trans('global.view') }}
                            </a>
                        @endforeach
                    </td>
                    <td> 
                        @can('beneficiary_order_followup_edit')
                            <a class="btn btn-sm btn-icon btn-info-light" href="javascript:void(0)"
                                onclick="showAjaxModal('{{ route('admin.beneficiary-order-followups.edit') }}',{id: {{ $followup->id }}})">
                                <i class="ri-edit-line"></i>
                            </a>
                        @endcan
                        @can('beneficiary_order_followup_delete')
                            <button type="button" class="btn btn-sm btn-icon btn-danger-light"
                                onclick="ajaxDeleteFromTable('{{ route('admin.beneficiary-order-followups.destroy') }}', '{{ $followup->id }}', 'tr-beneficiary-order-followup-{{ $followup->id }}')">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        @endcan

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
