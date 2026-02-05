<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\DynamicServiceHelper;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\Admin\MassDestroyBeneficiaryOrderRequest;
use App\Http\Requests\Admin\StoreBeneficiaryOrderRequest;
use App\Http\Requests\Admin\UpdateBeneficiaryOrderRequest;
use App\Models\AccommodationType;
use App\Models\Beneficiary;
use App\Models\BeneficiaryOrder; 
use App\Models\Course; 
use App\Models\CustomActivityLog;
use App\Models\District;
use App\Models\DynamicService;
use App\Models\EducationalQualification;
use App\Models\FamilyRelationship;
use App\Models\JobType;
use App\Models\MaritalStatus;
use App\Models\Service;
use App\Models\ServiceStatus;
use App\Models\User;
use App\Models\Loan;
use App\Models\ServiceLoanInstallment;
use App\Services\BeneficiaryOrderService;
use App\Services\OdooService;
use App\Services\DonationAllocationService;
use App\Http\Requests\Admin\StoreBeneficiaryOrderDonationAllocationRequest;
use App\Models\Donation;
use App\Models\DonationAllocationItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class BeneficiaryOrdersController extends Controller
{
    use MediaUploadingTrait;
    protected $beneficiaryOrderService;
    protected $odooService;
    protected $donationAllocationService;

    public function __construct(
        BeneficiaryOrderService $beneficiaryOrderService,
        OdooService $odooService,
        DonationAllocationService $donationAllocationService
    ) {
        $this->beneficiaryOrderService   = $beneficiaryOrderService;
        $this->odooService               = $odooService;
        $this->donationAllocationService = $donationAllocationService;
    }

    public function updateStatus(BeneficiaryOrder $beneficiaryOrder, Request $request)
    {
        try { 
            DB::beginTransaction(); 
            $beneficiaryOrder->update($request->all());
            if ($request->has('finish')) {
                $beneficiaryOrder->update(['done' => 1]);
                if($beneficiaryOrder->service_type == 'consultant'){
                    $beneficiaryOrder->beneficiaryOrderAppointment->update(['status' => 'confirmed']);
                }
            }

            if ($request->has('loan-pay') && $beneficiaryOrder->service_type == 'loan') { 
                if($beneficiaryOrder->serviceLoan->status == 'pending'){
                    $startDate = $request->installment_date ? now()->parse(Carbon::createFromFormat(config('panel.date_format'), $request->installment_date)->format('Y-m-d')) : now();

                    $beneficiaryOrder->serviceLoan->addInstallments($startDate);

                    $beneficiaryOrder->serviceLoan->update(['status' => 'loan_paid']);

                    // Sync to Odoo: ensure partner exists and register outbound payment
                    $this->odooService->syncLoanPayment($beneficiaryOrder); 
                }
            } 
            DB::commit();
        } catch (\Throwable $th) {
            Log::error('Error syncing loan payment to Odoo: ' . $th->getMessage());
            DB::rollBack();
        }
        return redirect()->route('admin.beneficiary-orders.show', $beneficiaryOrder);
    } 

    public function signatureDownload($id)
    {
        $order = BeneficiaryOrder::findOrFail($id);

        $base64Image = $order->signature; // Assuming this contains the "data:image/png;base64,..."
    
        // Remove header (data:image/png;base64,)
        if (str_contains($base64Image, ',')) {
            $base64Image = explode(',', $base64Image)[1];
        }
    
        $imageData = base64_decode($base64Image);
    
        return response($imageData)
            ->header('Content-Type', 'image/png')
            ->header('Content-Disposition', 'attachment; filename="signature.png"');
    }
    

    public function index(Request $request)
    {
        abort_if(Gate::denies('beneficiary_order_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $status = $request->status;
            $query = BeneficiaryOrder::with(['beneficiary', 'service', 'status', 'specialist']);
            if (Gate::denies('beneficiary_orders_management_access')) {
                $query->where('specialist_id', auth()->id());
            }
            if ($status) {
                switch ($status) {
                    case 'current':
                        $query->where(function ($query) {
                            $query->where('accept_status', 'yes')
                                ->orWhereNull('accept_status');
                        })->where('done', 0)->where('is_archived', 0);
                        break;
                    case 'finished':
                        $query->where('accept_status', 'yes')->where('done', 1)->where('is_archived', 0);
                        break;
                    case 'rejected':
                        $query->where('accept_status', 'no')->where('is_archived', 0);
                        break;
                    case 'archived':
                        $query->where('is_archived', 1);
                        break;
                    case 'all': 
                        break;
                    default:
                        abort(404);
                }
            } else {
                abort(404);
            }
            $query->select(sprintf('%s.*', (new BeneficiaryOrder)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) use ($status) {
                $viewGate      = 'beneficiary_order_show';
                $editGate      = 'beneficiary_order_edit';
                $deleteGate    = 'beneficiary_order_delete';
                $crudRoutePart = 'beneficiary-orders';

                if ($row->is_archived == 0) {
                    $prependButtons = [
                        [
                            'gate' => 'archive_create',
                            'title' => trans('global.archive'),
                            'url' => '#',
                            'color' => 'success',
                            'icon' => 'ri-inbox-archive-line',
                            'attributes' => 'onclick="addToArchive(' . $row->id . ',\'BeneficiaryOrder\')"',
                        ]
                    ];
                } else {
                    $prependButtons = [];
                }
                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row',
                    'prependButtons'
                ));
            });

            $table->editColumn('id', function ($row) {
                return $row->id ? $row->id : '';
            });
            $table->addColumn('beneficiary_user_name', function ($row) {
                return $row->beneficiary && $row->beneficiary->user ? $row->beneficiary->user->name : '';
            });

            $table->editColumn('service_type', function ($row) {
                if (DynamicServiceHelper::isDynamicService($row->service_type)) {
                    return DynamicServiceHelper::getServiceTitle($row->service_type);
                }
                return $row->service_type ? Service::TYPE_SELECT[$row->service_type] : '';
            }); 

            $table->editColumn('service.title', function ($row) {
                return $row->service ? (is_string($row->service) ? $row->service : $row->service->title) : '';
            });
            $table->editColumn('attachment', function ($row) {
                return $row->attachment ? '<a href="' . $row->attachment->getUrl() . '" target="_blank">' . trans('global.downloadFile') . '</a>' : '';
            });
            $table->addColumn('status_name', function ($row) {
                return $row->status ? '<span class="badge bg-' . $row->status->badge_class . '-transparent">' . $row->status->name . '</span>' : '';
            });

            $table->editColumn('accept_status', function ($row) {
                return $row->accept_status ? BeneficiaryOrder::ACCEPT_STATUS_RADIO[$row->accept_status] : '';
            });
            $table->editColumn('done', function ($row) {
                return '<input type="checkbox" disabled ' . ($row->done ? 'checked' : null) . '>';
            });
            $table->addColumn('specialist_name', function ($row) {
                return $row->specialist ? $row->specialist->name : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'beneficiary.user.name', 'service', 'attachment', 'status', 'status_name', 'specialist']);

            return $table->make(true);
        }

        return view('admin.beneficiaryOrders.index');
    }

    public function create()
    {
        abort_if(Gate::denies('beneficiary_order_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $serviceType = request()->get('service_type');
        // Check if it's a dynamic service
        if (str_starts_with($serviceType, 'dynamic_')) {
            $dynamicServiceId = str_replace('dynamic_', '', $serviceType);
            $dynamicService = DynamicService::active()->findOrFail($dynamicServiceId);
        } else {
            // Check if it's a standard service
            if (!request()->get('service_type') || !in_array(request()->get('service_type'), array_keys(Service::TYPE_SELECT))) {
                abort(404); 
            }
        }

        $beneficiaries = Beneficiary::with('user')->get()->pluck('user.name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $accommodationTypes = AccommodationType::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $maritalStatuses = MaritalStatus::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $educationalQualifications = EducationalQualification::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $jobTypes = JobType::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $districts = District::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), ''); 

        $services = Service::where('active', 1)
            ->where('type', request()->get('service_type'))
            ->get()
            ->pluck('title', 'id');
        $courses = Course::active()
            ->get()
            ->pluck('title', 'id')
            ->prepend(trans('global.pleaseSelect'), '');

        $serviceKeyNames = Service::where('active', 1)
            ->where('type', request()->get('service_type'))
            ->get()
            ->pluck('key_name', 'id');

        $statuses = ServiceStatus::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $specialists = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $familyRelationships = FamilyRelationship::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $loans = Loan::get();
        foreach ($loans as $loan) {
            $loan->name = 'قيمة القرض: ' . $loan->amount . ' - قيمة القسط: ' . $loan->installment . ' - عدد الشهور: ' . $loan->months;
            $loan->id = $loan->id;
        }

        // Get dynamic services for the service list
        $dynamicServices = DynamicService::active()->with('media')->get();

        return view('admin.beneficiaryOrders.create', compact('beneficiaries', 'services', 'serviceKeyNames', 'specialists', 'statuses', 'courses', 'accommodationTypes', 'maritalStatuses', 'educationalQualifications', 'jobTypes', 'districts', 'familyRelationships', 'loans', 'dynamicServices'));
    }

    public function store(StoreBeneficiaryOrderRequest $request)
    {  
        $beneficiaryOrder = $this->beneficiaryOrderService->createBeneficiaryOrder($request);

        return redirect()->route('admin.beneficiary-orders.index');
    }

    public function edit(BeneficiaryOrder $beneficiaryOrder)
    {
        abort_if(Gate::denies('beneficiary_order_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if (Gate::denies('beneficiary_orders_management_access')) {
            if ($beneficiaryOrder->specialist_id != auth()->id()) {
                abort(403);
            }
        }

        $services = Service::pluck('type', 'id')->prepend(trans('global.pleaseSelect'), '');

        $statuses = ServiceStatus::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $specialists = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $beneficiaryOrder->load('beneficiary', 'service', 'status', 'specialist');

        return view('admin.beneficiaryOrders.edit', compact('beneficiaryOrder', 'services', 'specialists', 'statuses'));
    }

    public function update(UpdateBeneficiaryOrderRequest $request, BeneficiaryOrder $beneficiaryOrder)
    {
        $beneficiaryOrder->update($request->all());

        if ($request->input('attachment', false)) {
            if (! $beneficiaryOrder->attachment || $request->input('attachment') !== $beneficiaryOrder->attachment->file_name) {
                if ($beneficiaryOrder->attachment) {
                    $beneficiaryOrder->attachment->delete();
                }
                $beneficiaryOrder->addMedia(storage_path('tmp/uploads/' . basename($request->input('attachment'))))->toMediaCollection('attachment');
            }
        } elseif ($beneficiaryOrder->attachment) {
            $beneficiaryOrder->attachment->delete();
        }

        return redirect()->route('admin.beneficiary-orders.index');
    }

    public function show(BeneficiaryOrder $beneficiaryOrder)
    {  
        abort_if(Gate::denies('beneficiary_order_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if (Gate::denies('beneficiary_orders_management_access')) {
            if ($beneficiaryOrder->specialist_id != auth()->id()) {
                abort(403);
            }
        }

        $beneficiaryOrder->load(
            'beneficiary',
            'service',
            'status',
            'specialist',
            'beneficiaryOrderFollowups.user',
            'dynamicServiceOrder.dynamicService',
            'dynamicServiceOrder.workflow.transitions.user',
            'donationAllocations.donation.donator',
            'donationAllocations.donation.items',
            'donationAllocations.items.donationItem'
        );

        $activityLogs = CustomActivityLog::inLog('beneficiary_order_activity-' . $beneficiaryOrder->id)->orderBy('id', 'desc')->paginate(10);
        $statuses = ServiceStatus::orderBy('ordering', 'desc')->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $specialists = User::where('user_type', 'staff')->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $totalAllocated = $beneficiaryOrder->donationAllocations->sum('allocated_amount');
        $fundingSummary = [
            'total_allocated'   => $totalAllocated,
        ];

        $availableDonations = Donation::with(['donator', 'items', 'project'])
            ->where('remaining_amount', '>', 0)
            ->get();

        $donationsData = $availableDonations->mapWithKeys(function (Donation $donation) {
            $typeLabel = Donation::DONATION_TYPE_SELECT[$donation->donation_type] ?? $donation->donation_type;

            return [
                $donation->id => [
                    'id'               => $donation->id,
                    'donator'          => optional($donation->donator)->name,
                    'project_id'       => $donation->project_id,
                    'project_name'     => optional($donation->project)->name,
                    'donation_type'    => $donation->donation_type,
                    'type'             => $typeLabel,
                    'remaining_amount' => (float) $donation->remaining_amount,
                    'items'            => $donation->items->map(function ($item) {
                        // Calculate remaining quantity for this item
                        $allocatedQuantity = \App\Models\DonationAllocationItem::where('donation_item_id', $item->id)
                            ->sum('allocated_quantity');
                        $remainingQuantity = max(0, (float) $item->quantity - (float) $allocatedQuantity);
                        
                        return [
                            'item_id'         => $item->id,
                            'item_name'       => $item->item_name,
                            'quantity'        => (float) $item->quantity,
                            'remaining_quantity' => $remainingQuantity,
                            'unit_price'      => (float) $item->unit_price,
                        ];
                    })->filter(function ($item) {
                        // Only include items with remaining quantity > 0
                        return $item['remaining_quantity'] > 0;
                    })->values()->toArray(),
                ],
            ];
        })->toArray();

        if (request()->ajax()) {
            return response()->json([
                'html' => view('partials.activity', compact('activityLogs'))->render(),
                'hasMorePages' => $activityLogs->hasMorePages()
            ]);
        }

        return view('admin.beneficiaryOrders.show', compact(
            'beneficiaryOrder',
            'activityLogs',
            'statuses',
            'specialists',
            'fundingSummary',
            'availableDonations',
            'donationsData'
        ));
    }

    public function allocateDonation(
        StoreBeneficiaryOrderDonationAllocationRequest $request,
        BeneficiaryOrder $beneficiaryOrder
    ) {
        abort_if(Gate::denies('donation_allocation_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $data = $request->validated();

        $itemIndex = $request->has('allocation_item') ? (int) $request->input('allocation_item') : null;
        $itemQuantity = $request->has('item_quantity') ? (float) $request->input('item_quantity') : null;

        $this->donationAllocationService->allocate(
            (int) $data['donation_id'],
            (int) $beneficiaryOrder->id,
            (float) $data['allocated_amount'],
            $itemIndex,
            $itemQuantity
        );

        return redirect()->route('admin.beneficiary-orders.show', $beneficiaryOrder->id);
    }

    public function removeDonationAllocation(BeneficiaryOrder $beneficiaryOrder, \App\Models\DonationAllocation $donationAllocation)
    {
        abort_if(Gate::denies('donation_allocation_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($donationAllocation->beneficiary_order_id !== $beneficiaryOrder->id) {
            abort(404);
        }

        $this->donationAllocationService->deallocate($donationAllocation);

        return redirect()->route('admin.beneficiary-orders.show', $beneficiaryOrder->id);
    }

    public function destroy(BeneficiaryOrder $beneficiaryOrder)
    {
        abort_if(Gate::denies('beneficiary_order_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if (Gate::denies('beneficiary_orders_management_access')) {
            if ($beneficiaryOrder->specialist_id != auth()->id()) {
                abort(403);
            }
        }

        $beneficiaryOrder->delete();

        return back();
    }

    public function massDestroy(MassDestroyBeneficiaryOrderRequest $request)
    {
        $beneficiaryOrders = BeneficiaryOrder::find(request('ids'));

        foreach ($beneficiaryOrders as $beneficiaryOrder) {
            if (Gate::denies('beneficiary_orders_management_access')) {
                if ($beneficiaryOrder->specialist_id != auth()->id()) {
                    abort(403);
                }
            }
            $beneficiaryOrder->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('beneficiary_order_create') && Gate::denies('beneficiary_order_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new BeneficiaryOrder();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
}
