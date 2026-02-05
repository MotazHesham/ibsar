<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreDonationRequest;
use App\Models\Donation;
use App\Models\Donator;
use App\Models\Project;
use App\Services\DonationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class DonationsController extends Controller
{
    public function __construct(
        protected DonationService $donationService
    ) {
    }

    public function index(Request $request)
    {
        abort_if(Gate::denies('donation_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Donation::with(['donator', 'project'])->select(sprintf('%s.*', (new Donation)->table));
            $table = DataTables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'donation_show';
                $editGate      = false;
                $deleteGate    = false;
                $crudRoutePart = 'donations';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->editColumn('id', fn($row) => $row->id ?: '');

            $table->addColumn('donator_name', function ($row) {
                return $row->donator?->name;
            });

            $table->addColumn('project_name', function ($row) {
                return $row->project?->name;
            });

            $table->editColumn('donation_type', function ($row) {
                return \App\Models\Donation::DONATION_TYPE_SELECT[$row->donation_type] ?? $row->donation_type;
            });

            $table->editColumn('total_amount', fn($row) => $row->total_amount ?: '0.00');

            $table->editColumn('donated_at', fn($row) => $row->donated_at ?: '');

            $table->rawColumns(['actions', 'placeholder']);

            return $table->make(true);
        }

        return view('admin.donations.index');
    }

    public function create()
    {
        abort_if(Gate::denies('donation_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $donators = Donator::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $projects = Project::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $donationTypes = \App\Models\Donation::DONATION_TYPE_SELECT;

        return view('admin.donations.create', compact('donators', 'projects', 'donationTypes'));
    }

    public function store(StoreDonationRequest $request)
    {
        $data = $request->validated();

        if ($data['donation_type'] === \App\Models\Donation::TYPE_ITEMS) {
            $donation = $this->donationService->createDonationWithItems($data);
        } else { 
            $data['remaining_amount'] = $data['total_amount'];
            $data['used_amount'] = 0;
            $donation = Donation::create($data);
        }

        return redirect()->route('admin.donations.show', $donation->id);
    }

    public function show(Donation $donation)
    {
        abort_if(Gate::denies('donation_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $donation->load(['donator', 'project', 'items']);

        return view('admin.donations.show', compact('donation'));
    }
}

