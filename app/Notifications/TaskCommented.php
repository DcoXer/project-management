<?php

namespace App\Notifications;

use App\Models\Task;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TaskCommented extends Notification
{
    use Queueable;

    public function __construct(
        public Task $task,
        public User $commentedBy,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'task_commented',
            'message' => "{$this->commentedBy->name} menambahkan komentar di task \"{$this->task->title}\"",
            'task_id' => $this->task->id,
            'task_title' => $this->task->title,
        ];
    }
}
