<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:View Roles',only:['index']),
            new Middleware('permission:Edit Roles',only:['edit']),
            new Middleware('permission:Create Roles',only:['create']),
            new Middleware('permission:Delete Roles',only:['destroy']),
        ];
    }
    // this func will show roles page
    public function index() {
        $roles=Role::orderby("name","ASC")->paginate(10);
        return view('roles.list',[
            'roles'=>$roles
        ]);
    }

    //This func will show a create role page
    public function create() {
        $permissions=Permission::orderby("name","ASC")->get();
        return view('roles.create',[
            'permissions'=>$permissions,
        ]);
    }

    //This func will insert role in DB
    public function store(Request $request){
        $validated = $request->validate([
            'name' => 'required|string|min:3|max:30|unique:roles',
        ]);
        $role= Role::create([
            'name'=>$validated['name'], 
        ]);
        if($request->permission){
            foreach ($request->permission as $id) {
                $role->givePermissionTo($id);
            }
            return redirect()->route('roles.index')->with('success','Roles added successfully.');
        }
         else {
            return redirect()->route('roles.index')->with('error','Roles added successfully without any permissions.');

         }
    }

    //This func will show an edit role page
    public function edit($id){
        $role=Role::findOrFail($id);
        $hasPermissions=$role->permissions->pluck('name');
        $permissions=Permission::orderby("name","ASC")->get();

        return view('roles.edit',[
            'role'=>$role,
            'permissions'=>$permissions,
            'hasPermissions'=>$hasPermissions,
        ]);

    }

    public function update($id,Request $request){
        $role=Role::findOrFail($id);
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'min:3',
                'max:30',
                Rule::unique('roles')->ignore($id),
            ],
        ]);   
         $role->name=$validated['name'];
        $role->save();

        if($request->permission){
            $role->syncPermissions($request->permission);
            return redirect()->route('roles.index')->with('success','Roles added successfully.');
        }
        else {
            $role->syncPermissions([]);
            return redirect()->route('roles.index')->with('success','Roles added successfully.');

        }
        return redirect()->route('roles.index')->with('error','Oops! Some thing goes wrong please try agsin later.');

    }

    public function destroy($id) {
        $role=Role::findOrFail($id);
        $role->delete();
        return redirect()->route('roles.index')->with('success','Roles deleted successfully.');

    }


}
