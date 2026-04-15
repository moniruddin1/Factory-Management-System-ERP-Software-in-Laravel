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
        }

        /* Select2 Dark Mode */
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
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Item Ledger (Transaction History)</h2>
                    <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">Track IN and OUT movements for specific items</p>
                </div>
                <div class="flex items-center gap-3">
                    <button type="button" onclick="window.print()" class="bg-white dark:bg-[#1e293b] border border-gray-200 dark:border-slate-700 hover:bg-gray-50 dark:hover:bg-slate-800 text-gray-700 dark:text-slate-300 px-4 py-2 rounded-lg text-sm font-medium transition shadow-sm flex items-center">
                        <i class="fa-solid fa-print mr-2"></i> Print Ledger
                    </button>
                </div>
            </div>

            <div class="bg-white dark:bg-[#1e293b] rounded-xl shadow-sm border border-gray-200 dark:border-slate-700/50 overflow-hidden mb-6 no-print">
                <form method="GET" action="{{ route('inventory.ledger') }}" class="p-5 bg-gray-50/50 dark:bg-[#0f172a]/30">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Select Product <span class="text-rose-500">*</span></label>
                            <select name="product_id" id="product_select" class="w-full text-sm" required>
                                <option value="">Search Product...</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                                        {{ $product->name }} ({{ $product->code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Start Date</label>
                            <input type="date" name="start_date" value="{{ request('start_date') }}" class="bg-white dark:bg-[#0f172a] border border-gray-300 dark:border-slate-700 text-gray-700 dark:text-slate-300 text-sm rounded-lg focus:ring-blue-500 block w-full p-2">
                        </div>

                        <div class="flex gap-2">
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">End Date</label>
                                <input type="date" name="end_date" value="{{ request('end_date') }}" class="bg-white dark:bg-[#0f172a] border border-gray-300 dark:border-slate-700 text-gray-700 dark:text-slate-300 text-sm rounded-lg focus:ring-blue-500 block w-full p-2">
                            </div>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition shadow-sm h-[38px] mt-6">
                                Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div id="printable-area">
                @if($selectedProduct)
                    <div class="bg-white dark:bg-[#1e293b] p-5 border-b border-gray-200 dark:border-slate-700 flex justify-between items-center rounded-t-xl">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $selectedProduct->name }}</h3>
                            <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">Code: {{ $selectedProduct->code }} | Type: {{ $selectedProduct->type }}</p>
                        </div>
                        @if(request('start_date') || request('end_date'))
                            <div class="text-right text-sm text-gray-500 dark:text-slate-400 bg-gray-100 dark:bg-slate-800 px-3 py-1.5 rounded">
                                Date Range: {{ request('start_date', 'Start') }} to {{ request('end_date', 'Today') }}
                            </div>
                        @endif
                    </div>

                    <div class="bg-white dark:bg-[#1e293b] shadow-sm border border-t-0 border-gray-200 dark:border-slate-700/50 rounded-b-xl overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm text-gray-600 dark:text-slate-300">
                                <thead class="text-xs text-gray-500 dark:text-slate-400 uppercase bg-gray-50 dark:bg-[#1e293b] border-b border-gray-200 dark:border-slate-700">
                                <tr>
                                    <th class="px-6 py-4">Date</th>
                                    <th class="px-6 py-4">Ref Type & No</th>
                                    <th class="px-6 py-4">Location</th>
                                    <th class="px-6 py-4">Type</th>
                                    <th class="px-6 py-4 text-center">IN Qty</th>
                                    <th class="px-6 py-4 text-center">OUT Qty</th>
                                    <th class="px-6 py-4 text-right">Unit Cost</th>
                                </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-slate-700/50">
                                @forelse($transactions as $transaction)
                                    @php
                                        // সহজ লজিক: কোন কোন টাইপগুলো স্টকে মাল বাড়ায় (IN)
                                        $inTypes = ['in', 'purchase', 'issue_to_production_in', 'production_return_in', 'finished_good_in'];
                                        $isIncoming = in_array(strtolower($transaction->transaction_type), $inTypes);
                                    @endphp
                                    <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/20">
                                        <td class="px-6 py-4">{{ \Carbon\Carbon::parse($transaction->date)->format('d M, Y') }}</td>
                                        <td class="px-6 py-4">
            <span class="font-medium text-gray-900 dark:text-white capitalize">
                {{ str_replace('_', ' ', $transaction->reference_type) }}
            </span>
                                            <p class="text-xs text-gray-500 dark:text-slate-400">#{{ $transaction->reference_id ?? 'N/A' }}</p>
                                        </td>
                                        <td class="px-6 py-4">
            <span class="px-2 py-1 bg-gray-100 dark:bg-slate-800 rounded text-xs">
                {{ optional($transaction->location)->name ?? 'Main Store' }}
            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($isIncoming)
                                                <span class="bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400 px-2 py-1 rounded text-xs font-medium">IN</span>
                                            @else
                                                <span class="bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-400 px-2 py-1 rounded text-xs font-medium">OUT</span>
                                            @endif
                                            <p class="text-[10px] text-gray-400 mt-1 uppercase">{{ str_replace('_', ' ', $transaction->transaction_type) }}</p>
                                        </td>

                                        <td class="px-6 py-4 text-center font-bold text-emerald-600 dark:text-emerald-400">
                                            {{ $isIncoming ? number_format($transaction->quantity, 2) : '-' }}
                                        </td>

                                        <td class="px-6 py-4 text-center font-bold text-rose-600 dark:text-rose-400">
                                            {{ !$isIncoming ? number_format($transaction->quantity, 2) : '-' }}
                                        </td>

                                        <td class="px-6 py-4 text-right font-mono">৳ {{ number_format($transaction->unit_cost, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-12 text-center text-gray-500 dark:text-slate-400">
                                            <i class="fa-solid fa-file-invoice text-4xl mb-3 opacity-50"></i>
                                            <p class="text-base font-medium mt-2">No transactions found for this period.</p>
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if(isset($transactions) && $transactions->hasPages())
                            <div class="px-6 py-4 border-t border-gray-200 dark:border-slate-700 bg-gray-50/50 dark:bg-transparent no-print">
                                {{ $transactions->appends(request()->query())->links() }}
                            </div>
                        @endif
                    </div>
                @else
                    <div class="bg-white dark:bg-[#1e293b] rounded-xl shadow-sm border border-gray-200 dark:border-slate-700/50 p-12 text-center no-print">
                        <i class="fa-solid fa-magnifying-glass text-4xl text-gray-300 dark:text-slate-600 mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Select a Product</h3>
                        <p class="text-gray-500 dark:text-slate-400 mt-2">Please select a product from the dropdown above to view its transaction ledger.</p>
                    </div>
                @endif
            </div>

        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#product_select').select2({
                placeholder: "Search and select a product...",
                allowClear: true
            });
        });
    </script>
</x-app-layout>
