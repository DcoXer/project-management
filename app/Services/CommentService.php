<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Comment;
use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskCommented;

class CommentService
{
    public function store(Task $task, User $actor, string $body): Comment
    {
        $comment = $task->comments()->create([
            'user_id' => $actor->id,
            'body' => $body,
        ]);

        ActivityLog::record(
            $actor,
            $task,
            'comment_added',
            "{$actor->name} menambahkan komentar"
        );

        $task->load('assignee', 'creator');

        if ($task->assignee && $task->assigned_to !== $actor->id) {
            $task->assignee->notify(new TaskCommented($task, $actor));
        }

        if ($task->creator && $task->created_by !== $actor->id) {
            $task->creator->notify(new TaskCommented($task, $actor));
        }

        return $comment;
    }

    public function destroy(Comment $comment): void
    {
        $comment->delete();
    }
}
