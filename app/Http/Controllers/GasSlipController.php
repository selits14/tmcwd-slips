<?php

namespace App\Http\Controllers;

use App\Models\GasSlip;
use App\Models\Vehicle;
use App\Models\User;
use App\Support\SlipNumber;
use App\Support\SlipVisibility;
use App\Support\SlipNotifier;
use App\Support\SlipTransition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GasSlipController extends Controller
{
public function index(Request $request)
{
    $query = SlipVisibility::query(GasSlip::with(['vehicle', 'driver', 'requestedBy', 'division']), Auth::user(), 'gas')
        ->latest();

    if ($request->filled('search')) {
        $query->where(function ($q) use ($request) {
            $q->where('slip_number', 'like', '%' . $request->search . '%')
              ->orWhereHas('driver', fn($q) =>
                    $q->where('name', 'like', '%' . $request->search . '%'))
              ->orWhereHas('vehicle', fn($q) =>
                    $q->where('plate_number', 'like', '%' . $request->search . '%'))
              ->orWhere('places_to_visit', 'like', '%' . $request->search . '%')
              ->orWhere('purpose', 'like', '%' . $request->search . '%');
        });
    }
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }
    if ($request->filled('vehicle_id')) {
        $query->where('vehicle_id', $request->vehicle_id);
    }
    if ($request->filled('date_from')) {
        $query->whereDate('created_at', '>=', $request->date_from);
    }
    if ($request->filled('date_to')) {
        $query->whereDate('created_at', '<=', $request->date_to);
    }

    $gasSlips  = $query->paginate(15)->withQueryString();
    $vehicles  = \App\Models\Vehicle::where('is_active', true)->get();

    return view('gas-slips.index', compact('gasSlips', 'vehicles'));
}

    public function create()
    {
        $vehicles = Vehicle::where('is_active', true)->get();
        $drivers  = User::where('is_active', true)
            ->where(fn ($query) => $query->where('position', 'Driver')
                ->orWhereHas('roles', fn ($roles) => $roles->where('name', 'driver')))
            ->orderBy('name')
            ->get();
        return view('gas-slips.create', compact('vehicles', 'drivers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'vehicle_id'      => 'required|exists:vehicles,id,is_active,1',
            'driver_user_id'  => 'required|exists:users,id,is_active,1',
            'fuel_type'       => 'required|in:Diesel,Gasoline',
            'gas_station'     => 'required|string|max:255',
            'last_refuel_date'=> 'nullable|date|before_or_equal:today',
            'requested_liters'=> 'nullable|numeric|min:0|max:999999.99|decimal:0,2',
            'passenger_name'  => 'nullable|string|max:255',
            'places_to_visit' => 'required|string|max:255',
            'purpose'         => 'required|string|max:500',
            'balance_in_tank_begin' => 'nullable|numeric|min:0|max:999999.99|decimal:0,2',
            'issued_from_stock' => 'nullable|numeric|min:0|max:999999.99|decimal:0,2',
            'remarks' => 'nullable|string|max:2000',
        ]);

        $driver = User::whereKey($request->driver_user_id)
            ->where('is_active', true)
            ->where(fn ($query) => $query->where('position', 'Driver')
                ->orWhereHas('roles', fn ($roles) => $roles->where('name', 'driver')))
            ->first();

        if (! $driver) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'driver_user_id' => 'Select an active user assigned as a driver.',
            ]);
        }

        $gasSlip = DB::transaction(function () use ($request, $driver) {
            $displayPrefix = 'GS-' . now()->year . '-';

            return GasSlip::create([
                'slip_number'          => SlipNumber::next('GS-', 4, $displayPrefix),
                'vehicle_id'           => $request->vehicle_id,
                'driver_user_id'       => $request->driver_user_id,
                'fuel_type'            => $request->fuel_type,
                'gas_station'          => $request->gas_station,
                'last_refuel_date'     => $request->last_refuel_date,
                'requested_liters'     => $request->requested_liters,
                'requested_by_user_id' => $driver->id,
                'encoded_by_user_id'   => Auth::id(),
                'division_id'          => $driver->division_id ?? Auth::user()->division_id,
                'passenger_name'       => $request->passenger_name,
                'places_to_visit'      => $request->places_to_visit,
                'purpose'              => $request->purpose,
                'balance_in_tank_begin'=> $request->balance_in_tank_begin,
                'issued_from_stock'    => $request->issued_from_stock ?? 0,
                'status'               => 'pending',
                'remarks'              => $request->remarks,
            ]);
        });

        SlipNotifier::submitted($gasSlip, 'Gas Slip', 'gas-slips.show');

        return redirect()->route('gas-slips.index')
            ->with('success', 'Gas Slip submitted successfully!');
    }

    public function show(GasSlip $gasSlip)
    {
        abort_unless(SlipVisibility::allows(Auth::user(), $gasSlip, 'gas'), 404);
        $gasSlip->load(['vehicle', 'driver', 'requestedBy', 'encodedBy', 'division', 'approvedBy', 'rejectedBy', 'releasedBy']);
        return view('gas-slips.show', compact('gasSlip'));
    }

    public function approve(GasSlip $gasSlip)
    {
        abort_unless(Auth::user()->canApproveSlipInDivision($gasSlip->division), 403, 'Only the General Manager, division manager, or a super admin can approve gas slips.');
        SlipTransition::update($gasSlip, 'pending', [
            'status'              => 'approved',
            'approved_by_user_id' => Auth::id(),
            'approved_at'         => now(),
        ]);
        SlipNotifier::decided($gasSlip, 'Gas Slip', 'gas-slips.show', 'approved', Auth::id());

        return back()->with('success', 'Gas Slip approved!');
    }

    public function reject(Request $request, GasSlip $gasSlip)
    {
        abort_unless(Auth::user()->canApproveSlipInDivision($gasSlip->division), 403, 'Only the General Manager, division manager, or a super admin can reject gas slips.');
        $request->validate(['remarks' => 'nullable|string|max:2000']);

        SlipTransition::update($gasSlip, 'pending', [
            'status'              => 'rejected',
            'remarks'             => $request->remarks,
            'rejected_by_user_id' => Auth::id(),
            'rejected_at'         => now(),
        ]);
        SlipNotifier::decided($gasSlip, 'Gas Slip', 'gas-slips.show', 'rejected', Auth::id());

        return back()->with('success', 'Gas Slip rejected.');
    }

    // Driver fills Section B after the trip
    public function complete(Request $request, GasSlip $gasSlip)
    {
        abort_unless(Auth::user()->hasRole('super_admin') || $gasSlip->driver_user_id === Auth::id(), 403, 'Only the assigned driver can complete this gas slip.');
        $request->validate([
            'time_departure_office'      => 'required|date',
            'time_arrival_destination'   => 'required|date|after_or_equal:time_departure_office',
            'time_departure_destination' => 'required|date|after_or_equal:time_arrival_destination',
            'time_arrival_office'        => 'required|date|after_or_equal:time_departure_destination',
            'distance_travelled'         => 'required|numeric|min:0|max:999999.99|decimal:0,2',
            'fuel_used'                  => 'required|numeric|min:0|max:999999.99|decimal:0,2',
            'purchased_during_trip'      => 'nullable|numeric|min:0|max:999999.99|decimal:0,2',
            'oil_used'                   => 'nullable|numeric|min:0|max:999999.99|decimal:0,2',
            'odometer_end'               => 'nullable|integer|min:0|max:2147483647',
        ]);

        $totalFuel = ($gasSlip->balance_in_tank_begin ?? 0)
                   + ($gasSlip->issued_from_stock ?? 0)
                   + ($request->purchased_during_trip ?? 0);

        $balanceEnd = $totalFuel - $request->fuel_used;
        if ($balanceEnd < 0) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'fuel_used' => 'Fuel used cannot exceed the fuel available for this trip.',
            ]);
        }

        SlipTransition::update($gasSlip, 'approved', [
            'status'                     => 'released',
            'time_departure_office'      => $request->time_departure_office,
            'time_arrival_destination'   => $request->time_arrival_destination,
            'time_departure_destination' => $request->time_departure_destination,
            'time_arrival_office'        => $request->time_arrival_office,
            'distance_travelled'         => $request->distance_travelled,
            'purchased_during_trip'      => $request->purchased_during_trip ?? 0,
            'fuel_used'                  => $request->fuel_used,
            'balance_in_tank_end'        => $balanceEnd,
            'released_by_user_id'        => Auth::id(),
            'released_at'                => now(),
            'oil_used'     => $request->oil_used ?? 0,
            'odometer_end' => $request->odometer_end,
        ]);

        return back()->with('success', 'Trip details recorded successfully!');
    }

