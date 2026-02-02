<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MassDestroyTaskReportRequest;
use App\Http\Requests\Admin\StoreTaskReportRequest;
use App\Http\Requests\Admin\UpdateTaskReportRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TaskReportsController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('task_report_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.taskReports.index');
    }

    public function create()
    {
        abort_if(Gate::denies('task_report_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.taskReports.create');
    }

    public function store(StoreTaskReportRequest $request)
    {
        $taskReport = TaskReport::create($request->all());

        return redirect()->route('admin.task-reports.index');
    }

    public function edit(TaskReport $taskReport)
    {
        abort_if(Gate::denies('task_report_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.taskReports.edit', compact('taskReport'));
    }

    public function update(UpdateTaskReportRequest $request, TaskReport $taskReport)
    {
        $taskReport->update($request->all());

        return redirect()->route('admin.task-reports.index');
    }

    public function show(TaskReport $taskReport)
    {
        abort_if(Gate::denies('task_report_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.taskReports.show', compact('taskReport'));
    }

    public function destroy(TaskReport $taskReport)
    {
        abort_if(Gate::denies('task_report_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $taskReport->delete();

        return back();
    }

    public function massDestroy(MassDestroyTaskReportRequest $request)
    {
        $taskReports = TaskReport::find(request('ids'));

        foreach ($taskReports as $taskReport) {
            $taskReport->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
