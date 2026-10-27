<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>

    {{-- Welcome Bar --}}
    <div class="mb-6">
        <div>
            <h3 class="text-xl font-semibold tracking-tight text-slate-800">
                Good {{ now()->hour < 12 ? 'Morning' : (now()->hour < 17 ? 'Afternoon' : 'Evening') }},
                {{ explode(' ', Auth::user()->name)[0] }}
            </h3>
            <p class="text-sm text-slate-500 mt-1">
                {{ now()->format('l, F d, Y') }} <span class="mx-1 text-slate-300">/</span>
                Here's what's happening today.
            </p>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">

        {{-- Pass Slips Pending --}}
        <a href="{{ route('pass-slips.index') }}?status=pending"
           class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition">
            <div class="flex items-center justify-between mb-3">
                <div class="bg-blue-100 p-2 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                @if($stats['pass_slips_pending'] > 0)
                    <span class="bg-red-100 text-red-600 text-xs font-bold px-2 py-0.5 rounded-full">
                        Action needed
                    </span>
                @endif
            </div>
            <p class="text-3xl font-bold text-gray-800">{{ $stats['pass_slips_pending'] }}</p>
            <p class="text-xs text-gray-500 mt-1">Pass Slips Pending</p>
                <p class="text-xs text-blue-600 mt-1">{{ $stats['pass_slips_today'] }} created today</p>
        </a>

        {{-- Gate Passes Pending --}}
        <a href="{{ route('gate-passes.index') }}?status=pending"
           class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition">
            <div class="flex items-center justify-between mb-3">
                <div class="bg-green-100 p-2 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 17a2 2 0 100-4 2 2 0 000 4zm8 0a2 2 0 100-4 2 2 0 000 4zM3 9l1.5-5h13L19 9m0 0H5m14 0v6H5V9"/>
                    </svg>
                </div>
                @if($stats['gate_passes_pending'] > 0)
                    <span class="bg-red-100 text-red-600 text-xs font-bold px-2 py-0.5 rounded-full">
                        Action needed
                    </span>
                @endif
            </div>
            <p class="text-3xl font-bold text-gray-800">{{ $stats['gate_passes_pending'] }}</p>
            <p class="text-xs text-gray-500 mt-1">Gate Passes Pending</p>
            <p class="text-xs text-green-600 mt-1">{{ $stats['gate_passes_today'] }} created today</p>
        </a>

        {{-- Gas Slips Pending --}}
        <a href="{{ route('gas-slips.index') }}?status=pending"
           class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition">
            <div class="flex items-center justify-between mb-3">
                <div class="bg-yellow-100 p-2 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M19.77 7.23l.01-.01-3.72-3.72-1.06 1.06 1.83 1.83A3.003 3.003 0 0 0 15 9v6c0 .55.45 1 1 1s1-.45 1-1V9c0-.36.18-.68.45-.88l.01-.01 1.25 1.25a2.99 2.99 0 0 1 .87 2.12V17c0 .55-.45 1-1 1s-1 .45-1 1 .45 1 1 1a3 3 0 0 0 3-3v-5.88c0-1.07-.42-2.09-1.17-2.84zM15 3H5C3.9 3 3 3.9 3 5v14c0 1.1.9 2 2 2h10c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3zm3-8H7V5h6v4z"/>
                    </svg>
                </div>
                @if($stats['gas_slips_pending'] > 0)
                    <span class="bg-red-100 text-red-600 text-xs font-bold px-2 py-0.5 rounded-full">
                        Action needed
                    </span>
                @endif
            </div>
            <p class="text-3xl font-bold text-gray-800">{{ $stats['gas_slips_pending'] }}</p>
            <p class="text-xs text-gray-500 mt-1">Gas Slips Pending</p>
            <p class="text-xs text-yellow-700 mt-1">{{ $stats['gas_slips_today'] }} created today</p>
        </a>

        {{-- Currently Out --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="bg-purple-100 p-2 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-800">
                {{ $stats['pass_slips_out'] + $stats['gate_passes_released'] }}
            </p>
            <p class="text-xs text-gray-500 mt-1">Currently Out / On Trip</p>
            <p class="text-xs text-purple-500 mt-1">
                {{ $stats['pass_slips_out'] }} employees <span class="mx-1 text-slate-300">/</span> {{ $stats['gate_passes_released'] }} vehicles
            </p>
        </div>

    </div>

    {{-- Main Content: Pending + Activity --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

        {{-- Pending for Approval --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-4 border-b flex items-center justify-between">
                <h4 class="font-semibold text-slate-700 text-sm">Pending Approval</h4>
                @php $totalPending = $stats['pass_slips_pending'] + $stats['gate_passes_pending'] + $stats['gas_slips_pending']; @endphp
                @if($totalPending > 0)
                    <span class="bg-red-100 text-red-600 text-xs font-bold px-2 py-0.5 rounded-full">
                        {{ $totalPending }} pending
                    </span>
                @else
                    <span class="bg-green-100 text-green-600 text-xs font-medium px-2 py-0.5 rounded-full">
                        All clear
                    </span>
                @endif
            </div>

            <div class="divide-y divide-gray-50">

                {{-- Pending Pass Slips --}}
                @forelse($pendingPassSlips as $slip)
                <a href="{{ route('pass-slips.show', $slip) }}"
                   class="flex items-center justify-between px-5 py-3 hover:bg-blue-50 transition">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 rounded-full bg-blue-500 flex-shrink-0"></div>
                        <div>
                            <p class="text-xs font-semibold text-gray-700">{{ $slip->employee->name }}</p>
                            <p class="text-xs text-gray-400">{{ $slip->slip_number }} / Pass Slip</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-gray-400">{{ $slip->created_at->diffForHumans() }}</p>
                        <span class="text-xs bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full">Pending</span>
                    </div>
                </a>
                @empty
                @endforelse

                {{-- Pending Gate Passes --}}
                @forelse($pendingGatePasses as $pass)
                <a href="{{ route('gate-passes.show', $pass) }}"
                   class="flex items-center justify-between px-5 py-3 hover:bg-green-50 transition">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 rounded-full bg-green-500 flex-shrink-0"></div>
                        <div>
                            <p class="text-xs font-semibold text-gray-700">{{ $pass->driver_name }}</p>
                            <p class="text-xs text-gray-400">{{ $pass->pass_number }} / Vehicle Pass</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-gray-400">{{ $pass->created_at->diffForHumans() }}</p>
                        <span class="text-xs bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full">Pending</span>
                    </div>
                </a>
                @empty
                @endforelse

                {{-- Pending Gas Slips --}}
                @forelse($pendingGasSlips as $slip)
                <a href="{{ route('gas-slips.show', $slip) }}"
                   class="flex items-center justify-between px-5 py-3 hover:bg-yellow-50 transition">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 rounded-full bg-yellow-500 flex-shrink-0"></div>
                        <div>
                            <p class="text-xs font-semibold text-gray-700">{{ $slip->driver->name }}</p>
                            <p class="text-xs text-gray-400">{{ $slip->slip_number }} / Gas Slip / {{ $slip->vehicle->plate_number }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-gray-400">{{ $slip->created_at->diffForHumans() }}</p>
                        <span class="text-xs bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full">Pending</span>
                    </div>
                </a>
                @empty
                @endforelse

                @if($totalPending === 0)
                <div class="px-5 py-8 text-center text-gray-400 text-sm">
                    No pending slips
                </div>
                @endif

            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-4 border-b">
                <h4 class="font-semibold text-slate-700 text-sm">Recent Activity</h4>
            </div>

            <div class="divide-y divide-gray-50">
                @forelse($recentActivity as $activity)
                <a href="{{ $activity['route'] }}"
                   class="flex items-center justify-between px-5 py-3 hover:bg-gray-50 transition">
                    <div class="flex items-center gap-3">
                        {{-- Color dot --}}
                        @php
                            $dotColor = match($activity['color']) {
                                'blue'   => 'bg-blue-500',
                                'green'  => 'bg-green-500',
                                'yellow' => 'bg-yellow-500',
                                default  => 'bg-gray-400',
                            };
                            $statusColors = [
                                'draft'    => 'bg-gray-100 text-gray-600',
                                'pending'  => 'bg-yellow-100 text-yellow-700',
                                'approved' => 'bg-green-100 text-green-700',
                                'released' => 'bg-blue-100 text-blue-700',
                                'out'      => 'bg-blue-100 text-blue-700',
                                'returned' => 'bg-purple-100 text-purple-700',
                                'rejected' => 'bg-red-100 text-red-700',
                            ];
                        @endphp
                        <div class="w-2 h-2 rounded-full {{ $dotColor }} flex-shrink-0"></div>
                        <div>
                            <p class="text-xs font-semibold text-gray-700">{{ $activity['name'] }}</p>
                            <p class="text-xs text-gray-400">
                                {{ $activity['number'] }} / {{ $activity['type'] }}
                            </p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-gray-400">{{ $activity['time']->diffForHumans() }}</p>
                        <span class="text-xs px-2 py-0.5 rounded-full {{ $statusColors[$activity['status']] ?? 'bg-gray-100 text-gray-600' }}">
                            {{ ucfirst($activity['status']) }}
                        </span>
                    </div>
                </a>
                @empty
                <div class="px-5 py-8 text-center text-gray-400 text-sm">
                    No recent activity
                </div>
                @endforelse
            </div>
        </div>

    </div>

    <div class="grid grid-cols-1 {{ Auth::user()->hasRole('super_admin') ? 'lg:grid-cols-2' : '' }} gap-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <h4 class="font-semibold text-slate-700 text-sm mb-4">Quick Actions</h4>
        <div class="flex flex-wrap gap-3">
            @role('super_admin|encoder')
            <a href="{{ route('pass-slips.create') }}"
               class="flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                New Pass Slip
            </a>
            @endrole
            @role('super_admin|encoder')
            <a href="{{ route('gate-passes.create') }}"
               class="flex items-center gap-2 bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                New Vehicle Pass
            </a>
            @endrole
            @role('super_admin|encoder')
            <a href="{{ route('gas-slips.create') }}"
               class="flex items-center gap-2 bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-yellow-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                New Gas Slip
            </a>
            @endrole
            @role('super_admin|approver|encoder|viewer')
            <a href="{{ route('reports.index') }}"
               class="flex items-center gap-2 bg-gray-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-gray-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                View Reports
            </a>
            @endrole
        </div>
    </div>
    @role('super_admin')
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b flex items-center justify-between gap-4">
            <div>
                <h4 class="font-semibold text-slate-700 text-sm">Monthly Fuel Allocation</h4>
                <p class="text-xs text-gray-400 mt-1">{{ now()->format('F Y') }} · usage from completed trips</p>
            </div>
            <label class="sr-only" for="fuel-filter">Filter vehicles by fuel use</label>
            <select id="fuel-filter" class="rounded-lg border-gray-300 text-xs" onchange="document.querySelectorAll('[data-fuel-status]').forEach(row => row.hidden = this.value !== 'all' && row.dataset.fuelStatus !== this.value)">
                <option value="all">All vehicles</option>
                <option value="limited">At/over allocation</option>
                <option value="within">Within allocation</option>
            </select>
        </div>
        <div id="fuel-vehicles" class="divide-y divide-gray-50 max-h-52 overflow-y-auto">
            @forelse($limitedFuelVehicles as $vehicle)
                @php $fuelLimited = (float) $vehicle->monthly_fuel_used >= (float) $vehicle->monthly_fuel_allocation; @endphp
                <div data-fuel-status="{{ $fuelLimited ? 'limited' : 'within' }}" class="px-5 py-3 flex items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold text-gray-700">{{ $vehicle->plate_number }} · {{ $vehicle->description }}</p>
                        <p class="text-xs text-gray-400">{{ number_format((float) $vehicle->monthly_fuel_used, 2) }} / {{ number_format((float) $vehicle->monthly_fuel_allocation, 2) }} L used</p>
                    </div>
                    <span class="text-xs px-2 py-0.5 rounded-full {{ $fuelLimited ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">{{ $fuelLimited ? 'At/over limit' : 'Within limit' }}</span>
                </div>
            @empty
                <p class="px-5 py-8 text-center text-sm text-gray-400">No monthly fuel allocations set.</p>
            @endforelse
            <p id="fuel-filter-empty" hidden class="px-5 py-8 text-center text-sm text-gray-400">No vehicles match this filter.</p>
        </div>
    </div>
    <script>
        document.getElementById('fuel-filter').addEventListener('change', function () {
            const rows = [...document.querySelectorAll('[data-fuel-status]')];
            rows.forEach(row => row.hidden = this.value !== 'all' && row.dataset.fuelStatus !== this.value);
            document.getElementById('fuel-filter-empty').hidden = !rows.length || rows.some(row => !row.hidden);
        });
    </script>
    @endrole
    </div>

</x-app-layout>
