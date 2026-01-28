<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Models\Role;

class Admin extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'password','role_id','forget_password_token','image','status','admin_type','slug','about_us'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function modules()
    {
        return $this->belongsToMany(
            \App\Models\Module::class,
            'admin_module'
        );
    }

    // public function hasModule($moduleName)
    // {
    //     return $this->modules()->where('name', $moduleName)->exists()
    //         || optional($this->role)->modules()->where('name', $moduleName)->exists();
    // }


// public function hasModule($moduleName)
// {
//     return $this->role
//         && $this->role->modules
//             ->pluck('name')
//             ->map(fn($n) => strtolower($n))
//             ->contains(strtolower($moduleName));
// }
public function hasModule($moduleName)
{

    if ($this->id == 1) {
        return true;
    }

    // No role or no modules
    if (!$this->role || !$this->role->modules) {
        return false;
    }

    return $this->role->modules
        ->pluck('name')
        ->map(fn ($name) => strtolower(trim($name)))
        ->contains(strtolower(trim($moduleName)));
}





}

