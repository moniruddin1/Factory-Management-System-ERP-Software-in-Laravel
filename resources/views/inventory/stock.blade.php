<x-app-layout>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <style>
        @media print {
            body * { visibility: hidden; }
            #printable-area, #printable-area * { visibility: visible; }
            #printable-area { position: absolute; left: 0; top: 0; width: 100%; }
            .no-print { display: none !important; }
            .details-row { display: table-row !important; } /* প্রিন্ট করার সময় সব ডিটেইলস দেখাবে */
        }

        /* Select2 Dark Mode (আপনার আগের ডিজাইন) */
        .select2-container .select2-selection--single { height: 38px; border: 1px solid #d1d5db; border-radius: 0.5rem; }
        .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 38px; color: #374151; }
        .select2-container--default .select2-selection--single .select2-selection__arrow { height: 36px; }
        .dark .select2-container--default .select2-selection--single { background-color: #0f172a; border-color: #334155; }
        .dark .select2-container--default .select2-selection--single .select2-selection__rendered { color: #f8fafc; }
        .dark .select2-dropdown { background-color: #1e293b; border-color: #334155; }
        .dark .select2-results__option { color: #cbd5e1; }
        .dark .select2-results__option[aria-selected=true] { background-color: #334155; }
        .dark .select2-container--default .select2-results__option--highlighted[aria-selected] { background-color: #3b82f6; }
        .dark .select2-search input { background-color: #0f172a; border-color: #334155; color: white; }
    </style>

    <div class="py-6 bg-gray-50 dark:bg-[#0f172a] min-h-screen font-sans transition-colors duration-200">
        <div class="w-full mx-auto px-4 sm:px-6 lg:px-8">

            <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Inventory Stock Report</h2>
                    <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">Monitor real-time product availability and locations</p>
                </div>
                <div class="flex items-center gap-3">
                    <button type="button" onclick="window.print()" class="bg-white dark:bg-[#1e293b] border border-gray-200 dark:border-slate-700 hover:bg-gray-50 dark:hover:bg-slate-800 text-gray-700 dark:text-slate-300 px-4 py-2 rounded-lg text-sm font-medium transition shadow-sm flex items-center">
                        <i class="fa-solid fa-print mr-2"></i> Print Report
                    </button>
                </div>
            </div>

            <div class="bg-white dark:bg-[#1e293b] rounded-xl shadow-sm border border-gray-200 dark:border-slate-700/50 overflow-hidden">

                <form method="GET" action="{{ route('inventory.stock') }}" id="filter-form" class="p-4 border-b border-gray-200 dark:border-slate-700/50 flex flex-col xl:flex-row justify-between items-center gap-4 no-print bg-gray-50/50 dark:bg-[#0f172a]/30">
                    <div class="flex items-center text-sm text-gray-600 dark:text-slate-300 w-full xl:w-auto">
                        <span>Show</span>
                        <div class="relative mx-2">
                            <select name="per_page" onchange="document.getElementById('filter-form').submit()" class="bg-white dark:bg-[#0f172a] border border-gray-300 dark:border-slate-700 text-gray-700 dark:text-white text-sm rounded-lg focus:ring-blue-500 block w-full px-3 py-1.5 pr-8 cursor-pointer">
                                <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                            </select>
                        </div>
                        <span>entries</span>
                    </div>

                    <div class="flex flex-wrap items-center gap-3 w-full xl:w-auto">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none"><i class="fa-solid fa-search text-gray-400"></i></div>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, code..." class="bg-white dark:bg-[#0f172a] border border-gray-300 dark:border-slate-700 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-blue-500 block pl-9 p-2 w-52 sm:w-64">
                        </div>
                        <button type="submit" class="bg-gray-100 hover:bg-gray-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-gray-700 dark:text-slate-300 px-4 py-2 rounded-lg text-sm font-medium transition">
                            Search
                        </button>
                    </div>
                </form>

                <div id="printable-area">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-gray-600 dark:text-slate-300">
                            <thead class="text-xs text-gray-500 dark:text-slate-400 uppercase bg-gray-50 dark:bg-[#1e293b] border-b border-gray-200 dark:border-slate-700">
                            <tr>
                                <th class="px-6 py-4">#</th>
                                <th class="px-6 py-4 font-medium">Product Details</th>
                                <th class="px-6 py-4 font-medium">Category / Type</th>
                                <th class="px-6 py-4 font-medium text-right">Total Quantity</th>
                                <th class="px-6 py-4 font-medium text-center no-print">Action</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-slate-700/50">
                            @forelse($products as $index => $product)
                                <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/20 group">
                                    <td class="px-6 py-4">{{ $products->firstItem() + $index }}</td>
                                    <td class="px-6 py-4">
                                        <p class="text-gray-900 dark:text-white font-medium text-base">{{ $product->name }}</p>
                                        <p class="text-gray-500 dark:text-slate-400 text-xs mt-0.5">Code: {{ $product->code }}</p>
                                    </td>
                                    <td class="px-6 py-4">
                                            <span class="bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-slate-300 px-2 py-1 rounded text-xs">
                                                {{ optional($product->category)->name ?? 'General' }}
                                            </span>
                                        <p class="text-xs text-gray-500 dark:text-slate-500 mt-1 uppercase">{{ $product->type }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <span class="text-lg font-bold text-emerald-600 dark:text-emerald-400">{{ number_format($product->total_stock, 2) }}</span>
                                        <span class="text-sm text-gray-500 dark:text-slate-400 ml-1">{{ optional($product->unit)->name ?? 'Unit' }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-center no-print">
                                        <button onclick="toggleDetails({{ $product->id }})" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium text-sm flex items-center justify-center mx-auto transition-colors">
                                            <i class="fa-solid fa-eye mr-1.5"></i> View Details
                                        </button>
                                    </td>
                                </tr>

                                <tr id="details-{{ $product->id }}" class="hidden bg-slate-50/50 dark:bg-[#0f172a]/50 details-row">
                                    <td colspan="5" class="px-6 py-4 border-b border-gray-100 dark:border-slate-700/50">
                                        <div class="ml-12 mr-6 border border-gray-200 dark:border-slate-700 rounded-lg overflow-hidden shadow-sm">
                                            <table class="w-full text-left text-xs text-gray-600 dark:text-slate-300">
                                                <thead class="bg-gray-100 dark:bg-slate-800 text-gray-500 dark:text-slate-400 uppercase">
                                                <tr>
                                                    <th class="px-4 py-2">Location / Godown</th>
                                                    <th class="px-4 py-2">Batch / Invoice No.</th>
                                                    <th class="px-4 py-2 text-right">Quantity</th>
                                                </tr>
                                                </thead>
                                                <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                                                @foreach($product->stocks->where('quantity', '>', 0) as $stock)
                                                    <tr class="hover:bg-white dark:hover:bg-slate-700/30">
                                                        <td class="px-4 py-2 font-medium text-gray-800 dark:text-slate-200">
                                                            <i class="fa-solid fa-location-dot text-rose-500 mr-1.5"></i> {{ optional($stock->location)->name ?? 'Unknown Location' }}
                                                        </td>
                                                        <td class="px-4 py-2 text-blue-600 dark:text-blue-400">{{ $stock->batch_no ?? '-' }}</td>
                                                        <td class="px-4 py-2 text-right font-semibold">{{ number_format($stock->quantity, 2) }}</td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-500 dark:text-slate-400">
                                        <i class="fa-solid fa-box-open text-4xl mb-3 opacity-50"></i>
                                        <p class="text-base font-medium mt-2">No stock available currently.</p>
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($products->hasPages())
                        <div class="px-6 py-4 border-t border-gray-200 dark:border-slate-700 bg-gray-50/50 dark:bg-transparent no-print">
                            {{ $products->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>

    <script>
        function toggleDetails(productId) {
            const row = document.getElementById(`details-${productId}`);
            if (row.classList.contains('hidden')) {
                row.classList.remove('hidden');
                // Optional: add a slight transition effect using opacity if desired
            } else {
                row.classList.add('hidden');
            }
        }
    </script>
</x-app-layout>
