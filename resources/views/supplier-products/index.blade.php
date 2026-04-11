<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200">
                    <i class="fa-solid fa-boxes-packing mr-2 text-blue-500"></i> Supplier Items Mapping
                </h2>
            </div>

            @if(session('success'))
                <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-xl p-6 mb-6">
                <div class="max-w-md mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Select Supplier to Map Items <span class="text-red-500">*</span></label>
                    <select onchange="window.location.href='?supplier_id='+this.value"
                            class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">-- Choose a Supplier --</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->code }} - {{ $supplier->company_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <hr class="border-gray-200 dark:border-slate-700 mb-6">

                @if($selectedSupplier)
                    <form action="{{ route('supplier-products.store', $selectedSupplier->id) }}" method="POST">
                        @csrf
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300">
                                Select Products for <span class="text-blue-600">{{ $selectedSupplier->company_name }}</span>
                            </h3>
                        </div>

                        @if($products->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                                @foreach($products as $product)
                                    <label class="flex items-start p-3 border border-gray-200 dark:border-slate-700 rounded-xl cursor-pointer hover:bg-blue-50 dark:hover:bg-slate-700 transition-all duration-200 group">
                                        <div class="flex items-center h-5 mt-0.5">
                                            <input type="checkbox" name="products[]" value="{{ $product->id }}"
                                                   {{ in_array($product->id, $mappedProductIds) ? 'checked' : '' }}
                                                   class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:bg-gray-700 dark:border-gray-600">
                                        </div>
                                        <div class="ml-3 flex-1">
                                            <h4 class="text-sm font-bold text-gray-900 dark:text-gray-100 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                                {{ $product->name }}
                                            </h4>

                                            <div class="mt-1.5 flex flex-wrap gap-1.5">
                                                @if($product->code)
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-gray-100 text-gray-600 dark:bg-slate-600 dark:text-gray-300">
                        <i class="fa-solid fa-barcode mr-1 opacity-70"></i> {{ $product->code }}
                    </span>
                                                @endif

                                                @if($product->category)
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300 border border-indigo-100 dark:border-indigo-800/30">
                        <i class="fa-solid fa-tags mr-1 opacity-70"></i> {{ $product->category->name }}
                    </span>
                                                @endif

                                                @if($product->unit)
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-300 border border-green-100 dark:border-green-800/30">
                        <i class="fa-solid fa-scale-balanced mr-1 opacity-70"></i> {{ $product->unit->name }}
                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>

                            <div class="flex justify-end">
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg shadow-md transition-all">
                                    <i class="fa-solid fa-floppy-disk mr-2"></i> Save Mapping
                                </button>
                            </div>
                        @else
                            <div class="text-center py-6 text-gray-500">
                                No products found in the database. Please add products first.
                            </div>
                        @endif
                    </form>
                @else
                    <div class="text-center py-8 text-gray-400 dark:text-gray-500 italic">
                        <i class="fa-solid fa-arrow-pointer text-3xl mb-3 block opacity-50"></i>
                        Please select a supplier from the dropdown above to view and assign products.
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
