<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Role;
// use App\Models\Admin;

class Module extends Model
{
    protected $fillable = ['name'];

     public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_module');
    }

    // public function admins()
    // {
    //     return $this->belongsToMany(Admin::class, 'admin_module');
    // }
}

?>
