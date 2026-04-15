<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="font-black text-2xl text-gray-800 dark:text-gray-100 leading-tight flex items-center gap-2">
                    <i class="fa-solid fa-shop text-indigo-600"></i> Main Store Inventory
                </h2>
                <p class="text-sm text-gray-500 mt-1">Monitor and manage products in Location: Main Store (4)</p>
            </div>
            <div class="flex gap-2">
                <button onclick="window.print()" class="bg-white dark:bg-slate-700 border border-gray-200 dark:border-slate-600 text-gray-700 dark:text-gray-200 px-4 py-2 rounded-xl text-sm font-bold shadow-sm hover:bg-gray-50 transition-all">
                    <i class="fa-solid fa-print mr-2"></i> Print Report
                </button>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white dark:bg-slate-800 p-6 rounded-3xl shadow-sm border border-gray-100 dark:border-slate-700">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-indigo-50 dark:bg-indigo-900/30 rounded-2xl flex items-center justify-center text-indigo-600">
                        <i class="fa-solid fa-boxes-stacked text-xl"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase">Total Items</p>
                        <h4 class="text-2xl font-black text-gray-800 dark:text-white">{{ number_format($stocks->sum('quantity')) }}</h4>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-slate-800 p-6 rounded-3xl shadow-sm border border-gray-100 dark:border-slate-700">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-amber-50 dark:bg-amber-900/30 rounded-2xl flex items-center justify-center text-amber-600">
                        <i class="fa-solid fa-money-bill-trend-up text-xl"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase">Total Stock Value (Cost)</p>
                        <h4 class="text-2xl font-black text-gray-800 dark:text-white">৳{{ number_format($stocks->sum(fn($s) => $s->quantity * $s->unit_cost), 2) }}</h4>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-slate-800 p-6 rounded-3xl shadow-sm border border-gray-100 dark:border-slate-700">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-emerald-50 dark:bg-emerald-900/30 rounded-2xl flex items-center justify-center text-emerald-600">
                        <i class="fa-solid fa-hand-holding-dollar text-xl"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase">Potential Sale Value</p>
                        <h4 class="text-2xl font-black text-gray-800 dark:text-white">৳{{ number_format($stocks->sum(fn($s) => $s->quantity * $s->retail_price), 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-700">
            <form action="{{ route('inventory.store_stock') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="md:col-span-2">
                    <label class="text-[10px] font-bold text-gray-400 uppercase mb-1 block px-1 text-xs">Search Product</label>
                    <div class="relative">
                        <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, category or batch..."
                               class="w-full pl-11 pr-4 py-3 border-gray-100 dark:border-slate-700 dark:bg-slate-900 rounded-2xl text-sm focus:ring-indigo-500 focus:border-indigo-500 transition-all shadow-sm">
                    </div>
                </div>

                <div>
                    <label class="text-[10px] font-bold text-gray-400 uppercase mb-1 block px-1 text-xs">Category Filter</label>
                    <select name="category_id" class="w-full border-gray-100 dark:border-slate-700 dark:bg-slate-900 rounded-2xl py-3 text-sm focus:ring-indigo-500 shadow-sm">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 bg-gray-900 dark:bg-indigo-600 hover:bg-black dark:hover:bg-indigo-700 text-white font-bold py-3 rounded-2xl text-sm shadow-md transition-all">
                        Apply Filters
                    </button>
                    <a href="{{ route('inventory.store_stock') }}" class="px-5 py-3 bg-gray-100 dark:bg-slate-700 text-gray-500 rounded-2xl hover:bg-gray-200 transition-all shadow-sm">
                        <i class="fa-solid fa-arrows-rotate"></i>
                    </a>
                </div>
            </form>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                    <tr class="bg-gray-50/80 dark:bg-slate-700/50 text-gray-400 dark:text-gray-400 text-[10px] font-black uppercase tracking-widest border-b border-gray-100 dark:border-slate-700">
                        <th class="py-5 px-6">Product Details</th>
                        <th class="py-5 px-6">Batch & Status</th>
                        <th class="py-5 px-6 text-center">Stock Quantity</th>
                        <th class="py-5 px-6 text-right">Pricing (Per Unit)</th>
                        <th class="py-5 px-6 text-right">Inventory Value</th>
                        <th class="py-5 px-6 text-center">Profit Margin</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-slate-700/50">
                    @forelse($stocks as $stock)
                        @php
                            $totalValue = $stock->quantity * $stock->unit_cost;
                            $margin = $stock->retail_price - $stock->unit_cost;
                            $marginPercent = $stock->unit_cost > 0 ? ($margin / $stock->unit_cost) * 100 : 0;
                        @endphp
                        <tr class="group hover:bg-indigo-50/30 dark:hover:bg-indigo-900/10 transition-all">
                            <td class="py-5 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900/40 rounded-xl flex items-center justify-center text-indigo-600 font-black text-xs">
                                        {{ substr($stock->product->name, 0, 2) }}
                                    </div>
                                    <div>
                                        <div class="font-black text-gray-800 dark:text-gray-200 text-sm group-hover:text-indigo-600 transition-colors uppercase tracking-tight">{{ $stock->product->name }}</div>
                                        <div class="text-[10px] text-gray-400 font-bold uppercase tracking-tighter">{{ $stock->product->category->name ?? 'No Category' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-5 px-6">
                                <div class="flex flex-col gap-1.5">
                                    <span class="text-[10px] w-fit font-mono font-bold text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/30 px-2 py-0.5 rounded border border-indigo-100 dark:border-indigo-800">
                                        {{ $stock->batch_no ?? 'UNBATCHED' }}
                                    </span>
                                    @if($stock->quantity <= 0)
                                        <span class="text-[9px] w-fit font-black bg-red-100 text-red-600 px-2 py-0.5 rounded-full uppercase">Out of Stock</span>
                                    @elseif($stock->quantity <= 10)
                                        <span class="text-[9px] w-fit font-black bg-amber-100 text-amber-600 px-2 py-0.5 rounded-full uppercase italic animate-pulse">Low Stock</span>
                                    @else
                                        <span class="text-[9px] w-fit font-black bg-emerald-100 text-emerald-600 px-2 py-0.5 rounded-full uppercase">In Stock</span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-5 px-6 text-center">
                                <div class="text-xl font-black text-gray-800 dark:text-white leading-none">
                                    {{ number_format($stock->quantity) }}
                                </div>
                                <span class="text-[9px] text-gray-400 uppercase font-black tracking-widest">{{ $stock->product->unit->short_name }}</span>
                            </td>
                            <td class="py-5 px-6 text-right">
                                <div class="text-[10px] font-bold text-gray-400 uppercase">Cost: ৳{{ number_format($stock->unit_cost, 2) }}</div>
                                <div class="text-sm font-black text-emerald-600">Sale: ৳{{ number_format($stock->retail_price, 2) }}</div>
                                <div class="text-[9px] text-blue-500 font-bold">WS: ৳{{ number_format($stock->wholesale_price, 2) }}</div>
                            </td>
                            <td class="py-5 px-6 text-right">
                                <div class="text-sm font-black text-gray-800 dark:text-gray-100">৳{{ number_format($totalValue, 2) }}</div>
                                <div class="text-[9px] text-gray-400 font-bold uppercase tracking-tighter">Asset Value</div>
                            </td>
                            <td class="py-5 px-6 text-center">
                                <div class="inline-flex flex-col items-center bg-gray-50 dark:bg-slate-900 p-2 rounded-2xl min-w-[80px] border border-gray-100 dark:border-slate-700">
                                    <span class="text-xs font-black {{ $margin > 0 ? 'text-emerald-500' : 'text-red-500' }}">
                                        {{ $margin > 0 ? '+' : '' }}৳{{ number_format($margin, 2) }}
                                    </span>
                                    <div class="w-full bg-gray-200 dark:bg-slate-700 h-1 rounded-full mt-1 overflow-hidden">
                                        <div class="bg-emerald-500 h-full" style="width: {{ min($marginPercent, 100) }}%"></div>
                                    </div>
                                    <span class="text-[9px] font-black text-gray-500 mt-1">
                                        {{ number_format($marginPercent, 1) }}%
                                    </span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-32 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-20 h-20 bg-gray-50 dark:bg-slate-900 rounded-full flex items-center justify-center mb-4">
                                        <i class="fa-solid fa-box-open text-3xl text-gray-200"></i>
                                    </div>
                                    <p class="text-gray-400 font-bold uppercase text-xs tracking-widest">No Inventory Data Found</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            @if($stocks->hasPages())
                <div class="p-6 bg-gray-50/50 dark:bg-slate-700/30 border-t border-gray-100 dark:border-slate-700">
                    {{ $stocks->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
