<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    public function view(User $user, Task $task): bool
    {
        return $user->role === 'admin'
            || $task->assigned_to === $user->id
            || $task->created_by === $user->id
            || $task->project->members()->where('user_id', $user->id)->exists();
    }

    public function updateStatus(User $user, Task $task): bool
    {
        return $user->role === 'admin'
            || $task->assigned_to === $user->id
            || $task->created_by === $user->id
            || $task->project->members()
                ->where('user_id', $user->id)
                ->where('role', 'manager')
                ->exists();
    }

    public function comment(User $user, Task $task): bool
    {
        return $user->role === 'admin'
            || $task->assigned_to === $user->id
            || $task->created_by === $user->id
            || $task->project->members()->where('user_id', $user->id)->exists();
    }
}
