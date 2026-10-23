<x-app-layout>
    <x-slot name="title">Gas Slips</x-slot>

    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-5">
        <div>
            <h3 class="text-xl font-bold text-gray-800">Gas Slips</h3>
            <p class="text-xs text-gray-500 mt-0.5">Driver's Record of Travel — TMCWD-AGSD-022</p>
        </div>
        <div class="flex gap-2">
            <button type="submit" form="batchForm"
                    class="flex items-center gap-2 bg-gray-700 text-white px-4 py-2 rounded-lg text-sm hover:bg-gray-800">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Print Selected
            </button>
            @role('super_admin|encoder')
            <a href="{{ route('gas-slips.create') }}"
               class="flex items-center gap-2 bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-yellow-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                New Gas Slip
            </a>
            @endrole
        </div>
    </div>

    {{-- Search & Filter --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-4">
        <form method="GET" action="{{ route('gas-slips.index') }}">
            <div class="grid grid-cols-2 md:grid-cols-6 gap-3 items-end">

                <div class="col-span-2">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Slip no., driver, plate, destination..."
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Vehicle</label>
                    <select name="vehicle_id"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <option value="">All Vehicles</option>
                        @foreach($vehicles as $v)
                            <option value="{{ $v->id }}" {{ request('vehicle_id') == $v->id ? 'selected' : '' }}>
                                {{ $v->plate_number }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
                    <select name="status"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <option value="">All Status</option>
                        @foreach(['pending','approved','released','rejected'] as $s)
                            <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>
                                {{ ucfirst($s) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Date From</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Date To</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500">
                </div>

            </div>

            <div class="flex gap-2 mt-3">
                <button type="submit"
                        class="bg-yellow-600 text-white px-5 py-2 rounded-lg text-sm hover:bg-yellow-700">
                    Filter
                </button>
                <a href="{{ route('gas-slips.index') }}"
                   class="bg-gray-100 text-gray-600 px-5 py-2 rounded-lg text-sm hover:bg-gray-200">
                    Reset
                </a>
                @if(request()->hasAny(['search','status','vehicle_id','date_from','date_to']))
                    <span class="text-xs text-gray-500 self-center ml-2">
                        {{ $gasSlips->total() }} result(s) found
                    </span>
                @endif
            </div>
        </form>
    </div>

    {{-- Table --}}
    <form method="POST" action="{{ route('gas-slips.batch-print') }}" id="batchForm">
        @csrf
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

            <div class="px-6 py-3 bg-gray-50 border-b flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <input type="checkbox" onclick="toggleAll(this)"
                           class="rounded border-gray-300 text-yellow-600">
                    <span class="text-xs text-gray-500">Select all on this page</span>
                </div>
                <span class="text-xs text-gray-500">
                    Showing {{ $gasSlips->firstItem() ?? 0 }}–{{ $gasSlips->lastItem() ?? 0 }}
                    of {{ $gasSlips->total() }} records
                </span>
            </div>

            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 border-b text-gray-600 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3 w-10"></th>
                        <th class="px-6 py-3">Slip No.</th>
                        <th class="px-6 py-3">Date</th>
                        <th class="px-6 py-3">Vehicle</th>
                        <th class="px-6 py-3">Driver</th>
                        <th class="px-6 py-3">Destination</th>
                        <th class="px-6 py-3">Fuel Used</th>
                        <th class="px-6 py-3">Distance</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($gasSlips as $slip)
                    <tr class="hover:bg-yellow-50 transition-colors">
                        <td class="px-4 py-3">
                            <input type="checkbox" name="ids[]"
                                   value="{{ $slip->id }}" class="rounded border-gray-300 text-yellow-600 rowCheck">
                        </td>
                        <td class="px-6 py-3">
                            <span class="font-mono font-semibold text-yellow-600 text-xs">
                                {{ $slip->slip_number }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-gray-600 text-xs whitespace-nowrap">
                            {{ $slip->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-3">
                            <div class="font-bold text-gray-800">{{ $slip->vehicle->plate_number }}</div>
                            <div class="text-xs text-gray-400">{{ $slip->vehicle->description }}</div>
                        </td>
                        <td class="px-6 py-3 font-medium text-gray-700">
                            {{ $slip->driver->name }}
                        </td>
                        <td class="px-6 py-3 text-gray-700 max-w-xs truncate">
                            {{ $slip->places_to_visit }}
                        </td>
                        <td class="px-6 py-3">
                            @if($slip->fuel_used)
                                <span class="font-semibold text-gray-800">{{ number_format($slip->fuel_used, 2) }}</span>
                                <span class="text-xs text-gray-400">L</span>
                            @else
                                <span class="text-gray-300">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-3">
                            @if($slip->distance_travelled)
                                <span class="font-semibold text-gray-800">{{ number_format($slip->distance_travelled) }}</span>
                                <span class="text-xs text-gray-400">km</span>
                            @else
                                <span class="text-gray-300">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-3">
                            @php
                                $colors = [
                                    'pending'  => 'bg-yellow-100 text-yellow-700',
                                    'approved' => 'bg-green-100 text-green-700',
                                    'released' => 'bg-blue-100 text-blue-700',
                                    'rejected' => 'bg-red-100 text-red-700',
                                ];
                            @endphp
                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $colors[$slip->status] ?? 'bg-gray-100' }}">
                                {{ ucfirst($slip->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-3">
                            <div class="flex items-center gap-3">
                                <a href="{{ route('gas-slips.show', $slip) }}"
                                   class="text-yellow-600 hover:text-yellow-800 text-xs font-medium">
                                    View
                                </a>
                                <a href="{{ route('gas-slips.print', $slip) }}"
                                   target="_blank"
                                   class="text-gray-500 hover:text-gray-700 text-xs font-medium">
                                    Print
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="px-6 py-16 text-center">
                            <div class="text-gray-300 mb-2">
                                <svg class="w-12 h-12 mx-auto" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M19.77 7.23l.01-.01-3.72-3.72-1.06 1.06 1.83 1.83A3.003 3.003 0 0 0 15 9v6c0 .55.45 1 1 1s1-.45 1-1V9c0-.36.18-.68.45-.88l.01-.01 1.25 1.25a2.99 2.99 0 0 1 .87 2.12V17c0 .55-.45 1-1 1s-1 .45-1 1 .45 1 1 1a3 3 0 0 0 3-3v-5.88c0-1.07-.42-2.09-1.17-2.84zM15 3H5C3.9 3 3 3.9 3 5v14c0 1.1.9 2 2 2h10c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3zm3-8H7V5h6v4z"/>
                                </svg>
                            </div>
                            <p class="text-gray-400 text-sm">No gas slips found.</p>
                            @role('super_admin|encoder')
                            <a href="{{ route('gas-slips.create') }}"
                               class="mt-3 inline-block text-yellow-600 text-sm hover:underline">
                                Create the first one →
                            </a>
                            @endrole
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="px-6 py-4 border-t bg-gray-50">
                {{ $gasSlips->links() }}
            </div>

        </div>
    </form>

    <script>
    function toggleAll(source) {
        document.querySelectorAll('.rowCheck').forEach(cb => cb.checked = source.checked);
    }
    </script>

</x-app-layout>
