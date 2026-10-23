<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TMCWD - {{ $title ?? 'Slip System' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('tmcwd-logo.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans">

    <div class="flex h-screen overflow-hidden">

        {{-- SIDEBAR --}}
        <aside class="w-64 bg-blue-900 text-white flex flex-col flex-shrink-0">

            {{-- Logo --}}
            <div class="px-6 py-5 border-b border-blue-800">
                <h1 class="text-lg font-bold leading-tight">TMCWD</h1>
                <p class="text-xs text-blue-300">Slip Management System</p>
            </div>

            {{-- User Info --}}
            <div class="px-6 py-4 border-b border-blue-800">
                <p class="text-sm font-semibold">{{ auth()->user()->name }}</p>
                <p class="text-xs text-blue-300">{{ auth()->user()->getRoleNames()->first() }}</p>
                <p class="text-xs text-blue-400">{{ auth()->user()->division->name ?? 'No Division' }}</p>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 px-4 py-4 space-y-1 overflow-y-auto">
                @php
                    $pendingPassCount  = \App\Support\SlipVisibility::query(\App\Models\PassSlip::query(), auth()->user(), 'pass')->where('status','pending')->count();
                    $pendingGateCount  = \App\Support\SlipVisibility::query(\App\Models\GatePass::query(), auth()->user(), 'gate')->where('status','pending')->count();
                    $pendingGasCount   = \App\Support\SlipVisibility::query(\App\Models\GasSlip::query(), auth()->user(), 'gas')->where('status','pending')->count();
                    $totalPending      = $pendingPassCount + $pendingGateCount + $pendingGasCount;
                @endphp

                {{-- Dashboard --}}
                <a href="{{ route('dashboard') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm
                          {{ request()->routeIs('dashboard') ? 'bg-blue-700 text-white' : 'text-blue-200 hover:bg-blue-800' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Dashboard
                </a>

                {{-- Pass Slip --}}
                @role('super_admin|approver|encoder|driver|guard|viewer')
                <a href="{{ route('pass-slips.index') }}"
   class="flex items-center justify-between px-3 py-2 rounded-lg text-sm
          {{ request()->routeIs('pass-slips.*') ? 'bg-blue-700 text-white' : 'text-blue-200 hover:bg-blue-800' }}">
    <div class="flex items-center gap-3">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
        </svg>
        Pass Slip
    </div>
    @if($pendingPassCount > 0)
        <span class="bg-red-500 text-white text-xs font-bold px-1.5 py-0.5 rounded-full min-w-[18px] text-center">
            {{ $pendingPassCount }}
        </span>
    @endif
</a>
                @endrole

                {{-- Gate Pass --}}
                @role('super_admin|approver|encoder|driver|guard|viewer')
                <a href="{{ route('gate-passes.index') }}"
   class="flex items-center justify-between px-3 py-2 rounded-lg text-sm
          {{ request()->routeIs('gate-passes.*') ? 'bg-blue-700 text-white' : 'text-blue-200 hover:bg-blue-800' }}">
    <div class="flex items-center gap-3">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>
        </svg>
        Gate Pass
    </div>
    @if($pendingGateCount > 0)
        <span class="bg-red-500 text-white text-xs font-bold px-1.5 py-0.5 rounded-full min-w-[18px] text-center">
            {{ $pendingGateCount }}
        </span>
    @endif
</a>
                @endrole

                {{-- Gas Slip --}}
                @role('super_admin|approver|encoder|driver|viewer')
                <a href="{{ route('gas-slips.index') }}"
   class="flex items-center justify-between px-3 py-2 rounded-lg text-sm
          {{ request()->routeIs('gas-slips.*') ? 'bg-blue-700 text-white' : 'text-blue-200 hover:bg-blue-800' }}">
    <div class="flex items-center gap-3">
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
            <path d="M19.77 7.23l.01-.01-3.72-3.72-1.06 1.06 1.83 1.83A3.003 3.003 0 0 0 15 9v6c0 .55.45 1 1 1s1-.45 1-1V9c0-.36.18-.68.45-.88l.01-.01 1.25 1.25a2.99 2.99 0 0 1 .87 2.12V17c0 .55-.45 1-1 1s-1 .45-1 1 .45 1 1 1a3 3 0 0 0 3-3v-5.88c0-1.07-.42-2.09-1.17-2.84zM15 3H5C3.9 3 3 3.9 3 5v14c0 1.1.9 2 2 2h10c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3zm3-8H7V5h6v4z"/>
        </svg>
        Gas Slip
    </div>
    @if($pendingGasCount > 0)
        <span class="bg-red-500 text-white text-xs font-bold px-1.5 py-0.5 rounded-full min-w-[18px] text-center">
            {{ $pendingGasCount }}
        </span>
    @endif
</a>
                @endrole
                {{-- Reports --}}
                @role('super_admin|approver|encoder|viewer')
                <a href="{{ route('reports.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm
                        {{ request()->routeIs('reports.*') ? 'bg-blue-700 text-white' : 'text-blue-200 hover:bg-blue-800' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Reports
                </a>
                @endrole

                {{-- Admin Only --}}
                @role('super_admin')
                <div class="pt-4">
                    <p class="px-3 text-xs font-semibold text-blue-400 uppercase tracking-wider mb-2">
                        Admin
                    </p>

                    <a href="{{ route('admin.users.index') }}"
                       class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm
                              {{ request()->routeIs('admin.users.*') ? 'bg-blue-700 text-white' : 'text-blue-200 hover:bg-blue-800' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        Users
                    </a>

                    <a href="{{ route('admin.divisions.index') }}"
                       class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm mt-1
                              {{ request()->routeIs('admin.divisions.*') ? 'bg-blue-700 text-white' : 'text-blue-200 hover:bg-blue-800' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        Divisions
                    </a>

                    <a href="{{ route('admin.vehicles.index') }}"
                       class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm mt-1
                              {{ request()->routeIs('admin.vehicles.*') ? 'bg-blue-700 text-white' : 'text-blue-200 hover:bg-blue-800' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 17a2 2 0 100-4 2 2 0 000 4zm8 0a2 2 0 100-4 2 2 0 000 4zM3 9l1.5-5h13L19 9m0 0H5m14 0v6H5V9"/>
                        </svg>
                        Vehicles
                    </a>
                </div>
                @endrole

            </nav>

            {{-- Logout --}}
            <div class="px-4 py-4 border-t border-blue-800">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-blue-200 hover:bg-blue-800">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Logout
                    </button>
                </form>
            </div>

        </aside>

        {{-- MAIN CONTENT --}}
        <div class="flex-1 flex flex-col overflow-hidden">

            {{-- Top Bar --}}
            <header class="bg-white shadow-sm px-6 py-4 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-700">
                    {{ $title ?? 'Dashboard' }}
                </h2>
                <span class="text-sm text-gray-500">{{ now()->format('F d, Y') }}</span>
            </header>

            {{-- Flash Messages --}}
            <div class="px-6 pt-4">
                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        {{ session('error') }}
                    </div>
                @endif
            </div>

            {{-- Page Content --}}
            <main class="flex-1 overflow-y-auto px-6 py-4">
                {{ $slot }}
            </main>

        </div>
    </div>

</body>
</html>
