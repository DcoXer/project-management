<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Notifications\Notification;

class TaskOverdue extends Notification
{
    public function __construct(public Task $task) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'       => 'task_overdue',
            'message'    => "Task \"{$this->task->title}\" sudah melewati deadline!",
            'task_id'    => $this->task->id,
            'task_title' => $this->task->title,
        ];
    }
}
