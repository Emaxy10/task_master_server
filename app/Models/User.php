<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use App\Models\Task;
use App\Models\Role;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function tasks(){
        return $this->hasMany(Task::class);
    }

    // users can create many tasks
    public function createdTasks() {
        return $this->hasMany(Task::class, 'user_id');
    }

    public function assignedTasks()
    {
        return $this->belongsToMany(Task::class, 'task_user');
    }

    public function roles(){
        return $this->belongsToMany(Role::class, 'user_role');
    }

   public function subscription()
{
    return $this->hasOne(Subscription::class);
}


    public function hasRole($roles){
        if(is_array($roles)){
            return $this->roles->pluck('name')->intersect($roles)->isNotEmpty();
        }

        if(is_string($roles)){
            return $this->roles->contains('name', $roles);
        }
        
        if(is_numeric($roles)){
            return $this->roles->contains('name', $roles);
        }

        return false;
    }

    

    public function assignRole($role){
        if(is_string($role)){
            $role = Role::where('name', $role)->firstOrFail();
        }
        elseif(is_numeric($role)){
            $role = Role::findOrFail($role);
         }
      return $this->roles()->syncWithoutDetaching([$role->id]);
    }

    public function removeRole($role){
        return $this->roles()->detach($role);
    }

    public function hasSubscription($subs)
    {
        if (is_array($subs)) {
            return in_array($this->subscription?->plan, $subs);
        }

        if (is_string($subs)) {
            return $this->subscription?->plan === $subs;
        }

        return false;
    }

    // A supervisor has many members
    public function members()
    {
        return $this->hasMany(User::class, 'supervisor_id');
    }

    // A member belongs to one supervisor
    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }





}
