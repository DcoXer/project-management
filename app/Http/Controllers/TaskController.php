<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TaskController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Task::with(['project', 'assignee'])
            ->where('assigned_to', $request->user()->id);

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

        $projects = Project::whereHas('members', fn ($q) => $q->where('user_id', $request->user()->id))
            ->orWhere('created_by', $request->user()->id)
            ->get(['id', 'name']);

        return Inertia::render('Tasks/Index', [
            'tasks' => $tasks,
            'projects' => $projects,
            'filters' => $request->only(['status', 'priority', 'project_id']),
        ]);
    }

    public function show(Task $task): Response
    {
        $task->load(['project', 'assignee', 'creator']);

        return Inertia::render('Tasks/Show', [
            'task' => $task,
        ]);
    }
}
