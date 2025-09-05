<?php

namespace App\Models;
use App\Models\User;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    //
    protected $fillable = [
        "title",
        "description",
        "start_date",
        "end_date",
        "due_date",
        "status",
        "is_completed",
        "completed_at",
        "is_recurring",
        "recurrence_rule",
        "user_id",
        "custom_date",
        "custom_time",
        "weekly_day",
        "priority",
        "assigned_by"
        
    ] ;

    public function user(){
        return $this->belongsTo(User::class);
    }


    public function assignees()
    {
        return $this->belongsToMany(User::class, 'task_user');
    }

    public function subTasks(){
        return $this->hasMany(SubTask::class);
    }
}
