<?php

namespace App\Observers;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskAssigned;

class TaskObserver
{
    public function created(Task $task): void
    {
        if (! $task->assigned_to) {
            return;
        }

        $assignee = User::find($task->assigned_to);
        $creator = User::find($task->created_by);

        if ($assignee && $creator && $assignee->id !== $creator->id) {
            $assignee->notify(new TaskAssigned($task, $creator));
        }
    }

    public function updated(Task $task): void
    {
        // Notifikasi kalau assigned_to berubah
        if ($task->wasChanged('assigned_to') && $task->assigned_to) {
            $assignee  = User::find($task->assigned_to);
            $changedBy = auth()->user() ?? User::find($task->created_by);

            if ($assignee && $changedBy && $assignee->id !== $changedBy->id) {
                $assignee->notify(new TaskAssigned($task, $changedBy));
            }
        }

        // Auto-update status project saat status task berubah
        if ($task->wasChanged('status')) {
            $this->syncProjectStatus($task->project_id);
        }
    }

    private function syncProjectStatus(int $projectId): void
    {
        $project = Project::find($projectId);

        if (! $project || $project->status === 'planning') {
            return;
        }

        $totalTasks = $project->tasks()->count();

        if ($totalTasks === 0) {
            return;
        }

        $doneTasks = $project->tasks()->where('status', 'done')->count();

        if ($doneTasks === $totalTasks) {
            $project->update(['status' => 'completed']);
        } elseif ($project->status === 'completed') {
            // Kalau ada task yang di-reopen, kembalikan ke in_progress
            $project->update(['status' => 'in_progress']);
        }
    }
}
