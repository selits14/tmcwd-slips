<?php

namespace App\Http\Controllers;

use App\Models\GasSlip;
use App\Models\GatePass;
use App\Models\PassSlip;
use App\Models\User;
use App\Models\Division;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Pending counts
        $stats = [
            'pass_slips_pending'  => PassSlip::where('status', 'pending')->count(),
            'gate_passes_pending' => GatePass::where('status', 'pending')->count(),
            'gas_slips_pending'   => GasSlip::where('status', 'pending')->count(),
            'pass_slips_today'    => PassSlip::whereDate('created_at', today())->count(),
            'gate_passes_today'   => GatePass::whereDate('created_at', today())->count(),
            'gas_slips_today'     => GasSlip::whereDate('created_at', today())->count(),
            'pass_slips_out'      => PassSlip::where('status', 'out')->count(),
            'gate_passes_released'=> GatePass::where('status', 'released')->count(),
        ];

        // Recent activity — last 10 across all modules
        $recentPassSlips  = PassSlip::with(['employee', 'division'])
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

        $recentGatePasses = GatePass::with(['requestedBy', 'division'])
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

        $recentGasSlips   = GasSlip::with(['driver', 'vehicle'])
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
        $pendingPassSlips  = PassSlip::with(['employee', 'division'])
            ->where('status', 'pending')
            ->latest()->take(5)->get();

        $pendingGatePasses = GatePass::with(['requestedBy', 'division'])
            ->where('status', 'pending')
            ->latest()->take(5)->get();

        $pendingGasSlips   = GasSlip::with(['driver', 'vehicle'])
            ->where('status', 'pending')
            ->latest()->take(5)->get();

        return view('dashboard', compact(
            'stats',
            'recentActivity',
            'pendingPassSlips',
            'pendingGatePasses',
            'pendingGasSlips'
        ));
    }
}