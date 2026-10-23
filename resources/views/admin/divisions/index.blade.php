<x-app-layout>
    <x-slot name="title">Divisions</x-slot>

    <div class="flex items-center justify-between mb-6">
        <h3 class="text-lg font-semibold text-gray-700">Divisions</h3>
        <a href="{{ route('admin.divisions.create') }}"
           class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">
            + Add Division
        </a>
    </div>

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                <tr>
                    <th class="px-6 py-3">Code</th>
                    <th class="px-6 py-3">Division Name</th>
                    <th class="px-6 py-3">Division Head</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($divisions as $division)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 font-mono font-bold text-blue-600">
                        {{ $division->code }}
                    </td>
                    <td class="px-6 py-4 font-semibold">{{ $division->name }}</td>
                    <td class="px-6 py-4">{{ $division->head->name ?? '—' }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded-full text-xs
                            {{ $division->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $division->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 flex gap-3">
                        <a href="{{ route('admin.divisions.edit', $division) }}"
                           class="text-blue-600 hover:underline text-xs">Edit</a>
                        <form method="POST" action="{{ route('admin.divisions.destroy', $division) }}">
                            @csrf @method('DELETE')
                            <button class="text-red-500 hover:underline text-xs">Deactivate</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-10 text-center text-gray-400">
                        No divisions found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4 border-t">
            {{ $divisions->links() }}
        </div>
    </div>

</x-app-layout>