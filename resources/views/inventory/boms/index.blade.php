<x-app-layout>
    <div class="py-8 bg-gray-50 dark:bg-slate-900 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Bill of Materials (BOM)</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Manage shoe manufacturing formulas</p>
                </div>
                <a href="{{ route('boms.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl shadow-sm text-sm font-medium transition-colors flex items-center">
                    <i class="fa-solid fa-plus mr-2"></i> Create BOM
                </a>
            </div>

            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded-xl relative">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-xl overflow-hidden border border-gray-100 dark:border-slate-700">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                        <tr class="bg-gray-50 dark:bg-slate-700/50 text-gray-500 dark:text-gray-400 text-xs uppercase tracking-wider">
                            <th class="p-4 font-medium border-b border-gray-100 dark:border-slate-700">ID</th>
                            <th class="p-4 font-medium border-b border-gray-100 dark:border-slate-700">BOM Name</th>
                            <th class="p-4 font-medium border-b border-gray-100 dark:border-slate-700">Finished Product (Shoe)</th>
                            <th class="p-4 font-medium border-b border-gray-100 dark:border-slate-700 text-center">Materials Count</th>
                            <th class="p-4 font-medium border-b border-gray-100 dark:border-slate-700">Created Date</th>
                            <th class="p-4 font-medium border-b border-gray-100 dark:border-slate-700">Created By</th>
                            <th class="p-4 font-medium border-b border-gray-100 dark:border-slate-700 text-right">Actions</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-slate-700 text-sm">
                        @forelse($boms as $bom)
                            <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors">
                                <td class="p-4 text-gray-900 dark:text-white font-medium">#{{ $bom->id }}</td>
                                <td class="p-4 text-gray-700 dark:text-gray-300 font-bold text-indigo-600 dark:text-indigo-400">{{ $bom->name }}</td>
                                <td class="p-4 text-gray-700 dark:text-gray-300">
                                    {{ optional($bom->finishedProduct)->name }}
                                    @if(optional($bom->finishedProduct)->code)
                                        <span class="text-xs text-gray-500 block">{{ optional($bom->finishedProduct)->code }}</span>
                                    @endif
                                </td>
                                <td class="p-4 text-center">
                                    <span class="bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 py-1 px-2.5 rounded-lg text-xs font-semibold">
                                        {{ $bom->items->count() }} Items
                                    </span>
                                </td>
                                <td class="p-4 text-gray-500 dark:text-gray-400">
                                    <div class="text-gray-700 dark:text-gray-300">{{ $bom->created_at->format('d M, Y') }}</div>
                                    <div class="text-xs">{{ $bom->created_at->format('h:i A') }}</div>
                                </td>
                                <td class="p-4 text-gray-500 dark:text-gray-400">{{ optional($bom->creator)->name }}</td>
                                <td class="p-4 text-right">
                                    <div class="flex items-center justify-end space-x-3">
                                        <a href="{{ route('boms.show', $bom->id) }}" class="text-blue-500 hover:text-blue-700 dark:text-blue-400 transition" title="View Details">
                                            <i class="fa-regular fa-eye"></i>
                                        </a>
                                        <form action="{{ route('boms.destroy', $bom->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this formula?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700 dark:text-red-400 transition" title="Delete">
                                                <i class="fa-regular fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-8 text-center text-gray-500 dark:text-gray-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="fa-solid fa-clipboard-list text-4xl mb-3 text-gray-300 dark:text-slate-600"></i>
                                        <p>No BOM found. Create your first formula!</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                @if($boms->hasPages())
                    <div class="p-4 border-t border-gray-100 dark:border-slate-700">
                        {{ $boms->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
