<x-app-layout>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container .select2-selection--single {
            height: 38px !important;
            border-color: #d1d5db !important;
            border-radius: 0.5rem !important;
            display: flex;
            align-items: center;
        }
        .dark .select2-container .select2-selection--single {
            background-color: #0f172a !important;
            border-color: #475569 !important;
            color: #fff !important;
        }
        .dark .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #e2e8f0 !important;
        }
        .dark .select2-dropdown {
            background-color: #1e293b !important;
            border-color: #475569 !important;
            color: #fff !important;
        }
        .dark .select2-container--default .select2-search--dropdown .select2-search__field {
            background-color: #0f172a !important;
            border-color: #475569 !important;
            color: #fff !important;
        }
        .dark .select2-container--default .select2-results__option[aria-selected=true] {
            background-color: #334155 !important;
        }
    </style>

    @php
        $finishedShoes = $products->where('type', 'finished_good');
        $rawMaterials = $products->where('type', 'raw_material');
    @endphp

    <div class="py-8 bg-gray-50 dark:bg-slate-900 min-h-screen">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Create BOM Formula</h2>
                <a href="{{ route('boms.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition">
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

            <form action="{{ route('boms.store') }}" method="POST">
                @csrf

                <div class="bg-white dark:bg-slate-800 shadow-sm rounded-xl p-6 mb-6 border border-gray-100 dark:border-slate-700">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Finished Product (Shoe) *</label>
                            <select name="finished_product_id" required class="searchable-select w-full bg-white dark:bg-slate-900 text-gray-900 dark:text-white border-gray-300 dark:border-slate-600 rounded-lg text-sm">
                                <option value="">Search & Select Shoe Model</option>
                                @foreach($finishedShoes as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }} {{ $product->code ? '('.$product->code.')' : '' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Formula Name *</label>
                            <input type="text" name="name" required placeholder="e.g., Standard Oxford Recipe" class="w-full bg-white dark:bg-slate-900 text-gray-900 dark:text-white border-gray-300 dark:border-slate-600 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm h-[38px]">
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-800 shadow-sm rounded-xl p-6 border border-gray-100 dark:border-slate-700"
                     x-data="{
                         items: [{ raw_material_id: '', quantity: '', unit_id: '' }],
                         productUnits: {{ $products->mapWithKeys(fn($p) => [$p->id => $p->unit_id])->toJson() }},

                         addItem() {
                             this.items.push({ raw_material_id: '', quantity: '', unit_id: '' });
                             setTimeout(() => { initSelect2ForRawMaterials(); }, 50);
                         },

                         removeItem(index) {
                             this.items.splice(index, 1);
                         },

                         updateUnit(index, value) {
                             this.items[index].raw_material_id = value;
                             this.items[index].unit_id = this.productUnits[value] || '';
                         }
                     }">

                    <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4 border-b pb-2">Raw Materials Required (Per Pair/Unit)</h3>

                    <table class="w-full text-left mb-4">
                        <thead>
                        <tr class="text-sm text-gray-500 dark:text-gray-400 uppercase">
                            <th class="pb-2 w-1/2">Raw Material</th>
                            <th class="pb-2 w-1/4">Quantity</th>
                            <th class="pb-2 w-1/5">Unit</th>
                            <th class="pb-2 text-right">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <template x-for="(item, index) in items" :key="index">
                            <tr>
                                <td class="pr-2 pb-3">
                                    <select :name="`items[${index}][raw_material_id]`"
                                            x-model="item.raw_material_id"
                                            x-init="$nextTick(() => {
                                                    $($el).select2({ width: '100%', placeholder: 'Search Material' })
                                                    .on('change', function(e) {
                                                        updateUnit(index, e.target.value);
                                                    });
                                                })"
                                            required
                                            class="raw-material-select w-full bg-white dark:bg-slate-900 text-gray-900 dark:text-white border-gray-300 dark:border-slate-600 rounded-lg text-sm">
                                        <option value="">Search Material</option>
                                        @foreach($rawMaterials as $product)
                                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="pr-2 pb-3">
                                    <input type="number" step="0.0001" :name="`items[${index}][quantity]`" x-model="item.quantity" required placeholder="0.00" class="w-full h-[38px] bg-white dark:bg-slate-900 text-gray-900 dark:text-white border-gray-300 dark:border-slate-600 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                </td>
                                <td class="pr-2 pb-3">
                                    <select :name="`items[${index}][unit_id]`" x-model="item.unit_id" required class="w-full h-[38px] bg-gray-100 dark:bg-slate-700 text-gray-500 dark:text-gray-400 border-gray-300 dark:border-slate-600 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm pointer-events-none" readonly tabindex="-1">
                                        <option value="">Unit</option>
                                        @foreach($units as $unit)
                                            <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="pb-3 text-right">
                                    <button type="button" @click="removeItem(index)" x-show="items.length > 1" class="text-red-500 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-900/30 p-2 rounded-lg transition mt-1">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                </td>
                            </tr>
                        </template>
                        </tbody>
                    </table>

                    <div class="flex justify-between items-center mt-2 pt-4 border-t border-gray-100 dark:border-slate-700">
                        <button type="button" @click="addItem()" class="text-sm font-medium text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 flex items-center">
                            <i class="fa-solid fa-plus-circle mr-1"></i> Add Another Material
                        </button>

                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-xl shadow-md text-sm font-medium transition-colors">
                            <i class="fa-solid fa-save mr-1"></i> Save Formula
                        </button>
                    </div>

                </div>
            </form>

        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            $('.searchable-select').select2({
                width: '100%',
                placeholder: 'Search & Select Shoe Model'
            });
        });

        function initSelect2ForRawMaterials() {
            $('.raw-material-select').each(function() {
                if (!$(this).hasClass("select2-hidden-accessible")) {
                    $(this).select2({
                        width: '100%',
                        placeholder: 'Search Material'
                    });
                }
            });
        }
    </script>
</x-app-layout>
