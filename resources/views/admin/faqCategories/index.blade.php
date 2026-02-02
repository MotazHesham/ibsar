@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.generalSetting.title'), 'url' => '#'],
            [
                'title' => trans('global.list') . ' ' . trans('cruds.faqCategory.title'),
                'url' => route('admin.faq-categories.index'),
            ],
        ];
        $buttons = [
            [
                'title' => trans('global.add') . ' ' . trans('cruds.faqCategory.title_singular'),
                'url' => route('admin.faq-categories.create'),
                'permission' => 'faq_category_create',
            ],
        ];
    @endphp
    @include('partials.breadcrumb')
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover w-100 datatable-FaqCategory">
                    <thead>
                        <tr>
                            <th width="10">

                            </th>
                            <th>
                                {{ trans('cruds.faqCategory.fields.id') }}
                            </th>
                            <th>
                                {{ trans('cruds.faqCategory.fields.category') }}
                            </th>
                            <th>
                                &nbsp;
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($faqCategories as $key => $faqCategory)
                            <tr data-entry-id="{{ $faqCategory->id }}">
                                <td>

                                </td>
                                <td>
                                    {{ $faqCategory->id ?? '' }}
                                </td>
                                <td>
                                    {{ $faqCategory->category ?? '' }}
                                </td>
                                <td>
                                    @include('partials.datatablesActions', [
                                        'viewGate' => 'faq_category_show',
                                        'editGate' => 'faq_category_edit',
                                        'deleteGate' => 'faq_category_delete',
                                        'crudRoutePart' => 'faq-categories',
                                        'row' => $faqCategory,
                                    ])
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @parent
    <script>
        $(function() {
            let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
            @can('faq_category_delete')
                let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
                let deleteButton = {
                    text: deleteButtonTrans,
                    url: "{{ route('admin.faq-categories.massDestroy') }}",
                    className: 'btn-danger-light rounded-pill',
                    action: function(e, dt, node, config) {
                        var ids = $.map(dt.rows({
                            selected: true
                        }).nodes(), function(entry) {
                            return $(entry).data('entry-id')
                        });

                        if (ids.length === 0) {
                            alert('{{ trans('global.datatables.zero_selected') }}')

                            return
                        }

                        if (confirm('{{ trans('global.areYouSure') }}')) {
                            $.ajax({
                                    headers: {
                                        'x-csrf-token': _token
                                    },
                                    method: 'POST',
                                    url: config.url,
                                    data: {
                                        ids: ids,
                                        _method: 'DELETE'
                                    }
                                })
                                .done(function() {
                                    location.reload()
                                })
                        }
                    }
                }
                dtButtons.push(deleteButton)
            @endcan

            $.extend(true, $.fn.dataTable.defaults, {
                orderCellsTop: true,
                order: [
                    [1, 'desc']
                ],
                pageLength: 100,
            });
            let table = $('.datatable-FaqCategory:not(.ajaxTable)').DataTable({
                buttons: dtButtons
            })
            $('a[data-bs-toggle="tab"]').on('shown.bs.tab click', function(e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });

        })
    </script>
@endsection
