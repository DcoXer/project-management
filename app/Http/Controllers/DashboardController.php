<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();

        return $user->role === 'project_manager'
            ? $this->pmDashboard($user)
            : $this->devDashboard($user);
    }

    private function devDashboard($user): Response
    {
        $myProjectIds = Project::where(fn ($q) => $q
            ->where('created_by', $user->id)
            ->orWhereHas('members', fn ($m) => $m->where('user_id', $user->id))
        )->pluck('id');

        $statusCounts = Task::where('assigned_to', $user->id)
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $stats = [
            'my_projects' => $myProjectIds->count(),
            'my_tasks'    => Task::where('assigned_to', $user->id)->count(),
            'todo'        => $statusCounts['todo']        ?? 0,
            'in_progress' => $statusCounts['in_progress'] ?? 0,
            'review'      => $statusCounts['review']      ?? 0,
            'done'        => $statusCounts['done']        ?? 0,
        ];

        $tasksByStatus   = $statusCounts;
        $tasksByPriority = Task::where('assigned_to', $user->id)
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

        return Inertia::render('DashboardDev', [
            'stats'          => $stats,
            'recentProjects' => $recentProjects,
            'myTasks'        => $myTasks,
            'tasksByStatus'  => $tasksByStatus,
            'tasksByPriority'=> $tasksByPriority,
        ]);
    }

    private function pmDashboard($user): Response
    {
        $managedProjectIds = Project::whereHas('members', fn ($q) => $q
            ->where('user_id', $user->id)
            ->where('project_members.role', 'manager')
        )->pluck('id');

        $teamTaskQuery = fn () => Task::whereIn('project_id', $managedProjectIds);

        $statusCounts   = $teamTaskQuery()->selectRaw('status, count(*) as count')->groupBy('status')->pluck('count', 'status');
        $priorityCounts = $teamTaskQuery()->selectRaw('priority, count(*) as count')->groupBy('priority')->pluck('count', 'priority');

        $teamMembers = \DB::table('project_members')
            ->whereIn('project_id', $managedProjectIds)
            ->where('project_members.role', '!=', 'manager')
            ->distinct('user_id')
            ->count('user_id');

        $stats = [
            'managed_projects' => $managedProjectIds->count(),
            'total_tasks'      => $teamTaskQuery()->count(),
            'pending_review'   => $statusCounts['review'] ?? 0,
            'team_members'     => $teamMembers,
        ];

        $recentProjects = Project::with('creator')
            ->withCount([
                'tasks',
                'tasks as tasks_done_count' => fn ($q) => $q->where('status', 'done'),
            ])
            ->whereIn('id', $managedProjectIds)
            ->latest()
            ->take(5)
            ->get(['id', 'name', 'status', 'priority', 'end_date', 'created_by']);

        $pendingReviewTasks = Task::with(['project', 'assignee'])
            ->whereIn('project_id', $managedProjectIds)
            ->where('status', 'review')
            ->latest()
            ->take(5)
            ->get(['id', 'title', 'priority', 'due_date', 'project_id', 'assigned_to']);

        return Inertia::render('DashboardPm', [
            'stats'              => $stats,
            'recentProjects'     => $recentProjects,
            'pendingReviewTasks' => $pendingReviewTasks,
            'tasksByStatus'      => $statusCounts,
            'tasksByPriority'    => $priorityCounts,
        ]);
    }
}
