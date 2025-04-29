<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Validation\ValidationException;


class PermissionController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:View Permissions',only:['index']),
            new Middleware('permission:Edit Permissions',only:['edit']),
            new Middleware('permission:Create Permissions',only:['create']),
            new Middleware('permission:Delete Permissions',only:['destroy']),
        ];
    }

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
        try {
            $validated = $request->validate([
                'name' => 'required|string|min:3|max:30|unique:permissions',
            ]);
    
            Permission::create([
                'name' => $validated['name'],
            ]);
            return redirect()->route('permissions.index')->with('success', 'Permission created successfully.');
        } catch (ValidationException $e) {
            Log::error('Validation failed while creating permission: ' . json_encode($e->errors()));
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Failed to create permission: ' . $e->getMessage());
            Log::error('Request data: ' . json_encode($request->all()));
            return redirect()->back()->with('error', 'Something went wrong while creating the permission.')->withInput();
        }
    }
    public function edit($id){
        $permission=Permission::findOrFail($id);
        return view('permissions.edit',[
            'permission'=>$permission,
        ]);


    }
    //this for update a permission in db 
    public function update($id, Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => [
                    'required',
                    'string',
                    'min:3',
                    'max:30',
                    Rule::unique('permissions')->ignore($id),
                ],
            ]);
    
            $permission = Permission::findOrFail($id);
            $permission->name = $validated['name'];
            $permission->save();
    
            return redirect()->route('permissions.index')->with('success', 'Permission Updated successfully.');
        } catch (ValidationException $e) {
            Log::error('Validation failed while updating permission (ID: ' . $id . '): ' . json_encode($e->errors()));
            return redirect()->back()->withErrors($e->errors())->withInput();
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
            try {
                $permission = Permission::findOrFail($id);
                $permission->delete();
                return redirect()->route('permissions.index')->with('success','Permission Deleted successfully');
            } catch (\Exception $e) {
                Log::error('Failed to delete permission (ID: ' . $id . '): ' . $e->getMessage());
                return redirect()->route('permissions.index')->with('error', 'Failed to delete permission.');
            }
        }
        
}
