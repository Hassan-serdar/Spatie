<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rule;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;


class UserController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:View Users',only:['index']),
            new Middleware('permission:Edit Users',only:['edit']),
            new Middleware('permission:Delete Users',only:['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users=User::latest()->paginate(10);
        return view('users.list',[
            'users'=>$users
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user=User::findOrFail($id);
        $hasRoles=$user->roles->pluck('name');
        $roles=Role::orderBy('name','ASC')->get();
        return view('users.edit',[
            'user'=>$user,
            'roles'=>$roles,
            'hasRoles'=>$hasRoles,
        ]);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user=User::findOrFail($id);
        $validated=request()->validate([
            'name'=>'required|min:3',
            'email'=>[
            'required',
            'email',
            Rule::unique('users')->ignore($id),
            ],
            ]);
        $user->Update($validated);
        $user->syncRoles($request->role);
        return redirect()->route('users.index')->with('success','Users Updated successfully.');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user=User::findOrFail($id);
        if($user->hasRole('SuperAdmin')){
            return redirect()->route('users.index')->with('error','You Can not delete this account.');
        }
        $user->delete();
        return redirect()->route('users.index')->with('success','Users Deleted successfully.');

    }
}
