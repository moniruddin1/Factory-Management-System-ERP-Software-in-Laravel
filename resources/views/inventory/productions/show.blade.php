<x-app-layout>
    <div class="py-8 bg-gray-50 dark:bg-slate-900 min-h-screen">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Production Details</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $production->reference_no }}</p>
                </div>
                <div class="space-x-2">
                    <button onclick="window.print()" class="bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-700 px-4 py-2.5 rounded-xl shadow-sm text-sm font-medium transition-colors inline-flex items-center">
                        <i class="fa-solid fa-print mr-2"></i> Print
                    </button>
                    <a href="{{ route('productions.index') }}" class="bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-700 px-4 py-2.5 rounded-xl shadow-sm text-sm font-medium transition-colors inline-flex items-center">
                        <i class="fa-solid fa-arrow-left mr-2"></i> Back
                    </a>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-xl overflow-hidden border border-gray-100 dark:border-slate-700 mb-6">
                <div class="p-6 grid grid-cols-2 md:grid-cols-4 gap-6">
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Production Date</p>
                        <h3 class="text-md font-bold text-gray-800 dark:text-white">{{ \Carbon\Carbon::parse($production->production_date)->format('d M, Y') }}</h3>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Formula (BOM)</p>
                        <h3 class="text-md font-bold text-indigo-600 dark:text-indigo-400">{{ optional($production->bom)->name }}</h3>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Target Quantity</p>
                        <h3 class="text-md font-bold text-emerald-600 dark:text-emerald-400">{{ rtrim(rtrim($production->target_quantity, '0'), '.') }} Pairs</h3>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Finished Product</p>
                        <h3 class="text-md font-bold text-gray-800 dark:text-white">{{ optional($production->finishedProduct)->name }}</h3>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Total Cost</p>
                        <h3 class="text-md font-bold text-red-500">৳ {{ number_format($production->total_cost, 2) }}</h3>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Cost Per Pair</p>
                        <h3 class="text-md font-bold text-red-500">৳ {{ number_format($production->unit_cost, 2) }}</h3>
                    </div>
                    <div class="col-span-2">
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Notes</p>
                        <p class="text-sm text-gray-700 dark:text-gray-300">{{ $production->notes ?: 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-xl overflow-hidden border border-gray-100 dark:border-slate-700">
                <div class="p-4 border-b border-gray-100 dark:border-slate-700 bg-gray-50/50 dark:bg-slate-800/50">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white">Raw Material Usage</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                        <tr class="bg-gray-50 dark:bg-slate-700/50 text-gray-500 dark:text-gray-400 text-xs uppercase tracking-wider">
                            <th class="p-4 font-medium border-b border-gray-100 dark:border-slate-700">Raw Material</th>
                            <th class="p-4 font-medium border-b border-gray-100 dark:border-slate-700 text-right">Estimated Qty</th>
                            <th class="p-4 font-medium border-b border-gray-100 dark:border-slate-700 text-right">Actual Used Qty</th>
                            <th class="p-4 font-medium border-b border-gray-100 dark:border-slate-700 text-right">Unit Price</th>
                            <th class="p-4 font-medium border-b border-gray-100 dark:border-slate-700 text-right">Subtotal</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-slate-700 text-sm">
                        @foreach($production->items as $item)
                            <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/20 transition-colors">
                                <td class="p-4 text-gray-900 dark:text-white font-medium">
                                    {{ optional($item->rawMaterial)->name }}
                                </td>
                                <td class="p-4 text-right text-gray-500 dark:text-gray-400">
                                    {{ rtrim(rtrim($item->estimated_qty, '0'), '.') }}
                                </td>
                                <td class="p-4 text-right font-bold text-gray-800 dark:text-white">
                                    {{ rtrim(rtrim($item->actual_qty, '0'), '.') }}

                                    @if($item->actual_qty > $item->estimated_qty)
                                        <span class="text-xs text-red-500 block">Wastage: +{{ rtrim(rtrim($item->actual_qty - $item->estimated_qty, '0'), '.') }}</span>
                                    @elseif($item->actual_qty < $item->estimated_qty)
                                        <span class="text-xs text-green-500 block">Savings: -{{ rtrim(rtrim($item->estimated_qty - $item->actual_qty, '0'), '.') }}</span>
                                    @endif
                                </td>
                                <td class="p-4 text-right text-gray-500 dark:text-gray-400">
                                    ৳ {{ number_format($item->unit_cost, 2) }}
                                </td>
                                <td class="p-4 text-right font-medium text-gray-800 dark:text-white">
                                    ৳ {{ number_format($item->subtotal_cost, 2) }}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50 dark:bg-slate-700/50">
                        <tr>
                            <td colspan="4" class="p-4 text-right font-bold text-gray-700 dark:text-gray-300">Total Material Cost:</td>
                            <td class="p-4 text-right font-bold text-red-500 text-lg">৳ {{ number_format($production->total_cost, 2) }}</td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <style>
                @media print {
                    body { visibility: hidden; }
                    .max-w-5xl { visibility: visible; position: absolute; left: 0; top: 0; width: 100%; padding: 0;}
                    button, a { display: none !important; }
                    .dark\:bg-slate-800 { background-color: #fff !important; color: #000 !important; }
                    .dark\:text-white { color: #000 !important; }
                    .shadow-sm { box-shadow: none !important; border: 1px solid #ddd; }
                }
            </style>

        </div>
    </div>
</x-app-layout>
