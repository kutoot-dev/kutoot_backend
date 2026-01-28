<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;

class RoleController extends Controller
{
    public function ajaxStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100'
        ]);

        $role = Role::firstOrCreate([
            'name' => $request->name
        ]);

        return response()->json([
            'success' => true,
            'role' => $role
        ]);
    }





}
