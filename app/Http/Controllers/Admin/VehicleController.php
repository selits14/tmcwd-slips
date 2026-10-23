<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\Division;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function index()
    {
        $vehicles = Vehicle::with('division')->latest()->paginate(15);
        return view('admin.vehicles.index', compact('vehicles'));
    }

    public function create()
    {
        $divisions = Division::where('is_active', true)->get();
        return view('admin.vehicles.create', compact('divisions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'plate_number' => 'required|string|unique:vehicles,plate_number',
            'description'  => 'required|string|max:255',
            'type'         => 'required|string|max:100',
        ]);

        Vehicle::create([
    'plate_number'         => strtoupper($request->plate_number),
    'description'          => $request->description,
    'type'                 => $request->type,
    'assigned_division_id' => $request->assigned_division_id,
    'num_cylinders'        => $request->num_cylinders,        // ← add
    'normal_km_per_liter'  => $request->normal_km_per_liter,  // ← add
    'is_active'            => true,
]);

        return redirect()->route('admin.vehicles.index')
            ->with('success', 'Vehicle added successfully!');
    }

    public function edit(Vehicle $vehicle)
    {
        $divisions = Division::where('is_active', true)->get();
        return view('admin.vehicles.edit', compact('vehicle', 'divisions'));
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $request->validate([
            'plate_number' => 'required|string|unique:vehicles,plate_number,' . $vehicle->id,
            'description'  => 'required|string|max:255',
            'type'         => 'required|string|max:100',
        ]);

        $vehicle->update([
    'plate_number'         => strtoupper($request->plate_number),
    'description'          => $request->description,
    'type'                 => $request->type,
    'assigned_division_id' => $request->assigned_division_id,
    'num_cylinders'        => $request->num_cylinders,        // ← add
    'normal_km_per_liter'  => $request->normal_km_per_liter,  // ← add
    'is_active'            => $request->has('is_active'),
]);

        return redirect()->route('admin.vehicles.index')
            ->with('success', 'Vehicle updated!');
    }

    public function destroy(Vehicle $vehicle)
    {
        $vehicle->update(['is_active' => false]);
        return back()->with('success', 'Vehicle deactivated.');
    }
}