<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class RoleController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:View Roles', only: ['index']),
            new Middleware('permission:Edit Roles', only: ['edit']),
            new Middleware('permission:Create Roles', only: ['create']),
            new Middleware('permission:Delete Roles', only: ['destroy']),
        ];
    }

    public function index()
    {
        try {
            $roles = Role::orderby("name", "ASC")->paginate(10);
            return view('roles.list', ['roles' => $roles]);
        } catch (\Exception $e) {
            return redirect()->route('roles.index')->with('error', 'Failed to fetch roles.');
        }
    }

    public function create()
    {
        try {
            $permissions = Permission::orderby("name", "ASC")->get();
            return view('roles.create', ['permissions' => $permissions]);
        } catch (\Exception $e) {
            return redirect()->route('roles.index')->with('error', 'Failed to load create form.');
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|min:3|max:30|unique:roles',
            ]);
    
            $role = Role::create(['name' => $validated['name']]);
    
            if ($request->permission) {
                foreach ($request->permission as $id) {
                    $role->givePermissionTo($id);
                }
                return redirect()->route('roles.index')->with('success', 'Role added successfully.');
            } else {
                return redirect()->route('roles.index')->with('error', 'Role added successfully without any permissions.');
            }
        } catch (ValidationException $e) {
            Log::error('Validation failed while creating role: ' . json_encode($e->errors()));
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Unexpected error during role creation: ' . $e->getMessage());
            return redirect()->route('roles.create')->with('error', 'Failed to create role.');
        }
    }
    
    public function edit($id)
    {
        try {
            $role = Role::findOrFail($id);
            $hasPermissions = $role->permissions->pluck('name');
            $permissions = Permission::orderby("name", "ASC")->get();

            return view('roles.edit', [
                'role' => $role,
                'permissions' => $permissions,
                'hasPermissions' => $hasPermissions,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('roles.index')->with('error', 'Failed to load edit form.');
        }
    }

    public function update($id, Request $request)
    {
        try {
            $role = Role::findOrFail($id);
            $validated = $request->validate([
                'name' => [
                    'required',
                    'string',
                    'min:3',
                    'max:30',
                    Rule::unique('roles')->ignore($id),
                ],
            ]);
    
            $role->name = $validated['name'];
            $role->save();
    
            if ($request->permission) {
                $role->syncPermissions($request->permission);
            } else {
                $role->syncPermissions([]);
            }
            return redirect()->route('roles.index')->with('success', 'Roles Updated successfully.');
        } catch (ValidationException $e) {
            Log::error('Validation failed while updating role (ID: ' . $id . '): ' . json_encode($e->errors()));
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Unexpected error during role update (ID: ' . $id . '): ' . $e->getMessage());
            return redirect()->route('roles.edit', $id)->with('error', 'Failed to update role.');
        }
    }
    
    public function destroy($id)
    {
        try {
            $role = Role::findOrFail($id);
            $role->delete();
            return redirect()->route('roles.index')->with('success', 'Roles deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('roles.index')->with('error', 'Failed to delete role.');
        }
    }
}