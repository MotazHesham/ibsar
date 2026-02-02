<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MassDestroyConsultantScheduleRequest;
use App\Http\Requests\Admin\StoreConsultantScheduleRequest;
use App\Http\Requests\Admin\UpdateConsultantScheduleRequest;
use App\Models\Consultant;
use App\Models\ConsultantSchedule;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class ConsultantSchedulesController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('consultant_schedule_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = ConsultantSchedule::with(['consultant'])->select(sprintf('%s.*', (new ConsultantSchedule)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = false;
                $editGate      = 'consultant_schedule_edit';
                $deleteGate    = false;
                $crudRoutePart = 'consultant-schedules';

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
            $table->editColumn('consultant', function ($row) {
                return $row->consultant ? $row->consultant->name : '';
            });
            $table->editColumn('day', function ($row) {
                return $row->day ? ConsultantSchedule::DAY_SELECT[$row->day] : '';
            });
            $table->editColumn('start_time', function ($row) {
                return $row->start_time ? $row->start_time : '';
            });
            $table->editColumn('end_time', function ($row) {
                return $row->end_time ? $row->end_time : '';
            });
            $table->editColumn('slot_duration', function ($row) {
                return $row->slot_duration ? $row->slot_duration . ' minutes' : '';
            });
            $table->editColumn('attendance_type', function ($row) {
                return $row->attendance_type ? ConsultantSchedule::ATTENDANCE_TYPE_SELECT[$row->attendance_type] : '';
            });
            $table->editColumn('is_active', function ($row) {
                return $row->is_active ? '<span class="badge bg-success">' . trans('global.yes') . '</span>' : '<span class="badge bg-secondary">' . trans('global.no') . '</span>';
            });
            
            $table->editColumn('is_active', function ($row) { 
                $checked = $row->is_active ? 'checked' : '';
                return '<div class="custom-toggle-switch toggle-md ms-2">
                    <input onchange="updateStatuses(this, \'is_active\', \'' . addslashes("App\Models\ConsultantSchedule") . '\')" 
                        value="' . $row->id . '"  id="is_active-' . $row->id . '" type="checkbox" ' . $checked . '>
                    <label for="is_active-' . $row->id . '" class="label-success mb-2"></label>
                </div>';
            });

            $table->rawColumns(['actions', 'placeholder', 'is_active']);

            return $table->make(true);
        }

        return view('admin.consultantSchedules.index');
    }

    public function create()
    {
        abort_if(Gate::denies('consultant_schedule_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $consultants = Consultant::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.consultantSchedules.create', compact('consultants'));
    }

    public function store(StoreConsultantScheduleRequest $request)
    {
        $consultantSchedule = ConsultantSchedule::create($request->all());

        return redirect()->route('admin.consultant-schedules.index');
    }

    public function edit(ConsultantSchedule $consultantSchedule)
    {
        abort_if(Gate::denies('consultant_schedule_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $consultants = Consultant::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $consultantSchedule->load('consultant');

        return view('admin.consultantSchedules.edit', compact('consultantSchedule', 'consultants'));
    }

    public function update(UpdateConsultantScheduleRequest $request, ConsultantSchedule $consultantSchedule)
    {
        $consultantSchedule->update($request->all());

        return redirect()->route('admin.consultant-schedules.index');
    }

    public function show(ConsultantSchedule $consultantSchedule)
    {
        abort_if(Gate::denies('consultant_schedule_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $consultantSchedule->load('consultant');

        return view('admin.consultantSchedules.show', compact('consultantSchedule'));
    }

    public function destroy(ConsultantSchedule $consultantSchedule)
    {
        abort_if(Gate::denies('consultant_schedule_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $consultantSchedule->delete();

        return back();
    }

    public function massDestroy(MassDestroyConsultantScheduleRequest $request)
    {
        $consultantSchedules = ConsultantSchedule::find(request('ids'));

        foreach ($consultantSchedules as $consultantSchedule) {
            $consultantSchedule->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
} 