<x-app-layout>
    <x-slot name="title">Report of Fuel Consumption</x-slot>

    <div class="flex items-center justify-between mb-5">
        <div>
            <h3 class="text-xl font-bold text-gray-800">RFC</h3>
            <p class="text-xs text-gray-500 mt-0.5">Report of Fuel Consumption</p>
        </div>
        <a href="{{ route('reports.index') }}"
           class="text-sm text-blue-600 hover:underline">← Back to Reports</a>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-4">
        <form method="GET" action="{{ route('reports.vehicle-rfc') }}">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 items-end">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Month</label>
                    <input type="month" name="month" value="{{ $month }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>
                <div class="flex gap-2 items-end">
                    <button type="submit"
                            class="bg-red-600 text-white px-5 py-2 rounded-lg text-sm hover:bg-red-700">
                        Filter
                    </button>
                    <a href="{{ route('reports.vehicle-rfc.print') }}?month={{ $month }}"
                       target="_blank"
                       class="bg-gray-700 text-white px-4 py-2 rounded-lg text-sm hover:bg-gray-800">
                        🖨 Print PDF
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- RFC Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b bg-red-50">
            <p class="font-bold text-gray-800 text-sm uppercase">
                Report of Fuel Consumption —
                {{ \Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y') }}
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 border-b text-gray-600 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3">Type of Vehicle</th>
                        <th class="px-4 py-3">Plate No.</th>
                        <th class="px-4 py-3">Driver</th>
                        <th class="px-4 py-3">Cylinders</th>
                        <th class="px-4 py-3">Odo. Begin</th>
                        <th class="px-4 py-3">Odo. End</th>
                        <th class="px-4 py-3">Distance (A)</th>
                        <th class="px-4 py-3">Fuel Used (B)</th>
                        <th class="px-4 py-3">km/L (C=A/B)</th>
                        <th class="px-4 py-3">Normal km/L (D)</th>
                        <th class="px-4 py-3">Allowed (E=[A/D]×1.1)</th>
                        <th class="px-4 py-3">Excess (B-E)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($summary as $data)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">{{ $data['vehicle']->description }}</td>
                        <td class="px-4 py-3 font-bold">{{ $data['vehicle']->plate_number }}</td>
                        <td class="px-4 py-3">{{ $data['driver']->name }}</td>
                        <td class="px-4 py-3 text-center">{{ $data['vehicle']->num_cylinders ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $data['slips']->first()->balance_in_tank_begin ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $data['slips']->last()->odometer_end ?? '—' }}</td>
                        <td class="px-4 py-3 font-semibold">{{ number_format($data['total_distance'], 2) }}</td>
                        <td class="px-4 py-3 font-semibold">{{ number_format($data['total_fuel'], 2) }}</td>
                        <td class="px-4 py-3">{{ number_format($data['km_per_liter'], 2) }}</td>
                        <td class="px-4 py-3">{{ number_format($data['normal_km'], 2) }}</td>
                        <td class="px-4 py-3">{{ number_format($data['allowed_liters'], 2) }}</td>
                        <td class="px-4 py-3">
                            <span class="{{ $data['excess'] > 0 ? 'text-red-600 font-bold' : 'text-green-600' }}">
                                {{ number_format($data['excess'], 2) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="12" class="px-6 py-10 text-center text-gray-400">
                            No fuel consumption records found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Signatures --}}
        @if($summary->count() > 0)
        <div class="px-6 py-6 border-t">
            <div class="grid grid-cols-2 gap-10 mt-4">
                <div class="text-center">
                    <div class="border-t border-gray-400 pt-2 mt-10">
                        <p class="font-bold text-sm">JONA GRACE N. LOYOLA</p>
                        <p class="text-xs text-gray-500">Admin Services Aide</p>
                        <p class="text-xs text-gray-400">Prepared by:</p>
                    </div>
                </div>
                <div class="text-center">
                    <div class="border-t border-gray-400 pt-2 mt-10">
                        <p class="font-bold text-sm">MR. LORENZO R. SISANTE</p>
                        <p class="text-xs text-gray-500">DIVISION MANAGER - CMD</p>
                        <p class="text-xs text-gray-400">Verified and Found Correct:</p>
                    </div>
                </div>
            </div>
        </div>
        @endif

    </div>

</x-app-layout>