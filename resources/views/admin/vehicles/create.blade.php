<x-app-layout>
    <x-slot name="title">Add Vehicle</x-slot>

    <div class="max-w-xl mx-auto">
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-6">Add Vehicle</h3>

            <form method="POST" action="{{ route('admin.vehicles.store') }}">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Plate Number <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="plate_number"
                           value="{{ old('plate_number') }}"
                           placeholder="e.g. SAA 1750"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm uppercase focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('plate_number')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Description <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="description"
                           value="{{ old('description') }}"
                           placeholder="e.g. FB L300 / Service Vehicle"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('description')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Vehicle Type <span class="text-red-500">*</span>
                    </label>
                    <select name="type"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Select Type --</option>
                        <option value="FB L300" {{ old('type') === 'FB L300' ? 'selected' : '' }}>FB L300</option>
                        <option value="Pickup" {{ old('type') === 'Pickup' ? 'selected' : '' }}>Pickup</option>
                        <option value="Motorcycle" {{ old('type') === 'Motorcycle' ? 'selected' : '' }}>Motorcycle</option>
                        <option value="Van" {{ old('type') === 'Van' ? 'selected' : '' }}>Van</option>
                        <option value="Sedan" {{ old('type') === 'Sedan' ? 'selected' : '' }}>Sedan</option>
                        <option value="Truck" {{ old('type') === 'Truck' ? 'selected' : '' }}>Truck</option>
                        <option value="Other" {{ old('type') === 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('type')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Assigned Division
                    </label>
                    <select name="assigned_division_id"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- No specific division --</option>
                        @foreach($divisions as $division)
                            <option value="{{ $division->id }}"
                                {{ old('assigned_division_id') == $division->id ? 'selected' : '' }}>
                                {{ $division->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Monthly Fuel Allocation (liters)</label>
                    <input type="number" name="monthly_fuel_allocation" value="{{ old('monthly_fuel_allocation') }}" min="0" max="999999.99" step="0.01" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('monthly_fuel_allocation')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Cylinders & Normal km/L --}}
<div class="grid grid-cols-2 gap-4 mb-4">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Number of Cylinders
        </label>
        <input type="number" name="num_cylinders"
               value="{{ old('num_cylinders') }}"
               placeholder="e.g. 4"
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Normal km/Liter
        </label>
        <input type="number" name="normal_km_per_liter"
               value="{{ old('normal_km_per_liter') }}"
               placeholder="e.g. 10"
               step="0.01"
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>
</div>

                <div class="flex gap-3">
                    <button type="submit"
                            class="bg-blue-600 text-white px-6 py-2 rounded-lg text-sm hover:bg-blue-700">
                        Add Vehicle
                    </button>
                    <a href="{{ route('admin.vehicles.index') }}"
                       class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg text-sm hover:bg-gray-300">
                        Cancel
                    </a>
                </div>

            </form>
        </div>
    </div>

</x-app-layout>
