<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Module;

class UserController extends Controller
{
    /**
     * Show assign modules to role page
     */
    public function create()
    {
        $roles   = Role::orderBy('name')->get();
        $modules = Module::orderBy('name')->get();

        return view('admin.user.create', compact('roles', 'modules'));
    }

    /**
     * Assign modules to role
     */
    public function store(Request $request)
    {
        $request->validate([
            'role_id'   => 'required|exists:roles,id',
            'modules'   => 'required|array',
            'modules.*' => 'exists:modules,id',
        ]);

        $role = Role::findOrFail($request->role_id);

        // Assign modules to role (delete old + insert new)
        $role->modules()->sync($request->modules);

        return redirect()->back()->with('success', 'Modules assigned to role successfully');
    }


    public function destroy(Role $role)
{
    // Remove role-module relations first
    $role->modules()->detach();

    // Delete role
    $role->delete();

    return redirect()
        ->back()
        ->with('success', 'Role deleted successfully');
}



}







