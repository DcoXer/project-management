<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Notifications\Notification;

class TaskDeadlineReminder extends Notification
{
    public function __construct(
        public Task $task,
        public int $daysLeft,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $dayText = match ($this->daysLeft) {
            0       => 'hari ini',
            1       => 'besok',
            default => "{$this->daysLeft} hari lagi",
        };

        return [
            'type'       => 'deadline_reminder',
            'message'    => "Deadline task \"{$this->task->title}\" {$dayText}!",
            'task_id'    => $this->task->id,
            'task_title' => $this->task->title,
            'days_left'  => $this->daysLeft,
        ];
    }
}
