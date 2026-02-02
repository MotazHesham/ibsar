<?php

namespace App\Http\Controllers\Beneficiary;

use App\Helpers\DynamicServiceHelper;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\Beneficiary\StoreBeneficiaryOrderRequest;
use App\Http\Requests\Beneficiary\UpdateBeneficiaryOrderRequest;
use App\Models\AccommodationType;
use App\Models\Beneficiary;
use App\Models\BeneficiaryFamily;
use App\Services\BeneficiaryOrderService;
use App\Models\BeneficiaryOrder;
use App\Models\Course;
use App\Models\District;
use App\Models\DynamicService;
use App\Models\EducationalQualification;
use App\Models\FamilyRelationship;
use App\Models\JobType;
use App\Models\Loan;
use App\Models\MaritalStatus;
use App\Models\Service;
use App\Models\ServiceStatus;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class BeneficiaryOrdersController extends Controller
{
    use MediaUploadingTrait;
    protected $beneficiaryOrderService;

    public function __construct(BeneficiaryOrderService $beneficiaryOrderService)
    {
        $this->beneficiaryOrderService = $beneficiaryOrderService;
    }

    public function index(Request $request)
    {
        $beneficiary = auth()->user()->beneficiary;
        if ($request->ajax()) {
            $status = $request->status;
            $query = BeneficiaryOrder::with(['service', 'status', 'specialist'])
                    ->where('beneficiary_id', $beneficiary->id)
                    ->orWhereHas('serviceLoan.members', function ($query) use ($beneficiary) {
                        $query->where('beneficiary_id', $beneficiary->id);
                    });
            $query->select(sprintf('%s.*', (new BeneficiaryOrder)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) use ($status, $beneficiary) {
                $viewGate      = true;
                $editGate      = $row->beneficiary_id == $beneficiary->id ? true : false;
                $deleteGate    = $row->beneficiary_id == $beneficiary->id ? true : false;
                $crudRoutePart = 'beneficiary.beneficiary-orders';
                return view('partials.datatablesActions-nopermission', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row',
                ));
            });

            $table->editColumn('id', function ($row) {
                return $row->id ? $row->id : '';
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

            $table->rawColumns(['actions', 'placeholder',  'service', 'attachment', 'status', 'status_name', 'specialist']);

            return $table->make(true);
        }

        return view('beneficiary.orders.index');
    }

    public function create()
    {
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
        $beneficiary = auth()->user()->beneficiary;
        $beneficiaryFamilies = BeneficiaryFamily::where('beneficiary_id', $beneficiary->id)->get()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $services = Service::where('active', 1)
            ->where('type', request()->get('service_type'))
            ->get()
            ->pluck('title', 'id');

        $serviceKeyNames = Service::where('active', 1)
            ->where('type', request()->get('service_type'))
            ->get()
            ->pluck('key_name', 'id');

        $courses = Course::active()
            ->get()
            ->pluck('title', 'id')
            ->prepend(trans('global.pleaseSelect'), '');


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
        return view('beneficiary.orders.create', compact('services', 'serviceKeyNames', 'courses', 'beneficiaries', 'accommodationTypes', 'maritalStatuses', 'educationalQualifications', 'jobTypes', 'districts', 'familyRelationships', 'loans', 'statuses', 'specialists', 'beneficiaryFamilies', 'dynamicServices'));
    }

    public function store(StoreBeneficiaryOrderRequest $request)
    {
        $beneficiary = auth()->user()->beneficiary;

        if (!$beneficiary->canRequestOrder(true)) {
            return redirect()->route('beneficiary.profile.show');
        }

        $request->merge(['beneficiary_id' => $beneficiary->id]);

        $beneficiaryOrder = $this->beneficiaryOrderService->createBeneficiaryOrder($request);

        return redirect()->route('beneficiary.beneficiary-orders.index');
    }

    public function edit(BeneficiaryOrder $beneficiaryOrder)
    {

        $beneficiary = auth()->user()->beneficiary;
        if ($beneficiaryOrder->beneficiary_id != $beneficiary->id) {
            abort(403);
        }

        $beneficiaryOrder->load('beneficiary', 'service', 'status', 'specialist');

        return view('beneficiary.orders.edit', compact('beneficiaryOrder'));
    }

    public function update(UpdateBeneficiaryOrderRequest $request, BeneficiaryOrder $beneficiaryOrder)
    {
        $beneficiary = auth()->user()->beneficiary;
        if ($beneficiaryOrder->beneficiary_id != $beneficiary->id) {
            abort(403);
        }

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

        return redirect()->route('beneficiary.beneficiary-orders.index');
    }

    public function show(BeneficiaryOrder $beneficiaryOrder)
    {
        $beneficiary = auth()->user()->beneficiary;
        if ($beneficiaryOrder->beneficiary_id != $beneficiary->id) {
            if(!$beneficiaryOrder->serviceLoan || !$beneficiaryOrder->serviceLoan->members){
                abort(403);
            }
            if(!in_array($beneficiary->id, $beneficiaryOrder->serviceLoan->members->pluck('beneficiary_id')->toArray())){
                abort(403);
            } 
        }

        $dynamicService = null;
        if (str_starts_with($beneficiaryOrder->service_type, 'dynamic_')) {
            $dynamicServiceId = str_replace('dynamic_', '', $beneficiaryOrder->service_type);
            $dynamicService = DynamicService::find($dynamicServiceId); 
        }

        $beneficiaryOrder->load('beneficiary', 'service', 'status', 'specialist', 'beneficiaryOrderFollowups.user');
        return view('beneficiary.orders.show', compact('beneficiaryOrder', 'dynamicService'));
    }

    public function destroy(BeneficiaryOrder $beneficiaryOrder)
    {
        $beneficiary = auth()->user()->beneficiary;
        if ($beneficiaryOrder->beneficiary_id != $beneficiary->id) {
            abort(403);
        }

        $beneficiaryOrder->delete();

        return back();
    }

    public function storeCKEditorImages(Request $request)
    {
        $model         = new BeneficiaryOrder();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
}
