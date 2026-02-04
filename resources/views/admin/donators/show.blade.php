@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.donationManagement.title'), 'url' => '#'],
            ['title' => trans('cruds.donator.title_singular') . ' #' . $donator->id, 'url' => '#'],
        ];
    @endphp
    @include('partials.breadcrumb')

    <div class="card mb-3">
        <div class="card-body">
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <th>{{ trans('cruds.donator.fields.id') }}</th>
                        <td>{{ $donator->id }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.donator.fields.name') }}</th>
                        <td>{{ $donator->name }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.donator.fields.email') }}</th>
                        <td>{{ $donator->email }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.donator.fields.phone') }}</th>
                        <td>{{ $donator->phone }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.donator.fields.notes') }}</th>
                        <td>{{ $donator->notes }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            {{ trans('cruds.donation.title') }}
        </div>
        <div class="card-body">
            @if($donator->donations->count())
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>{{ trans('cruds.donation.fields.id') }}</th>
                                <th>{{ trans('cruds.donation.fields.project') }}</th>
                                <th>{{ trans('cruds.donation.fields.donation_type') }}</th>
                                <th>{{ trans('cruds.donation.fields.total_amount') }}</th>
                                <th>{{ trans('cruds.donation.fields.donated_at') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($donator->donations as $donation)
                                <tr>
                                    <td><a href="{{ route('admin.donations.show', $donation->id) }}">{{ $donation->id }}</a></td>
                                    <td>{{ $donation->project?->name }}</td>
                                    <td>{{ \App\Models\Donation::DONATION_TYPE_SELECT[$donation->donation_type] ?? $donation->donation_type }}</td>
                                    <td>{{ number_format($donation->total_amount, 2) }}</td>
                                    <td>{{ $donation->donated_at }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted mb-0">{{ trans('global.no_entries_in_table') }}</p>
            @endif
        </div>
    </div>
@endsection

