<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Division;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with(['division', 'roles'])->latest()->paginate(15);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $divisions = Division::where('is_active', true)->get();
        $roles     = Role::all();
        return view('admin.users.create', compact('divisions', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:users,email',
            'employee_id' => 'nullable|string|unique:users,employee_id',
            'password'    => 'required|string|min:8|confirmed',
            'role'        => 'required|exists:roles,name',
            'division_id' => 'nullable|exists:divisions,id',
        ]);

        $user = User::create([
            'name'        => $request->name,
            'email'       => $request->email,
            'employee_id' => $request->employee_id,
            'position'    => $request->position,
            'password'    => Hash::make($request->password),
            'division_id' => $request->division_id,
            'is_active'   => true,
        ]);

        $user->assignRole($request->role);

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully!');
    }

    public function edit(User $user)
    {
        $divisions = Division::where('is_active', true)->get();
        $roles     = Role::all();
        return view('admin.users.edit', compact('user', 'divisions', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:users,email,' . $user->id,
            'employee_id' => 'nullable|string|unique:users,employee_id,' . $user->id,
            'role'        => 'required|exists:roles,name',
            'division_id' => 'nullable|exists:divisions,id',
        ]);

        $user->update([
            'name'        => $request->name,
            'email'       => $request->email,
            'employee_id' => $request->employee_id,
            'position'    => $request->position,
            'division_id' => $request->division_id,
            'is_active'   => $request->has('is_active'),
        ]);

        // Update password only if provided
        if ($request->filled('password')) {
            $request->validate([
                'password' => 'min:8|confirmed',
            ]);
            $user->update(['password' => Hash::make($request->password)]);
        }

        // Sync role
        $user->syncRoles([$request->role]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated!');
    }

    public function destroy(User $user)
    {
        $user->update(['is_active' => false]);
        return back()->with('success', 'User deactivated.');
    }
}