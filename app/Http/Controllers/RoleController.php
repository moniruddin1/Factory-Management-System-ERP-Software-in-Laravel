<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use DB;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user()->can('view-roles')) { abort(403, 'Unauthorized access.'); }

        $roles = Role::with('permissions')->latest()->paginate($request->per_page ?? 25);
        $permissions = Permission::all();
        return view('setup.roles.index', compact('roles', 'permissions'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->can('manage-roles')) { abort(403, 'Unauthorized action.'); }

        $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions' => 'array'
        ]);

        $role = Role::create(['name' => $request->name]);
        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return redirect()->back()->with('success', 'Role created successfully.');
    }

    public function update(Request $request, Role $role)
    {
        if (!auth()->user()->can('manage-roles')) { abort(403, 'Unauthorized action.'); }

        $request->validate([
            'name' => 'required|unique:roles,name,' . $role->id,
            'permissions' => 'array'
        ]);

        $role->update(['name' => $request->name]);
        $role->syncPermissions($request->permissions ?? []);

        return redirect()->back()->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        if (!auth()->user()->can('delete-roles')) { abort(403, 'Unauthorized action.'); }

        // Protection for Super Admin
        if ($role->name === 'Super Admin') {
            return redirect()->back()->with('error', 'Super Admin role cannot be deleted!');
        }

        $role->delete();
        return redirect()->back()->with('success', 'Role deleted successfully.');
    }
}
