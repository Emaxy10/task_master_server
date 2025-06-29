<?php

namespace App\Models;

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
        
    ] ;
}
