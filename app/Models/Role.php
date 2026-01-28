<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';

    protected $fillable = ['name'];

    public function admins()
    {
        return $this->hasMany(Admin::class);
    }
      public function modules()
    {
        return $this->belongsToMany(Module::class, 'role_module');
    }

}
