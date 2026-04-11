<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskStatusChanged;

class TaskService
{
    public function updateStatus(Task $task, string $newStatus, User $actor): void
    {
        $oldStatus = $task->status;
        $task->update(['status' => $newStatus]);

        ActivityLog::record(
            $actor,
            $task,
            'status_changed',
            "{$actor->name} mengubah status dari {$oldStatus} ke {$newStatus}"
        );

        $task->load('assignee', 'creator');

        if ($task->assignee && $task->assigned_to !== $actor->id) {
            $task->assignee->notify(new TaskStatusChanged($task, $actor, $oldStatus, $newStatus));
        }

        if ($task->creator && $task->created_by !== $actor->id) {
            $task->creator->notify(new TaskStatusChanged($task, $actor, $oldStatus, $newStatus));
        }
    }
}
