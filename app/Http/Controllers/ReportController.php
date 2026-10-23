<?php

namespace App\Http\Controllers;

use App\Models\GasSlip;
use App\Models\GatePass;
use App\Models\PassSlip;
use App\Models\Vehicle;
use App\Models\Division;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Support\SlipVisibility;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    // Pass Slip Report
    public function passSlips(Request $request)
    {
        $query = SlipVisibility::query(PassSlip::with(['employee', 'division', 'approvedBy']), Auth::user(), 'pass')
            ->latest();

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->filled('division_id')) {
            $query->where('division_id', $request->division_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $passSlips = $query->paginate(20)->withQueryString();
        $divisions = Division::where('is_active', true)->get();

        return view('reports.pass-slips', compact('passSlips', 'divisions'));
    }

    // Vehicle Pass Report
    public function gatePasses(Request $request)
    {
        $query = SlipVisibility::query(GatePass::with(['requestedBy', 'division', 'approvedBy']), Auth::user(), 'gate')
            ->latest();

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->filled('plate_number')) {
            $query->where('plate_number', 'like', '%' . $request->plate_number . '%');
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $gatePasses = $query->paginate(20)->withQueryString();

        return view('reports.gate-passes', compact('gatePasses'));
    }

    // Monthly Fuel Consumption Report
    public function fuelConsumption(Request $request)
    {
        $month   = $request->get('month', now()->format('Y-m'));
        $vehicle = $request->get('vehicle_id');

        [$year, $mon] = explode('-', $month);

        $query = SlipVisibility::query(GasSlip::with(['vehicle', 'driver']), Auth::user(), 'gas')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $mon)
            ->where('status', 'released');

        if ($vehicle) {
            $query->where('vehicle_id', $vehicle);
        }

        $gasSlips = $query->get();
        $vehicles = Vehicle::where('is_active', true)->get();

        // Group by vehicle
        $summary = $gasSlips->groupBy('vehicle_id')->map(function ($slips) {
            return [
                'vehicle'          => $slips->first()->vehicle,
                'driver'           => $slips->first()->driver,
                'total_distance'   => $slips->sum('distance_travelled'),
                'total_fuel_used'  => $slips->sum('fuel_used'),
                'total_trips'      => $slips->count(),
                'slips'            => $slips,
            ];
        });

        return view('reports.fuel-consumption',
            compact('gasSlips', 'summary', 'vehicles', 'month'));
    }

    // Print Fuel Report PDF
    public function fuelConsumptionPrint(Request $request)
    {
        $month = $request->get('month', now()->format('Y-m'));
        [$year, $mon] = explode('-', $month);

        $gasSlips = SlipVisibility::query(GasSlip::with(['vehicle', 'driver']), Auth::user(), 'gas')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $mon)
            ->where('status', 'released')
            ->get();

        $summary = $gasSlips->groupBy('vehicle_id')->map(function ($slips) {
            return [
                'vehicle'        => $slips->first()->vehicle,
                'driver'         => $slips->first()->driver,
                'total_distance' => $slips->sum('distance_travelled'),
                'total_fuel'     => $slips->sum('fuel_used'),
                'slips'          => $slips,
            ];
        });

        $monthLabel = Carbon::createFromFormat('Y-m', $month)->format('F Y');

        $pdf = Pdf::loadView('reports.fuel-print',
            compact('summary', 'gasSlips', 'monthLabel', 'month'));
        $pdf->setPaper('a4', 'landscape');

        return $pdf->stream('fuel-report-' . $month . '.pdf');
    }
// MROT — Monthly Report of Official Travel (per vehicle)
public function vehicleMrot(Request $request)
{
    $month      = $request->get('month', now()->format('Y-m'));
    $vehicle_id = $request->get('vehicle_id');
    $vehicles   = \App\Models\Vehicle::where('is_active', true)->get();

    [$year, $mon] = explode('-', $month);

    $query = SlipVisibility::query(GasSlip::with(['vehicle', 'driver']), Auth::user(), 'gas')
        ->whereYear('created_at', $year)
        ->whereMonth('created_at', $mon)
        ->where('status', 'released')
        ->orderBy('created_at');

    if ($vehicle_id) {
        $query->where('vehicle_id', $vehicle_id);
    }

    $gasSlips = $query->get();
    $grouped  = $gasSlips->groupBy('vehicle_id');

    return view('reports.vehicle-mrot',
        compact('gasSlips', 'grouped', 'vehicles', 'month'));
}

