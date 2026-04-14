<x-app-layout>
    <div class="py-8 bg-gray-50 dark:bg-slate-900 min-h-screen">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">New Production</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Start a new shoe manufacturing batch</p>
                </div>
                <a href="{{ route('productions.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition">
                    <i class="fa-solid fa-arrow-left mr-1"></i> Back to List
                </a>
            </div>

            @if ($errors->any())
                <div class="mb-4 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl">
                    <ul class="list-disc ml-5 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('productions.store') }}" method="POST" x-data="productionForm()">
                @csrf

                <div class="bg-white dark:bg-slate-800 shadow-sm rounded-xl p-6 mb-6 border border-gray-100 dark:border-slate-700">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4 border-b pb-2">Production Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Select BOM (Formula) *</label>
                            <select name="bom_id" required x-model="bomId" @change="fetchBomDetails()" class="w-full bg-white dark:bg-slate-900 text-gray-900 dark:text-white border-gray-300 dark:border-slate-600 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm h-[38px]">
                                <option value="">Select a Formula</option>
                                @foreach($boms as $bom)
                                    <option value="{{ $bom->id }}">{{ $bom->name }} ({{ optional($bom->finishedProduct)->name }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Target Quantity (Pairs) *</label>
                            <input type="number" name="target_quantity" x-model="targetQuantity" @input="calculateEstimatedQty()" required min="1" placeholder="e.g., 100" class="w-full bg-white dark:bg-slate-900 text-gray-900 dark:text-white border-gray-300 dark:border-slate-600 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm h-[38px]">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Production Date *</label>
                            <input type="date" name="production_date" required value="{{ date('Y-m-d') }}" class="w-full bg-white dark:bg-slate-900 text-gray-900 dark:text-white border-gray-300 dark:border-slate-600 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm h-[38px]">
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notes / Remarks</label>
                        <input type="text" name="notes" placeholder="Optional notes regarding this production" class="w-full bg-white dark:bg-slate-900 text-gray-900 dark:text-white border-gray-300 dark:border-slate-600 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm h-[38px]">
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-800 shadow-sm rounded-xl p-6 border border-gray-100 dark:border-slate-700" x-show="materials.length > 0" style="display: none;">
                    <div class="flex justify-between items-end mb-4 border-b pb-2">
                        <h3 class="text-lg font-bold text-gray-800 dark:text-white">Required Raw Materials</h3>
                        <p class="text-sm text-indigo-600 dark:text-indigo-400 font-medium bg-indigo-50 dark:bg-indigo-900/30 px-3 py-1 rounded-lg">Adjust "Actual Used" if there is wastage or savings.</p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                            <tr class="bg-gray-50 dark:bg-slate-700/50 text-gray-500 dark:text-gray-400 text-xs uppercase tracking-wider">
                                <th class="p-3 font-medium border-b border-gray-100 dark:border-slate-700 w-1/3">Raw Material</th>
                                <th class="p-3 font-medium border-b border-gray-100 dark:border-slate-700 w-1/4">Estimated Need (Formula)</th>
                                <th class="p-3 font-medium border-b border-gray-100 dark:border-slate-700 w-1/4">Actual Used (Reality) *</th>
                                <th class="p-3 font-medium border-b border-gray-100 dark:border-slate-700 w-1/6">Unit</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-slate-700 text-sm">
                            <template x-for="(material, index) in materials" :key="index">
                                <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/20 transition-colors">
                                    <td class="p-3 text-gray-800 dark:text-gray-200 font-medium">
                                        <span x-text="material.name"></span>
                                        <input type="hidden" :name="`items[${index}][raw_material_id]`" :value="material.raw_material_id">
                                        <input type="hidden" :name="`items[${index}][estimated_qty]`" :value="material.estimated_qty">
                                    </td>
                                    <td class="p-3">
                                        <span class="font-bold text-gray-600 dark:text-gray-400" x-text="material.estimated_qty"></span>
                                    </td>
                                    <td class="p-3">
                                        <input type="number" step="0.0001" :name="`items[${index}][actual_qty]`" x-model="material.actual_qty" required min="0" class="w-full bg-white dark:bg-slate-900 text-gray-900 dark:text-white border-gray-300 dark:border-slate-600 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm h-[38px] text-right">
                                    </td>
                                    <td class="p-3 text-gray-500 dark:text-gray-400" x-text="material.unit_name"></td>
                                </tr>
                            </template>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-xl shadow-md text-sm font-medium transition-colors flex items-center">
                            <i class="fa-solid fa-industry mr-2"></i> Complete Production
                        </button>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-800 shadow-sm rounded-xl p-10 border border-gray-100 dark:border-slate-700 text-center" x-show="materials.length === 0">
                    <i class="fa-solid fa-box-open text-4xl text-gray-300 dark:text-slate-600 mb-3"></i>
                    <p class="text-gray-500 dark:text-gray-400 font-medium">Select a Formula (BOM) and target quantity to load raw materials.</p>
                </div>

            </form>

        </div>
    </div>

    <script>
        function productionForm() {
            return {
                bomId: '',
                targetQuantity: 1,
                bomItems: [], // Raw items directly from API (amount needed for 1 pair)
                materials: [], // Calculated items for the UI

                async fetchBomDetails() {
                    if (!this.bomId) {
                        this.materials = [];
                        this.bomItems = [];
                        return;
                    }

                    try {
                        let response = await fetch(`/productions/get-bom/${this.bomId}`);
                        let data = await response.json();

                        this.bomItems = data.items;
                        this.calculateEstimatedQty();
                    } catch (error) {
                        console.error('Error fetching BOM:', error);
                    }
                },

                calculateEstimatedQty() {
                    if (!this.bomItems.length || !this.targetQuantity) return;

                    let qty = parseFloat(this.targetQuantity) || 0;

                    this.materials = this.bomItems.map(item => {
                        let estimated = (parseFloat(item.quantity) * qty).toFixed(4);
                        return {
                            raw_material_id: item.raw_material_id,
                            name: item.raw_material.name,
                            unit_name: item.unit ? item.unit.name : 'Unit',
                            estimated_qty: estimated,
                            actual_qty: estimated // Default actual to estimated, user can change this
                        };
                    });
                }
            }
        }
    </script>
</x-app-layout>
