<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();

        $query = Project::with(['creator', 'members'])
            ->withCount([
                'tasks',
                'tasks as tasks_done_count' => fn ($q) => $q->where('status', 'done'),
            ]);

        // Zero trust: developer hanya lihat project yang dia ikuti
        if (! in_array($user->role, ['admin', 'project_manager'])) {
            $query->whereHas('members', fn ($q) => $q->where('user_id', $user->id));
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        $projects = $query->latest()->paginate(10)->withQueryString();

        return Inertia::render('Projects/Index', [
            'projects' => $projects,
            'filters' => $request->only(['search', 'status', 'priority']),
        ]);
    }

    public function show(Request $request, Project $project): Response
    {
        $user = $request->user();

        $isMember = $project->members()->where('user_id', $user->id)->exists();
        $isCreator = $project->created_by === $user->id;

        if (! in_array($user->role, ['admin', 'project_manager']) && ! $isMember && ! $isCreator) {
            abort(403, 'Kamu bukan anggota project ini.');
        }

        $project->loadCount([
            'tasks',
            'tasks as tasks_done_count' => fn ($q) => $q->where('status', 'done'),
        ]);
        $project->load(['creator', 'members', 'tasks.assignee']);

        return Inertia::render('Projects/Show', [
            'project' => $project,
        ]);
    }
}
