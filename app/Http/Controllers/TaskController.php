<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateTaskStatusRequest;
use App\Models\Project;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TaskController extends Controller
{
    public function __construct(private TaskService $taskService) {}

    public function index(Request $request): Response
    {
        $user = $request->user();
        $query = Task::with(['project', 'assignee']);

        if (! in_array($user->role, ['admin', 'project_manager'])) {
            $query->where('assigned_to', $user->id);
        }

        if ($user->role === 'project_manager') {
            $query->whereHas('project', function ($q) use ($user) {
                $q->where('created_by', $user->id)
                    ->orWhereHas('members', fn ($m) => $m->where('user_id', $user->id)->where('role', 'manager'));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        $tasks = $query->latest()->paginate(15)->withQueryString();

        $projects = in_array($user->role, ['admin', 'project_manager'])
            ? Project::orderBy('name')->get(['id', 'name'])
            : Project::whereHas('members', fn ($q) => $q->where('user_id', $user->id))
                ->orWhere('created_by', $user->id)
                ->get(['id', 'name']);

        return Inertia::render('Tasks/Index', [
            'tasks' => $tasks,
            'projects' => $projects,
            'filters' => $request->only(['status', 'priority', 'project_id']),
        ]);
    }

    public function show(Request $request, Task $task): Response
    {
        $this->authorize('view', $task);

        $task->load(['project', 'assignee', 'creator', 'comments.user', 'activities.user']);

        return Inertia::render('Tasks/Show', [
            'task' => $task,
        ]);
    }

    public function updateStatus(UpdateTaskStatusRequest $request, Task $task): RedirectResponse
    {
        $this->taskService->updateStatus($task, $request->status, $request->user());

        return back()->with('success', 'Status task berhasil diubah.');
    }
}
