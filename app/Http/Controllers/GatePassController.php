<?php

namespace App\Http\Controllers;

use App\Models\GatePass;
use App\Models\Division;
use App\Models\Vehicle;
use App\Models\User;
use App\Support\SlipNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GatePassController extends Controller
{
public function index(Request $request)
{
    $query = GatePass::with(['requestedBy', 'division', 'approvedBy'])
        ->latest();

    if ($request->filled('search')) {
        $query->where(function ($q) use ($request) {
            $q->where('pass_number', 'like', '%' . $request->search . '%')
              ->orWhere('driver_name', 'like', '%' . $request->search . '%')
              ->orWhere('plate_number', 'like', '%' . $request->search . '%')
              ->orWhere('destination', 'like', '%' . $request->search . '%')
              ->orWhere('purpose', 'like', '%' . $request->search . '%');
        });
    }
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }
    if ($request->filled('date_from')) {
        $query->whereDate('created_at', '>=', $request->date_from);
    }
    if ($request->filled('date_to')) {
        $query->whereDate('created_at', '<=', $request->date_to);
    }

    $gatePasses = $query->paginate(15)->withQueryString();

    return view('gate-passes.index', compact('gatePasses'));
}

    public function create()
    {
        $vehicles = Vehicle::where('is_active', true)->get();
        $requesters = User::with('division')->where('is_active', true)->orderBy('name')->get();
        return view('gate-passes.create', compact('vehicles', 'requesters'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'requested_by_user_id' => 'required|exists:users,id,is_active,1',
            'lr_number'    => 'nullable|string|max:50',
            'pr_number'    => 'nullable|string|max:50',
            'driver_name'  => 'required|string|max:255',
            'vehicle_type' => 'required|string|max:255',
            'plate_number' => 'required|string|max:50',
            'destination'  => 'required|string|max:255',
            'passengers'   => 'nullable|string|max:1000',
            'purpose'      => 'required|string|max:500',
            'time_of_departure' => 'required|date',
            'time_of_arrival' => 'nullable|date|after_or_equal:time_of_departure',
            'remarks'      => 'nullable|string|max:2000',
        ]);

        $requester = User::findOrFail($request->requested_by_user_id);

        DB::transaction(function () use ($request, $requester) {
            $displayPrefix = 'GP-' . now()->year . '-';

            GatePass::create([
                'pass_number'          => SlipNumber::next('GP-', 5, $displayPrefix),
                'requested_by_user_id' => $requester->id,
                'encoded_by_user_id'   => Auth::id(),
                'division_id'          => $requester->division_id ?? Auth::user()->division_id,
                'lr_number'            => $request->lr_number,
                'pr_number'            => $request->pr_number,
                'driver_name'          => $request->driver_name,
                'vehicle_type'         => $request->vehicle_type,
                'plate_number'         => $request->plate_number,
                'destination'          => $request->destination,
                'passengers'           => $request->passengers,
                'purpose'              => $request->purpose,
                'time_of_departure'    => $request->time_of_departure,
                'time_of_arrival'      => $request->time_of_arrival,
                'status'               => 'pending',
                'remarks'              => $request->remarks,
            ]);
        });

        return redirect()->route('gate-passes.index')
            ->with('success', 'Vehicle Pass submitted successfully!');
    }

    public function show(GatePass $gatePass)
    {
        $gatePass->load(['requestedBy', 'encodedBy', 'division', 'approvedBy', 'releasedBy']);
        return view('gate-passes.show', compact('gatePass'));
    }

    public function approve(GatePass $gatePass)
    {
        abort_unless($this->canApprove($gatePass), 403, 'Only the General Manager, division manager, or a super admin can approve vehicle passes.');
        abort_unless($gatePass->status === 'pending', 409, 'Only pending vehicle passes can be approved.');

        $gatePass->update([
            'status'              => 'approved',
            'approved_by_user_id' => Auth::id(),
            'approved_at'         => now(),
        ]);

        return back()->with('success', 'Vehicle Pass approved!');
    }

    public function reject(Request $request, GatePass $gatePass)
    {
        abort_unless($this->canApprove($gatePass), 403, 'Only the General Manager, division manager, or a super admin can reject vehicle passes.');
        abort_unless($gatePass->status === 'pending', 409, 'Only pending vehicle passes can be rejected.');

        $request->validate(['remarks' => 'nullable|string|max:2000']);

        $gatePass->update([
            'status'  => 'rejected',
            'remarks' => $request->remarks,
        ]);

        return back()->with('success', 'Vehicle Pass rejected.');
    }

    private function canApprove(GatePass $gatePass): bool
    {
        return Auth::user()->canApproveSlipInDivision($gatePass->division);
    }

    public function release(GatePass $gatePass)
    {
        abort_unless($gatePass->status === 'approved', 409, 'Only approved vehicle passes can be released.');

        $gatePass->update([
            'status'              => 'released',
            'released_by_user_id' => Auth::id(),
            'released_at'         => now(),
            'time_of_departure'   => now(),
        ]);

        return back()->with('success', 'Vehicle Pass released — time of departure recorded!');
    }

    public function returned(Request $request, GatePass $gatePass)
    {
        abort_unless($gatePass->status === 'released', 409, 'Only released vehicle passes can be marked returned.');

        $gatePass->update([
            'status'               => 'returned',
            'returned_at'          => now(),
            'time_of_arrival'      => now(),
            'returned_verified_by' => Auth::id(),
        ]);

        return back()->with('success', 'Vehicle arrived — time of arrival recorded!');
    }

public function print(GatePass $gatePass)
{
    $gatePass->load(['requestedBy', 'encodedBy', 'division', 'approvedBy', 'releasedBy']);
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('gate-passes.print', compact('gatePass'));
    $pdf->setPaper('a4', 'portrait'); // portrait for 2x2 layout
    return $pdf->stream('vehicle-pass-' . $gatePass->pass_number . '.pdf');
}
public function batchPrint(Request $request)
{
    $ids = $request->input('ids', []);

    if (empty($ids)) {
        return back()->with('error', 'No vehicle passes selected.');
    }

    $gatePasses = GatePass::with(['requestedBy', 'encodedBy', 'division', 'approvedBy'])
        ->whereIn('id', $ids)
        ->get();

    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
        'gate-passes.batch-print',
        compact('gatePasses')
    );
    $pdf->setPaper('a4', 'portrait');
    return $pdf->stream('gate-passes-batch.pdf');
}
}
