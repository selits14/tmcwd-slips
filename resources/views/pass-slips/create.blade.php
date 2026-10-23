<x-app-layout>
    <x-slot name="title">New Pass Slip</x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-xl shadow p-6">

            <h3 class="text-lg font-semibold text-gray-700 mb-6">
                New Pass Slip
            </h3>

            <form method="POST" action="{{ route('pass-slips.store') }}">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Employee requesting the pass <span class="text-red-500">*</span>
                    </label>
                    <select name="employee_user_id" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Select employee --</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ old('employee_user_id') == $employee->id ? 'selected' : '' }}>
                                {{ $employee->name }}{{ $employee->division ? ' — ' . $employee->division->name : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('employee_user_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Pass Type --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Pass Type <span class="text-red-500">*</span>
                    </label>
                    <select name="pass_type"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="official_business" {{ old('pass_type') === 'official_business' ? 'selected' : '' }}>
                            Official Business
                        </option>
                        <option value="personal" {{ old('pass_type') === 'personal' ? 'selected' : '' }}>
                            Personal
                        </option>
                    </select>
                    @error('pass_type')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Purpose --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Purpose <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="purpose" value="{{ old('purpose') }}"
                           placeholder="State your purpose..."
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('purpose')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Destination --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Destination <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="destination" value="{{ old('destination') }}"
                           placeholder="Where are you going?"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('destination')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Time Out & Expected Return --}}
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Time Out <span class="text-red-500">*</span>
                        </label>
                        <input type="datetime-local" name="time_out" value="{{ old('time_out') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('time_out')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Expected Return <span class="text-red-500">*</span>
                        </label>
                        <input type="datetime-local" name="expected_return" value="{{ old('expected_return') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('expected_return')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Remarks --}}
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Remarks
                    </label>
                    <textarea name="remarks" rows="3"
                              placeholder="Optional remarks..."
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('remarks') }}</textarea>
                </div>

                {{-- Buttons --}}
                <div class="flex gap-3">
                    <button type="submit"
                            class="bg-blue-600 text-white px-6 py-2 rounded-lg text-sm hover:bg-blue-700">
                        Submit Pass Slip
                    </button>
                    <a href="{{ route('pass-slips.index') }}"
                       class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg text-sm hover:bg-gray-300">
                        Cancel
                    </a>
                </div>

            </form>
        </div>
    </div>

</x-app-layout>
