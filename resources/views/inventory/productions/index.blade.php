<x-app-layout>
    <div class="py-8 bg-gray-50 dark:bg-slate-900 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Production History</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">View and manage shoe manufacturing records</p>
                </div>
                <a href="{{ route('productions.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl shadow-sm text-sm font-medium transition-colors flex items-center">
                    <i class="fa-solid fa-plus mr-2"></i> New Production
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
                            <th class="p-4 font-medium border-b border-gray-100 dark:border-slate-700">Ref No. & Date</th>
                            <th class="p-4 font-medium border-b border-gray-100 dark:border-slate-700">Finished Product</th>
                            <th class="p-4 font-medium border-b border-gray-100 dark:border-slate-700 text-center">Quantity</th>
                            <th class="p-4 font-medium border-b border-gray-100 dark:border-slate-700 text-right">Total Cost</th>
                            <th class="p-4 font-medium border-b border-gray-100 dark:border-slate-700 text-right">Unit Cost</th>
                            <th class="p-4 font-medium border-b border-gray-100 dark:border-slate-700 text-center">Actions</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-slate-700 text-sm">
                        @forelse($productions as $production)
                            <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors">
                                <td class="p-4">
                                    <div class="font-bold text-indigo-600 dark:text-indigo-400">{{ $production->reference_no }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ \Carbon\Carbon::parse($production->production_date)->format('d M, Y') }}</div>
                                </td>
                                <td class="p-4">
                                    <div class="text-gray-900 dark:text-white font-medium">{{ optional($production->finishedProduct)->name }}</div>
                                </td>
                                <td class="p-4 text-center">
                                        <span class="bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 py-1 px-2.5 rounded-lg text-xs font-bold">
                                            {{ rtrim(rtrim($production->target_quantity, '0'), '.') }} Pairs
                                        </span>
                                </td>
                                <td class="p-4 text-right font-medium text-gray-700 dark:text-gray-300">
                                    ৳ {{ number_format($production->total_cost, 2) }}
                                </td>
                                <td class="p-4 text-right text-gray-500 dark:text-gray-400">
                                    ৳ {{ number_format($production->unit_cost, 2) }}
                                </td>
                                <td class="p-4 text-center">
                                    <a href="{{ route('productions.show', $production->id) }}" class="text-blue-500 hover:text-blue-700 dark:text-blue-400 transition inline-block" title="View Details">
                                        <i class="fa-regular fa-eye text-lg"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-8 text-center text-gray-500 dark:text-gray-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="fa-solid fa-industry text-4xl mb-3 text-gray-300 dark:text-slate-600"></i>
                                        <p>No production records found. Start your first production!</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                @if($productions->hasPages())
                    <div class="p-4 border-t border-gray-100 dark:border-slate-700">
                        {{ $productions->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
