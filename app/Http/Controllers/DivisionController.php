<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Division;
use App\Models\User;
use Illuminate\Http\Request;

class DivisionController extends Controller
{
    public function index()
    {
        $divisions = Division::with('head')->latest()->paginate(15);
        return view('admin.divisions.index', compact('divisions'));
    }

    public function create()
    {
        $users = User::where('is_active', true)->orderBy('name')->get();
        return view('admin.divisions.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:divisions,code',
        ]);

        Division::create([
            'name'         => $request->name,
            'code'         => strtoupper($request->code),
            'head_user_id' => $request->head_user_id,
            'is_active'    => true,
        ]);

        return redirect()->route('admin.divisions.index')
            ->with('success', 'Division added successfully!');
    }

    public function edit(Division $division)
    {
        $users = User::where('is_active', true)->orderBy('name')->get();
        return view('admin.divisions.edit', compact('division', 'users'));
    }

    public function update(Request $request, Division $division)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:divisions,code,' . $division->id,
        ]);

        $division->update([
            'name'         => $request->name,
            'code'         => strtoupper($request->code),
            'head_user_id' => $request->head_user_id,
            'is_active'    => $request->has('is_active'),
        ]);

        return redirect()->route('admin.divisions.index')
            ->with('success', 'Division updated!');
    }

    public function destroy(Division $division)
    {
        $division->update(['is_active' => false]);
        return back()->with('success', 'Division deactivated.');
    }
}
