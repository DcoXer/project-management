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
        $query = Project::with(['creator', 'members'])
            ->withCount('tasks');

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

    public function show(Project $project): Response
    {
        $project->load(['creator', 'members', 'tasks.assignee']);

        return Inertia::render('Projects/Show', [
            'project' => $project,
        ]);
    }
}
