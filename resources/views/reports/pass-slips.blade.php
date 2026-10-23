<x-app-layout>
    <x-slot name="title">Pass Slip Report</x-slot>

    <div class="flex items-center justify-between mb-6">
        <h3 class="text-lg font-semibold text-gray-700">Pass Slip Log</h3>
        <a href="{{ route('reports.index') }}"
           class="text-sm text-blue-600 hover:underline">← Back to Reports</a>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow p-4 mb-6">
        <form method="GET" action="{{ route('reports.pass-slips') }}"
              class="flex flex-wrap gap-3 items-end">

            <div>
                <label class="block text-xs text-gray-600 mb-1">Date From</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                       class="border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-xs text-gray-600 mb-1">Date To</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                       class="border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-xs text-gray-600 mb-1">Division</label>
                <select name="division_id"
                        class="border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Divisions</option>
                    @foreach($divisions as $div)
                        <option value="{{ $div->id }}"
                            {{ request('division_id') == $div->id ? 'selected' : '' }}>
                            {{ $div->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-600 mb-1">Status</label>
                <select name="status"
                        class="border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Status</option>
                    @foreach(['pending','approved','out','returned','rejected'] as $s)
                        <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>
                            {{ ucfirst($s) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit"
                        class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700">
                    Filter
                </button>
                <a href="{{ route('reports.pass-slips') }}"
                   class="bg-gray-200 text-gray-700 px-4 py-2 rounded text-sm hover:bg-gray-300">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Results --}}
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <div class="px-6 py-3 bg-gray-50 border-b flex justify-between items-center">
            <span class="text-sm text-gray-600">
                {{ $passSlips->total() }} record(s) found
            </span>
        </div>
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                <tr>
                    <th class="px-6 py-3">Slip No.</th>
                    <th class="px-6 py-3">Date</th>
                    <th class="px-6 py-3">Employee</th>
                    <th class="px-6 py-3">Division</th>
                    <th class="px-6 py-3">Type</th>
                    <th class="px-6 py-3">Destination</th>
                    <th class="px-6 py-3">Time Out</th>
                    <th class="px-6 py-3">Time In</th>
                    <th class="px-6 py-3">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($passSlips as $slip)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-3 font-mono text-xs text-blue-600">
                        <a href="{{ route('pass-slips.show', $slip) }}" class="hover:underline">
                            {{ $slip->slip_number }}
                        </a>
                    </td>
                    <td class="px-6 py-3">{{ $slip->created_at->format('M d, Y') }}</td>
                    <td class="px-6 py-3">{{ $slip->employee->name }}</td>
                    <td class="px-6 py-3">{{ $slip->division->name ?? '—' }}</td>
                    <td class="px-6 py-3">
                        {{ $slip->pass_type === 'official_business' ? 'Official' : 'Personal' }}
                    </td>
                    <td class="px-6 py-3">{{ $slip->destination }}</td>
                    <td class="px-6 py-3">
                        {{ $slip->time_out ? \Carbon\Carbon::parse($slip->time_out)->format('h:i A') : '—' }}
                    </td>
                    <td class="px-6 py-3">
                        {{ $slip->actual_return ? \Carbon\Carbon::parse($slip->actual_return)->format('h:i A') : '—' }}
                    </td>
                    <td class="px-6 py-3">
                        @php
                            $colors = [
                                'pending'  => 'bg-yellow-100 text-yellow-700',
                                'approved' => 'bg-green-100 text-green-700',
                                'out'      => 'bg-blue-100 text-blue-700',
                                'returned' => 'bg-purple-100 text-purple-700',
                                'rejected' => 'bg-red-100 text-red-700',
                            ];
                        @endphp
                        <span class="px-2 py-1 rounded-full text-xs {{ $colors[$slip->status] ?? 'bg-gray-100' }}">
                            {{ ucfirst($slip->status) }}
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
            {{ $passSlips->links() }}
        </div>
    </div>

</x-app-layout>