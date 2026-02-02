<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MassDestroyBeneficiaryRequest;
use App\Http\Requests\Admin\StoreBeneficiaryRequest;
use App\Http\Requests\Admin\UpdateBeneficiaryRequest;
use App\Models\AccommodationEntity;
use App\Models\Beneficiary;
use App\Models\City;
use App\Models\CustomActivityLog;
use App\Models\DisabilityType;
use App\Models\District;
use App\Models\EconomicStatus;
use App\Models\EducationalQualification;
use App\Models\AccommodationType;
use App\Models\BeneficiaryCategory;
use App\Models\HealthCondition;
use App\Models\JobType;
use App\Models\MaritalStatus;
use App\Models\Nationality;
use App\Models\RequiredDocument;
use App\Models\Region;
use App\Models\User;
use App\Services\BeneficiaryService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class BeneficiariesController extends Controller
{

    public function __construct(
        protected BeneficiaryService $beneficiaryService
    ) {}
    public function updateStatus(Beneficiary $beneficiary, Request $request)
    {
        $beneficiary->update($request->all());

        return redirect()->route('admin.beneficiaries.show', $beneficiary->id);
    }

    public function updateCaseStudy(Beneficiary $beneficiary, Request $request)
    {

        $caseStudy = json_encode($request->except('_token', '_method'));
        $beneficiary->update(['case_study' => $caseStudy]);

        $incomes = EconomicStatus::where('type', 'income')->orderBy('order_level', 'desc')->get();
        $expenses = EconomicStatus::where('type', 'expense')->orderBy('order_level', 'desc')->get();

        if ($request->has('print')) {
            return view('admin.beneficiaries.case-study', compact('beneficiary', 'incomes', 'expenses'));
        }

        return redirect()->route('admin.beneficiaries.show', $beneficiary->id);
    }

    public function loginAsBeneficiary(Beneficiary $beneficiary)
    {
        abort_if(Gate::denies('beneficiary_control_all_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        Auth::loginUsingId($beneficiary->user_id);

        return redirect()->route('beneficiary.home');
    }

    public function index(Request $request)
    {
        abort_if(Gate::denies('beneficiary_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $status = $request->status;
            $query = Beneficiary::with(
                ['user', 'nationality', 'marital_status', 'job_type', 'educational_qualification', 'district', 'health_condition', 'disability_type', 'specialist']
            );
            
            if (Gate::denies('beneficiary_control_all_access')) {
                $query->where('specialist_id', auth()->id());
            }

            if ($request->benef_search && $request->benef_search != '') {
                $query->where(function($query) use ($request) {
                    $query->whereHas('user', function($query) use ($request) {
                        $query->where('phone', 'like', '%' . $request->benef_search . '%')
                            ->orWhere('identity_num', 'like', '%' . $request->benef_search . '%');
                    });
                });
            }

            if ($status) {
                switch ($status) {
                    case 'uncompleted':
                        $query->where('profile_status', 'uncompleted')->where('is_archived', 0);
                        break;
                    case 'rejected':
                        $query->where('profile_status', 'rejected')->where('is_archived', 0);
                        break;
                    case 'pending':
                        $query->whereIn('profile_status', ['request_join', 'in_review'])->where('is_archived', 0);
                        break;
                    case 'approved':
                        $query->where('profile_status', 'approved')->where('is_archived', 0);
                        break;
                    case 'archived':
                        $query->where('is_archived', 1);
                        break;
                    case 'all': 
                        break;
                    default:
                        abort(404);
                }
            }
            $query->select(sprintf('%s.*', (new Beneficiary)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) use ($status) {
                $viewGate      = 'beneficiary_show';
                $editGate      = 'beneficiary_edit';
                $deleteGate    = 'beneficiary_delete';
                $crudRoutePart = 'beneficiaries';
                if ($row->is_archived == 0) {
                    $prependButtons = [
                        [
                            'gate' => 'archive_create',
                            'title' => trans('global.archive'),
                            'url' => '#',
                            'color' => 'success',
                            'icon' => 'ri-inbox-archive-line',
                            'attributes' => 'onclick="addToArchive(' . $row->id . ',\'Beneficiary\')"',
                        ]
                    ];
                } else {
                    $prependButtons = [];
                }

                $appendButtons = [];
                if (Gate::allows('beneficiary_control_all_access')) {
                    $appendButtons[] = [
                        'gate' => 'beneficiary_control_all_access',
                        'title' => trans('global.login_as_beneficiary'),
                        'url' => route('admin.beneficiaries.login-as', $row->id),
                        'color' => 'warning',
                        'icon' => 'ri-login-box-line',
                    ];
                }

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row',
                    'prependButtons',
                    'appendButtons'
                ));
            });

            $table->editColumn('id', function ($row) {
                return $row->id ? $row->id : '';
            });
            $table->addColumn('user_name', function ($row) {
                return $row->user ? $row->user->name : '';
            });

            $table->editColumn('user.approved', function ($row) {
                $value = $row->user ? (is_string($row->user) ? $row->user : $row->user->approved) : 0;
                $checked = $value ? 'checked' : '';
                return '<div class="custom-toggle-switch toggle-md ms-2">
                    <input onchange="updateStatuses(this, \'approved\', \'' . addslashes("App\Models\User") . '\')" 
                        value="' . $row->user_id . '"  id="approved-' . $row->user_id . '" type="checkbox" ' . $checked . '>
                    <label for="approved-' . $row->user_id . '" class="label-success mb-2"></label>
                </div>';
            });
            $table->editColumn('profile_status', function ($row) {
                return $row->profile_status ? Beneficiary::PROFILE_STATUS_SELECT[$row->profile_status] : '';
            });
            $table->editColumn('user.phone', function ($row) {
                return $row->user ? (is_string($row->user) ? $row->user : $row->user->phone) : '';
            });
            $table->editColumn('user.identity_num', function ($row) {
                return $row->user ? (is_string($row->user) ? $row->user : $row->user->identity_num) : '';
            });
            $table->addColumn('specialist_name', function ($row) {
                return $row->specialist ? $row->specialist->name : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'user', 'specialist', 'user.approved']);

            return $table->make(true);
        }

        return view('admin.beneficiaries.index');
    }

    public function create()
    {
        abort_if(Gate::denies('beneficiary_create'), Response::HTTP_FORBIDDEN, '403 Forbidden'); 

        return view('admin.beneficiaries.create');
    }

    public function store(StoreBeneficiaryRequest $request)
    {
        $beneficiary = $this->beneficiaryService->createBeneficiary($request);

        if ($request->has('next')) {
            return redirect()->route('admin.beneficiaries.edit', $beneficiary->id);
        }

        return redirect()->route('admin.beneficiaries.index',['status' => 'all']);
    }

    public function edit(Beneficiary $beneficiary)
    {
        abort_if(Gate::denies('beneficiary_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if (Gate::denies('beneficiary_control_all_access')) {
            if ($beneficiary->specialist_id != auth()->id()) {
                abort(403);
            }
        }

        $nationalities = Nationality::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $marital_statuses = MaritalStatus::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $beneficiary_categories = BeneficiaryCategory::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $accommodation_types = AccommodationType::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $accommodation_entities_charity = AccommodationEntity::where('type', 'charity')->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $accommodation_entities_social = AccommodationEntity::where('type', 'social')->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        
        $job_types = JobType::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $job_types_need_job_details = JobType::where('required_job_details', 1)->pluck('id');

        $educational_qualifications = EducationalQualification::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), ''); 

        $region = Region::find($beneficiary->region_id);
        if ($region) {
            $cities = $region->cities()->get()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        } else {
            $cities = City::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        }

        $districts = District::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $health_conditions = HealthCondition::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $disability_types = DisabilityType::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $incomes = EconomicStatus::where('type', 'income')->orderBy('order_level', 'desc')->get();
        $expenses = EconomicStatus::where('type', 'expense')->orderBy('order_level', 'desc')->get();

        $requiredDocuments = RequiredDocument::all();

        $beneficiary->load('user', 'nationality', 'marital_status', 'job_type', 'educational_qualification', 'district', 'health_condition', 'disability_type', 'beneficiary_category');

        $user = $beneficiary->user;
        return view(
            'admin.beneficiaries.edit',
            compact(
                'beneficiary',
                'requiredDocuments',
                'disability_types',
                'districts',
                'cities',
                'educational_qualifications',
                'health_conditions',
                'job_types',
                'marital_statuses',
                'nationalities', 
                'user',
                'incomes',
                'expenses',
                'job_types_need_job_details',
                'accommodation_types',
                'accommodation_entities_charity',
                'accommodation_entities_social',
                'beneficiary_categories'
            )
        );
    }

    public function update(UpdateBeneficiaryRequest $request, Beneficiary $beneficiary)
    {
        $this->beneficiaryService->updateBeneficiary($beneficiary, $request);

        return redirect()->route('admin.beneficiaries.edit', $beneficiary->id);
    }

    public function show(Beneficiary $beneficiary)
    {
        abort_if(Gate::denies('beneficiary_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if (Gate::denies('beneficiary_control_all_access')) {
            if ($beneficiary->specialist_id != auth()->id()) {
                abort(403);
            }
        }

        $beneficiary->load('user', 'nationality', 'marital_status', 'job_type', 'educational_qualification', 'district', 'health_condition', 'disability_type', 'specialist', 'beneficiaryBeneficiaryOrders');

        $user = $beneficiary->user;

        $nationalities = Nationality::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $marital_statuses = MaritalStatus::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $job_types = JobType::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $educational_qualifications = EducationalQualification::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $districts = District::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $health_conditions = HealthCondition::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $disability_types = DisabilityType::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $specialists = User::where('user_type', 'staff')->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $incomes = EconomicStatus::where('type', 'income')->orderBy('order_level', 'desc')->get();
        $expenses = EconomicStatus::where('type', 'expense')->orderBy('order_level', 'desc')->get();

        $beneficiaryOrders = $beneficiary->beneficiaryBeneficiaryOrders()->withTrashed()->pluck('id');
        $activityLogs = CustomActivityLog::inLog(['beneficiary_activity-' . $beneficiary->id, 'user_beneficiary_activity-' . $beneficiary->user_id])
            ->orWhere(function($query) use ($beneficiaryOrders) {
                $query->whereIn('subject_id', $beneficiaryOrders)
                    ->where('subject_type', 'App\Models\BeneficiaryOrder');
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        if (request()->ajax()) {
            return response()->json([
                'html' => view('partials.activity', compact('activityLogs'))->render(),
                'hasMorePages' => $activityLogs->hasMorePages()
            ]);
        }

        return view('admin.beneficiaries.show', compact(
            'beneficiary',
            'user',
            'nationalities',
            'marital_statuses',
            'job_types',
            'educational_qualifications',
            'districts',
            'health_conditions',
            'disability_types',
            'incomes',
            'expenses',
            'activityLogs',
            'specialists'
        ));
    }

    public function destroy(Beneficiary $beneficiary)
    {
        abort_if(Gate::denies('beneficiary_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if (Gate::denies('beneficiary_control_all_access')) {
            if ($beneficiary->specialist_id != auth()->id()) {
                abort(403);
            }
        }

        $beneficiary->delete();

        return back();
    }

    public function massDestroy(MassDestroyBeneficiaryRequest $request)
    {
        abort_if(Gate::denies('beneficiary_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $beneficiaries = Beneficiary::find(request('ids'));


        foreach ($beneficiaries as $beneficiary) {
            if (Gate::denies('beneficiary_control_all_access')) {
                if ($beneficiary->specialist_id != auth()->id()) {
                    abort(403);
                }
            }
            $beneficiary->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
