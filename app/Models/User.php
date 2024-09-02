<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permissions', 'user_id', 'permission_id');
    }

    public function permissionIds()
    {
        return $this->permissions()->pluck('permissions.id')->toArray();
    }

    public function employee()
    {
        return $this->hasOne(Employees::class, 'employee_id', 'employee_id');
    }

    public function isAdmin()
    {
        return $this->is_admin == 1;
    }

    public function isEmployee()
    {
        return $this->is_admin == 2;
    }

    public function hasPermission($permissionId)
    {
        if ($this->isAdmin()) {
            return true; 
        }

        return $this->permissions()->where('permissions.id', $permissionId)->exists();
    }
}
