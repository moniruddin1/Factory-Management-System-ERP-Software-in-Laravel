<x-app-layout>
    <div class="py-8 bg-gray-50 dark:bg-[#0f172a] min-h-screen">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Production Details</h2>
                    <p class="text-sm text-gray-500 dark:text-slate-400">Ref: {{ $production->reference_no }}</p>
                </div>
                <div class="space-x-2 flex">
                    <button onclick="window.print()" class="bg-white dark:bg-[#1e293b] border border-gray-200 dark:border-slate-700 text-gray-700 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-800 px-4 py-2 rounded-lg text-sm font-medium transition flex items-center">
                        <i class="fa-solid fa-print mr-2"></i> Print
                    </button>
                    <a href="{{ route('productions.index') }}" class="bg-white dark:bg-[#1e293b] border border-gray-200 dark:border-slate-700 text-gray-700 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-800 px-4 py-2 rounded-lg text-sm font-medium transition flex items-center">
                        <i class="fa-solid fa-arrow-left mr-2"></i> Back
                    </a>
                </div>
            </div>

            <div class="bg-white dark:bg-[#1e293b] shadow-sm rounded-xl overflow-hidden border border-gray-100 dark:border-slate-700/50 mb-6">
                <div class="p-6 grid grid-cols-2 md:grid-cols-4 gap-6">
                    <div>
                        <p class="text-xs text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1">Production Date</p>
                        <h3 class="text-md font-bold text-gray-800 dark:text-white">{{ \Carbon\Carbon::parse($production->production_date)->format('d M, Y') }}</h3>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1">WIP Voucher</p>
                        <h3 class="text-md font-bold text-blue-600 dark:text-blue-400">{{ $production->issue ? $production->issue->voucher_no : 'N/A' }}</h3>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1">Target Quantity</p>
                        <h3 class="text-md font-bold text-emerald-600 dark:text-emerald-400">{{ rtrim(rtrim($production->target_quantity, '0'), '.') }} Pairs</h3>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1">Finished Product</p>
                        <h3 class="text-md font-bold text-gray-800 dark:text-white">{{ $production->finishedProduct ? $production->finishedProduct->name : 'N/A' }}</h3>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1">Total Material Cost</p>
                        <h3 class="text-md font-bold text-rose-500">৳ {{ number_format($production->total_cost, 2) }}</h3>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1">Cost Per Pair</p>
                        <h3 class="text-md font-bold text-rose-500">৳ {{ number_format($production->unit_cost, 2) }}</h3>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-[#1e293b] shadow-sm rounded-xl overflow-hidden border border-gray-100 dark:border-slate-700/50">
                <div class="p-4 border-b border-gray-100 dark:border-slate-700 bg-gray-50/50 dark:bg-[#0f172a]/50">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white">Material Consumption & Costing</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                        <tr class="bg-gray-50 dark:bg-slate-800/50 text-gray-500 dark:text-slate-400 text-xs uppercase tracking-wider">
                            <th class="p-4 font-medium border-b border-gray-100 dark:border-slate-700">Raw Material</th>
                            <th class="p-4 font-medium border-b border-gray-100 dark:border-slate-700 text-center">Estimated Qty</th>
                            <th class="p-4 font-medium border-b border-gray-100 dark:border-slate-700 text-center">Actual Used</th>
                            <th class="p-4 font-medium border-b border-gray-100 dark:border-slate-700 text-right">Unit Price</th>
                            <th class="p-4 font-medium border-b border-gray-100 dark:border-slate-700 text-right">Subtotal Cost</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-slate-700 text-sm">
                        @foreach($production->items as $item)
                            <tr class="hover:bg-gray-50 dark:hover:bg-slate-800 transition-colors">
                                <td class="p-4 text-gray-900 dark:text-white font-medium">
                                    {{ $item->rawMaterial ? $item->rawMaterial->name : 'Unknown Material' }}
                                </td>
                                <td class="p-4 text-center text-gray-500 dark:text-slate-400">
                                    {{ rtrim(rtrim($item->estimated_qty, '0'), '.') }}
                                </td>
                                <td class="p-4 text-center font-bold text-gray-800 dark:text-white">
                                    {{ rtrim(rtrim($item->actual_qty, '0'), '.') }}

                                    @if($item->actual_qty > $item->estimated_qty)
                                        <span class="text-xs text-rose-500 block font-normal">Extra: +{{ rtrim(rtrim($item->actual_qty - $item->estimated_qty, '0'), '.') }}</span>
                                    @elseif($item->actual_qty < $item->estimated_qty)
                                        <span class="text-xs text-emerald-500 block font-normal">Saved: -{{ rtrim(rtrim($item->estimated_qty - $item->actual_qty, '0'), '.') }}</span>
                                    @endif
                                </td>
                                <td class="p-4 text-right text-gray-500 dark:text-slate-400">
                                    ৳ {{ number_format($item->unit_cost, 2) }}
                                </td>
                                <td class="p-4 text-right font-medium text-gray-800 dark:text-white">
                                    ৳ {{ number_format($item->subtotal_cost, 2) }}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50 dark:bg-slate-800/50">
                        <tr>
                            <td colspan="4" class="p-4 text-right font-bold text-gray-700 dark:text-slate-300">Total Material Cost:</td>
                            <td class="p-4 text-right font-bold text-rose-500 text-lg">৳ {{ number_format($production->total_cost, 2) }}</td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <style>
                @media print {
                    body { visibility: hidden; background: #fff;}
                    .max-w-5xl { visibility: visible; position: absolute; left: 0; top: 0; width: 100%; padding: 0;}
                    button, a { display: none !important; }
                    .dark\:bg-\[\#1e293b\] { background-color: #fff !important; border: 1px solid #ddd; }
                    .dark\:text-white { color: #000 !important; }
                    .shadow-sm { box-shadow: none !important; }
                }
            </style>
        </div>
    </div>
</x-app-layout>
