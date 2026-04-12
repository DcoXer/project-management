<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'description', 'status', 'priority', 'start_date', 'end_date', 'created_by'])]
class Project extends Model
{
    use HasFactory;

    protected $appends = ['progress'];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'project_members')
            ->withPivot('role', 'specialization')
            ->withTimestamps();
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    protected function progress(): Attribute
    {
        return Attribute::make(
            get: function () {
                $total = $this->tasks_count ?? $this->tasks()->count();
                if ($total === 0) {
                    return 0;
                }
                $done = $this->tasks_done_count ?? $this->tasks()->where('status', 'done')->count();
                return (int) round(($done / $total) * 100);
            }
        );
    }
}