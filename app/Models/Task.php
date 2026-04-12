<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

#[Fillable(['project_id', 'title', 'description', 'status', 'priority', 'assigned_to', 'created_by', 'due_date', 'proof', 'proof_file', 'rejection_reason'])]
class Task extends Model
{
    use HasFactory;

    protected $appends = ['deadline_status'];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
        ];
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function activities()
    {
        return $this->morphMany(ActivityLog::class, 'subject')->latest();
    }

    protected function deadlineStatus(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (! $this->due_date || $this->status === 'done') {
                    return null;
                }

                $daysLeft = (int) Carbon::today()->diffInDays($this->due_date, false);

                return match (true) {
                    $daysLeft < 0  => 'overdue',
                    $daysLeft <= 3 => 'warning',
                    default        => 'upcoming',
                };
            }
        );
    }
}
