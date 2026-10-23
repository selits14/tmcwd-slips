<x-app-layout>
    <x-slot name="title">Reports</x-slot>

    <h3 class="text-lg font-semibold text-gray-700 mb-6">Reports</h3>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- Pass Slip Report --}}
        <a href="{{ route('reports.pass-slips') }}"
           class="bg-white rounded-xl shadow p-6 hover:shadow-md transition group">
            <div class="flex items-center gap-4">
                <div class="bg-blue-100 p-3 rounded-full group-hover:bg-blue-200">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-800">Pass Slip Log</h4>
                    <p class="text-xs text-gray-500 mt-1">
                        Employee off-premises log per date range and division
                    </p>
                </div>
            </div>
        </a>

        {{-- Vehicle Pass Report --}}
        <a href="{{ route('reports.gate-passes') }}"
           class="bg-white rounded-xl shadow p-6 hover:shadow-md transition group">
            <div class="flex items-center gap-4">
                <div class="bg-green-100 p-3 rounded-full group-hover:bg-green-200">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 17a2 2 0 100-4 2 2 0 000 4zm8 0a2 2 0 100-4 2 2 0 000 4zM3 9l1.5-5h13L19 9m0 0H5m14 0v6H5V9"/>
                    </svg>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-800">Vehicle Pass Log</h4>
                    <p class="text-xs text-gray-500 mt-1">
                        Vehicle trips log per date range and plate number
                    </p>
                </div>
            </div>
        </a>

        {{-- Fuel Consumption Report --}}
        <a href="{{ route('reports.fuel-consumption') }}"
           class="bg-white rounded-xl shadow p-6 hover:shadow-md transition group">
            <div class="flex items-center gap-4">
                <div class="bg-yellow-100 p-3 rounded-full group-hover:bg-yellow-200">
                    <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-800">Monthly Fuel Report</h4>
                    <p class="text-xs text-gray-500 mt-1">
                        Report of Fuel Consumption (RFC) per month per vehicle
                    </p>
                </div>
            </div>
        </a>


          {{-- MROT --}}
<a href="{{ route('reports.vehicle-mrot') }}"
   class="bg-white rounded-xl shadow p-6 hover:shadow-md transition group">
    <div class="flex items-center gap-4">
        <div class="bg-blue-100 p-3 rounded-full group-hover:bg-blue-200">
            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
        </div>
        <div>
            <h4 class="font-semibold text-gray-800">MROT</h4>
            <p class="text-xs text-gray-500 mt-1">
                Monthly Report of Official Travel per vehicle
            </p>
        </div>
    </div>
</a>

{{-- RFC --}}
<a href="{{ route('reports.vehicle-rfc') }}"
   class="bg-white rounded-xl shadow p-6 hover:shadow-md transition group">
    <div class="flex items-center gap-4">
        <div class="bg-red-100 p-3 rounded-full group-hover:bg-red-200">
            <svg class="w-8 h-8 text-red-600" fill="currentColor" viewBox="0 0 24 24">
                <path d="M19.77 7.23l.01-.01-3.72-3.72-1.06 1.06 1.83 1.83A3.003 3.003 0 0 0 15 9v6c0 .55.45 1 1 1s1-.45 1-1V9c0-.36.18-.68.45-.88l.01-.01 1.25 1.25a2.99 2.99 0 0 1 .87 2.12V17c0 .55-.45 1-1 1s-1 .45-1 1 .45 1 1 1a3 3 0 0 0 3-3v-5.88c0-1.07-.42-2.09-1.17-2.84zM15 3H5C3.9 3 3 3.9 3 5v14c0 1.1.9 2 2 2h10c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3zm3-8H7V5h6v4z"/>
            </svg>
        </div>
        <div>
            <h4 class="font-semibold text-gray-800">RFC</h4>
            <p class="text-xs text-gray-500 mt-1">
                Report of Fuel Consumption per vehicle
            </p>
        </div>
    </div>
</a>
    </div>
  

</x-app-layout>