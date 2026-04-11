<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    private function isInvolved(User $user, Task $task): bool
    {
        return $task->assigned_to === $user->id
            || $task->created_by === $user->id
            || $task->project->members()->where('user_id', $user->id)->exists();
    }

    private function isProjectManager(User $user, Task $task): bool
    {
        return $task->project->members()
            ->where('users.id', $user->id)
            ->wherePivot('role', 'manager')
            ->exists();
    }

    public function view(User $user, Task $task): bool
    {
        return $this->isInvolved($user, $task);
    }

    // Hanya developer yang di-assign; tidak bisa saat done atau review
    public function updateStatus(User $user, Task $task): bool
    {
        if ($task->status === 'done') return false;
        if ($task->status === 'review') return false;

        return $task->assigned_to === $user->id;
    }

    // Hanya PM; hanya saat status review
    public function approve(User $user, Task $task): bool
    {
        return $task->status === 'review' && $this->isProjectManager($user, $task);
    }

    public function reject(User $user, Task $task): bool
    {
        return $task->status === 'review' && $this->isProjectManager($user, $task);
    }

    public function comment(User $user, Task $task): bool
    {
        return $this->isInvolved($user, $task);
    }
}
