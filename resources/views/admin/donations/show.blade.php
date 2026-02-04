@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.donationManagement.title'), 'url' => '#'],
            ['title' => trans('cruds.donation.title_singular') . ' #' . $donation->id, 'url' => '#'],
        ];
    @endphp
    @include('partials.breadcrumb')

    <div class="card mb-3">
        <div class="card-body">
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <th>{{ trans('cruds.donation.fields.id') }}</th>
                        <td>{{ $donation->id }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.donation.fields.donator') }}</th>
                        <td>{{ $donation->donator?->name }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.donation.fields.project') }}</th>
                        <td>{{ $donation->project?->name }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.donation.fields.donation_type') }}</th>
                        <td>{{ \App\Models\Donation::DONATION_TYPE_SELECT[$donation->donation_type] ?? $donation->donation_type }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.donation.fields.total_amount') }}</th>
                        <td>{{ number_format($donation->total_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.donation.fields.donated_at') }}</th>
                        <td>{{ $donation->donated_at }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.donation.fields.notes') }}</th>
                        <td>{{ $donation->notes }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    @if ($donation->items->count())
        <div class="card">
            <div class="card-header">
                {{ trans('cruds.donation.fields.items') }}
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>{{ trans('cruds.donationItem.fields.item_name') }}</th>
                                <th>{{ trans('cruds.donationItem.fields.quantity') }}</th>
                                <th>{{ trans('cruds.donationItem.fields.unit_price') }}</th>
                                <th>{{ trans('cruds.donationItem.fields.total_price') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($donation->items as $item)
                                <tr>
                                    <td>{{ $item->item_name }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ number_format($item->unit_price, 2) }}</td>
                                    <td>{{ number_format($item->total_price, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
@endsection

