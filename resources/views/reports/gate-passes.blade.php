<x-app-layout>
    <x-slot name="title">Vehicle Pass Report</x-slot>

    <div class="flex items-center justify-between mb-6">
        <h3 class="text-lg font-semibold text-gray-700">Vehicle Pass Log</h3>
        <a href="{{ route('reports.index') }}"
           class="text-sm text-blue-600 hover:underline">← Back to Reports</a>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow p-4 mb-6">
        <form method="GET" action="{{ route('reports.gate-passes') }}"
              class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-xs text-gray-600 mb-1">Date From</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                       class="border border-gray-300 rounded px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs text-gray-600 mb-1">Date To</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                       class="border border-gray-300 rounded px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs text-gray-600 mb-1">Plate Number</label>
                <input type="text" name="plate_number"
                       value="{{ request('plate_number') }}"
                       placeholder="e.g. SAA 1750"
                       class="border border-gray-300 rounded px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs text-gray-600 mb-1">Status</label>
                <select name="status"
                        class="border border-gray-300 rounded px-3 py-2 text-sm">
                    <option value="">All Status</option>
                    @foreach(['pending','approved','released','returned','rejected'] as $s)
                        <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>
                            {{ ucfirst($s) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit"
                        class="bg-green-600 text-white px-4 py-2 rounded text-sm hover:bg-green-700">
                    Filter
                </button>
                <a href="{{ route('reports.gate-passes') }}"
                   class="bg-gray-200 text-gray-700 px-4 py-2 rounded text-sm hover:bg-gray-300">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Results --}}
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <div class="px-6 py-3 bg-gray-50 border-b">
            <span class="text-sm text-gray-600">
                {{ $gatePasses->total() }} record(s) found
            </span>
        </div>
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                <tr>
                    <th class="px-6 py-3">Pass No.</th>
                    <th class="px-6 py-3">Date</th>
                    <th class="px-6 py-3">Driver</th>
                    <th class="px-6 py-3">Plate No.</th>
                    <th class="px-6 py-3">Vehicle</th>
                    <th class="px-6 py-3">Destination</th>
                    <th class="px-6 py-3">Departure</th>
                    <th class="px-6 py-3">Arrival</th>
                    <th class="px-6 py-3">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($gatePasses as $pass)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-3 font-mono text-xs text-green-600">
                        <a href="{{ route('gate-passes.show', $pass) }}" class="hover:underline">
                            {{ $pass->pass_number }}
                        </a>
                    </td>
                    <td class="px-6 py-3">{{ $pass->created_at->format('M d, Y') }}</td>
                    <td class="px-6 py-3">{{ $pass->driver_name }}</td>
                    <td class="px-6 py-3 font-semibold">{{ $pass->plate_number }}</td>
                    <td class="px-6 py-3">{{ $pass->vehicle_type }}</td>
                    <td class="px-6 py-3">{{ $pass->destination }}</td>
                    <td class="px-6 py-3">
                        {{ $pass->time_of_departure ? $pass->time_of_departure->format('h:i A') : '—' }}
                    </td>
                    <td class="px-6 py-3">
                        {{ $pass->time_of_arrival ? $pass->time_of_arrival->format('h:i A') : '—' }}
                    </td>
                    <td class="px-6 py-3">
                        @php
                            $colors = [
                                'pending'  => 'bg-yellow-100 text-yellow-700',
                                'approved' => 'bg-green-100 text-green-700',
                                'released' => 'bg-blue-100 text-blue-700',
                                'returned' => 'bg-purple-100 text-purple-700',
                                'rejected' => 'bg-red-100 text-red-700',
                            ];
                        @endphp
                        <span class="px-2 py-1 rounded-full text-xs {{ $colors[$pass->status] ?? 'bg-gray-100' }}">
                            {{ ucfirst($pass->status) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-6 py-10 text-center text-gray-400">
                        No records found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4 border-t">
            {{ $gatePasses->links() }}
        </div>
    </div>

</x-app-layout>