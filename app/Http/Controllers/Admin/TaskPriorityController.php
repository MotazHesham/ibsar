<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MassDestroyTaskPriorityRequest;
use App\Http\Requests\Admin\StoreTaskPriorityRequest;
use App\Http\Requests\Admin\UpdateTaskPriorityRequest;
use App\Models\TaskPriority;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TaskPriorityController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('task_priority_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $taskPriorities = TaskPriority::all();

        return view('admin.taskPriorities.index', compact('taskPriorities'));
    }

    public function create()
    {
        abort_if(Gate::denies('task_priority_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.taskPriorities.create');
    }

    public function store(StoreTaskPriorityRequest $request)
    {
        $taskPriority = TaskPriority::create($request->all());

        return redirect()->route('admin.task-priorities.index');
    }

    public function edit(TaskPriority $taskPriority)
    {
        abort_if(Gate::denies('task_priority_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.taskPriorities.edit', compact('taskPriority'));
    }

    public function update(UpdateTaskPriorityRequest $request, TaskPriority $taskPriority)
    {
        $taskPriority->update($request->all());

        return redirect()->route('admin.task-priorities.index');
    }

    public function show(TaskPriority $taskPriority)
    {
        abort_if(Gate::denies('task_priority_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.taskPriorities.show', compact('taskPriority'));
    }

    public function destroy(TaskPriority $taskPriority)
    {
        abort_if(Gate::denies('task_priority_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $taskPriority->delete();

        return back();
    }

    public function massDestroy(MassDestroyTaskPriorityRequest $request)
    {
        $taskPriorities = TaskPriority::find(request('ids'));

        foreach ($taskPriorities as $taskPriority) {
            $taskPriority->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
