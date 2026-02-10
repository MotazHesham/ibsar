<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MassDestroyVolunteerTaskRequest;
use App\Http\Requests\Admin\StoreVolunteerTaskRequest;
use App\Http\Requests\Admin\UpdateVolunteerTaskRequest;
use App\Models\Volunteer;
use App\Models\VolunteerTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class VolunteerTasksController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('volunteer_task_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = VolunteerTask::with('volunteer')->select(sprintf('%s.*', (new VolunteerTask)->table));
            $table = DataTables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate   = 'volunteer_task_show';
                $editGate   = 'volunteer_task_edit';
                $deleteGate = 'volunteer_task_delete';
                $crudRoutePart = 'volunteer-tasks';
                $appendButtons = [
                    [
                        'gate'   => 'volunteer_task_show',
                        'title'  => 'QR',
                        'url'    => route('admin.volunteer-tasks.qr', $row->id),
                        'icon'   => 'ri-qr-code-line',
                        'color'  => 'info',
                    ],
                ];
                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row',
                    'appendButtons'
                ));
            });

            $table->editColumn('id', function ($row) {
                return $row->id ?: '';
            });
            $table->addColumn('volunteer_name', function ($row) {
                return $row->volunteer ? $row->volunteer->name : '';
            });
            $table->editColumn('name', function ($row) {
                return $row->name ?: '';
            });
            $table->editColumn('address', function ($row) {
                return $row->address ?: '';
            });
            $table->editColumn('phone', function ($row) {
                return $row->phone ?: '';
            });
            $table->editColumn('visit_type', function ($row) {
                return $row->visit_type ? (VolunteerTask::VISIT_TYPE_SELECT[$row->visit_type] ?? $row->visit_type) : '';
            });
            $table->editColumn('date', function ($row) {
                return $row->date ? $row->date  : '';
            });
            $table->editColumn('status', function ($row) {
                return $row->status ? (VolunteerTask::STATUS_SELECT[$row->status] ?? $row->status) : '';
            });

            $table->rawColumns(['actions', 'placeholder']);

            return $table->make(true);
        }

        return view('admin.volunteerTasks.index');
    }

    public function create()
    {
        abort_if(Gate::denies('volunteer_task_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $volunteers = Volunteer::orderBy('name')->pluck('name', 'id');

        return view('admin.volunteerTasks.create', compact('volunteers'));
    }

    public function store(StoreVolunteerTaskRequest $request)
    {
        VolunteerTask::create($request->all());

        return redirect()->route('admin.volunteer-tasks.index');
    }

    public function edit(VolunteerTask $volunteerTask)
    {
        abort_if(Gate::denies('volunteer_task_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $volunteers = Volunteer::orderBy('name')->pluck('name', 'id');

        return view('admin.volunteerTasks.edit', compact('volunteerTask', 'volunteers'));
    }

    public function update(UpdateVolunteerTaskRequest $request, VolunteerTask $volunteerTask)
    {
        $volunteerTask->update($request->all());

        return redirect()->route('admin.volunteer-tasks.index');
    }

    public function show(VolunteerTask $volunteerTask)
    {
        abort_if(Gate::denies('volunteer_task_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $volunteerTask->load('volunteer');

        return view('admin.volunteerTasks.show', compact('volunteerTask'));
    }

    public function destroy(VolunteerTask $volunteerTask)
    {
        abort_if(Gate::denies('volunteer_task_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $volunteerTask->delete();

        return back();
    }

    public function massDestroy(MassDestroyVolunteerTaskRequest $request)
    {
        $volunteerTasks = VolunteerTask::find($request->input('ids', []));

        foreach ($volunteerTasks as $volunteerTask) {
            $volunteerTask->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function qr($id)
    {
        abort_if(Gate::denies('volunteer_task_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $volunteerTask = VolunteerTask::with('volunteer')->findOrFail($id);

        return view('admin.volunteerTasks.qr', compact('volunteerTask'));
    }
}
