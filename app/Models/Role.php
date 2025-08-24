<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    //

     public function users(){
        
        return $this->belongsToMany(User::class, 'user_role');
    }

    public function permissions(){
        return $this->hasMany(Permission::class);
    }

    public function hasPermission($perm)
    {
        $allPermissions = $this->permissions->pluck('permissions')->flatten()->toArray();
        return in_array($perm, $allPermissions);
    }
}
