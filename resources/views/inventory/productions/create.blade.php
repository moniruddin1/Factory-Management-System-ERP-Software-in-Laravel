<x-app-layout>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <style>
        .select2-container .select2-selection--single { height: 42px; border: 1px solid #d1d5db; border-radius: 0.5rem; display: flex; align-items: center; }
        .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 42px; color: #374151; padding-left: 10px; width: 100%; }
        .select2-container--default .select2-selection--single .select2-selection__arrow { height: 40px; }

        /* Dark Mode Styles */
        .dark .select2-container--default .select2-selection--single { background-color: #0f172a; border-color: #334155; }
        .dark .select2-container--default .select2-selection--single .select2-selection__rendered { color: #f8fafc; }
        .dark .select2-dropdown { background-color: #1e293b; border-color: #334155; }
        .dark .select2-results__option { color: #cbd5e1; }
        .dark .select2-results__option[aria-selected=true] { background-color: #334155; }
        .dark .select2-container--default .select2-results__option--highlighted[aria-selected] { background-color: #3b82f6; color: white; }
        .dark .select2-search input { background-color: #0f172a; border-color: #334155; color: white; border-radius: 4px;}
    </style>

    <div class="py-8 bg-gray-50 dark:bg-[#0f172a] min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Final Production Entry</h2>
                    <p class="text-sm text-gray-500 dark:text-slate-400">Reconcile WIP materials and add finished goods to stock</p>
                </div>
                <a href="{{ route('productions.index') }}" class="bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-700 px-4 py-2 rounded-lg text-sm font-medium transition">
                    <i class="fa-solid fa-arrow-left mr-1"></i> Back to List
                </a>
            </div>

            @if ($errors->any())
                <div class="mb-4 bg-rose-50 border border-rose-200 text-rose-600 px-4 py-3 rounded-lg">
                    <ul class="list-disc ml-5 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('productions.store') }}" method="POST" x-data="productionForm()" x-init="initSelect2()">
                @csrf

                <div class="bg-white dark:bg-[#1e293b] shadow-sm rounded-xl p-6 mb-6 border border-gray-100 dark:border-slate-700/50">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4 border-b border-gray-100 dark:border-slate-700 pb-2">Production References</h3>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Issue Voucher (WIP) <span class="text-rose-500">*</span></label>
                            <select id="issue_id_select" name="production_issue_id" required class="w-full text-sm">
                                <option value="">Search Voucher / Staff...</option>
                                @foreach($issues as $issue)
                                    @php
                                        $staffName = \App\Models\Staff::find($issue->issued_to)->name ?? 'Unknown Staff';
                                    @endphp
                                    <option value="{{ $issue->id }}">
                                        {{ $issue->voucher_no }} ({{ $staffName }}) - {{ \Carbon\Carbon::parse($issue->date)->format('d M') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Select BOM (Formula) <span class="text-rose-500">*</span></label>
                            <select id="bom_id_select" name="bom_id" required class="w-full text-sm">
                                <option value="">Search Formula...</option>
                                @foreach($boms as $bom)
                                    <option value="{{ $bom->id }}">{{ $bom->name }} ({{ optional($bom->finishedProduct)->name }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Target Qty (Pairs) <span class="text-rose-500">*</span></label>
                            <input type="number" name="target_quantity" required x-model="targetQuantity" @input="calculateMaterials()" min="1" placeholder="e.g. 100" class="w-full bg-white dark:bg-[#0f172a] text-gray-900 dark:text-white border-gray-300 dark:border-slate-600 rounded-lg text-sm p-2.5 h-[42px]">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Production Date <span class="text-rose-500">*</span></label>
                            <input type="date" name="production_date" required value="{{ date('Y-m-d') }}" class="w-full bg-white dark:bg-[#0f172a] text-gray-900 dark:text-white border-gray-300 dark:border-slate-600 rounded-lg text-sm p-2.5 h-[42px]">
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-[#1e293b] shadow-sm rounded-xl p-6 border border-gray-100 dark:border-slate-700/50" x-show="materials.length > 0" style="display: none;">
                    <div class="flex justify-between items-end mb-4 border-b border-gray-100 dark:border-slate-700 pb-2">
                        <h3 class="text-lg font-bold text-gray-800 dark:text-white">Material Reconciliation</h3>
                        <p class="text-xs text-slate-500">Compare Issued WIP vs Estimated Need</p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                            <tr class="bg-gray-50 dark:bg-slate-800/50 text-gray-500 dark:text-slate-400 text-xs uppercase tracking-wider">
                                <th class="p-3 border-b border-gray-100 dark:border-slate-700 w-1/4">Raw Material</th>
                                <th class="p-3 border-b border-gray-100 dark:border-slate-700 w-1/5 text-center bg-blue-50 dark:bg-blue-900/10 text-blue-600 dark:text-blue-400">Issued (WIP)</th>
                                <th class="p-3 border-b border-gray-100 dark:border-slate-700 w-1/5 text-center bg-emerald-50 dark:bg-emerald-900/10 text-emerald-600 dark:text-emerald-400">Estimated (BOM)</th>
                                <th class="p-3 border-b border-gray-100 dark:border-slate-700 w-1/5 text-center">Actual Consumed *</th>
                                <th class="p-3 border-b border-gray-100 dark:border-slate-700 w-1/5 text-right">Status</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-slate-700 text-sm">
                            <template x-for="(mat, index) in materials" :key="index">
                                <tr class="hover:bg-gray-50 dark:hover:bg-slate-800 transition-colors">
                                    <td class="p-3">
                                        <span class="font-medium text-gray-800 dark:text-white" x-text="mat.name"></span>
                                        <input type="hidden" :name="`items[${index}][raw_material_id]`" :value="mat.product_id">
                                        <input type="hidden" :name="`items[${index}][estimated_qty]`" :value="mat.estimated_qty">
                                    </td>

                                    <td class="p-3 text-center bg-blue-50/50 dark:bg-blue-900/5">
                                        <span class="font-bold text-blue-600 dark:text-blue-400" x-text="mat.issued_qty || '0'"></span>
                                    </td>

                                    <td class="p-3 text-center bg-emerald-50/50 dark:bg-emerald-900/5" title="BOM Formula × Target Qty">
                                        <span class="font-bold text-emerald-600 dark:text-emerald-400" x-text="mat.estimated_qty || '0'"></span>
                                    </td>

                                    <td class="p-3">
                                        <input type="number" step="0.01" :name="`items[${index}][actual_qty]`" x-model="mat.actual_qty" required min="0" class="w-full bg-white dark:bg-[#0f172a] text-gray-900 dark:text-white border-gray-300 dark:border-slate-600 rounded-lg text-sm p-2 text-center font-bold">
                                    </td>

                                    <td class="p-3 text-right">
                                        <template x-if="parseFloat(mat.actual_qty) < parseFloat(mat.issued_qty)">
                                                <span class="text-xs bg-emerald-100 text-emerald-700 px-2 py-1 rounded dark:bg-emerald-900/30 dark:text-emerald-400" title="Will be returned to Main Godown">
                                                    Returns: <span x-text="(parseFloat(mat.issued_qty) - parseFloat(mat.actual_qty)).toFixed(2)"></span>
                                                </span>
                                        </template>
                                        <template x-if="parseFloat(mat.actual_qty) > parseFloat(mat.issued_qty)">
                                                <span class="text-xs bg-rose-100 text-rose-700 px-2 py-1 rounded dark:bg-rose-900/30 dark:text-rose-400" title="Will be deducted from Main Godown (FIFO)">
                                                    Extra: <span x-text="(parseFloat(mat.actual_qty) - parseFloat(mat.issued_qty)).toFixed(2)"></span>
                                                </span>
                                        </template>
                                        <template x-if="parseFloat(mat.actual_qty) === parseFloat(mat.issued_qty)">
                                            <span class="text-xs text-gray-400">Exact Match</span>
                                        </template>
                                    </td>
                                </tr>
                            </template>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 border-t border-gray-100 dark:border-slate-700 pt-4 flex justify-between items-center">
                        <div class="text-sm text-slate-500">
                            <i class="fa-solid fa-circle-info text-blue-500"></i> Extra materials will be deducted via FIFO. Unused materials will return to Main Godown.
                        </div>
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-lg text-sm font-medium transition shadow-sm flex items-center">
                            <i class="fa-solid fa-check-double mr-2"></i> Submit Final Production
                        </button>
                    </div>
                </div>

                <div class="bg-white dark:bg-[#1e293b] shadow-sm rounded-xl p-10 border border-gray-100 dark:border-slate-700/50 text-center" x-show="materials.length === 0">
                    <i class="fa-solid fa-clipboard-list text-4xl text-gray-300 dark:text-slate-600 mb-3"></i>
                    <p class="text-gray-500 dark:text-slate-400 font-medium">Select an Issue Voucher and Formula to load reconciliation data.</p>
                </div>
            </form>
        </div>
    </div>

    <script>
        function productionForm() {
            return {
                issueId: '',
                bomId: '',
                targetQuantity: 1,
                issuedData: [],
                bomData: [],
                materials: [],

                // Initialize Select2 and bind with Alpine variables
                initSelect2() {
                    let self = this;

                    // Init Voucher Select
                    $('#issue_id_select').select2({
                        placeholder: "Search Voucher / Staff...",
                        allowClear: true
                    }).on('change', function() {
                        self.issueId = $(this).val();
                        self.fetchData();
                    });

                    // Init BOM Select
                    $('#bom_id_select').select2({
                        placeholder: "Search Formula...",
                        allowClear: true
                    }).on('change', function() {
                        self.bomId = $(this).val();
                        self.fetchData();
                    });
                },

                async fetchData() {
                    // Fetch Issue Details if selected
                    if (this.issueId) {
                        try {
                            let response = await fetch(`/productions/get-issue/${this.issueId}`);
                            let data = await response.json();
                            this.issuedData = data.items || [];
                        } catch (e) { console.error('Error fetching issue:', e); }
                    } else {
                        this.issuedData = [];
                    }

                    // Fetch BOM Details if selected
                    if (this.bomId) {
                        try {
                            let response = await fetch(`/productions/get-bom/${this.bomId}`);
                            let data = await response.json();
                            this.bomData = data.items || [];
                        } catch (e) { console.error('Error fetching BOM:', e); }
                    } else {
                        this.bomData = [];
                    }

                    this.calculateMaterials();
                },

                calculateMaterials() {
                    if (!this.bomData.length && !this.issuedData.length) {
                        this.materials = [];
                        return;
                    }

                    let qty = parseFloat(this.targetQuantity) || 0;
                    let mergedMap = new Map();

                    // 1. Process Issued Items
                    this.issuedData.forEach(item => {
                        let productIdStr = String(item.product_id); // String এ কনভার্ট করা হলো
                        mergedMap.set(productIdStr, {
                            product_id: item.product_id,
                            name: item.product ? item.product.name : 'Unknown Material',
                            issued_qty: parseFloat(item.quantity) || 0,
                            estimated_qty: 0,
                            actual_qty: parseFloat(item.quantity) || 0
                        });
                    });

                    // 2. Process BOM Items (merge or add new)
                    this.bomData.forEach(item => {
                        let productIdStr = String(item.raw_material_id); // String এ কনভার্ট করা হলো
                        let estQty = (parseFloat(item.quantity) || 0) * qty;

                        if (mergedMap.has(productIdStr)) {
                            let existing = mergedMap.get(productIdStr);
                            existing.estimated_qty = estQty.toFixed(2);
                        } else {
                            mergedMap.set(productIdStr, {
                                product_id: item.raw_material_id,
                                name: item.raw_material ? item.raw_material.name : 'Unknown Material',
                                issued_qty: 0,
                                estimated_qty: estQty.toFixed(2),
                                actual_qty: estQty.toFixed(2) // Default to estimated if not in voucher
                            });
                        }
                    });

                    this.materials = Array.from(mergedMap.values());
                }
            }
        }
    </script>
</x-app-layout>
