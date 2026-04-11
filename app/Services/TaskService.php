<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskStatusChanged;

class TaskService
{
    public function updateStatus(Task $task, string $newStatus, User $actor, ?string $proof = null): void
    {
        $oldStatus = $task->status;

        $data = ['status' => $newStatus];

        if ($newStatus === 'review' && $proof) {
            $data['proof'] = $proof;
        }

        $task->update($data);

        ActivityLog::record(
            $actor,
            $task,
            'status_changed',
            "{$actor->name} mengubah status dari {$oldStatus} ke {$newStatus}"
        );

        $task->load('assignee', 'creator');
        $this->notifyStatusChange($task, $actor, $oldStatus, $newStatus);
    }

    public function approve(Task $task, User $actor): void
    {
        $oldStatus = $task->status;
        $task->update(['status' => 'done']);

        ActivityLog::record(
            $actor,
            $task,
            'status_changed',
            "{$actor->name} menyetujui task dan mengubah status menjadi Done"
        );

        $task->load('assignee', 'creator');
        $this->notifyStatusChange($task, $actor, $oldStatus, 'done');
    }

    public function reject(Task $task, User $actor, string $reason): void
    {
        $oldStatus = $task->status;
        $task->update(['status' => 'in_progress', 'proof' => null, 'rejection_reason' => $reason]);

        ActivityLog::record(
            $actor,
            $task,
            'status_changed',
            "{$actor->name} menolak review: {$reason}"
        );

        $task->load('assignee', 'creator');
        $this->notifyStatusChange($task, $actor, $oldStatus, 'in_progress');
    }

    private function notifyStatusChange(Task $task, User $actor, string $oldStatus, string $newStatus): void
    {
        if ($task->assignee && $task->assigned_to !== $actor->id) {
            $task->assignee->notify(new TaskStatusChanged($task, $actor, $oldStatus, $newStatus));
        }

        if ($task->creator && $task->created_by !== $actor->id) {
            $task->creator->notify(new TaskStatusChanged($task, $actor, $oldStatus, $newStatus));
        }
    }
}
