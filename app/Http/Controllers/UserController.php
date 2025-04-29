<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rule;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class UserController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:View Users', only: ['index']),
            new Middleware('permission:Edit Users', only: ['edit']),
            new Middleware('permission:Create Users', only: ['create']),
            new Middleware('permission:Delete Users', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $users = User::latest()->paginate(10);
            return view('users.list', [
                'users' => $users
            ]);
        } catch (\Exception $e) {
            Log::error('Unexpected error during return list users view' . $e->getMessage());
            return redirect()->route('/')->with('error', 'Failed to fetch users.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $roles = Role::where('name', '!=', 'SuperAdmin')->orderBy('name', 'ASC')->get();
            return view('users.create', [
                'roles' => $roles,
            ]);
        } catch (\Exception $e) {
            Log::error('Unexpected error during return create users view' . $e->getMessage());
            return redirect()->route('users.index')->with('error', 'Failed to load create form.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|min:3',
                'email' => [
                    'required',
                    'email',
                    Rule::unique('users'),
                ],
                'password' => 'required|min:5|confirmed',
                'role' => 'array',
                'role.*' => 'string|exists:roles,name',
            ]);
    
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => bcrypt($validated['password']),
            ]);
            if (in_array('SuperAdmin', $request->input('role', []))) {
                return back()->withErrors(['error' => 'You can not assign this role.']);
            }
            if (!empty($validated['role'] ?? null)) {
                $user->assignRole($validated['role']);
            }
             return redirect()->route('users.index')->with('success', 'Users Created successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed while creating user: ' .json_encode($e->errors()));
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Unexpected error during user creation: ' . json_encode($e->getMessage()));
            return redirect()->route('users.create')->with('error', 'Failed to create user.');
        }
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
        try {
            $user = User::findOrFail($id);
            $hasRoles = $user->roles->pluck('name');
            $currentUser=Auth()->user();
            if($currentUser->hasRole('SuperAdmin')){
                $roles = Role::orderBy('name', 'ASC')->get();
            }
            else{
            $roles = Role::where('name', '!=', 'SuperAdmin')->orderBy('name', 'ASC')->get();
            }
            return view('users.edit', [
                'user' => $user,
                'roles' => $roles,
                'hasRoles' => $hasRoles,
            ]);
        } catch (\Exception $e) {
            Log::error('Unexpected error during user editing (ID: ' . $id . '): ' . json_encode($e->getMessage()));
            return redirect()->route('users.index')->with('error', 'Failed to load edit form.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $user = User::findOrFail($id);
            $currentUser=Auth()->user();
            $validated = $request->validate([
                'name' => 'required|min:3',
                'email' => [
                    'required',
                    'email',
                    Rule::unique('users')->ignore($id),
                ],
                'role' => 'required|array',
                'role.*' => 'string|exists:roles,name',
            ]);
            if (($currentUser->hasRole('Admin') ||$currentUser->hasRole('writer') ) && $user->hasRole('SuperAdmin')) {
                return back()->withErrors(['error' => 'You can not make that change']);
            }
            $user->update($validated);
            $user->syncRoles($request->role);
    
            return redirect()->route('users.index')->with('success', 'User Updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed while updating user (ID: ' . $id . '): ' . json_encode($e->errors()));
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Unexpected error during user update (ID: ' . $id . '): ' . $e->getMessage());
            return redirect()->route('users.edit', $id)->with('error', 'Failed to update user.');
        }
    }
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $user = User::findOrFail($id);
            if ($user->hasRole('SuperAdmin')) {
                return redirect()->route('users.index')->with('error', 'You Can not delete this account.');
            }
            $user->delete();
            return redirect()->route('users.index')->with('success', 'Users Deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Unexpected error during user delete (ID: ' . $id . '): ' . $e->getMessage());
            return redirect()->route('users.index')->with('error', 'Failed to delete user.');
        }
    }
}