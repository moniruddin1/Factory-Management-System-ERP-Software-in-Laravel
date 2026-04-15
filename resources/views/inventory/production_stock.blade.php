<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-gray-800 dark:text-white flex items-center gap-2">
            <i class="fa-solid fa-industry text-orange-500"></i> Production Floor (WIP) Inventory
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden">
            <table class="w-full text-left">
                <thead>
                <tr class="bg-gray-50 dark:bg-slate-700/50 text-gray-400 text-[10px] font-black uppercase tracking-widest border-b">
                    <th class="py-4 px-6">Raw Material</th>
                    <th class="py-4 px-6">Category</th>
                    <th class="py-4 px-6 text-center">In Production Qty</th>
                    <th class="py-4 px-6 text-right">Action</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-slate-700">
                @forelse($summaryStocks as $stock)
                    <tr class="hover:bg-orange-50/30 transition-all">
                        <td class="py-4 px-6 font-bold text-gray-800 dark:text-gray-200">
                            {{ $stock->product->name }}
                        </td>
                        <td class="py-4 px-6 text-xs text-gray-500 uppercase">
                            {{ $stock->product->category->name ?? 'N/A' }}
                        </td>
                        <td class="py-4 px-6 text-center">
                            <span class="text-lg font-black text-orange-600">{{ number_format($stock->total_qty) }}</span>
                            <span class="text-[10px] text-gray-400 font-bold uppercase">{{ $stock->product->unit->short_name }}</span>
                        </td>
                        <td class="py-4 px-6 text-right">
                            <a href="{{ route('inventory.production_item_details', $stock->product_id) }}"
                               class="inline-flex items-center gap-2 bg-slate-900 text-white px-4 py-2 rounded-xl text-xs font-bold hover:bg-orange-600 transition-all">
                                <i class="fa-solid fa-users-viewfinder"></i> View Breakdown
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-20 text-center text-gray-400 italic">No materials currently on production floor.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
