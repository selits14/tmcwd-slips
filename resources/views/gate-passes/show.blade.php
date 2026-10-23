<x-app-layout>
    <x-slot name="title">Vehicle Pass — {{ $gatePass->pass_number }}</x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-xl shadow p-6">

            {{-- Header --}}
            <div class="flex items-center justify-between mb-2">
                <div>
                    <h3 class="text-lg font-bold text-gray-800">VEHICLE PASS</h3>
                    <p class="text-sm font-mono text-green-600">{{ $gatePass->pass_number }}</p>
                </div>
                @php
                    $colors = [
                        'pending'  => 'bg-yellow-100 text-yellow-700',
                        'approved' => 'bg-green-100 text-green-700',
                        'released' => 'bg-blue-100 text-blue-700',
                        'returned' => 'bg-purple-100 text-purple-700',
                        'rejected' => 'bg-red-100 text-red-700',
                    ];
                @endphp
                <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $colors[$gatePass->status] ?? 'bg-gray-100 text-gray-600' }}">
                    {{ ucfirst($gatePass->status) }}
                </span>
            </div>
            <p class="text-xs text-gray-400 mb-6">{{ $gatePass->created_at->format('F d, Y') }}</p>

            {{-- LR / PR --}}
            <div class="grid grid-cols-2 gap-4 mb-4 p-3 bg-gray-50 rounded-lg">
                <div>
                    <p class="text-xs text-gray-500">LR No.</p>
                    <p class="text-sm font-semibold">{{ $gatePass->lr_number ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">PR No.</p>
                    <p class="text-sm font-semibold">{{ $gatePass->pr_number ?? '-' }}</p>
                </div>
            </div>

            {{-- Vehicle Info --}}
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <p class="text-xs text-gray-500">Driver's Name</p>
                    <p class="text-sm font-semibold">{{ $gatePass->driver_name }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Requested By</p>
                    <p class="text-sm font-semibold">{{ $gatePass->requestedBy->name }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Encoded By</p>
                    <p class="text-sm font-semibold">{{ $gatePass->encodedBy?->name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Vehicle Type</p>
                    <p class="text-sm font-semibold">{{ $gatePass->vehicle_type }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Plate Number</p>
                    <p class="text-sm font-semibold">{{ $gatePass->plate_number }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Destination</p>
                    <p class="text-sm font-semibold">{{ $gatePass->destination }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Division</p>
                    <p class="text-sm font-semibold">{{ $gatePass->division->name ?? '-' }}</p>
                </div>
                <div class="col-span-2">
                    <p class="text-xs text-gray-500">Passengers</p>
                    <p class="text-sm font-semibold">{{ $gatePass->passengers ?? '-' }}</p>
                </div>
                <div class="col-span-2">
                    <p class="text-xs text-gray-500">Purpose</p>
                    <p class="text-sm font-semibold">{{ $gatePass->purpose }}</p>
                </div>
            </div>

            {{-- Time Info --}}
            <div class="grid grid-cols-2 gap-4 mb-6 p-3 bg-blue-50 rounded-lg">
                <div>
                    <p class="text-xs text-gray-500">Time of Departure</p>
                    <p class="text-sm font-semibold">
                        {{ $gatePass->time_of_departure ? $gatePass->time_of_departure->format('M d, Y h:i A') : '-' }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Time of Arrival</p>
                    <p class="text-sm font-semibold">
                        {{ $gatePass->time_of_arrival ? $gatePass->time_of_arrival->format('M d, Y h:i A') : '-' }}
                    </p>
                </div>
            </div>

            {{-- Approval Info --}}
            @if($gatePass->approvedBy)
            <div class="border-t pt-4 mb-4">
                <p class="text-xs text-gray-500">Approved By</p>
                <p class="text-sm font-semibold">{{ $gatePass->approvedBy->name }}</p>
                <p class="text-xs text-gray-400">{{ $gatePass->approved_at?->format('M d, Y h:i A') }}</p>
            </div>
            @endif

            {{-- Action Buttons --}}
            <div class="border-t pt-4 flex flex-wrap gap-3">

                @if(auth()->user()->canApproveSlipInDivision($gatePass->division))
                    @if($gatePass->status === 'pending')
                        <form method="POST" action="{{ route('gate-passes.approve', $gatePass) }}">
                            @csrf
                            <button class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700">
                                ✓ Approve
                            </button>
                        </form>
                        <form method="POST" action="{{ route('gate-passes.reject', $gatePass) }}">
                            @csrf
                            <input type="hidden" name="remarks" value="Rejected by approver">
                            <button class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-red-700">
                                ✗ Reject
                            </button>
                        </form>
                    @endif
                @endif

                @role('super_admin|guard')
                    @if($gatePass->status === 'approved')
                        <form method="POST" action="{{ route('gate-passes.release', $gatePass) }}">
                            @csrf
                            <button class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">
                                → Log Departure
                            </button>
                        </form>
                    @endif
                    @if($gatePass->status === 'released')
                        <form method="POST" action="{{ route('gate-passes.returned', $gatePass) }}">
                            @csrf
                            <button class="bg-purple-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-purple-700">
                                ← Log Arrival
                            </button>
                        </form>
                    @endif
                @endrole
                <a href="{{ route('gate-passes.print', $gatePass) }}"
                target="_blank"
                class="bg-gray-700 text-white px-4 py-2 rounded-lg text-sm hover:bg-gray-800">
                    🖨 Print
                </a>

                <a href="{{ route('gate-passes.index') }}"
                   class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-300">
                    ← Back
                </a>

            </div>

        </div>
    </div>

</x-app-layout>
