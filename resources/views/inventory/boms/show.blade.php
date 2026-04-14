<x-app-layout>
    <div class="py-8 bg-gray-50 dark:bg-slate-900 min-h-screen">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">BOM Details</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">View manufacturing formula</p>
                </div>
                <div class="flex items-center space-x-2">
                    <a href="{{ route('boms.index') }}" class="bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-700 px-4 py-2.5 rounded-xl shadow-sm text-sm font-medium transition-colors inline-flex items-center">
                        <i class="fa-solid fa-arrow-left mr-2"></i> Back
                    </a>
                    <form action="{{ route('boms.destroy', $bom->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this formula?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800/30 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/40 px-4 py-2.5 rounded-xl shadow-sm text-sm font-medium transition-colors inline-flex items-center">
                            <i class="fa-regular fa-trash-can mr-2"></i> Delete
                        </button>
                    </form>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-xl overflow-hidden border border-gray-100 dark:border-slate-700">

                <div class="p-6 border-b border-gray-100 dark:border-slate-700 bg-gray-50/50 dark:bg-slate-800/50">
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Formula Name</p>
                            <h3 class="text-lg font-bold text-indigo-600 dark:text-indigo-400">{{ $bom->name }}</h3>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Finished Product (Shoe)</p>
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white">
                                {{ optional($bom->finishedProduct)->name }}
                                @if(optional($bom->finishedProduct)->code)
                                    <span class="text-xs font-normal text-gray-500 block">{{ optional($bom->finishedProduct)->code }}</span>
                                @endif
                            </h3>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Total Materials</p>
                            <p class="text-lg font-bold text-gray-800 dark:text-white">{{ $bom->items->count() }} <span class="text-sm font-normal text-gray-500">Items</span></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Created By</p>
                            <p class="text-sm text-gray-700 dark:text-gray-300">{{ optional($bom->creator)->name }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Created At</p>
                            <p class="text-sm text-gray-700 dark:text-gray-300">{{ $bom->created_at->format('d M, Y h:i A') }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Last Updated</p>
                            <p class="text-sm text-gray-700 dark:text-gray-300">{{ $bom->updated_at->format('d M, Y h:i A') }}</p>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <h4 class="text-md font-bold text-gray-800 dark:text-white mb-4 flex items-center">
                        <i class="fa-solid fa-list-check mr-2 text-indigo-500"></i> Materials Required for 1 Unit/Pair
                    </h4>

                    <div class="overflow-x-auto border border-gray-100 dark:border-slate-700 rounded-lg">
                        <table class="w-full text-left border-collapse">
                            <thead>
                            <tr class="bg-gray-50 dark:bg-slate-700/50 text-gray-500 dark:text-gray-400 text-xs uppercase tracking-wider">
                                <th class="p-3 font-medium border-b border-gray-100 dark:border-slate-700 w-12">#</th>
                                <th class="p-3 font-medium border-b border-gray-100 dark:border-slate-700">Raw Material Name</th>
                                <th class="p-3 font-medium border-b border-gray-100 dark:border-slate-700 text-right">Quantity Required</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-slate-700 text-sm">
                            @foreach($bom->items as $index => $item)
                                <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/20 transition-colors">
                                    <td class="p-3 text-gray-500">{{ $index + 1 }}</td>
                                    <td class="p-3 text-gray-800 dark:text-gray-200 font-medium">
                                        {{ optional($item->rawMaterial)->name }}
                                        @if(optional($item->rawMaterial)->code)
                                            <span class="text-xs text-gray-500 dark:text-gray-400 block">{{ optional($item->rawMaterial)->code }}</span>
                                        @endif
                                    </td>
                                    <td class="p-3 text-right">
                                        <span class="font-bold text-gray-800 dark:text-white">{{ rtrim(rtrim($item->quantity, '0'), '.') }}</span>
                                        <span class="text-gray-500 dark:text-gray-400 text-xs ml-1">{{ optional($item->unit)->name }}</span>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
