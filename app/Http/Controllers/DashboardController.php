<?php

namespace App\Http\Controllers;

use App\Models\GasSlip;
use App\Models\GatePass;
use App\Models\PassSlip;
use App\Models\User;
use App\Models\Division;
use App\Models\Vehicle;
use Illuminate\Support\Carbon;
use App\Support\SlipVisibility;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $limitedFuelVehicles = collect();
        if ($user->hasRole('super_admin')) {
            $month = Carbon::now();
            $limitedFuelVehicles = Vehicle::where('is_active', true)
                ->whereNotNull('monthly_fuel_allocation')
                ->withSum(['gasSlips as monthly_fuel_used' => fn ($query) => $query
                    ->where('status', 'released')
                    ->whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)], 'fuel_used')
                ->get()
                ->each(fn ($vehicle) => $vehicle->monthly_fuel_used = $vehicle->monthly_fuel_used ?? 0);
        }

        // Pending counts
        $stats = [
            'pass_slips_pending'  => SlipVisibility::query(PassSlip::query(), $user, 'pass')->where('status', 'pending')->count(),
            'gate_passes_pending' => SlipVisibility::query(GatePass::query(), $user, 'gate')->where('status', 'pending')->count(),
            'gas_slips_pending'   => SlipVisibility::query(GasSlip::query(), $user, 'gas')->where('status', 'pending')->count(),
            'pass_slips_today'    => SlipVisibility::query(PassSlip::query(), $user, 'pass')->whereDate('created_at', today())->count(),
            'gate_passes_today'   => SlipVisibility::query(GatePass::query(), $user, 'gate')->whereDate('created_at', today())->count(),
            'gas_slips_today'     => SlipVisibility::query(GasSlip::query(), $user, 'gas')->whereDate('created_at', today())->count(),
            'pass_slips_out'      => SlipVisibility::query(PassSlip::query(), $user, 'pass')->where('status', 'out')->count(),
            'gate_passes_released'=> SlipVisibility::query(GatePass::query(), $user, 'gate')->where('status', 'released')->count(),
        ];

        // Recent activity — last 10 across all modules
        $recentPassSlips  = SlipVisibility::query(PassSlip::with(['employee', 'division']), $user, 'pass')
            ->latest()->take(5)->get()
            ->map(fn($s) => [
                'type'       => 'Pass Slip',
                'number'     => $s->slip_number,
                'name'       => $s->employee->name,
                'status'     => $s->status,
                'time'       => $s->updated_at,
                'color'      => 'blue',
                'route'      => route('pass-slips.show', $s->id),
            ]);

        $recentGatePasses = SlipVisibility::query(GatePass::with(['requestedBy', 'division']), $user, 'gate')
            ->latest()->take(5)->get()
            ->map(fn($g) => [
                'type'       => 'Vehicle Pass',
                'number'     => $g->pass_number,
                'name'       => $g->driver_name,
                'status'     => $g->status,
                'time'       => $g->updated_at,
                'color'      => 'green',
                'route'      => route('gate-passes.show', $g->id),
            ]);

        $recentGasSlips   = SlipVisibility::query(GasSlip::with(['driver', 'vehicle']), $user, 'gas')
            ->latest()->take(5)->get()
            ->map(fn($g) => [
                'type'       => 'Gas Slip',
                'number'     => $g->slip_number,
                'name'       => $g->driver->name,
                'status'     => $g->status,
                'time'       => $g->updated_at,
                'color'      => 'yellow',
                'route'      => route('gas-slips.show', $g->id),
            ]);

        // Merge and sort by time
        $recentActivity = $recentPassSlips
            ->concat($recentGatePasses)
            ->concat($recentGasSlips)
            ->sortByDesc('time')
            ->take(10)
            ->values();

        // Pending slips list for approvers
        $pendingPassSlips  = SlipVisibility::query(PassSlip::with(['employee', 'division']), $user, 'pass')
            ->where('status', 'pending')
            ->latest()->take(5)->get();

        $pendingGatePasses = SlipVisibility::query(GatePass::with(['requestedBy', 'division']), $user, 'gate')
            ->where('status', 'pending')
            ->latest()->take(5)->get();

        $pendingGasSlips   = SlipVisibility::query(GasSlip::with(['driver', 'vehicle']), $user, 'gas')
            ->where('status', 'pending')
            ->latest()->take(5)->get();

        return view('dashboard', compact(
            'stats',
            'recentActivity',
            'pendingPassSlips',
            'pendingGatePasses',
            'pendingGasSlips',
            'limitedFuelVehicles'
        ));
    }
}
