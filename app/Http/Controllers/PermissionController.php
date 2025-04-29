<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    //this for show  permission page 
    public function index(){
        $permissions=Permission::orderby("created_at",'DESC')->paginate(25);
        return view('permissions.list',[
            'permissions'=>$permissions,
        ]); 
    }



    //this for show  a create permission page 
    public function  create(){
        return view('permissions.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|min:3|max:30|unique:permissions',
        ]);
        try {
    
        
            Permission::create([
                'name' => $validated['name'],
            ]);
            return redirect()->route('permissions.index')->with('success', 'Permission created successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to create permission: ' . $e->getMessage());
            Log::error('Request data: ', $request->all());
        
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }
    
    //this for show  an edit permission page 
    public function edit($id){
        $permission=Permission::findOrFail($id);
        return view('permissions.edit',[
            'permission'=>$permission,
        ]);


    }

    //this for update a permission in db 
public function update($id, Request $request)
{
    $validated = $request->validate([
        'name' => [
            'required',
            'string',
            'min:3',
            'max:30',
            Rule::unique('permissions')->ignore($id),
        ],
    ]);
    try {
        $permission = Permission::findOrFail($id);
        $permission->name = $validated['name'];
        $permission->save();

        return redirect()->route('permissions.index')->with('success', 'Permission Updated successfully.');

    } catch (\Exception $e) {
        Log::error('Error updating permission: ' . $e->getMessage(), [
            'id' => $id,
            'stack' => $e->getTraceAsString(),
        ]);

        return redirect()->route('permissions.index')->with('error','Something went wrong while updating the permission.');
    }
}
    //this for delete permission from db  
    public function destroy($id){
        $permission=Permission::findOrFail($id);
        $permission->delete();
        return redirect()->route('permissions.index')->with('success','Permission Deleted successfully');
    }

}
