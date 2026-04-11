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

        $myProjectIds = Project::where(fn ($q) => $q
            ->where('created_by', $user->id)
            ->orWhereHas('members', fn ($m) => $m->where('user_id', $user->id))
        )->pluck('id');

        $myTaskQuery = fn () => Task::where(fn ($q) => $q
            ->where('assigned_to', $user->id)
            ->orWhere('created_by', $user->id)
            ->orWhereIn('project_id', $myProjectIds)
        );

        $isDeveloper = $user->role === 'developer';

        $myAssignedTaskQuery = fn () => Task::where('assigned_to', $user->id);

        // Hitung status task yang di-assign ke user
        $statusCounts = $myAssignedTaskQuery()
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $stats = $isDeveloper ? [
            'my_projects' => $myProjectIds->count(),
            'my_tasks'    => $myAssignedTaskQuery()->count(),
            'todo'        => $statusCounts['todo']        ?? 0,
            'in_progress' => $statusCounts['in_progress'] ?? 0,
            'review'      => $statusCounts['review']      ?? 0,
            'done'        => $statusCounts['done']        ?? 0,
        ] : [
            'total_projects' => $myProjectIds->count(),
            'total_tasks'    => $myTaskQuery()->count(),
            'total_users'    => User::count(),
            'my_tasks'       => $myAssignedTaskQuery()->count(),
        ];

        $tasksByStatus = $myAssignedTaskQuery()
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $tasksByPriority = $myAssignedTaskQuery()
            ->selectRaw('priority, count(*) as count')
            ->groupBy('priority')
            ->pluck('count', 'priority');

        $recentProjects = Project::with('creator')
            ->withCount([
                'tasks',
                'tasks as tasks_done_count' => fn ($q) => $q->where('status', 'done'),
            ])
            ->whereIn('id', $myProjectIds)
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
            'stats'          => $stats,
            'is_developer'   => $isDeveloper,
            'recentProjects' => $recentProjects,
            'myTasks'        => $myTasks,
            'tasksByStatus'  => $tasksByStatus,
            'tasksByPriority'=> $tasksByPriority,
        ]);
    }
}
