<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Staffs & Supervisors</h2>
                <a href="{{ route('staffs.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-bold shadow-sm transition">
                    + Add New Staff
                </a>
            </div>

            <div class="bg-white dark:bg-[#1e293b] rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden">
                <table class="w-full text-left text-sm border-collapse">
                    <thead>
                    <tr class="bg-gray-50 dark:bg-slate-800 text-gray-600 dark:text-slate-300 uppercase text-xs font-bold border-b border-gray-200 dark:border-slate-700">
                        <th class="p-4">Name</th>
                        <th class="p-4">Designation</th>
                        <th class="p-4">Phone</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-right">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-slate-700">
                    @foreach($staffs as $staff)
                        <tr class="hover:bg-gray-50 dark:hover:bg-slate-800/50">
                            <td class="p-4 font-bold text-gray-900 dark:text-white">{{ $staff->name }}</td>
                            <td class="p-4 text-gray-600 dark:text-slate-400">{{ $staff->designation }}</td>
                            <td class="p-4 text-gray-600 dark:text-slate-400">{{ $staff->phone }}</td>
                            <td class="p-4">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-md {{ $staff->is_active ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' }}">
                                        {{ $staff->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                            </td>
                            <td class="p-4 text-right flex justify-end gap-2">
                                <a href="{{ route('staffs.edit', $staff->id) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 bg-blue-50 hover:bg-blue-100 dark:bg-blue-900/30 dark:hover:bg-blue-900/50 px-3 py-1.5 rounded-lg transition">Edit</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
