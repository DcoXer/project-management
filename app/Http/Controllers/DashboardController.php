<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();

        $stats = [
            'total_projects' => Project::count(),
            'total_tasks' => Task::count(),
            'total_users' => User::count(),
            'my_tasks' => Task::where('assigned_to', $user->id)->count(),
        ];

        $recentProjects = Project::with('creator')
            ->withCount([
                'tasks',
                'tasks as tasks_done_count' => fn ($q) => $q->where('status', 'done'),
            ])
            ->latest()
            ->take(5)
            ->get(['id', 'name', 'status', 'priority', 'end_date', 'created_by']);

        $myTasks = Task::with('project')
            ->where('assigned_to', $user->id)
            ->whereIn('status', ['todo', 'in_progress', 'review'])
            ->latest()
            ->take(5)
            ->get(['id', 'title', 'status', 'priority', 'due_date', 'project_id']);

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'recentProjects' => $recentProjects,
            'myTasks' => $myTasks,
        ]);
    }
}
