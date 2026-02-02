<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\Admin\MassDestroyConsultantRequest;
use App\Http\Requests\Admin\StoreConsultantRequest;
use App\Http\Requests\Admin\UpdateConsultantRequest;
use App\Models\Consultant;
use App\Models\ConsultationType;
use App\Models\ConsultantSchedule;
use App\Models\BeneficiaryOrderAppointment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class ConsultantsController extends Controller
{
    use MediaUploadingTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('consultant_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Consultant::with(['consultationType'])->select(sprintf('%s.*', (new Consultant)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'consultant_show';
                $editGate      = 'consultant_edit';
                $deleteGate    = 'consultant_delete';
                $crudRoutePart = 'consultants';

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
            $table->editColumn('consultation_type', function ($row) {
                return $row->consultationType ? $row->consultationType->name : '';
            });
            $table->editColumn('name', function ($row) {
                return $row->name ? $row->name : '';
            });
            $table->editColumn('national_id', function ($row) {
                return $row->national_id ? $row->national_id : '';
            });
            $table->editColumn('phone_number', function ($row) {
                return $row->phone_number ? $row->phone_number : '';
            });
            $table->editColumn('academic_degree', function ($row) {
                return $row->academic_degree ? $row->academic_degree : '';
            });

            $table->rawColumns(['actions', 'placeholder']);

            return $table->make(true);
        }

        return view('admin.consultants.index');
    }

    public function create()
    {
        abort_if(Gate::denies('consultant_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $consultationTypes = ConsultationType::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.consultants.create', compact('consultationTypes'));
    }

    public function store(StoreConsultantRequest $request)
    {
        $consultant = Consultant::create($request->all());

        foreach ($request->input('documents', []) as $file) {
            $consultant->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('documents');
        }

        return redirect()->route('admin.consultants.index');
    }

    public function edit(Consultant $consultant)
    {
        abort_if(Gate::denies('consultant_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $consultationTypes = ConsultationType::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $consultant->load('consultationType');

        return view('admin.consultants.edit', compact('consultant', 'consultationTypes'));
    }

    public function update(UpdateConsultantRequest $request, Consultant $consultant)
    {
        $consultant->update($request->all());

        
        if (count($consultant->documents) > 0) {
            foreach ($consultant->documents as $media) {
                if (! in_array($media->file_name, $request->input('documents', []))) {
                    $media->delete();
                }
            }
        }
        $media = $consultant->documents->pluck('file_name')->toArray();
        foreach ($request->input('documents', []) as $file) {
            if (count($media) === 0 || ! in_array($file, $media)) {
                $consultant->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('documents');
            }
        }

        return redirect()->route('admin.consultants.index');
    }

    public function show(Consultant $consultant)
    {
        abort_if(Gate::denies('consultant_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $consultant->load('consultationType', 'schedules');

        return view('admin.consultants.show', compact('consultant'));
    }

    public function destroy(Consultant $consultant)
    {
        abort_if(Gate::denies('consultant_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $consultant->delete();

        return back();
    }

    public function massDestroy(MassDestroyConsultantRequest $request)
    {
        $consultants = Consultant::find(request('ids'));

        foreach ($consultants as $consultant) {
            $consultant->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
 
} 