public function vehicleMrotPrint(Request $request)
{
    $month      = $request->get('month', now()->format('Y-m'));
    $vehicle_id = $request->get('vehicle_id');

    [$year, $mon] = explode('-', $month);

    $query = SlipVisibility::query(GasSlip::with(['vehicle', 'driver']), Auth::user(), 'gas')
        ->whereYear('created_at', $year)
        ->whereMonth('created_at', $mon)
        ->where('status', 'released')
        ->orderBy('created_at');

    if ($vehicle_id) {
        $query->where('vehicle_id', $vehicle_id);
    }

    $gasSlips   = $query->get();
    $grouped    = $gasSlips->groupBy('vehicle_id');
    $monthLabel = Carbon::createFromFormat('Y-m', $month)->format('F Y');

    $pdf = Pdf::loadView('reports.vehicle-mrot-print',
        compact('grouped', 'monthLabel'));
    $pdf->setPaper('a4', 'portrait');

    return $pdf->stream('mrot-' . $month . '.pdf');
}

// RFC — Report of Fuel Consumption (summary)
public function vehicleRfc(Request $request)
{
    $month    = $request->get('month', now()->format('Y-m'));
    $vehicles = \App\Models\Vehicle::where('is_active', true)->get();

    [$year, $mon] = explode('-', $month);

    $gasSlips = SlipVisibility::query(GasSlip::with(['vehicle', 'driver']), Auth::user(), 'gas')
        ->whereYear('created_at', $year)
        ->whereMonth('created_at', $mon)
        ->where('status', 'released')
        ->get();

    $summary = $gasSlips->groupBy('vehicle_id')->map(function ($slips) {
        $vehicle        = $slips->first()->vehicle;
        $totalDistance  = $slips->sum('distance_travelled');
        $totalFuel      = $slips->sum('fuel_used');
        $kmPerLiter     = $totalFuel > 0 ? $totalDistance / $totalFuel : 0;
        $normalKm       = $vehicle->normal_km_per_liter ?? 10;
        $allowedLiters  = $normalKm > 0 ? ($totalDistance / $normalKm) * 1.1 : 0;
        $excess         = $totalFuel - $allowedLiters;
        $odomBegin      = $slips->whereNotNull('balance_in_tank_begin')->first()?->balance_in_tank_begin;
        $odomEnd        = $slips->last()?->odometer_end;

        return [
            'vehicle'        => $vehicle,
            'driver'         => $slips->first()->driver,
            'odometer_begin' => $slips->first()->balance_in_tank_begin ?? 0,
            'odometer_end'   => $slips->last()->odometer_end ?? 0,
            'total_distance' => $totalDistance,
            'total_fuel'     => $totalFuel,
            'km_per_liter'   => $kmPerLiter,
            'normal_km'      => $normalKm,
            'allowed_liters' => $allowedLiters,
            'excess'         => $excess,
            'slips'          => $slips,
        ];
    });

    return view('reports.vehicle-rfc',
        compact('summary', 'vehicles', 'month'));
}

public function vehicleRfcPrint(Request $request)
{
    $month = $request->get('month', now()->format('Y-m'));
    [$year, $mon] = explode('-', $month);

    $gasSlips = SlipVisibility::query(GasSlip::with(['vehicle', 'driver']), Auth::user(), 'gas')
        ->whereYear('created_at', $year)
        ->whereMonth('created_at', $mon)
        ->where('status', 'released')
        ->orderBy('created_at')
        ->get();

    $summary = $gasSlips->groupBy('vehicle_id')->map(function ($slips) {
        $vehicle       = $slips->first()->vehicle;
        $totalDistance = $slips->sum('distance_travelled');
        $totalFuel     = $slips->sum('fuel_used');
        $kmPerLiter    = $totalFuel > 0 ? $totalDistance / $totalFuel : 0;
        $normalKm      = $vehicle->normal_km_per_liter ?? 10;
        $allowedLiters = $normalKm > 0 ? ($totalDistance / $normalKm) * 1.1 : 0;
        $excess        = $totalFuel - $allowedLiters;

        return [
            'vehicle'        => $vehicle,
            'driver'         => $slips->first()->driver,
            'odometer_begin' => $slips->whereNotNull('odometer_before')->first()?->odometer_before ?? '—',
            'odometer_end'   => $slips->whereNotNull('odometer_end')->last()?->odometer_end ?? '—',
            'total_distance' => $totalDistance,
            'total_fuel'     => $totalFuel,
            'km_per_liter'   => $kmPerLiter,
            'normal_km'      => $normalKm,
            'allowed_liters' => $allowedLiters,
            'excess'         => $excess,
            'slips'          => $slips, // ← this was missing
        ];
    });

    $monthLabel = Carbon::createFromFormat('Y-m', $month)->format('F Y');

    $pdf = Pdf::loadView('reports.vehicle-rfc-print',
        compact('summary', 'monthLabel'));
    $pdf->setPaper('a4', 'landscape');

    return $pdf->stream('rfc-' . $month . '.pdf');
}
    
}
