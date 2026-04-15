<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="font-black text-xl text-gray-800 dark:text-white">
                    <span class="text-orange-500">{{ $product->name }}</span> - Possession Breakdown
                </h2>
                <p class="text-xs text-gray-500">List of staff holding this item on production floor</p>
            </div>
            <a href="{{ route('inventory.production_stock') }}" class="bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-gray-300 px-4 py-2 rounded-xl text-xs font-bold hover:bg-gray-200 transition-all">
                <i class="fa-solid fa-arrow-left mr-1"></i> Back to Summary
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 dark:bg-slate-700/50">
                <tr class="text-[10px] font-black uppercase text-gray-400 tracking-widest border-b">
                    <th class="py-4 px-6">Staff Name</th>
                    <th class="py-4 px-6 text-center">Batch No</th>
                    <th class="py-4 px-6 text-center">Issued On</th>
                    <th class="py-4 px-6 text-center">Voucher No</th>
                    <th class="py-4 px-6 text-right">Held Quantity</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-slate-700/50">
                @forelse($staffStocks as $item)
                    <tr class="hover:bg-blue-50/30 dark:hover:bg-blue-900/10 transition-all">
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-orange-100 dark:bg-orange-900/30 rounded-full flex items-center justify-center text-orange-600 text-xs font-bold">
                                    {{ substr($item->staff_name, 0, 1) }}
                                </div>
                                <div class="font-bold text-gray-800 dark:text-gray-200 uppercase text-sm">{{ $item->staff_name }}</div>
                            </div>
                        </td>
                        <td class="py-4 px-6 text-center">
                            <span class="text-xs font-mono font-bold bg-slate-100 dark:bg-slate-900 px-2 py-1 rounded text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                                {{ $item->batch_no }}
                            </span>
                        </td>
                        <td class="py-4 px-6 text-center text-sm text-gray-600 dark:text-gray-400 font-medium">
                            {{ date('d M, Y', strtotime($item->issue_date)) }}
                        </td>
                        <td class="py-4 px-6 text-center">
                            <span class="text-xs font-bold text-indigo-500 bg-indigo-50 dark:bg-indigo-900/30 px-2 py-1 rounded">
                                #{{ $item->issue_ref }}
                            </span>
                        </td>
                        <td class="py-4 px-6 text-right">
                            <span class="text-lg font-black text-gray-900 dark:text-white">{{ number_format($item->quantity) }}</span>
                            <span class="text-[10px] text-gray-400 font-bold uppercase">{{ $product->unit->short_name }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-10 text-center text-gray-400">No staff breakdown found.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
