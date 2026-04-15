<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 dark:text-gray-200 leading-tight flex items-center gap-2">
            <i class="fa-solid fa-box-open text-blue-500"></i> Product Management (Master Setup)
        </h2>
    </x-slot>

    <div class="space-y-6">
        @if(session('success'))
            <div class="bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 p-4 rounded-xl border border-emerald-200 dark:border-emerald-800 text-sm font-medium print:hidden">
                <i class="fa-solid fa-circle-check mr-2"></i> {{ session('success') }}
            </div>
        @endif
        @if($errors->any())
            <div class="bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 p-4 rounded-xl border border-rose-200 dark:border-rose-800 text-sm print:hidden">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="hidden print:block text-center mb-6">
            <h2 class="text-2xl font-bold text-black uppercase tracking-wider">Shoe ERP</h2>
            <p class="text-lg font-semibold text-gray-800">Product (Master Setup) List</p>
            <p class="text-sm text-gray-500">Printed on: {{ now()->format('d M Y, h:i A') }}</p>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-700 p-5 print:shadow-none print:border-none print:p-0">

            <form method="GET" action="{{ route('products.index') }}" class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6 print:hidden">

                <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
                    <span>Show</span>
                    <select name="per_page" onchange="this.form.submit()" class="rounded-lg border-gray-200 dark:border-slate-700 dark:bg-slate-900 text-sm py-1.5 pl-3 pr-8 focus:ring-blue-500 focus:border-blue-500">
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                        <option value="250" {{ request('per_page') == 250 ? 'selected' : '' }}>250</option>
                    </select>
                    <span>entries</span>
                </div>

                <div class="flex flex-wrap items-center gap-3 w-full md:w-auto justify-end">

                    <div class="relative w-full md:w-auto">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="fa-solid fa-magnifying-glass text-gray-400"></i>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..." onblur="this.form.submit()"
                               class="w-full md:w-48 pl-9 rounded-lg border-gray-200 dark:border-slate-700 dark:bg-slate-900 dark:text-white text-sm py-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <select name="type" onchange="this.form.submit()" class="w-full md:w-auto rounded-lg border-gray-200 dark:border-slate-700 dark:bg-slate-900 dark:text-white text-sm py-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="all" {{ request('type') == 'all' ? 'selected' : '' }}>All Types</option>
                        <option value="raw_material" {{ request('type') == 'raw_material' ? 'selected' : '' }}>Raw Material</option>
                        <option value="finished_good" {{ request('type') == 'finished_good' ? 'selected' : '' }}>Finished Good</option>
                    </select>

                    <button type="button" onclick="window.print()" class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-600 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                        <i class="fa-solid fa-print"></i> Print
                    </button>

                    <button type="button" onclick="openModal('addModal')" class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fa-solid fa-plus"></i> Add New
                    </button>
                </div>
            </form>

            <div class="overflow-x-auto print:overflow-visible">
                <table class="w-full text-left border-collapse print:border print:border-gray-800">
                    <thead>
                    <tr class="bg-transparent text-gray-500 dark:text-gray-400 text-xs font-semibold uppercase border-y border-gray-200 dark:border-slate-700 print:bg-gray-100 print:border-gray-800 print:text-black">
                        <th class="py-3 px-4 text-center w-16 print:border print:border-gray-800">Image</th>
                        <th class="py-3 px-4 print:border print:border-gray-800">Product Info</th>
                        <th class="py-3 px-4 print:border print:border-gray-800">Category & Unit</th>

                        <th class="py-3 px-4 text-center print:border print:border-gray-800">Alert Qty</th>
                        <th class="py-3 px-4 text-center print:hidden">Action</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-slate-700/50 print:divide-gray-800">
                    @forelse($products as $product)
                        <tr class="hover:bg-blue-50/50 dark:hover:bg-slate-800/30 transition-colors text-sm print:text-black print:hover:bg-transparent">
                            <td class="py-3 px-4 text-center print:border print:border-gray-800">
                                @if($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}" alt="Img" class="w-10 h-10 rounded-lg object-cover border border-gray-200 dark:border-slate-600 mx-auto print:border-gray-400">
                                @else
                                    <div class="w-10 h-10 rounded-lg bg-gray-100 dark:bg-slate-700 flex items-center justify-center text-gray-400 mx-auto print:bg-transparent print:border print:border-gray-300">
                                        <i class="fa-solid fa-image"></i>
                                    </div>
                                @endif
                            </td>

                            <td class="py-3 px-4 print:border print:border-gray-800">
                                <div class="font-bold text-gray-800 dark:text-gray-200 print:text-black">{{ $product->name }}</div>
                                <div class="text-xs text-gray-400 mt-0.5 flex gap-2 print:text-gray-600">
                                    <span>Code: {{ $product->code ?? 'N/A' }}</span>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase border {{ $product->type == 'raw_material' ? 'border-purple-200 text-purple-600 bg-purple-50 dark:bg-purple-900/20' : 'border-emerald-200 text-emerald-600 bg-emerald-50 dark:bg-emerald-900/20' }} print:border-gray-400 print:text-black print:bg-transparent">
                                            {{ str_replace('_', ' ', $product->type) }}
                                        </span>
                                </div>
                            </td>

                            <td class="py-3 px-4 text-gray-600 dark:text-gray-300 print:border print:border-gray-800 print:text-black">
                                <div>{{ $product->category->name ?? 'N/A' }}</div>
                                <div class="text-xs text-gray-400 print:text-gray-600">{{ $product->unit->name ?? 'N/A' }} ({{ $product->unit->short_name ?? '' }})</div>
                            </td>



                            <td class="py-3 px-4 text-center font-semibold text-rose-500 print:border print:border-gray-800 print:text-black">
                                {{ $product->alert_quantity }}
                            </td>

                            <td class="py-3 px-4 text-center print:hidden">
                                <div class="flex justify-center items-center gap-3">
                                    <button onclick="openModal('editModal{{ $product->id }}')" class="text-gray-400 hover:text-blue-500 transition-colors" title="Edit">
                                        <i class="fa-regular fa-pen-to-square text-lg"></i>
                                    </button>

                                    <form action="{{ route('products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-gray-400 hover:text-red-500 transition-colors" title="Delete">
                                            <i class="fa-regular fa-trash-can text-lg"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        <div id="editModal{{ $product->id }}" class="fixed inset-0 z-50 hidden overflow-y-auto print:hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                <div class="fixed inset-0 bg-gray-500/75 dark:bg-gray-900/80 transition-opacity" onclick="closeModal('editModal{{ $product->id }}')"></div>
                                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                                <div class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full">
                                    <div class="bg-gray-50 dark:bg-slate-800/80 px-6 py-4 border-b border-gray-100 dark:border-slate-700 flex justify-between items-center">
                                        <h3 class="text-lg leading-6 font-bold text-gray-900 dark:text-white">Edit Product</h3>
                                        <button onclick="closeModal('editModal{{ $product->id }}')" class="text-gray-400 hover:text-gray-500">
                                            <i class="fa-solid fa-xmark text-xl"></i>
                                        </button>
                                    </div>
                                    <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        <div class="px-6 py-5">
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div class="md:col-span-2">
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Product Name *</label>
                                                    <input type="text" name="name" value="{{ $product->name }}" required class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 text-sm">
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Product Code/SKU</label>
                                                    <input type="text" name="code" value="{{ $product->code }}" class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 text-sm">
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Type *</label>
                                                    <select name="type" required class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 text-sm">
                                                        <option value="raw_material" {{ $product->type == 'raw_material' ? 'selected' : '' }}>Raw Material</option>
                                                        <option value="finished_good" {{ $product->type == 'finished_good' ? 'selected' : '' }}>Finished Good</option>
                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Category *</label>
                                                    <select name="category_id" required class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 text-sm">
                                                        @foreach($categories as $category)
                                                            <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Unit *</label>
                                                    <select name="unit_id" required class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 text-sm">
                                                        @foreach($units as $unit)
                                                            <option value="{{ $unit->id }}" {{ $product->unit_id == $unit->id ? 'selected' : '' }}>{{ $unit->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Alert Quantity</label>
                                                    <input type="number" name="alert_quantity" value="{{ $product->alert_quantity }}" class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 text-sm">
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Product Image</label>
                                                    <input type="file" name="image" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-slate-700 dark:file:text-gray-300">
                                                </div>
                                                <div class="md:col-span-2">
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                                                    <textarea name="description" rows="2" class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 text-sm">{{ $product->description }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="bg-gray-50 dark:bg-slate-800/80 px-6 py-4 border-t border-gray-100 dark:border-slate-700 flex justify-end gap-3">
                                            <button type="button" onclick="closeModal('editModal{{ $product->id }}')" class="px-4 py-2 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 rounded-lg text-gray-700 dark:text-gray-200 text-sm font-medium hover:bg-gray-50 dark:hover:bg-slate-600 transition-colors">Cancel</button>
                                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">Update Product</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-gray-400 print:border print:border-gray-800">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fa-solid fa-box-open text-4xl mb-3 opacity-20 print:hidden"></i>
                                    <p>No products found.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            @if($products->hasPages())
                <div class="pt-5 mt-4 border-t border-gray-100 dark:border-slate-700 print:hidden">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>

    <div id="addModal" class="fixed inset-0 z-50 hidden overflow-y-auto print:hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500/75 dark:bg-gray-900/80 transition-opacity" onclick="closeModal('addModal')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

            <div class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full">
                <div class="bg-gray-50 dark:bg-slate-800/80 px-6 py-4 border-b border-gray-100 dark:border-slate-700 flex justify-between items-center">
                    <h3 class="text-lg leading-6 font-bold text-gray-900 dark:text-white">Add New Product</h3>
                    <button onclick="closeModal('addModal')" class="text-gray-400 hover:text-gray-500">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>
                <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="px-6 py-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Product Name *</label>
                                <input type="text" name="name" required class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Product Code/SKU</label>
                                <input type="text" name="code" class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Type *</label>
                                <select name="type" required class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 text-sm">
                                    <option value="raw_material">Raw Material</option>
                                    <option value="finished_good">Finished Good</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Category *</label>
                                <select name="category_id" required class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 text-sm">
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Unit *</label>
                                <select name="unit_id" required class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 text-sm">
                                    <option value="">Select Unit</option>
                                    @foreach($units as $unit)
                                        <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Alert Quantity</label>
                                <input type="number" name="alert_quantity" value="0" class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Product Image</label>
                                <input type="file" name="image" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-slate-700 dark:file:text-gray-300">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                                <textarea name="description" rows="2" class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 text-sm"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-slate-800/80 px-6 py-4 border-t border-gray-100 dark:border-slate-700 flex justify-end gap-3">
                        <button type="button" onclick="closeModal('addModal')" class="px-4 py-2 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 rounded-lg text-gray-700 dark:text-gray-200 text-sm font-medium hover:bg-gray-50 dark:hover:bg-slate-600 transition-colors">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">Save Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        @media print {
            body { font-size: 12pt; background: white !important; }
            /* Hide the main layouts navigation/sidebar if they aren't using print:hidden by default */
            aside, header, nav { display: none !important; }
            main { padding: 0 !important; margin: 0 !important; }
        }
    </style>

    <script>
        function openModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }
    </script>
</x-app-layout>
