<?php

namespace App\Observers;

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
        // Hanya trigger kalau assigned_to berubah
        if (! $task->wasChanged('assigned_to') || ! $task->assigned_to) {
            return;
        }

        $assignee = User::find($task->assigned_to);

        // Cari siapa yang ubah — pakai auth() karena observer ga punya request context
        $changedBy = auth()->user() ?? User::find($task->created_by);

        if ($assignee && $changedBy && $assignee->id !== $changedBy->id) {
            $assignee->notify(new TaskAssigned($task, $changedBy));
        }
    }
}
