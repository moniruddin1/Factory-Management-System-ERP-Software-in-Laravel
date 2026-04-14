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
            <div class="bg-white dark:bg-[#1e293b] rounded-xl shadow-sm overflow-hidden border border-gray-100 dark:border-slate-700">
                <div class="p-4 border-b border-gray-100 dark:border-slate-700 bg-gray-50/50">
                    <h3 class="font-bold text-gray-700 dark:text-white">Top 5 High-Wastage Batches</h3>
                </div>
                <table class="w-full text-left text-sm">
                    <thead>
                    <tr class="text-gray-400 uppercase text-[10px] tracking-widest border-b">
                        <th class="p-4">Batch No</th>
                        <th class="p-4">Product</th>
                        <th class="p-4 text-center">Efficiency Rate</th>
                        <th class="p-4 text-right">Wastage Cost</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($topWastageBatches as $prd)
                        <tr class="border-b dark:border-slate-700 hover:bg-gray-50 dark:hover:bg-slate-800">
                            <td class="p-4 font-bold text-indigo-600">{{ $prd->batch_no }}</td>
                            <td class="p-4">{{ $prd->finishedProduct->name }}</td>
                            <td class="p-4 text-center">
                                <span class="px-2 py-1 rounded text-xs {{ $prd->efficiency_rate >= 100 ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                    {{ number_format($prd->efficiency_rate, 1) }}%
                                </span>
                            </td>
                            <td class="p-4 text-right font-bold text-rose-500">৳ {{ number_format($prd->material_variance, 2) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
