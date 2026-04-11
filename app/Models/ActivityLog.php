<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_id', 'subject_type', 'subject_id', 'action', 'description'])]
class ActivityLog extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subject()
    {
        return $this->morphTo();
    }

    public static function record(User $user, Model $subject, string $action, string $description): void
    {
        static::create([
            'user_id' => $user->id,
            'subject_type' => get_class($subject),
            'subject_id' => $subject->id,
            'action' => $action,
            'description' => $description,
        ]);
    }
}
