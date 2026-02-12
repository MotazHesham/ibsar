<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\VolunteerTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class VolunteerDashboardController extends Controller
{
    public function index()
    {
        /** @var \App\Models\Volunteer $volunteer */
        $volunteer = auth('volunteer')->user();
        $tasks = $volunteer->volunteerTasks()
            ->orderByRaw("CASE status WHEN 'approved' THEN 1 WHEN 'in_progress' THEN 2 WHEN 'completed' THEN 3 ELSE 4 END")
            ->orderBy('date')
            ->get();

        return view('volunteer.dashboard', compact('tasks'));
    }

    public function show(VolunteerTask $task)
    {
        $this->authorizeTask($task);
        return view('volunteer.tasks.show', compact('task'));
    }

    public function start(VolunteerTask $task)
    {
        $this->authorizeTask($task);
        if (!in_array($task->status, [ 'pending'])) {
            return back()->with('error', trans('frontend.volunteer.status_error'));
        }
        $task->update([
            'status'     => 'in_progress',
            'started_at' => now(),
        ]);
        return back()->with('success', trans('frontend.volunteer.task_started'));
    }

    public function finish(Request $request, VolunteerTask $task)
    {
        $this->authorizeTask($task);
        // if (!in_array($task->status, ['in_progress', 'approved'])) {
        //     return back()->with('error', trans('frontend.volunteer.status_error'));
        // }
        $request->validate([
            'report' => 'nullable|string|max:65535',
            'report_files.*' => 'nullable|file|max:10240',
        ]);

        $task->update([
            'status'      => 'completed',
            'finished_at' => now(),
            'report'      => $request->input('report'),
        ]);

        if ($request->hasFile('report_files')) {
            foreach ($request->file('report_files') as $file) {
                $task->addMedia($file)->toMediaCollection('report_files');
            }
        }

        return redirect()->route('volunteer.dashboard')->with('success', trans('frontend.volunteer.task_finished'));
    }

    /**
     * Public signed URL: show minimal task info (for QR verification).
     */
    public function verify(VolunteerTask $task)
    {
        return view('volunteer.task-verify', compact('task'));
    }

    protected function authorizeTask(VolunteerTask $task): void
    {
        if ($task->volunteer_id !== auth('volunteer')->id()) {
            abort(403);
        }
    }
}
