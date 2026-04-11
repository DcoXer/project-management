<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateTaskStatusRequest;
use App\Models\Project;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
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

        // Hanya tampilkan task yang di-assign ke user (My Tasks)
        $query = Task::with(['project', 'assignee'])
            ->where('assigned_to', $user->id);

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

        // Dropdown filter: hanya project yang user punya task di sana
        $projects = Project::whereHas('tasks', fn ($q) => $q->where('assigned_to', $user->id))
            ->orderBy('name')->get(['id', 'name']);

        return Inertia::render('Tasks/Index', [
            'tasks'   => $tasks,
            'projects'=> $projects,
            'filters' => $request->only(['status', 'priority', 'project_id']),
        ]);
    }

    public function show(Request $request, Task $task): Response
    {
        $this->authorize('view', $task);

        $task->load(['project', 'assignee', 'creator', 'comments.user', 'activities.user']);

        $user = $request->user();

        return Inertia::render('Tasks/Show', [
            'task'        => $task,
            'is_assignee' => $task->assigned_to === $user->id,
            'is_pm'       => $task->project->members()
                                ->where('users.id', $user->id)
                                ->wherePivot('role', 'manager')
                                ->exists(),
        ]);
    }

    public function kanban(Request $request): Response
    {
        $user = $request->user();

        // Kanban hanya tampilkan task milik user sendiri
        $query = Task::with(['project', 'assignee'])
            ->where('assigned_to', $user->id);

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        $tasks = $query->get();

        $columns = [
            'todo'        => $tasks->where('status', 'todo')->values(),
            'in_progress' => $tasks->where('status', 'in_progress')->values(),
            'review'      => $tasks->where('status', 'review')->values(),
            'done'        => $tasks->where('status', 'done')->values(),
        ];

        $projects = Project::where(fn ($q) => $q
            ->where('created_by', $user->id)
            ->orWhereHas('members', fn ($m) => $m->where('user_id', $user->id))
        )->orderBy('name')->get(['id', 'name']);

        return Inertia::render('Tasks/Kanban', [
            'columns'  => $columns,
            'projects' => $projects,
            'filters'  => $request->only(['project_id']),
        ]);
    }

    public function updateStatus(UpdateTaskStatusRequest $request, Task $task): RedirectResponse|JsonResponse
    {
        $this->taskService->updateStatus($task, $request->status, $request->user(), $request->proof);

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Status task berhasil diubah.');
    }

    public function approve(Request $request, Task $task): RedirectResponse
    {
        $this->authorize('approve', $task);
        $this->taskService->approve($task, $request->user());

        return back()->with('success', 'Task berhasil disetujui dan ditandai selesai.');
    }

    public function reject(Request $request, Task $task): RedirectResponse
    {
        $this->authorize('reject', $task);

        $request->validate([
            'rejection_reason' => ['required', 'string', 'max:1000'],
        ], [
            'rejection_reason.required' => 'Alasan penolakan wajib diisi.',
        ]);

        $this->taskService->reject($task, $request->user(), $request->rejection_reason);

        return back()->with('success', 'Review ditolak, task dikembalikan ke In Progress.');
    }
}
