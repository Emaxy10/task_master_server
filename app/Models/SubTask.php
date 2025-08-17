<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubTask extends Model
{
    //
     protected $fillable = [
        'task_id',
        'title',
        'description',
        'is_completed',
        'end_date',
    ];

    /**
     * A subtask belongs to a task.
     */
    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
