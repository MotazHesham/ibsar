<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreDonationAllocationRequest;
use App\Models\Donation;
use App\Models\DonationAllocation;
use App\Services\DonationAllocationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class DonationAllocationController extends Controller
{
    public function __construct(
        protected DonationAllocationService $allocationService
    ) {
    }

    public function index(Request $request)
    {
        abort_if(Gate::denies('donation_allocation_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = DonationAllocation::with(['donation.donator', 'beneficiaryOrder'])->select(sprintf('%s.*', (new DonationAllocation)->table));
            $table = DataTables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = null;
                $editGate      = null;
                $deleteGate    = 'donation_allocation_create';
                $crudRoutePart = 'donation-allocations';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->editColumn('id', fn($row) => $row->id ?: '');
            $table->addColumn('donation_id', fn($row) => $row->donation?->id);
            $table->addColumn('donation_donator', fn($row) => $row->donation?->donator?->name);
            $table->addColumn('beneficiary_order_id', fn($row) => $row->beneficiaryOrder?->id);
            $table->editColumn('allocated_amount', fn($row) => $row->allocated_amount);

            $table->rawColumns(['actions', 'placeholder']);

            return $table->make(true);
        }

        $donations = Donation::with('donator')->get();

        return view('admin.donationAllocations.index', compact('donations'));
    }

    public function store(StoreDonationAllocationRequest $request)
    {
        $data = $request->validated();

        $this->allocationService->allocate(
            (int) $data['donation_id'],
            (int) $data['beneficiary_order_id'],
            (float) $data['allocated_amount']
        );

        return back();
    }

    public function destroy(DonationAllocation $donationAllocation)
    {
        abort_if(Gate::denies('donation_allocation_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $this->allocationService->deallocate($donationAllocation);

        return back();
    }
}

