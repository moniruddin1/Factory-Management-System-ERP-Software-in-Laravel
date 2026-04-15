<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 dark:text-gray-200 leading-tight flex items-center gap-2">
            <i class="fa-solid fa-truck-ramp-box text-blue-500"></i> Stock Transfer (Production to Store)
        </h2>
    </x-slot>

    <div class="space-y-6">
        @if(session('success'))
            <div class="bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 p-4 rounded-xl border border-emerald-200 dark:border-emerald-800 text-sm font-medium">
                <i class="fa-solid fa-circle-check mr-2"></i> {{ session('success') }}
            </div>
        @endif

        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-700 p-5">
            <div class="mb-6 flex justify-between items-end">
                <div>
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white">Ready Products Inventory</h3>
                    <p class="text-sm text-gray-500">List of batches completed in production and ready for pricing.</p>
                </div>
                <div class="text-right">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest px-3 py-1 bg-gray-100 dark:bg-slate-700 rounded-full">
                        Location: Ready Production (3)
                    </span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                    <tr class="bg-transparent text-gray-500 dark:text-gray-400 text-[11px] font-bold uppercase border-y border-gray-200 dark:border-slate-700">
                        <th class="py-3 px-4">Product Info</th>
                        <th class="py-3 px-4">Production Info</th>
                        <th class="py-3 px-4 text-center">Available Qty</th>
                        <th class="py-3 px-4 text-right">Unit Cost</th>
                        <th class="py-3 px-4 text-right">Total Value</th>
                        <th class="py-3 px-4 text-center">Action</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-slate-700/50">
                    @forelse($stocks as $stock)
                        @php
                            // প্রোডাকশন টেবিল থেকে কস্ট নিয়ে আসা (যদি স্টক টেবিলে নাল থাকে)
                            $actualUnitCost = $stock->unit_cost > 0 ? $stock->unit_cost : ($stock->production->unit_cost ?? 0);
                            $totalBatchValue = $stock->quantity * $actualUnitCost;
                        @endphp
                        <tr class="hover:bg-blue-50/50 dark:hover:bg-slate-800/30 transition-colors text-sm">
                            <td class="py-3 px-4">
                                <div class="font-bold text-gray-800 dark:text-gray-200">{{ $stock->product->name }}</div>
                                <div class="text-[10px] text-gray-400 mt-0.5 uppercase">Cat: {{ $stock->product->category->name ?? 'N/A' }}</div>
                            </td>
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-0.5 rounded bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 font-mono text-[11px] font-bold">
                                        {{ $stock->batch_no }}
                                    </span>
                                    <span class="text-[10px] text-gray-400 font-medium">
                                        {{ $stock->production ? \Carbon\Carbon::parse($stock->production->production_date)->format('d M, Y') : 'N/A' }}
                                    </span>
                                </div>
                                <div class="text-[10px] text-gray-500 mt-1">Ref: {{ $stock->production->reference_no ?? 'N/A' }}</div>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <div class="font-bold text-gray-800 dark:text-white">{{ number_format($stock->quantity) }}</div>
                                <div class="text-[10px] text-gray-400 uppercase tracking-tighter">{{ $stock->product->unit->short_name }}</div>
                            </td>
                            <td class="py-3 px-4 text-right">
                                <div class="font-bold text-gray-700 dark:text-gray-300">৳{{ number_format($actualUnitCost, 2) }}</div>
                                <div class="text-[10px] text-gray-400 italic">Actual Cost</div>
                            </td>
                            <td class="py-3 px-4 text-right">
                                <div class="font-bold text-emerald-600">৳{{ number_format($totalBatchValue, 2) }}</div>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <button onclick="openTransferModal({{ $stock->id }}, '{{ $stock->product->name }}', '{{ $stock->batch_no }}', {{ $stock->quantity }}, {{ $actualUnitCost }}, '{{ $stock->production->reference_no ?? 'N/A' }}')"
                                        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-[11px] font-bold rounded-lg transition-all shadow-sm">
                                    <i class="fa-solid fa-tags"></i> Set Price & Transfer
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-gray-400 italic">
                                <i class="fa-solid fa-box-open text-3xl mb-2 opacity-20"></i>
                                <p>No products ready in production location.</p>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="transferModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-hidden="true">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-gray-500/75 dark:bg-gray-900/80 transition-opacity" onclick="closeTransferModal()"></div>

            <div class="relative bg-white dark:bg-slate-800 rounded-2xl shadow-xl w-full max-w-md overflow-hidden transform transition-all">
                <div class="p-5 border-b border-gray-100 dark:border-slate-700 flex justify-between items-center bg-gray-50 dark:bg-slate-800/80">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white tracking-tight">Set Sales Pricing</h3>
                    <button onclick="closeTransferModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>

                <form action="{{ route('inventory.transfer_to_store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="stock_id" id="modal_stock_id">

                    <div class="p-6 space-y-5">
                        <div class="bg-slate-50 dark:bg-slate-900/50 p-4 rounded-xl border border-slate-100 dark:border-slate-700">
                            <div id="modal_product_info" class="font-bold text-gray-800 dark:text-white text-base"></div>
                            <div class="flex items-center gap-3 mt-1">
                                <span id="modal_batch_info" class="text-[10px] text-blue-500 font-bold font-mono"></span>
                                <span id="modal_ref_info" class="text-[10px] text-gray-400"></span>
                            </div>

                            <div class="mt-4 pt-3 border-t border-slate-200 dark:border-slate-700 flex justify-between items-center">
                                <div class="text-center">
                                    <div class="text-[9px] text-gray-500 uppercase font-bold">In Stock</div>
                                    <div id="modal_stock_display" class="font-bold text-gray-800 dark:text-white"></div>
                                </div>
                                <div class="h-8 w-px bg-slate-200 dark:bg-slate-700"></div>
                                <div class="text-center">
                                    <div class="text-[9px] text-gray-500 uppercase font-bold">Production Cost</div>
                                    <div id="modal_unit_cost_display" class="font-bold text-emerald-600"></div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-slate-300 uppercase tracking-wider mb-2 text-center">Transfer Quantity</label>
                            <input type="number" step="0.01" name="transfer_qty" id="modal_transfer_qty" required
                                   class="w-full bg-white dark:bg-slate-900 border-gray-300 dark:border-slate-700 rounded-xl text-center text-lg font-bold p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-bold text-gray-500 dark:text-slate-400 uppercase mb-2">Wholesale Price</label>
                                <div class="relative group">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 group-focus-within:text-blue-500 transition-colors">৳</span>
                                    <input type="number" step="0.01" name="wholesale_price" required
                                           class="w-full bg-white dark:bg-slate-900 border-gray-300 dark:border-slate-700 rounded-xl text-sm p-3 pl-7 focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-500 dark:text-slate-400 uppercase mb-2">Retail Price</label>
                                <div class="relative group">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 group-focus-within:text-blue-500 transition-colors">৳</span>
                                    <input type="number" step="0.01" name="retail_price" required
                                           class="w-full bg-white dark:bg-slate-900 border-gray-300 dark:border-slate-700 rounded-xl text-sm p-3 pl-7 focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-5 bg-gray-50 dark:bg-slate-800/80 border-t border-gray-100 dark:border-slate-700 flex gap-3">
                        <button type="button" onclick="closeTransferModal()"
                                class="flex-1 px-4 py-3 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 rounded-xl text-gray-700 dark:text-white text-xs font-bold hover:bg-gray-50 transition-colors uppercase tracking-widest">
                            Cancel
                        </button>
                        <button type="submit"
                                class="flex-1 px-4 py-3 bg-blue-600 text-white rounded-xl text-xs font-bold hover:bg-blue-700 shadow-lg shadow-blue-200 dark:shadow-none transition-all uppercase tracking-widest">
                            Confirm Transfer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openTransferModal(stockId, productName, batchNo, maxQty, unitCost, refNo) {
            document.getElementById('modal_stock_id').value = stockId;
            document.getElementById('modal_product_info').innerText = productName;
            document.getElementById('modal_batch_info').innerText = 'BATCH: ' + batchNo;
            document.getElementById('modal_ref_info').innerText = 'REF: ' + refNo;
            document.getElementById('modal_stock_display').innerText = maxQty;

            // Unit cost formatting
            document.getElementById('modal_unit_cost_display').innerText = '৳' + parseFloat(unitCost).toLocaleString(undefined, {minimumFractionDigits: 2});

            document.getElementById('modal_transfer_qty').value = maxQty;
            document.getElementById('modal_transfer_qty').max = maxQty;

            document.getElementById('transferModal').classList.remove('hidden');
        }

        function closeTransferModal() {
            document.getElementById('transferModal').classList.add('hidden');
        }
    </script>
</x-app-layout>
