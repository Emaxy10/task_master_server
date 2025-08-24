<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    //
    protected $fillable = ['user_id', 'permissions'];

    protected $casts = [
        'permissions' => 'array',
    ];

    public function roles(){
        return $this->belongsTo(Role::class);
    }
}
