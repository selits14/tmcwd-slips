<x-app-layout>
    <x-slot name="title">Gas Slip — {{ $gasSlip->slip_number }}</x-slot>

    <div class="max-w-2xl mx-auto space-y-4">

        {{-- Header Card --}}
        <div class="bg-white rounded-xl shadow p-6">
            <div class="flex items-center justify-between mb-1">
                <div>
                    <h3 class="text-lg font-bold text-gray-800">DRIVER'S RECORD OF TRAVEL</h3>
                    <p class="text-xs text-gray-400">TMCWD-AGSD-022</p>
                    <p class="font-mono text-sm text-yellow-600 mt-1">{{ $gasSlip->slip_number }}</p>
                </div>
                @php
                    $colors = [
                        'pending'  => 'bg-yellow-100 text-yellow-700',
                        'approved' => 'bg-green-100 text-green-700',
                        'released' => 'bg-blue-100 text-blue-700',
                        'rejected' => 'bg-red-100 text-red-700',
                    ];
                @endphp
                <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $colors[$gasSlip->status] ?? 'bg-gray-100' }}">
                    {{ ucfirst($gasSlip->status) }}
                </span>
            </div>
            <p class="text-xs text-gray-400">{{ $gasSlip->created_at->format('F d, Y') }}</p>
        </div>

        {{-- Section A --}}
        <div class="bg-white rounded-xl shadow p-6">
            <p class="text-xs font-bold text-blue-600 uppercase mb-4">
                Section A — Administrative (Before Trip)
            </p>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-xs text-gray-500">1. Driver / Requester</p>
                    <p class="text-sm font-semibold">{{ $gasSlip->driver->name }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Encoded by</p>
                    <p class="text-sm font-semibold">{{ $gasSlip->encodedBy?->name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">2. Vehicle (Plate No.)</p>
                    <p class="text-sm font-semibold">{{ $gasSlip->vehicle->plate_number }}</p>
                    <p class="text-xs text-gray-400">{{ $gasSlip->vehicle->description }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">3. Authorized Passenger/s</p>
                    <p class="text-sm font-semibold">{{ $gasSlip->passenger_name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Division</p>
                    <p class="text-sm font-semibold">{{ $gasSlip->division->name ?? '-' }}</p>
                </div>
                <div class="col-span-2">
                    <p class="text-xs text-gray-500">4. Place/s to be Visited</p>
                    <p class="text-sm font-semibold">{{ $gasSlip->places_to_visit }}</p>
                </div>
                <div class="col-span-2">
                    <p class="text-xs text-gray-500">5. Purpose</p>
                    <p class="text-sm font-semibold">{{ $gasSlip->purpose }}</p>
                </div>
            </div>

            @if($gasSlip->approvedBy)
            <div class="mt-4 pt-4 border-t">
                <p class="text-xs text-gray-500">Approved by</p>
                <p class="text-sm font-semibold">{{ $gasSlip->approvedBy->name }}</p>
                <p class="text-xs text-gray-400">{{ $gasSlip->approved_at?->format('M d, Y h:i A') }}</p>
            </div>
            @endif
            @if($gasSlip->rejectedBy)
            <div class="mt-4 pt-4 border-t">
                <p class="text-xs text-gray-500">Rejected by</p>
                <p class="text-sm font-semibold">{{ $gasSlip->rejectedBy->name }}</p>
                <p class="text-xs text-gray-400">{{ $gasSlip->rejected_at?->format('M d, Y h:i A') }}</p>
            </div>
            @endif
        </div>

        {{-- Section B --}}
        <div class="bg-white rounded-xl shadow p-6">
            <p class="text-xs font-bold text-green-600 uppercase mb-4">
                Section B — Driver's Record (After Trip)
            </p>

            @if($gasSlip->status === 'approved' && (auth()->user()->hasRole('super_admin') || $gasSlip->driver_user_id === auth()->id()))
                @role('super_admin|driver')
                {{-- Driver fills this form --}}
                <form method="POST" action="{{ route('gas-slips.complete', $gasSlip) }}">
                    @csrf
                    @if($errors->any())
                        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                            @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
                        </div>
                    @endif

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-xs text-gray-600 mb-1">1. Time Departure from Office</label>
                            <input type="datetime-local" name="time_departure_office"
                                   value="{{ old('time_departure_office') }}"
                                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-600 mb-1">2. Time Arrival at Destination</label>
                            <input type="datetime-local" name="time_arrival_destination"
                                   value="{{ old('time_arrival_destination') }}"
                                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-600 mb-1">3. Time Departure from Destination</label>
                            <input type="datetime-local" name="time_departure_destination"
                                   value="{{ old('time_departure_destination') }}"
                                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-600 mb-1">4. Time Arrival back to Office</label>
                            <input type="datetime-local" name="time_arrival_office"
                                   value="{{ old('time_arrival_office') }}"
                                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-600 mb-1">5. Distance Travelled (km)</label>
                            <input type="number" name="distance_travelled"
                                   value="{{ old('distance_travelled') }}"
                                   placeholder="0" step="0.01" min="0"
                                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                        </div>
                        {{-- Add after distance_travelled field --}}
<div>
    <label class="block text-xs text-gray-600 mb-1">
        Oil Used (Liters)
    </label>
    <input type="number" name="oil_used"
           value="{{ old('oil_used') }}"
           placeholder="0.00" step="0.01" min="0"
           class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
</div>
<div>
    <label class="block text-xs text-gray-600 mb-1">
        Odometer End (km)
    </label>
    <input type="number" name="odometer_end"
           value="{{ old('odometer_end') }}"
           placeholder="e.g. 12600"
           class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
</div>
                    </div>

                    {{-- Fuel Section B --}}
                    <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg mb-4">
                        <p class="text-sm font-semibold text-gray-700 mb-3">6. Gasoline</p>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">a. Balance in Tank (beginning)</span>
                                <span class="font-semibold">{{ $gasSlip->balance_in_tank_begin ?? 0 }} L</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">b. Issued from Office Stock</span>
                                <span class="font-semibold">{{ $gasSlip->issued_from_stock ?? 0 }} L</span>
                            </div>
                            <div class="border-t pt-2">
                                <label class="block text-xs text-gray-600 mb-1">c. Purchased during Trip (L)</label>
                                <input type="number" name="purchased_during_trip"
                                       value="{{ old('purchased_during_trip', 0) }}" step="0.01" min="0"
                                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500">
                            </div>
                            <div class="border-t pt-2">
                                <label class="block text-xs text-gray-600 mb-1">e. Fuel Used during Trip (L)</label>
                                <input type="number" name="fuel_used" required
                                       value="{{ old('fuel_used') }}"
                                       placeholder="0.00" step="0.01" min="0"
                                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500">
                            </div>
                        </div>
                    </div>

                    <button type="submit"
                            class="bg-green-600 text-white px-6 py-2 rounded-lg text-sm hover:bg-green-700">
                        ✓ Submit Trip Details
                    </button>
                </form>
                @else
                    <p class="text-sm text-gray-500">Trip details will be entered by the assigned driver.</p>
                @endrole

            @else
                {{-- Show completed Section B --}}
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <p class="text-xs text-gray-500">1. Departure from Office</p>
                        <p class="text-sm font-semibold">
                            {{ $gasSlip->time_departure_office?->format('M d, Y h:i A') ?? '-' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">2. Arrival at Destination</p>
                        <p class="text-sm font-semibold">
                            {{ $gasSlip->time_arrival_destination?->format('M d, Y h:i A') ?? '-' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">3. Departure from Destination</p>
                        <p class="text-sm font-semibold">
                            {{ $gasSlip->time_departure_destination?->format('M d, Y h:i A') ?? '-' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">4. Arrival back to Office</p>
                        <p class="text-sm font-semibold">
                            {{ $gasSlip->time_arrival_office?->format('M d, Y h:i A') ?? '-' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">5. Distance Travelled</p>
                        <p class="text-sm font-semibold">
                            {{ $gasSlip->distance_travelled ? number_format($gasSlip->distance_travelled) . ' km' : '-' }}
                        </p>
                    </div>
                </div>

                {{-- Fuel Summary --}}
                <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <p class="text-sm font-semibold text-gray-700 mb-3">6. Fuel Summary</p>
                    <div class="space-y-1 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">a. Balance in Tank (beginning)</span>
                            <span class="font-semibold">{{ $gasSlip->balance_in_tank_begin ?? 0 }} L</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">b. Issued from Office Stock</span>
                            <span class="font-semibold">{{ $gasSlip->issued_from_stock ?? 0 }} L</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">c. Purchased during Trip</span>
                            <span class="font-semibold">{{ $gasSlip->purchased_during_trip ?? 0 }} L</span>
                        </div>
                        <div class="flex justify-between border-t pt-1 font-bold">
                            <span>d. TOTAL</span>
                            <span>{{ $gasSlip->total_fuel }} L</span>
                        </div>
                        <div class="flex justify-between text-red-600">
                            <span>e. Used during Trip</span>
                            <span>{{ $gasSlip->fuel_used ?? 0 }} L</span>
                        </div>
                        <div class="flex justify-between border-t pt-1 font-bold text-green-700">
                            <span>f. Balance at End of Trip</span>
                            <span>{{ $gasSlip->balance_in_tank_end ?? 0 }} L</span>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Action Buttons --}}
        <div class="bg-white rounded-xl shadow p-4 flex flex-wrap gap-3">
            @if(auth()->user()->canApproveSlipInDivision($gasSlip->division))
                @if($gasSlip->status === 'pending')
                    <form method="POST" action="{{ route('gas-slips.approve', $gasSlip) }}">
                        @csrf
                        <button class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700">
                            ✓ Approve
                        </button>
                    </form>
                    <form method="POST" action="{{ route('gas-slips.reject', $gasSlip) }}">
                        @csrf
                        <input type="hidden" name="remarks" value="Rejected by approver">
                        <button class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-red-700">
                            ✗ Reject
                        </button>
                    </form>
                @endif
            @endif
            <a href="{{ route('gas-slips.print', $gasSlip) }}"
            target="_blank"
            class="bg-gray-700 text-white px-4 py-2 rounded-lg text-sm hover:bg-gray-800">
                🖨 Print
            </a>
            <a href="{{ route('gas-slips.index') }}"
               class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-300">
                ← Back
            </a>
        </div>

    </div>

</x-app-layout>
