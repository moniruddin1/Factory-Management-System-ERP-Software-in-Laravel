<x-app-layout>
    <div class="py-8 bg-gray-50 dark:bg-[#0f172a] min-h-screen font-sans">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Final Productions</h2>
                    <p class="text-sm text-gray-500 dark:text-slate-400">View finished goods and reconciled WIP</p>
                </div>
                <a href="{{ route('productions.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg shadow-sm text-sm font-medium transition-colors flex items-center">
                    <i class="fa-solid fa-plus mr-2"></i> New Production
                </a>
                <a href="{{ route('productions.analytics') }}" class="bg-amber-500 hover:bg-amber-600 text-white px-5 py-2.5 rounded-lg shadow-sm text-sm font-medium transition-colors flex items-center">
                    <i class="fa-solid fa-chart-line mr-2"></i> View Analytics
                </a>
            </div>

            @if(session('success'))
                <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg relative flex items-center text-sm">
                    <i class="fa-solid fa-circle-check mr-2 text-emerald-500"></i> {{ session('success') }}
                </div>
            @endif

            <div class="bg-white dark:bg-[#1e293b] shadow-sm rounded-xl overflow-hidden border border-gray-100 dark:border-slate-700/50">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                        <tr class="bg-gray-50 dark:bg-slate-800/50 text-gray-500 dark:text-slate-400 text-xs uppercase tracking-wider">
                            <th class="p-4 font-medium border-b border-gray-100 dark:border-slate-700">Ref No & Date</th>
                            <th class="p-4 font-medium border-b border-gray-100 dark:border-slate-700">Linked WIP Voucher</th>
                            <th class="p-4 font-medium border-b border-gray-100 dark:border-slate-700">Finished Shoe</th>
                            <th class="p-4 font-medium border-b border-gray-100 dark:border-slate-700 text-center">Qty (Pairs)</th>
                            <th class="p-4 font-medium border-b border-gray-100 dark:border-slate-700 text-right">Total Material Cost</th>
                            <th class="p-4 font-medium border-b border-gray-100 dark:border-slate-700 text-right">Cost/Pair</th>
                            <th class="p-4 font-medium border-b border-gray-100 dark:border-slate-700 text-center">Action</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-slate-700 text-sm">
                        @forelse($productions as $production)
                            <tr class="hover:bg-gray-50 dark:hover:bg-slate-800 transition-colors">
                                <td class="p-4">
                                    <div class="font-bold text-indigo-600 dark:text-indigo-400">{{ $production->reference_no }}</div>
                                    <div class="text-xs text-gray-500 dark:text-slate-400">{{ \Carbon\Carbon::parse($production->production_date)->format('d M, Y') }}</div>
                                </td>
                                <td class="p-4">
                                    @if($production->issue)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                                                <i class="fa-solid fa-link mr-1"></i> {{ $production->issue->voucher_no }}
                                            </span>
                                    @else
                                        <span class="text-gray-400 dark:text-slate-500 italic text-xs">Direct Production</span>
                                    @endif
                                </td>
                                <td class="p-4">
                                    <div class="text-gray-900 dark:text-white font-medium">
                                        {{ $production->finishedProduct ? $production->finishedProduct->name : 'N/A' }}
                                    </div>                                </td>
                                <td class="p-4 text-center">
                                        <span class="bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 py-1 px-3 rounded-lg text-xs font-bold border border-emerald-200 dark:border-emerald-800/50">
                                            {{ rtrim(rtrim($production->target_quantity, '0'), '.') }}
                                        </span>
                                </td>
                                <td class="p-4 text-right font-medium text-gray-700 dark:text-slate-300">
                                    ৳ {{ number_format($production->total_cost, 2) }}
                                </td>
                                <td class="p-4 text-right text-rose-500 dark:text-rose-400 font-medium">
                                    ৳ {{ number_format($production->unit_cost, 2) }}
                                </td>
                                <td class="p-4 text-center">
                                    <a href="{{ route('productions.show', $production->id) }}" class="text-blue-500 hover:text-blue-700 dark:text-blue-400 transition inline-block bg-blue-50 dark:bg-blue-900/20 p-2 rounded-lg" title="View Details">
                                        <i class="fa-regular fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-8 text-center text-gray-500 dark:text-slate-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="fa-solid fa-industry text-4xl mb-3 text-gray-300 dark:text-slate-600"></i>
                                        <p>No production records found.</p>
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