public function print(GasSlip $gasSlip)
{
    abort_unless(SlipVisibility::allows(Auth::user(), $gasSlip, 'gas'), 404);
    $gasSlip->load(['vehicle', 'driver', 'requestedBy', 'encodedBy', 'division', 'approvedBy', 'rejectedBy']);
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('gas-slips.print', compact('gasSlip'));
    $pdf->setPaper('a4', 'landscape'); // wide short for 2 copies
    return $pdf->stream('gas-slip-' . $gasSlip->slip_number . '.pdf');
}
public function batchPrint(Request $request)
{
    $ids = $request->validate(['ids' => 'required|array', 'ids.*' => 'integer|min:1'])['ids'];

    if (empty($ids)) {
        return back()->with('error', 'No gas slips selected.');
    }

    $ids = array_values(array_unique($ids));
    $gasSlips = SlipVisibility::query(GasSlip::with(['vehicle', 'driver', 'requestedBy', 'encodedBy', 'approvedBy']), Auth::user(), 'gas')
        ->whereIn('id', $ids)
        ->get();
    abort_unless($gasSlips->count() === count($ids), 404);

    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
        'gas-slips.batch-print',
        compact('gasSlips')
    );
    $pdf->setPaper('a4', 'landscape');
    return $pdf->stream('gas-slips-batch.pdf');
}
}
