<x-app-layout>
    <x-slot name="title">Monthly Fuel Report</x-slot>

    <div class="flex items-center justify-between mb-6">
        <h3 class="text-lg font-semibold text-gray-700">
            Report of Fuel Consumption (RFC)
        </h3>
        <a href="{{ route('reports.index') }}"
           class="text-sm text-blue-600 hover:underline">← Back to Reports</a>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow p-4 mb-6">
        <form method="GET" action="{{ route('reports.fuel-consumption') }}"
              class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-xs text-gray-600 mb-1">Month</label>
                <input type="month" name="month" value="{{ $month }}"
                       class="border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500">
            </div>
            <div>
                <label class="block text-xs text-gray-600 mb-1">Vehicle</label>
                <select name="vehicle_id"
                        class="border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500">
                    <option value="">All Vehicles</option>
                    @foreach($vehicles as $v)
                        <option value="{{ $v->id }}"
                            {{ request('vehicle_id') == $v->id ? 'selected' : '' }}>
                            {{ $v->plate_number }} — {{ $v->description }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit"
                        class="bg-yellow-600 text-white px-4 py-2 rounded text-sm hover:bg-yellow-700">
                    Filter
                </button>
                <a href="{{ route('reports.fuel-consumption') }}"
                   class="bg-gray-200 text-gray-700 px-4 py-2 rounded text-sm hover:bg-gray-300">
                    Reset
                </a>
                <a href="{{ route('reports.fuel-consumption.print') }}?month={{ $month }}&vehicle_id={{ request('vehicle_id') }}"
                   target="_blank"
                   class="bg-gray-700 text-white px-4 py-2 rounded text-sm hover:bg-gray-800">
                    🖨 Print PDF
                </a>
            </div>
        </form>
    </div>

    {{-- Summary Cards --}}
    @forelse($summary as $vehicleId => $data)
    <div class="bg-white rounded-xl shadow mb-6">

        {{-- Vehicle Header --}}
        <div class="px-6 py-4 border-b bg-yellow-50 flex justify-between items-center">
            <div>
                <p class="font-bold text-gray-800">
                    {{ $data['vehicle']->plate_number }}
                    — {{ $data['vehicle']->description }}
                </p>
                <p class="text-xs text-gray-500">Driver: {{ $data['driver']->name }}</p>
            </div>
            <div class="text-right">
                <p class="text-sm font-semibold text-yellow-700">
                    Total: {{ number_format($data['total_fuel_used'], 2) }} L
                </p>
                <p class="text-xs text-gray-500">
                    {{ number_format($data['total_distance'], 2) }} km
                    | {{ $data['total_trips'] }} trip(s)
                </p>
            </div>
        </div>

        {{-- Trip Details --}}
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                <tr>
                    <th class="px-6 py-3">Slip No.</th>
                    <th class="px-6 py-3">Date</th>
                    <th class="px-6 py-3">Destination</th>
                    <th class="px-6 py-3">Purpose</th>
                    <th class="px-6 py-3">Distance (km)</th>
                    <th class="px-6 py-3">Fuel Used (L)</th>
                    <th class="px-6 py-3">Balance End (L)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($data['slips'] as $slip)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-3 font-mono text-xs text-yellow-600">
                        <a href="{{ route('gas-slips.show', $slip) }}" class="hover:underline">
                            {{ $slip->slip_number }}
                        </a>
                    </td>
                    <td class="px-6 py-3">{{ $slip->created_at->format('M d, Y') }}</td>
                    <td class="px-6 py-3">{{ $slip->places_to_visit }}</td>
                    <td class="px-6 py-3">{{ $slip->purpose }}</td>
                    <td class="px-6 py-3">
                        {{ $slip->distance_travelled ? number_format($slip->distance_travelled, 2) : '—' }}
                    </td>
                    <td class="px-6 py-3 font-semibold">
                        {{ number_format($slip->fuel_used ?? 0, 2) }}
                    </td>
                    <td class="px-6 py-3">
                        {{ number_format($slip->balance_in_tank_end ?? 0, 2) }}
                    </td>
                </tr>
                @endforeach
                {{-- Totals Row --}}
                <tr class="bg-yellow-50 font-bold">
                    <td colspan="4" class="px-6 py-3 text-right">TOTAL:</td>
                    <td class="px-6 py-3">
                        {{ number_format($data['total_distance'], 2) }} km
                    </td>
                    <td class="px-6 py-3">
                        {{ number_format($data['total_fuel_used'], 2) }} L
                    </td>
                    <td class="px-6 py-3">—</td>
                </tr>
            </tbody>
        </table>
    </div>
    @empty
    <div class="bg-white rounded-xl shadow p-10 text-center text-gray-400">
        No fuel records found for this period.
    </div>
    @endforelse

</x-app-layout>