<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ActivityLogHelper;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\Admin\MassDestroyTaskRequest;
use App\Http\Requests\Admin\StoreTaskRequest;
use App\Http\Requests\Admin\UpdateTaskRequest;
use App\Models\CustomActivityLog;
use App\Models\Task;
use App\Models\TaskBoard;
use App\Models\TaskPriority;
use App\Models\TaskStatus;
use App\Models\TaskTag;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class TaskController extends Controller
{
    use MediaUploadingTrait;

    public function updateStatus(Request $request)
    { 
        try {
            DB::beginTransaction();
            
            // Update the main task's status
            $task = Task::findOrFail($request->task_id);
            $task->status_id = $request->status_id;
            $task->save();
            
            // Update order for all tasks in the status
            foreach ($request->order_data as $orderItem) {
                Task::where('id', $orderItem['id'])
                    ->update(['ordering' => $orderItem['order']]);
            }
            
            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function index(Request $request)
    {
        abort_if(Gate::denies('task_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        
        if ($request->ajax()) {
            $query = Task::with(['status', 'task_priority', 'tags', 'assigned_tos', 'task_board'])->select(sprintf('%s.*', (new Task)->table));
            $table = DataTables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'task_show';
                $editGate      = false;
                $deleteGate    = false;
                $crudRoutePart = 'tasks';

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
            $table->editColumn('created_at', function ($row) {
                return $row->created_at ? $row->created_at->format('d-m-Y') : '';
            });
            $table->editColumn('status', function ($row) {
                if ($row->status) {
                    return '<span class="fw-medium text-' . $row->status->badge_class . '">' . $row->status->name . '</span>';
                }
                return '';
            });
            $table->editColumn('due_date', function ($row) {
                return $row->due_date ? $row->due_date : '';
            });
            $table->editColumn('task_priority', function ($row) {
                if ($row->task_priority) {
                    return '<span class="' . $row->task_priority->badge_class . '">' . $row->task_priority->name . '</span>';
                }
                return '';
            });
            $table->editColumn('assigned_tos', function ($row) {
                $html = '';
                foreach ($row->assigned_tos as $assigned_to) {
                    $html .= view('utilities.user-avatar', ['user' => $assigned_to])->render();
                }
                return $html;
            });
            $table->editColumn('task_board', function ($row) {
                return $row->task_board ? $row->task_board->name : '';
            });
            $table->editColumn('tags', function ($row) {
                $labels = []; 
                foreach ($row->tags as $tag) {
                    $labels[] = sprintf('<span class="badge bg-%s">%s</span>', $tag->badge_class, $tag->name);
                }

                return implode(' ', $labels);
            });

            $table->rawColumns(['actions', 'placeholder', 'status', 'task_priority', 'assigned_tos', 'task_board', 'tags']);

            return $table->make(true);
        }

        return view('admin.tasks.index');
    }

    public function create(Request $request)
    {
        abort_if(Gate::denies('task_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        
        $status_id = $request->status_id;
        $task_board_id = $request->task_board_id;

        return view('admin.tasks.create', compact('status_id', 'task_board_id'));
    }

    public function store(StoreTaskRequest $request)
    {
        $task = Task::create($request->all());
        $task->tags()->sync($request->input('tags', [])); 
        $task->assigned_tos()->sync($request->input('assigned_tos', []));
        foreach ($request->input('attachment', []) as $file) {
            $task->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('attachment');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $task->id]);
        }

        if($request->ajax()){
            return response()->json([
                'html' => view('admin.taskBoards.partials.task-card', ['task' => $task, 'taskStatus' => $task->status])->render(),
                'append' => '#task-status-' . $task->status_id . '-tasks-draggable',
                'message' => trans('global.flash.created', ['title' => trans('cruds.task.title_singular')])
            ]);
        }

        return redirect()->route('admin.tasks.index');
    }

    public function edit(Task $task)
    {
        abort_if(Gate::denies('task_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $statuses = TaskStatus::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $task_priorities = TaskPriority::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $tags = TaskTag::pluck('name', 'id');

        $assigned_tos = User::pluck('name', 'id');

        $task_boards = TaskBoard::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $assigned_bies = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $task->load('status', 'task_priority', 'tags', 'assigned_tos', 'task_board', 'assigned_by');

        return view('admin.tasks.edit', compact('assigned_bies', 'assigned_tos', 'statuses', 'tags', 'task', 'task_boards', 'task_priorities'));
    }

    public function update(UpdateTaskRequest $request, Task $task)
    {
        $task->update($request->all());

        $tagsSyncResult = $task->tags()->sync($request->input('tags', []));
        $properties = [];
        if (!empty($tagsSyncResult['attached'])) {
            $tags = TaskTag::whereIn('id', $tagsSyncResult['attached'])->get();
            $description = "تم إضافة علامة ";
            $tmpArray = [];
            foreach ($tags as $tag) {
                $tmpArray[] = "<span class='" . $tag->badge_class . "' mb-3'>" . $tag->name . "</span>";
            }
            $properties['attributes']['tag'] = implode(' ', $tmpArray);
            ActivityLogHelper::logModelActivity( $task, $description, $properties,
                'task_activity-'.$task->id, 'updated', 
            );
        }
        if (!empty($tagsSyncResult['detached'])) {
            $tags = TaskTag::whereIn('id', $tagsSyncResult['detached'])->get();
            $description = "تم حذف علامة ";
            $tmpArray = [];
            foreach ($tags as $tag) {
                $tmpArray[] = "<span class='" . $tag->badge_class . "' mb-3'>" . $tag->name . "</span>";
            }
            $properties['attributes']['tag'] = implode(' ', $tmpArray);
            ActivityLogHelper::logModelActivity( $task, $description, $properties,
                'task_activity-'.$task->id, 'updated', 
            );
        }

        $assignedTosSyncResult = $task->assigned_tos()->sync($request->input('assigned_tos', [])); 
        $properties = [];
        if (!empty($assignedTosSyncResult['attached'])) {
            $assignedTos = User::whereIn('id', $assignedTosSyncResult['attached'])->get();
            $description = "تم إضافة عضو ";
            $tmpArray = [];
            foreach ($assignedTos as $assigned_to) {
                $tmpArray[] = "<span class='badge bg-success-transparent mb-3'>" . $assigned_to->name . "</span>";
            }
            $properties['attributes']['assigned_to'] = implode(' ', $tmpArray);
            ActivityLogHelper::logModelActivity( $task, $description, $properties,
                'task_activity-'.$task->id, 'updated', 
            );
        }
        if (!empty($assignedTosSyncResult['detached'])) {
            $assignedTos = User::whereIn('id', $assignedTosSyncResult['detached'])->get();
            $description = "تم حذف عضو ";
            $tmpArray = [];
            foreach ($assignedTos as $assigned_to) {
                $tmpArray[] = "<span class='badge bg-danger-transparent mb-3'>" . $assigned_to->name . "</span>";
            }
            $properties['attributes']['assigned_to'] = implode(' ', $tmpArray);
            ActivityLogHelper::logModelActivity( $task, $description, $properties,
                'task_activity-'.$task->id, 'updated', 
            );
        }
        if (count($task->attachment) > 0) {
            foreach ($task->attachment as $media) {
                if (! in_array($media->file_name, $request->input('attachment', []))) {
                    $media->delete();
                }
            }
        }
        $media = $task->attachment->pluck('file_name')->toArray();
        foreach ($request->input('attachment', []) as $file) {
            if (count($media) === 0 || ! in_array($file, $media)) {
                $task->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('attachment');
            }
        }

        if($request->ajax()){
            return response()->json([
                'html' => view('admin.taskBoards.partials.task-card', ['task' => $task, 'taskStatus' => $task->status])->render(),
                'replace' => '[data-task-id="' . $task->id . '"]',
                'message' => trans('global.flash.updated', ['title' => trans('cruds.task.title_singular')])
            ]);
        }

        return redirect()->route('admin.tasks.index');
    }

    public function show(Task $task)
    {
        abort_if(Gate::denies('task_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $task->load('status', 'task_priority', 'tags', 'assigned_tos', 'task_board', 'assigned_by');

        $activityLogs = CustomActivityLog::inLog('task_activity-'.$task->id)->orderBy('id', 'desc')->paginate(15);
        if (request()->ajax()) {
            return response()->json([
                'html' => view('partials.activity', compact('activityLogs'))->render(),
                'hasMorePages' => $activityLogs->hasMorePages()
            ]);
        }
        return view('admin.tasks.show', compact('task', 'activityLogs'));
    }

    public function destroy(Task $task)
    {
        abort_if(Gate::denies('task_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $task->delete();

        return back();
    }

    public function massDestroy(MassDestroyTaskRequest $request)
    {
        $tasks = Task::find(request('ids'));

        foreach ($tasks as $task) {
            $task->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('task_create') && Gate::denies('task_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new Task();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
}
