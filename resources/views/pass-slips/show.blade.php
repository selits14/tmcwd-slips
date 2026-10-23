<x-app-layout>
    <x-slot name="title">Pass Slip — {{ $passSlip->slip_number }}</x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-xl shadow p-6">

            {{-- Header --}}
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-bold text-gray-800">{{ $passSlip->slip_number }}</h3>
                    <p class="text-sm text-gray-500">{{ $passSlip->created_at->format('F d, Y h:i A') }}</p>
                </div>
                @php
                    $colors = [
                        'draft'    => 'bg-gray-100 text-gray-600',
                        'pending'  => 'bg-yellow-100 text-yellow-700',
                        'approved' => 'bg-green-100 text-green-700',
                        'out'      => 'bg-blue-100 text-blue-700',
                        'returned' => 'bg-purple-100 text-purple-700',
                        'rejected' => 'bg-red-100 text-red-700',
                    ];
                @endphp
                <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $colors[$passSlip->status] ?? '' }}">
                    {{ ucfirst($passSlip->status) }}
                </span>
            </div>

            {{-- Details --}}
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <p class="text-xs text-gray-500">Requested by</p>
                    <p class="text-sm font-semibold">{{ $passSlip->employee->name }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Encoded by</p>
                    <p class="text-sm font-semibold">{{ $passSlip->encodedBy?->name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Division</p>
                    <p class="text-sm font-semibold">{{ $passSlip->division->name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Pass Type</p>
                    <p class="text-sm font-semibold">
                        {{ $passSlip->pass_type === 'official_business' ? 'Official Business' : 'Personal' }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Destination</p>
                    <p class="text-sm font-semibold">{{ $passSlip->destination }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Purpose</p>
                    <p class="text-sm font-semibold">{{ $passSlip->purpose }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Remarks</p>
                    <p class="text-sm font-semibold">{{ $passSlip->remarks ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Time Out</p>
                    <p class="text-sm font-semibold">
                        {{ $passSlip->time_out ? \Carbon\Carbon::parse($passSlip->time_out)->format('M d, Y h:i A') : '-' }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Expected Return</p>
                    <p class="text-sm font-semibold">
                        {{ $passSlip->expected_return ? \Carbon\Carbon::parse($passSlip->expected_return)->format('M d, Y h:i A') : '-' }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Actual Return</p>
                    <p class="text-sm font-semibold">
                        {{ $passSlip->actual_return ? \Carbon\Carbon::parse($passSlip->actual_return)->format('M d, Y h:i A') : '-' }}
                    </p>
                </div>
            </div>

            {{-- Approval Info --}}
            @if($passSlip->approvedBy)
            <div class="border-t pt-4 mb-4">
                <p class="text-xs text-gray-500">Approved By</p>
                <p class="text-sm font-semibold">{{ $passSlip->approvedBy->name }}</p>
                <p class="text-xs text-gray-400">{{ $passSlip->approved_at?->format('M d, Y h:i A') }}</p>
            </div>
            @endif

            {{-- Action Buttons --}}
            <div class="border-t pt-4 flex flex-wrap gap-3">

                {{-- Approver Actions --}}
                @if(auth()->user()->canApproveSlipInDivision($passSlip->division))
                    @if($passSlip->status === 'pending')
                        <form method="POST" action="{{ route('pass-slips.approve', $passSlip) }}">
                            @csrf
                            <button class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700">
                                ✓ Approve
                            </button>
                        </form>
                        <form method="POST" action="{{ route('pass-slips.reject', $passSlip) }}">
                            @csrf
                            <input type="hidden" name="remarks" value="Rejected by approver">
                            <button class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-red-700">
                                ✗ Reject
                            </button>
                        </form>
                    @endif
                @endif

                {{-- Guard Actions --}}
                @role('super_admin|guard')
                    @if($passSlip->status === 'approved')
                        <form method="POST" action="{{ route('pass-slips.time-out', $passSlip) }}">
                            @csrf
                            <button class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">
                                → Log Time Out
                            </button>
                        </form>
                    @endif
                    @if($passSlip->status === 'out')
                        <form method="POST" action="{{ route('pass-slips.time-in', $passSlip) }}">
                            @csrf
                            <button class="bg-purple-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-purple-700">
                                ← Log Time In
                            </button>
                        </form>
                    @endif
                @endrole
                <a href="{{ route('pass-slips.print', $passSlip) }}"
                target="_blank"
                class="bg-gray-700 text-white px-4 py-2 rounded-lg text-sm hover:bg-gray-800">
                    🖨 Print
                </a>
                <a href="{{ route('pass-slips.index') }}"
                   class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-300">
                    ← Back
                </a>

            </div>

        </div>
    </div>

</x-app-layout>
