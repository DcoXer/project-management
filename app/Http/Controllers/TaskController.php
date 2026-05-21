<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateTaskStatusRequest;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Services\TaskService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Inertia\Inertia;
use Inertia\Response;

class TaskController extends Controller
{
    public function __construct(private TaskService $taskService) {}

    public function index(Request $request): Response
    {
        $user  = $request->user();
        $isPm  = $user->role === 'project_manager';

        if ($isPm) {
            // PM: tampilkan semua task dari project yang dia manage
            $query = Task::with(['project', 'assignee'])
                ->whereHas('project.members', fn ($q) => $q
                    ->where('user_id', $user->id)
                    ->where('project_members.role', 'manager')
                );

            $projects = Project::whereHas('members', fn ($q) => $q
                ->where('user_id', $user->id)
                ->where('project_members.role', 'manager')
            )->orderBy('name')->get(['id', 'name']);

            $managedProjects = Project::whereHas('members', fn ($q) => $q
                ->where('user_id', $user->id)
                ->where('project_members.role', 'manager')
            )->orderBy('name')->get(['id', 'name']);
        } else {
            // Developer: hanya task dari project yang sudah dimulai (bukan planning)
            $query = Task::with(['project', 'assignee'])
                ->where('assigned_to', $user->id)
                ->whereHas('project', fn ($q) => $q->where('status', '!=', 'planning'));

            $projects = Project::whereHas('tasks', fn ($q) => $q->where('assigned_to', $user->id))
                ->where('status', '!=', 'planning')
                ->orderBy('name')->get(['id', 'name']);
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

        if ($request->filled('start_date')) {
            $query->where('start_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->where('end_date', '<=', $request->end_date);
        }

        $allowedSorts = ['title', 'status', 'priority', 'start_date', 'end_date', 'due_date', 'created_at'];
        $sortBy  = in_array($request->get('sort_by'), $allowedSorts) ? $request->get('sort_by') : 'created_at';
        $sortDir = $request->get('sort_dir') === 'asc' ? 'asc' : 'desc';

        $tasks = $query->orderBy($sortBy, $sortDir)->paginate(15)->withQueryString();

        return Inertia::render('Tasks/Index', [
            'tasks'           => $tasks,
            'projects'        => $projects,
            'managed_projects'=> $managedProjects ?? [],
            'developers'      => $isPm ? User::where('role', 'developer')->orderBy('name')->get(['id', 'name', 'specialization']) : [],
            'filters'         => $request->only(['status', 'priority', 'project_id', 'start_date', 'end_date', 'sort_by', 'sort_dir']),
            'is_pm'           => $isPm,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Task::class);

        $validated = $request->validate([
            'project_id'     => ['required', 'exists:projects,id'],
            'assigned_to'    => ['required', 'exists:users,id'],
            'title'          => ['required', 'string', 'max:255'],
            'description'    => ['nullable', 'string', 'max:5000'],
            'priority'       => ['required', 'in:low,medium,high'],
            'due_date'       => ['nullable', 'date'],
            'start_date'     => ['nullable', 'date'],
            'end_date'       => ['nullable', 'date'],
        ], [
            'project_id.required'  => 'Project wajib dipilih.',
            'assigned_to.required' => 'Assignee wajib dipilih.',
            'title.required'       => 'Judul task wajib diisi.',
            'priority.required'    => 'Priority wajib dipilih.',
        ]);

        $task = Task::create([
            ...$validated,
            'status'     => 'todo',
            'created_by' => $request->user()->id,
        ]);

        $project   = Project::find($validated['project_id']);
        $developer = User::find($validated['assigned_to']);
        $pivotData = ['role' => 'developer', 'specialization' => $developer->specialization];

        $alreadyMember = $project->members()->where('user_id', $developer->id)->exists();
        if ($alreadyMember) {
            $project->members()->updateExistingPivot($developer->id, $pivotData);
        } else {
            $project->members()->attach($developer->id, $pivotData);
        }

        return redirect()->route('tasks.show', $task)->with('success', 'Task berhasil dibuat.');
    }

    public function show(Request $request, Task $task): Response
    {
        $this->authorize('view', $task);

        $task->load(['project', 'assignee', 'creator', 'comments.user', 'activities.user']);

        $user = $request->user();

        if ($user->role === 'developer' && $task->project->status === 'planning') {
            abort(403, 'Project belum dimulai oleh Project Manager.');
        }

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
        $isPm = $user->role === 'project_manager';

        if ($isPm) {
            $query = Task::with(['project', 'assignee'])
                ->whereHas('project.members', fn ($q) => $q
                    ->where('user_id', $user->id)
                    ->where('project_members.role', 'manager')
                );

            $projects = Project::whereHas('members', fn ($q) => $q
                ->where('user_id', $user->id)
                ->where('project_members.role', 'manager')
            )->orderBy('name')->get(['id', 'name']);
        } else {
            $query = Task::with(['project', 'assignee'])
                ->where('assigned_to', $user->id)
                ->whereHas('project', fn ($q) => $q->where('status', '!=', 'planning'));

            $projects = Project::where(fn ($q) => $q
                ->where('created_by', $user->id)
                ->orWhereHas('members', fn ($m) => $m->where('user_id', $user->id))
            )->where('status', '!=', 'planning')->orderBy('name')->get(['id', 'name']);
        }

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

        return Inertia::render('Tasks/Kanban', [
            'columns'  => $columns,
            'projects' => $projects,
            'filters'  => $request->only(['project_id']),
            'is_pm'    => $isPm,
        ]);
    }

    public function exportPdf(Request $request): HttpResponse
    {
        $user = $request->user();
        $isPm = $user->role === 'project_manager';

        if ($isPm) {
            $query = Task::with(['project', 'assignee'])
                ->whereHas('project.members', fn ($q) => $q
                    ->where('user_id', $user->id)
                    ->where('project_members.role', 'manager')
                );
        } else {
            $query = Task::with(['project', 'assignee'])
                ->where('assigned_to', $user->id);
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

        $tasks = $query->orderBy('project_id')->orderBy('created_at')->get();

        $pdf = Pdf::loadView('pdf.tasks', [
            'tasks'       => $tasks,
            'user'        => $user,
            'isPm'        => $isPm,
            'filters'     => $request->only(['status', 'priority', 'project_id']),
            'generatedAt' => now()->format('d M Y, H:i'),
        ])->setPaper('a4', 'portrait');

        $prefix  = $isPm ? 'laporan-team-tasks' : 'laporan-my-tasks';
        $filename = $prefix . '-' . now()->format('Ymd') . '.pdf';

        return $pdf->download($filename);
    }

    public function updateStatus(UpdateTaskStatusRequest $request, Task $task): RedirectResponse|JsonResponse
    {
        $this->taskService->updateStatus($task, $request->status, $request->user(), $request->proof, $request->file('proof_file'));

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
