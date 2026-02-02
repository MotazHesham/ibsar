<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MassDestroyJobTypeRequest;
use App\Http\Requests\Admin\StoreJobTypeRequest;
use App\Http\Requests\Admin\UpdateJobTypeRequest;
use App\Models\JobType;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class JobTypesController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('job_type_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = JobType::query()->select(sprintf('%s.*', (new JobType)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'job_type_show';
                $editGate      = 'job_type_edit';
                $deleteGate    = 'job_type_delete';
                $crudRoutePart = 'job-types';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->editColumn('id', function ($row) {
                return $row->id ? $row->id : '';
            });
            $table->editColumn('name', function ($row) {
                return $row->name ? $row->name : '';
            });
            $table->editColumn('required_job_details', function ($row) {
                return $row->required_job_details ? trans('global.yes') : trans('global.no');
            });
            $table->editColumn('required_job_details', function ($row) {
                $value = $row->required_job_details ? 'checked' : '';  
                return '<div class="custom-toggle-switch toggle-md ms-2">
                    <input onchange="updateStatuses(this, \'required_job_details\', \'' . addslashes("App\Models\JobType") . '\')" 
                        value="' . $row->id . '"  id="required_job_details-' . $row->id . '" type="checkbox" ' . $value . '>
                    <label for="required_job_details-' . $row->id . '" class="label-success mb-2"></label>
                </div>';
            });

            $table->rawColumns(['actions', 'placeholder', 'required_job_details']);

            return $table->make(true);
        }

        return view('admin.jobTypes.index');
    }

    public function create()
    {
        abort_if(Gate::denies('job_type_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.jobTypes.create');
    }

    public function store(StoreJobTypeRequest $request)
    {
        $jobType = JobType::create($request->all());

        return redirect()->route('admin.job-types.index');
    }

    public function edit(JobType $jobType)
    {
        abort_if(Gate::denies('job_type_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.jobTypes.edit', compact('jobType'));
    }

    public function update(UpdateJobTypeRequest $request, JobType $jobType)
    { 
        $jobType->setTranslation('name', $request->lang, $request->name);
        $jobType->save();

        return redirect()->route('admin.job-types.index');
    }

    public function show(JobType $jobType)
    {
        abort_if(Gate::denies('job_type_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.jobTypes.show', compact('jobType'));
    }

    public function destroy(JobType $jobType)
    {
        abort_if(Gate::denies('job_type_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $jobType->delete();

        return back();
    }

    public function massDestroy(MassDestroyJobTypeRequest $request)
    {
        $jobTypes = JobType::find(request('ids'));

        foreach ($jobTypes as $jobType) {
            $jobType->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
