<x-app-layout>
    <x-slot name="title">Edit Vehicle</x-slot>

    <div class="max-w-xl mx-auto">
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-6">
                Edit Vehicle — {{ $vehicle->plate_number }}
            </h3>

            <form method="POST" action="{{ route('admin.vehicles.update', $vehicle) }}">
                @csrf @method('PUT')

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Plate Number</label>
                    <input type="text" name="plate_number"
                           value="{{ old('plate_number', $vehicle->plate_number) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm uppercase focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('plate_number')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <input type="text" name="description"
                           value="{{ old('description', $vehicle->description) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Vehicle Type</label>
                    <select name="type"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @foreach(['FB L300','Pickup','Motorcycle','Van','Sedan','Truck','Other'] as $t)
                            <option value="{{ $t }}" {{ old('type', $vehicle->type) === $t ? 'selected' : '' }}>
                                {{ $t }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Assigned Division</label>
                    <select name="assigned_division_id"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- No specific division --</option>
                        @foreach($divisions as $division)
                            <option value="{{ $division->id }}"
                                {{ old('assigned_division_id', $vehicle->assigned_division_id) == $division->id ? 'selected' : '' }}>
                                {{ $division->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Monthly Fuel Allocation (liters)</label>
                    <input type="number" name="monthly_fuel_allocation" value="{{ old('monthly_fuel_allocation', $vehicle->monthly_fuel_allocation) }}" min="0" max="999999.99" step="0.01" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('monthly_fuel_allocation')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="mb-6">
                    <label class="flex items-center gap-2 text-sm font-medium text-gray-700">
                        <input type="checkbox" name="is_active" value="1"
                               {{ old('is_active', $vehicle->is_active) ? 'checked' : '' }}>
                        Active
                    </label>
                </div>
<div class="grid grid-cols-2 gap-4 mb-4">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Number of Cylinders</label>
        <input type="number" name="num_cylinders"
               value="{{ old('num_cylinders', $vehicle->num_cylinders) }}"
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Normal km/Liter</label>
        <input type="number" name="normal_km_per_liter"
               value="{{ old('normal_km_per_liter', $vehicle->normal_km_per_liter) }}"
               step="0.01"
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>
</div>
                <div class="flex gap-3">
                    <button type="submit"
                            class="bg-blue-600 text-white px-6 py-2 rounded-lg text-sm hover:bg-blue-700">
                        Save Changes
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
