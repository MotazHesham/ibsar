@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.generalSetting.title'), 'url' => '#'],
            [
                'title' => trans('global.list') . ' ' . trans('cruds.faqQuestion.title'),
                'url' => route('admin.faq-questions.index'),
            ],
        ];
        $buttons = [
            [
                'title' => trans('global.add') . ' ' . trans('cruds.faqQuestion.title_singular'),
                'url' => route('admin.faq-questions.create'),
                'permission' => 'faq_question_create',
            ],
        ];
    @endphp
    @include('partials.breadcrumb')
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover w-100 datatable-FaqQuestion">
                    <thead>
                        <tr>
                            <th width="10">

                            </th>
                            <th>
                                {{ trans('cruds.faqQuestion.fields.id') }}
                            </th>
                            <th>
                                {{ trans('cruds.faqQuestion.fields.category') }}
                            </th>
                            <th>
                                {{ trans('cruds.faqQuestion.fields.question') }}
                            </th>
                            <th>
                                {{ trans('cruds.faqQuestion.fields.answer') }}
                            </th>
                            <th>
                                &nbsp;
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($faqQuestions as $key => $faqQuestion)
                            <tr data-entry-id="{{ $faqQuestion->id }}">
                                <td>

                                </td>
                                <td>
                                    {{ $faqQuestion->id ?? '' }}
                                </td>
                                <td>
                                    {{ $faqQuestion->category->category ?? '' }}
                                </td>
                                <td>
                                    {{ $faqQuestion->question ?? '' }}
                                </td>
                                <td>
                                    {{ $faqQuestion->answer ?? '' }}
                                </td>
                                <td>
                                    @include('partials.datatablesActions', [
                                        'viewGate' => 'faq_question_show',
                                        'editGate' => 'faq_question_edit',
                                        'deleteGate' => 'faq_question_delete',
                                        'crudRoutePart' => 'faq-questions',
                                        'row' => $faqQuestion,
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
            @can('faq_question_delete')
                let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
                let deleteButton = {
                    text: deleteButtonTrans,
                    url: "{{ route('admin.faq-questions.massDestroy') }}",
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
            let table = $('.datatable-FaqQuestion:not(.ajaxTable)').DataTable({
                buttons: dtButtons
            })
            $('a[data-bs-toggle="tab"]').on('shown.bs.tab click', function(e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });

        })
    </script>
@endsection
