<x-app-layout>
    <x-slot name="title">New Gas Slip</x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-xl shadow p-6">

            <h3 class="text-lg font-bold text-gray-800 mb-1">DRIVER'S RECORD OF TRAVEL</h3>
            <p class="text-xs text-gray-400 mb-1">TMCWD-AGSD-022</p>
            <p class="text-xs text-blue-600 mb-6">Section A — To be filled by Admin before the trip</p>

            @if($vehicles->isEmpty())
                <div class="bg-yellow-50 border border-yellow-300 text-yellow-700 px-4 py-3 rounded mb-4 text-sm">
                    ⚠ No vehicles found. Ask admin to add vehicles first.
                </div>
            @endif

            <form method="POST" action="{{ route('gas-slips.store') }}">
                @csrf
                @if($errors->any())
                    <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                        @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
                    </div>
                @endif

                {{-- Vehicle --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        2. Government Car to be Used (Plate No.) <span class="text-red-500">*</span>
                    </label>
                    <select name="vehicle_id" id="vehicleSelect" onchange="fillVehicle(this)"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <option value="">-- Select Vehicle --</option>
                        @foreach($vehicles as $vehicle)
                            <option value="{{ $vehicle->id }}"
                                    data-plate="{{ $vehicle->plate_number }}"
                                    data-type="{{ $vehicle->type }}"
                                    {{ old('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                                {{ $vehicle->plate_number }} — {{ $vehicle->description }}
                            </option>
                        @endforeach
                    </select>
                    @error('vehicle_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Fuel Type --}}
<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700 mb-1">
        Fuel Type <span class="text-red-500">*</span>
    </label>
    <select name="fuel_type"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500">
        <option value="Diesel" {{ old('fuel_type') === 'Diesel' ? 'selected' : '' }}>Diesel</option>
        <option value="Gasoline" {{ old('fuel_type') === 'Gasoline' ? 'selected' : '' }}>Gasoline</option>
    </select>
</div>

{{-- Gas Station --}}
<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700 mb-1">Gas Station</label>
    <select name="gas_station"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500">
        <option value="Petron Inocencio / Del Mundo's Gas Station">
            Petron Inocencio / Del Mundo's Gas Station
        </option>
        <option value="Other">Other</option>
    </select>
</div>

{{-- Last Refuel & Requested Liters --}}
<div class="grid grid-cols-2 gap-4 mb-4">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Last Refuel Date</label>
        <input type="date" name="last_refuel_date"
               value="{{ old('last_refuel_date') }}"
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Requested Liters / Full Tank
        </label>
        <input type="number" name="requested_liters"
               value="{{ old('requested_liters') }}"
               placeholder="Enter liters"
               min="0" step="0.01"
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500">
        @error('requested_liters')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>
</div>
                {{-- Driver --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        1. Driver requesting the gas slip <span class="text-red-500">*</span>
                    </label>
                    <select name="driver_user_id"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <option value="">-- Select Driver --</option>
                        @foreach($drivers as $driver)
                        <option value="{{ $driver->id }}"
                            {{ old('driver_user_id') == $driver->id ? 'selected' : '' }}>
                            {{ $driver->name }}
                            {{ $driver->position ? '— ' . $driver->position : '' }}
                        </option>
                    @endforeach
                    </select>
                    @error('driver_user_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Passenger --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        3. Name of Authorized Passenger/s
                    </label>
                    <input type="text" name="passenger_name" value="{{ old('passenger_name') }}"
                           placeholder="e.g. PAULO ESTRELLA / JUAN DELA CRUZ"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500">
                </div>

                {{-- Places to Visit --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        4. Place/s to be Visited/Inspected <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="places_to_visit" value="{{ old('places_to_visit') }}"
                           placeholder="e.g. WITHIN TRECE / INDANG / GENERAL TRIAS"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500">
                    @error('places_to_visit')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Purpose --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        5. Purpose <span class="text-red-500">*</span>
                    </label>

                    {{-- Common purpose checkboxes --}}
                    <div class="flex flex-wrap gap-2 mb-2">
                        @foreach([
                            'Monitoring of pumping station',
                            'Pumping Station Operation and Recording',
                            'Maintenance/Inspection of gate valve',
                            'Chlorine mixing/checking of chlorine level',
                            'Leak repair/Change WM',
                            'Inspection/Tapping',
                        ] as $p)
                        <label class="flex items-center gap-1 text-xs bg-gray-100 px-2 py-1 rounded cursor-pointer hover:bg-yellow-50">
                            <input type="checkbox" class="purposeCheck" value="{{ $p }}">
                            {{ $p }}
                        </label>
                        @endforeach
                    </div>

                    <textarea name="purpose" id="purposeText" rows="2"
                              placeholder="Or type purpose manually..."
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500">{{ old('purpose') }}</textarea>
                    @error('purpose')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Fuel Section --}}
                <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <p class="text-sm font-semibold text-gray-700 mb-3">
                        6. Gasoline — Beginning Balance
                    </p>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs text-gray-600 mb-1">
                                a. Balance in Tank (liters)
                            </label>
                            <input type="number" name="balance_in_tank_begin"
                                   value="{{ old('balance_in_tank_begin') }}"
                                   placeholder="0.00" step="0.01" min="0"
                                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-600 mb-1">
                                b. Issued from Office Stock (liters)
                            </label>
                            <input type="number" name="issued_from_stock"
                                   value="{{ old('issued_from_stock', 0) }}"
                                   placeholder="0.00" step="0.01" min="0"
                                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        </div>
                    </div>
                    <p class="text-xs text-gray-400 mt-2">
                        * Section B (trip details + fuel used) will be filled by the driver after the trip.
                    </p>
                </div>

                {{-- Remarks --}}
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Remarks</label>
                    <textarea name="remarks" rows="2"
                              placeholder="Optional remarks..."
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500">{{ old('remarks') }}</textarea>
                </div>

                <div class="flex gap-3">
                    <button type="submit"
                            class="bg-yellow-600 text-white px-6 py-2 rounded-lg text-sm hover:bg-yellow-700">
                        Submit for Approval
                    </button>
                    <a href="{{ route('gas-slips.index') }}"
                       class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg text-sm hover:bg-gray-300">
                        Cancel
                    </a>
                </div>

            </form>
        </div>
    </div>

    <script>
        // Checkboxes auto-fill the purpose textarea
        document.querySelectorAll('.purposeCheck').forEach(cb => {
            cb.addEventListener('change', function () {
                const checked = [...document.querySelectorAll('.purposeCheck:checked')]
                    .map(c => c.value).join(' / ');
                document.getElementById('purposeText').value = checked;
            });
        });
    </script>

</x-app-layout>
