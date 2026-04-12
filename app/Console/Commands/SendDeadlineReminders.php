<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Notifications\TaskDeadlineReminder;
use App\Notifications\TaskOverdue;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class SendDeadlineReminders extends Command
{
    protected $signature   = 'deadlines:remind';
    protected $description = 'Kirim notifikasi reminder deadline dan overdue untuk task';

    public function handle(): void
    {
        $today = Carbon::today();

        $tasks = Task::with('assignee')
            ->whereNotNull('assigned_to')
            ->whereNotNull('due_date')
            ->where('status', '!=', 'done')
            ->get();

        $reminded = 0;
        $overdued = 0;

        foreach ($tasks as $task) {
            if (! $task->assignee) {
                continue;
            }

            $daysLeft = (int) $today->diffInDays($task->due_date, false);

            if ($daysLeft < 0) {
                // Overdue — kirim sekali saja (cek apakah pernah dikirim sebelumnya)
                if (! $this->alreadyNotified($task->assignee->id, TaskOverdue::class, $task->id)) {
                    $task->assignee->notify(new TaskOverdue($task));
                    $overdued++;
                }
            } elseif (in_array($daysLeft, [1, 3])) {
                // H-1 dan H-3 — kirim sekali per hari
                if (! $this->alreadyNotified($task->assignee->id, TaskDeadlineReminder::class, $task->id, todayOnly: true)) {
                    $task->assignee->notify(new TaskDeadlineReminder($task, $daysLeft));
                    $reminded++;
                }
            }
        }

        $this->info("Selesai: {$reminded} reminder, {$overdued} overdue alert dikirim.");
    }

    private function alreadyNotified(int $userId, string $type, int $taskId, bool $todayOnly = false): bool
    {
        $query = DB::table('notifications')
            ->where('notifiable_id', $userId)
            ->where('type', $type);

        if ($todayOnly) {
            $query->whereDate('created_at', today());
        }

        return $query->get()->contains(function ($n) use ($taskId) {
            $data = json_decode($n->data, true);
            return ($data['task_id'] ?? null) === $taskId;
        });
    }
}
