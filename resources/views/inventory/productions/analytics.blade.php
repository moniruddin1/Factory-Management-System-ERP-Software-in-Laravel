<x-app-layout>
    <div class="py-8 bg-gray-50 dark:bg-[#0f172a] min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">Production Performance & Wastage</h2>

            {{-- Summary Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white dark:bg-[#1e293b] p-6 rounded-xl shadow-sm border-l-4 border-rose-500">
                    <p class="text-xs text-gray-500 uppercase font-bold tracking-wider">Total Wastage (Loss)</p>
                    <h3 class="text-2xl font-bold text-rose-600 mt-1">৳ {{ number_format($totalWastageCost, 2) }}</h3>
                    <p class="text-[10px] text-gray-400 mt-2">Extra material used above BOM</p>
                </div>

                <div class="bg-white dark:bg-[#1e293b] p-6 rounded-xl shadow-sm border-l-4 border-emerald-500">
                    <p class="text-xs text-gray-500 uppercase font-bold tracking-wider">Total Savings (Efficiency)</p>
                    <h3 class="text-2xl font-bold text-emerald-600 mt-1">৳ {{ number_format($totalSavingsCost, 2) }}</h3>
                    <p class="text-[10px] text-gray-400 mt-2">Material saved from smart production</p>
                </div>

                <div class="bg-white dark:bg-[#1e293b] p-6 rounded-xl shadow-sm border-l-4 border-indigo-500">
                    <p class="text-xs text-gray-500 uppercase font-bold tracking-wider">Net Performance</p>
                    @php $net = $totalSavingsCost - $totalWastageCost; @endphp
                    <h3 class="text-2xl font-bold {{ $net >= 0 ? 'text-emerald-600' : 'text-rose-600' }} mt-1">
                        ৳ {{ number_format($net, 2) }}
                    </h3>
                    <p class="text-[10px] text-gray-400 mt-2">Overall impact on production cost</p>
                </div>
            </div>

            {{-- Top Wastage Batches Table --}}
            {{-- Top Wastage Batches Table --}}
            <div class="bg-white dark:bg-[#1e293b] rounded-xl shadow-sm overflow-hidden border border-gray-100 dark:border-slate-700">
                <div class="p-4 border-b border-gray-100 dark:border-slate-700 bg-gray-50/50 flex justify-between items-center">
                    <h3 class="font-bold text-gray-700 dark:text-white">Production Efficiency Analysis</h3>
                    <span class="text-xs text-gray-400">Showing top batches by variance</span>
                </div>
                <table class="w-full text-left text-sm">
                    <thead>
                    <tr class="text-gray-400 uppercase text-[10px] tracking-widest border-b bg-gray-50 dark:bg-slate-800/50">
                        <th class="p-4">Date & Batch</th>
                        <th class="p-4">Product Name</th>
                        <th class="p-4">Produced By (Staff)</th>
                        <th class="p-4 text-center">Qty</th>
                        <th class="p-4 text-center">Efficiency</th>
                        <th class="p-4 text-right">Variance (Cost)</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($topWastageBatches as $prd)
                        <tr class="border-b dark:border-slate-700 hover:bg-gray-50 dark:hover:bg-slate-800 transition">
                            <td class="p-4">
                                <div class="text-xs text-gray-400 mb-1">{{ \Carbon\Carbon::parse($prd->production_date)->format('d M, Y') }}</div>
                                <div class="font-bold text-indigo-600">{{ $prd->batch_no }}</div>
                            </td>
                            <td class="p-4">
                                <div class="font-medium text-gray-700 dark:text-gray-200">{{ $prd->finishedProduct->name }}</div>
                                <div class="text-[10px] text-gray-400">Ref: {{ $prd->reference_no }}</div>
                            </td>
                            <td class="p-4">
                                <div class="flex items-center">
                                    <div class="h-8 w-8 rounded-full bg-gray-100 dark:bg-slate-700 flex items-center justify-center mr-2">
                                        <i class="fa-solid fa-user-gear text-gray-400 text-xs"></i>
                                    </div>
                                    <span class="font-medium text-gray-600 dark:text-slate-300">
    {{ $staffs[$prd->issue->issued_to ?? null] ?? 'N/A' }}
</span>
                                </div>
                            </td>
                            <td class="p-4 text-center font-bold text-gray-600 dark:text-slate-400">
                                {{ number_format($prd->target_quantity) }}
                            </td>
                            <td class="p-4 text-center">
                        <span class="px-2 py-1 rounded-full text-[10px] font-bold {{ $prd->efficiency_rate >= 100 ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-400' }}">
                            {{ number_format($prd->efficiency_rate, 1) }}%
                        </span>
                            </td>
                            <td class="p-4 text-right">
                                <div class="font-bold {{ $prd->material_variance > 0 ? 'text-rose-500' : 'text-emerald-500' }}">
                                    {{ $prd->material_variance > 0 ? '+' : '' }}৳ {{ number_format($prd->material_variance, 2) }}
                                </div>
                                <div class="text-[10px] text-gray-400">
                                    {{ $prd->material_variance > 0 ? 'Extra Cost' : 'Savings' }}
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
