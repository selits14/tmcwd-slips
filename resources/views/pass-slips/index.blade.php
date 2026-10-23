<x-app-layout>
    <x-slot name="title">Pass Slips</x-slot>

    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-5">
        <div>
            <h3 class="text-xl font-bold text-gray-800">Pass Slips</h3>
            <p class="text-xs text-gray-500 mt-0.5">Manage employee off-premises requests</p>
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
            <a href="{{ route('pass-slips.create') }}"
               class="flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                New Pass Slip
            </a>
            @endrole
        </div>
    </div>

    {{-- Search & Filter --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-4">
        <form method="GET" action="{{ route('pass-slips.index') }}">
            <div class="grid grid-cols-2 md:grid-cols-6 gap-3 items-end">

                <div class="col-span-2">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Slip no., employee, purpose..."
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Type</label>
                    <select name="pass_type"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Types</option>
                        <option value="official_business" {{ request('pass_type') === 'official_business' ? 'selected' : '' }}>Official</option>
                        <option value="personal" {{ request('pass_type') === 'personal' ? 'selected' : '' }}>Personal</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
                    <select name="status"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Status</option>
                        @foreach(['pending','approved','out','returned','rejected'] as $s)
                            <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>
                                {{ ucfirst($s) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Date From</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Date To</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

            </div>

            <div class="flex gap-2 mt-3">
                <button type="submit"
                        class="bg-blue-600 text-white px-5 py-2 rounded-lg text-sm hover:bg-blue-700">
                    Filter
                </button>
                <a href="{{ route('pass-slips.index') }}"
                   class="bg-gray-100 text-gray-600 px-5 py-2 rounded-lg text-sm hover:bg-gray-200">
                    Reset
                </a>
                @if(request()->hasAny(['search','status','pass_type','date_from','date_to']))
                    <span class="text-xs text-gray-500 self-center ml-2">
                        {{ $passSlips->total() }} result(s) found
                    </span>
                @endif
            </div>
        </form>
    </div>

    {{-- Table --}}
    <form method="POST" action="{{ route('pass-slips.batch-print') }}" id="batchForm">
        @csrf
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

            {{-- Table Stats Bar --}}
            <div class="px-6 py-3 bg-gray-50 border-b flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <input type="checkbox" onclick="toggleAll(this)"
                           class="rounded border-gray-300 text-blue-600">
                    <span class="text-xs text-gray-500">Select all on this page</span>
                </div>
                <span class="text-xs text-gray-500">
                    Showing {{ $passSlips->firstItem() ?? 0 }}–{{ $passSlips->lastItem() ?? 0 }}
                    of {{ $passSlips->total() }} records
                </span>
            </div>

            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 border-b text-gray-600 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3 w-10"></th>
                        <th class="px-6 py-3">Slip No.</th>
                        <th class="px-6 py-3">Employee</th>
                        <th class="px-6 py-3">Division</th>
                        <th class="px-6 py-3">Type</th>
                        <th class="px-6 py-3">Purpose</th>
                        <th class="px-6 py-3">Time Out</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($passSlips as $slip)
                    <tr class="hover:bg-blue-50 transition-colors">
                        <td class="px-4 py-3">
                            <input type="checkbox" name="ids[]"
                                   value="{{ $slip->id }}" class="rounded border-gray-300 text-blue-600 rowCheck">
                        </td>
                        <td class="px-6 py-3">
                            <span class="font-mono font-semibold text-blue-600 text-xs">
                                {{ $slip->slip_number }}
                            </span>
                        </td>
                        <td class="px-6 py-3">
                            <div class="font-semibold text-gray-800">{{ $slip->employee->name }}</div>
                            <div class="text-xs text-gray-400">{{ $slip->employee->position ?? '' }}</div>
                        </td>
                        <td class="px-6 py-3 text-gray-600">{{ $slip->division->name ?? '—' }}</td>
                        <td class="px-6 py-3">
                            <span class="px-2 py-1 rounded-full text-xs font-medium
                                {{ $slip->pass_type === 'official_business' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700' }}">
                                {{ $slip->pass_type === 'official_business' ? 'Official' : 'Personal' }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-gray-700 max-w-xs truncate">{{ $slip->purpose }}</td>
                        <td class="px-6 py-3 text-gray-600 text-xs whitespace-nowrap">
                            {{ $slip->time_out ? \Carbon\Carbon::parse($slip->time_out)->format('M d, Y h:i A') : '—' }}
                        </td>
                        <td class="px-6 py-3">
                            @php
                                $colors = [
                                    'draft'    => 'bg-gray-100 text-gray-600',
                                    'pending'  => 'bg-yellow-100 text-yellow-700',
                                    'approved' => 'bg-green-100 text-green-700',
                                    'out'      => 'bg-blue-100 text-blue-700',
                                    'returned' => 'bg-purple-100 text-purple-700',
                                    'rejected' => 'bg-red-100 text-red-700',
                                ];
                            @endphp
                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $colors[$slip->status] ?? 'bg-gray-100' }}">
                                {{ ucfirst($slip->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-3">
                            <div class="flex items-center gap-3">
                                <a href="{{ route('pass-slips.show', $slip) }}"
                                   class="text-blue-600 hover:text-blue-800 text-xs font-medium">
                                    View
                                </a>
                                <a href="{{ route('pass-slips.print', $slip) }}"
                                   target="_blank"
                                   class="text-gray-500 hover:text-gray-700 text-xs font-medium">
                                    Print
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-16 text-center">
                            <div class="text-gray-300 mb-2">
                                <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <p class="text-gray-400 text-sm">No pass slips found.</p>
                            @role('super_admin|encoder')
                            <a href="{{ route('pass-slips.create') }}"
                               class="mt-3 inline-block text-blue-600 text-sm hover:underline">
                                Create the first one →
                            </a>
                            @endrole
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Pagination --}}
            <div class="px-6 py-4 border-t bg-gray-50">
                {{ $passSlips->links() }}
            </div>

        </div>
    </form>

    <script>
    function toggleAll(source) {
        document.querySelectorAll('.rowCheck').forEach(cb => cb.checked = source.checked);
    }
    </script>

</x-app-layout>
