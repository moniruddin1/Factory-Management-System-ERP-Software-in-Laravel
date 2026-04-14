<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Locations (Stores)</h2>
                <a href="{{ route('locations.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-bold shadow-sm transition">
                    + Add New Location
                </a>
            </div>

            <div class="bg-white dark:bg-[#1e293b] rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden">
                <table class="w-full text-left text-sm border-collapse">
                    <thead>
                    <tr class="bg-gray-50 dark:bg-slate-800 text-gray-600 dark:text-slate-300 uppercase text-xs font-bold border-b border-gray-200 dark:border-slate-700">
                        <th class="p-4">Sl</th>
                        <th class="p-4">Name</th>
                        <th class="p-4">Type</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-right">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-slate-700">
                    @foreach($locations as $index => $location)
                        <tr class="hover:bg-gray-50 dark:hover:bg-slate-800/50">
                            <td class="p-4 text-gray-600 dark:text-slate-400">{{ $index + 1 }}</td>
                            <td class="p-4 font-bold text-gray-900 dark:text-white">{{ $location->name }}</td>
                            <td class="p-4">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-md bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 uppercase">
                                        {{ str_replace('_', ' ', $location->type) }}
                                    </span>
                            </td>
                            <td class="p-4">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-md {{ $location->is_active ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' }}">
                                        {{ $location->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                            </td>
                            <td class="p-4 text-right flex justify-end gap-2">
                                <a href="{{ route('locations.edit', $location->id) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-900/30 dark:hover:bg-indigo-900/50 px-3 py-1.5 rounded-lg transition">Edit</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
