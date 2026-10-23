<x-app-layout>
    <x-slot name="title">Monthly Report of Official Travel</x-slot>

    <div class="flex items-center justify-between mb-5">
        <div>
            <h3 class="text-xl font-bold text-gray-800">MROT</h3>
            <p class="text-xs text-gray-500 mt-0.5">Monthly Report of Official Travel</p>
        </div>
        <a href="{{ route('reports.index') }}"
           class="text-sm text-blue-600 hover:underline">← Back to Reports</a>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-4">
        <form method="GET" action="{{ route('reports.vehicle-mrot') }}">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 items-end">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Month</label>
                    <input type="month" name="month" value="{{ $month }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Vehicle</label>
                    <select name="vehicle_id"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Vehicles</option>
                        @foreach($vehicles as $v)
                            <option value="{{ $v->id }}" {{ request('vehicle_id') == $v->id ? 'selected' : '' }}>
                                {{ $v->plate_number }} — {{ $v->description }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-2 items-end">
                    <button type="submit"
                            class="bg-blue-600 text-white px-5 py-2 rounded-lg text-sm hover:bg-blue-700">
                        Filter
                    </button>
                    <a href="{{ route('reports.vehicle-mrot.print') }}?month={{ $month }}&vehicle_id={{ request('vehicle_id') }}"
                       target="_blank"
                       class="bg-gray-700 text-white px-4 py-2 rounded-lg text-sm hover:bg-gray-800">
                        🖨 Print PDF
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Per Vehicle MROT --}}
    @forelse($grouped as $vehicleId => $slips)
    @php
        $vehicle = $slips->first()->vehicle;
        $driver  = $slips->first()->driver;
    @endphp

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6 overflow-hidden">
        {{-- Vehicle Header --}}
        <div class="px-6 py-4 bg-blue-50 border-b flex justify-between items-center">
            <div>
                <p class="font-bold text-gray-800 text-sm">
                    {{ $vehicle->description }} — {{ $vehicle->plate_number }}
                </p>
                <p class="text-xs text-gray-500">Driver: {{ strtoupper($driver->name) }}</p>
            </div>
            <div class="text-right">
                <p class="text-xs text-gray-500">
                    {{ \Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y') }}
                </p>
            </div>
        </div>

        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 border-b text-gray-600 uppercase text-xs">
                <tr>
                    <th class="px-6 py-3">Date</th>
                    <th class="px-6 py-3">Distance (km)</th>
                    <th class="px-6 py-3">Fuel Consumed (L)</th>
                    <th class="px-6 py-3">Oil Used (L)</th>
                    <th class="px-6 py-3">Slip No.</th>
                    <th class="px-6 py-3">Destination / Remarks</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($slips as $slip)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-3 text-xs whitespace-nowrap">
                        {{ strtoupper($slip->created_at->format('F j, Y')) }}
                    </td>
                    <td class="px-6 py-3 font-semibold">
                        {{ $slip->distance_travelled ? number_format($slip->distance_travelled, 2) : '—' }}
                    </td>
                    <td class="px-6 py-3 font-semibold">
                        {{ $slip->fuel_used ? number_format($slip->fuel_used, 2) : '—' }}
                    </td>
                    <td class="px-6 py-3">
                        {{ $slip->oil_used ? number_format($slip->oil_used, 2) : '—' }}
                    </td>
                    <td class="px-6 py-3 font-mono text-xs text-yellow-600">
                        <a href="{{ route('gas-slips.show', $slip) }}" class="hover:underline">
                            {{ $slip->slip_number }}
                        </a>
                    </td>
                    <td class="px-6 py-3 text-gray-600 text-xs">
                        {{ $slip->places_to_visit }}
                    </td>
                </tr>
                @endforeach

                {{-- Totals --}}
                <tr class="bg-blue-50 font-bold border-t-2 border-blue-200">
                    <td class="px-6 py-3">TOTALS</td>
                    <td class="px-6 py-3">{{ number_format($slips->sum('distance_travelled'), 2) }} km</td>
                    <td class="px-6 py-3">{{ number_format($slips->sum('fuel_used'), 2) }} L</td>
                    <td class="px-6 py-3">{{ number_format($slips->sum('oil_used'), 2) }} L</td>
                    <td colspan="2"></td>
                </tr>
            </tbody>
        </table>
    </div>
    @empty
    <div class="bg-white rounded-xl shadow p-10 text-center text-gray-400">
        No travel records found for this period.
    </div>
    @endforelse

</x-app-layout>