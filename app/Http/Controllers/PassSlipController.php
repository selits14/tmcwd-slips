<?php

namespace App\Http\Controllers;

use App\Models\PassSlip;
use App\Models\Division;
use App\Models\User;
use App\Support\SlipNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PassSlipController extends Controller
{
public function index(Request $request)
{
    $query = PassSlip::with(['employee', 'division', 'approvedBy'])
        ->latest();

    if ($request->filled('search')) {
        $query->where(function ($q) use ($request) {
            $q->where('slip_number', 'like', '%' . $request->search . '%')
              ->orWhereHas('employee', fn($q) =>
                    $q->where('name', 'like', '%' . $request->search . '%'))
              ->orWhere('purpose', 'like', '%' . $request->search . '%')
              ->orWhere('destination', 'like', '%' . $request->search . '%');
        });
    }
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }
    if ($request->filled('pass_type')) {
        $query->where('pass_type', $request->pass_type);
    }
    if ($request->filled('date_from')) {
        $query->whereDate('created_at', '>=', $request->date_from);
    }
    if ($request->filled('date_to')) {
        $query->whereDate('created_at', '<=', $request->date_to);
    }

    $passSlips = $query->paginate(15)->withQueryString();

    return view('pass-slips.index', compact('passSlips'));
}

    public function create()
    {
        $divisions = Division::where('is_active', true)->get();
        $employees = User::with('division')->where('is_active', true)->orderBy('name')->get();
        return view('pass-slips.create', compact('divisions', 'employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_user_id' => 'required|exists:users,id,is_active,1',
            'pass_type'       => 'required|in:official_business,personal',
            'purpose'         => 'required|string|max:255',
            'destination'     => 'required|string|max:255',
            'time_out'        => 'required|date',
            'expected_return' => 'required|date|after:time_out',
            'remarks'         => 'nullable|string|max:2000',
        ]);

        $employee = User::findOrFail($request->employee_user_id);

        DB::transaction(function () use ($request, $employee) {
            $displayPrefix = 'PS-' . now()->year . '-';

            PassSlip::create([
                'slip_number'      => SlipNumber::next('PS-', 5, $displayPrefix),
                'employee_user_id' => $employee->id,
                'encoded_by_user_id' => Auth::id(),
                'division_id'      => $employee->division_id ?? Auth::user()->division_id,
                'pass_type'        => $request->pass_type,
                'purpose'          => $request->purpose,
                'destination'      => $request->destination,
                'time_out'         => $request->time_out,
                'expected_return'  => $request->expected_return,
                'status'           => 'pending',
                'remarks'          => $request->remarks,
            ]);
        });

        return redirect()->route('pass-slips.index')
            ->with('success', 'Pass Slip submitted successfully!');
    }

    public function show(PassSlip $passSlip)
    {
        $passSlip->load(['employee', 'encodedBy', 'division', 'approvedBy', 'verifiedOutBy', 'verifiedInBy']);
        return view('pass-slips.show', compact('passSlip'));
    }

    public function approve(PassSlip $passSlip)
    {
        abort_unless(Auth::user()->canApproveSlipInDivision($passSlip->division), 403, 'Only the General Manager, division manager, or a super admin can approve pass slips.');
        abort_unless($passSlip->status === 'pending', 409, 'Only pending pass slips can be approved.');

        $passSlip->update([
            'status'             => 'approved',
            'approved_by_user_id'=> Auth::id(),
            'approved_at'        => now(),
        ]);

        return back()->with('success', 'Pass Slip approved!');
    }

    public function reject(Request $request, PassSlip $passSlip)
    {
        abort_unless(Auth::user()->canApproveSlipInDivision($passSlip->division), 403, 'Only the General Manager, division manager, or a super admin can reject pass slips.');
        abort_unless($passSlip->status === 'pending', 409, 'Only pending pass slips can be rejected.');

        $request->validate(['remarks' => 'nullable|string|max:2000']);

        $passSlip->update([
            'status'  => 'rejected',
            'remarks' => $request->remarks,
        ]);

        return back()->with('success', 'Pass Slip rejected.');
    }

    public function timeOut(PassSlip $passSlip)
    {
        abort_unless($passSlip->status === 'approved', 409, 'Only approved pass slips can be checked out.');

        $passSlip->update([
            'status'          => 'out',
            'time_out'        => now(),
            'verified_out_by' => Auth::id(),
        ]);

        return back()->with('success', 'Time out recorded!');
    }

    public function timeIn(PassSlip $passSlip)
    {
        abort_unless($passSlip->status === 'out', 409, 'Only checked out pass slips can be checked in.');

        $passSlip->update([
            'status'         => 'returned',
            'actual_return'  => now(),
            'verified_in_by' => Auth::id(),
        ]);

        return back()->with('success', 'Time in recorded!');
    }

public function print(PassSlip $passSlip)
{
    $passSlip->load(['employee', 'encodedBy', 'division', 'approvedBy', 'verifiedOutBy', 'verifiedInBy']);
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pass-slips.print', compact('passSlip'));
    $pdf->setPaper('a4', 'landscape'); // wide short page for 3 copies
    return $pdf->stream('pass-slip-' . $passSlip->slip_number . '.pdf');
}

public function batchPrint(Request $request)
{
    $ids = $request->input('ids', []);

    if (empty($ids)) {
        return back()->with('error', 'No pass slips selected.');
    }

    $passSlips = PassSlip::with(['employee', 'encodedBy', 'division', 'approvedBy'])
        ->whereIn('id', $ids)
        ->get();

    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
        'pass-slips.batch-print',
        compact('passSlips')
    );
    $pdf->setPaper('a4', 'landscape');
    return $pdf->stream('pass-slips-batch.pdf');
}
}
