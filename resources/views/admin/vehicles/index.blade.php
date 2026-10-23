<x-app-layout>
    <x-slot name="title">Vehicles</x-slot>

    <div class="flex items-center justify-between mb-6">
        <h3 class="text-lg font-semibold text-gray-700">Vehicles</h3>
        <a href="{{ route('admin.vehicles.create') }}"
           class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">
            + Add Vehicle
        </a>
    </div>

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                <tr>
                    <th class="px-6 py-3">Plate Number</th>
                    <th class="px-6 py-3">Description</th>
                    <th class="px-6 py-3">Type</th>
                    <th class="px-6 py-3">Division</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($vehicles as $vehicle)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 font-bold">{{ $vehicle->plate_number }}</td>
                    <td class="px-6 py-4">{{ $vehicle->description }}</td>
                    <td class="px-6 py-4">{{ $vehicle->type }}</td>
                    <td class="px-6 py-4">{{ $vehicle->division->name ?? '-' }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded-full text-xs
                            {{ $vehicle->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $vehicle->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 flex gap-2">
                        <a href="{{ route('admin.vehicles.edit', $vehicle) }}"
                           class="text-blue-600 hover:underline text-xs">Edit</a>
                        <form method="POST" action="{{ route('admin.vehicles.destroy', $vehicle) }}">
                            @csrf @method('DELETE')
                            <button class="text-red-500 hover:underline text-xs">
                                Deactivate
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-10 text-center text-gray-400">
                        No vehicles found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4 border-t">
            {{ $vehicles->links() }}
        </div>
    </div>

</x-app-layout>