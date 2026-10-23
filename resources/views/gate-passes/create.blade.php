<x-app-layout>
    <x-slot name="title">New Vehicle Pass</x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-xl shadow p-6">

            <h3 class="text-lg font-semibold text-gray-700 mb-1">VEHICLE PASS</h3>
            <p class="text-xs text-gray-400 mb-6">Fill in all required fields</p>

            <form method="POST" action="{{ route('gate-passes.store') }}">
                @csrf
                @if($errors->any())
                    <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                        @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
                    </div>
                @endif

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Requested by <span class="text-red-500">*</span>
                    </label>
                    <select name="requested_by_user_id" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="">-- Select requester --</option>
                        @foreach($requesters as $requester)
                            <option value="{{ $requester->id }}" {{ old('requested_by_user_id') == $requester->id ? 'selected' : '' }}>
                                {{ $requester->name }}{{ $requester->division ? ' — ' . $requester->division->name : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('requested_by_user_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- LR / PR --}}
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">LR No.</label>
                        <input type="text" name="lr_number" value="{{ old('lr_number') }}"
                               placeholder="LR Number"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">PR No.</label>
                        <input type="text" name="pr_number" value="{{ old('pr_number') }}"
                               placeholder="PR Number"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                </div>

                {{-- Driver --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Driver's Name <span class="text-red-500">*</span>
                    </label>
                    @if(!$vehicles->isEmpty())
                        <select name="driver_name" id="driverSelect"
                                onchange="fillVehicleInfo(this)"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                            <option value="">-- Select or type driver --</option>
                            @foreach($vehicles as $v)
                                <option value="{{ $v->description }}"
                                        data-type="{{ $v->type }}"
                                        data-plate="{{ $v->plate_number }}">
                                    {{ $v->plate_number }} — {{ $v->description }}
                                </option>
                            @endforeach
                        </select>
                    @endif
                    <input type="text" name="driver_name" value="{{ old('driver_name') }}"
                           id="driverName"
                           placeholder="Driver's full name"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm mt-1 focus:outline-none focus:ring-2 focus:ring-green-500">
                    @error('driver_name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Vehicle Type & Plate --}}
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Vehicle Type <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="vehicle_type" id="vehicleType"
                               value="{{ old('vehicle_type') }}"
                               placeholder="e.g. FB L300, Pickup"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                        @error('vehicle_type')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Plate Number <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="plate_number" id="plateNumber"
                               value="{{ old('plate_number') }}"
                               placeholder="e.g. SAA 1749"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                        @error('plate_number')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Destination --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Destination <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="destination" value="{{ old('destination') }}"
                           placeholder="Where is the vehicle going?"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                    @error('destination')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Passengers --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Passengers</label>
                    <input type="text" name="passengers" value="{{ old('passengers') }}"
                           placeholder="e.g. VILLAR / DUNGAO / DELA CRUZ"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>

                {{-- Purpose --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Purpose <span class="text-red-500">*</span>
                    </label>
                    <textarea name="purpose" rows="2"
                              placeholder="e.g. LEAK REPAIR / CHANGE WM / INSPECTION"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">{{ old('purpose') }}</textarea>
                    @error('purpose')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Time of Departure & Arrival --}}
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Time of Departure <span class="text-red-500">*</span>
                        </label>
                        <input type="datetime-local" name="time_of_departure"
                               value="{{ old('time_of_departure') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                        @error('time_of_departure')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Expected Time of Arrival
                        </label>
                        <input type="datetime-local" name="time_of_arrival"
                               value="{{ old('time_of_arrival') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                </div>

                {{-- Buttons --}}
                <div class="flex gap-3">
                    <button type="submit"
                            class="bg-green-600 text-white px-6 py-2 rounded-lg text-sm hover:bg-green-700">
                        Submit Vehicle Pass
                    </button>
                    <a href="{{ route('gate-passes.index') }}"
                       class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg text-sm hover:bg-gray-300">
                        Cancel
                    </a>
                </div>

            </form>
        </div>
    </div>

    <script>
        function fillVehicleInfo(select) {
            const option = select.options[select.selectedIndex];
            if (option.value) {
                document.getElementById('vehicleType').value = option.dataset.type || '';
                document.getElementById('plateNumber').value = option.dataset.plate || '';
            }
        }
    </script>

</x-app-layout>
