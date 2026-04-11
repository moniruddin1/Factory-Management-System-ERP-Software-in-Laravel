<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 dark:text-gray-200 leading-tight flex items-center gap-2">
            <i class="fa-solid fa-truck-field text-blue-500"></i> Supplier Management
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
            <p class="text-lg font-semibold text-gray-800">Supplier List & Balance</p>
            <p class="text-sm text-gray-500">Printed on: {{ now()->format('d M Y, h:i A') }}</p>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-700 p-5 print:shadow-none print:border-none print:p-0">

            <form method="GET" action="{{ route('suppliers.index') }}" class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6 print:hidden">

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
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search company, phone..." onblur="this.form.submit()"
                               class="w-full md:w-56 pl-9 rounded-lg border-gray-200 dark:border-slate-700 dark:bg-slate-900 dark:text-white text-sm py-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <select name="material_type" onchange="this.form.submit()" class="w-full md:w-auto rounded-lg border-gray-200 dark:border-slate-700 dark:bg-slate-900 dark:text-white text-sm py-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="all" {{ request('material_type') == 'all' ? 'selected' : '' }}>All Items</option>
                        <option value="Raw Material" {{ request('material_type') == 'Raw Material' ? 'selected' : '' }}>Raw Material</option>
                        <option value="Chemicals" {{ request('material_type') == 'Chemicals' ? 'selected' : '' }}>Chemicals</option>
                        <option value="Packaging" {{ request('material_type') == 'Packaging' ? 'selected' : '' }}>Packaging</option>
                        <option value="Others" {{ request('material_type') == 'Others' ? 'selected' : '' }}>Others</option>
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
                        <th class="py-3 px-4 print:border print:border-gray-800">Supplier Info</th>
                        <th class="py-3 px-4 print:border print:border-gray-800">Contact Details</th>
                        <th class="py-3 px-4 print:border print:border-gray-800">Supply Type</th>
                        <th class="py-3 px-4 text-right print:border print:border-gray-800">Current Balance</th>
                        <th class="py-3 px-4 text-center print:hidden">Action</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-slate-700/50 print:divide-gray-800">
                    @forelse($suppliers as $supplier)
                        <tr class="hover:bg-blue-50/50 dark:hover:bg-slate-800/30 transition-colors text-sm print:text-black print:hover:bg-transparent">

                            <td class="py-3 px-4 print:border print:border-gray-800">
                                <div class="font-bold text-gray-800 dark:text-gray-200 print:text-black text-base">{{ $supplier->company_name }}</div>
                                <div class="text-xs text-gray-500 mt-0.5 print:text-gray-700">Code: <span class="font-mono">{{ $supplier->code }}</span></div>
                                @if($supplier->contact_person)
                                    <div class="text-xs text-gray-400 mt-0.5 print:text-gray-600"><i class="fa-regular fa-user mr-1"></i>{{ $supplier->contact_person }}</div>
                                @endif
                            </td>

                            <td class="py-3 px-4 text-gray-600 dark:text-gray-300 print:border print:border-gray-800 print:text-black">
                                <div class="flex items-center gap-2"><i class="fa-solid fa-phone text-xs text-gray-400 print:hidden"></i> {{ $supplier->phone }}</div>
                                @if($supplier->email)
                                    <div class="text-xs text-gray-400 mt-0.5"><i class="fa-regular fa-envelope text-xs print:hidden"></i> {{ $supplier->email }}</div>
                                @endif
                            </td>

                            <td class="py-3 px-4 print:border print:border-gray-800">
                                @if($supplier->material_type)
                                    <span class="px-2.5 py-1 rounded-full text-xs font-medium border border-blue-200 text-blue-700 bg-blue-50 dark:bg-blue-900/20 dark:border-blue-800 dark:text-blue-300 print:border-gray-400 print:text-black print:bg-transparent">
                                            {{ $supplier->material_type }}
                                        </span>
                                @else
                                    <span class="text-gray-400 text-xs">N/A</span>
                                @endif
                            </td>

                            <td class="py-3 px-4 text-right print:border print:border-gray-800 print:text-black">
                                @if($supplier->current_balance > 0)
                                    <div class="text-rose-600 dark:text-rose-400 font-bold">৳ {{ number_format($supplier->current_balance, 2) }}</div>
                                    <div class="text-[10px] text-gray-400 uppercase tracking-wider">Payable (Due)</div>
                                @elseif($supplier->current_balance < 0)
                                    <div class="text-emerald-600 dark:text-emerald-400 font-bold">৳ {{ number_format(abs($supplier->current_balance), 2) }}</div>
                                    <div class="text-[10px] text-gray-400 uppercase tracking-wider">Advance Given</div>
                                @else
                                    <div class="text-gray-600 dark:text-gray-400 font-bold">৳ 0.00</div>
                                    <div class="text-[10px] text-gray-400 uppercase tracking-wider">Settled</div>
                                @endif
                            </td>

                            <td class="py-3 px-4 text-center print:hidden">
                                <div class="flex justify-center items-center gap-3">
                                    <button onclick="openModal('editModal{{ $supplier->id }}')" class="text-gray-400 hover:text-blue-500 transition-colors" title="Edit">
                                        <i class="fa-regular fa-pen-to-square text-lg"></i>
                                    </button>

                                    <form action="{{ route('suppliers.destroy', $supplier->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this supplier?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-gray-400 hover:text-red-500 transition-colors" title="Delete">
                                            <i class="fa-regular fa-trash-can text-lg"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        <div id="editModal{{ $supplier->id }}" class="fixed inset-0 z-50 hidden overflow-y-auto print:hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                <div class="fixed inset-0 bg-gray-500/75 dark:bg-gray-900/80 transition-opacity" onclick="closeModal('editModal{{ $supplier->id }}')"></div>
                                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                                <div class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full">
                                    <div class="bg-gray-50 dark:bg-slate-800/80 px-6 py-4 border-b border-gray-100 dark:border-slate-700 flex justify-between items-center">
                                        <h3 class="text-lg leading-6 font-bold text-gray-900 dark:text-white">Edit Supplier</h3>
                                        <button type="button" onclick="closeModal('editModal{{ $supplier->id }}')" class="text-gray-400 hover:text-gray-500">
                                            <i class="fa-solid fa-xmark text-xl"></i>
                                        </button>
                                    </div>
                                    <form action="{{ route('suppliers.update', $supplier->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="px-6 py-5">
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div class="md:col-span-2">
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Company Name *</label>
                                                    <input type="text" name="company_name" value="{{ $supplier->company_name }}" required class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 text-sm">
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Contact Person</label>
                                                    <input type="text" name="contact_person" value="{{ $supplier->contact_person }}" class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 text-sm">
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Phone Number *</label>
                                                    <input type="text" name="phone" value="{{ $supplier->phone }}" required class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 text-sm">
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email Address</label>
                                                    <input type="email" name="email" value="{{ $supplier->email }}" class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 text-sm">
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Supply Category</label>
                                                    <select name="material_type" class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 text-sm">
                                                        <option value="">Select Type</option>
                                                        <option value="Raw Material" {{ $supplier->material_type == 'Raw Material' ? 'selected' : '' }}>Raw Material</option>
                                                        <option value="Chemicals" {{ $supplier->material_type == 'Chemicals' ? 'selected' : '' }}>Chemicals</option>
                                                        <option value="Packaging" {{ $supplier->material_type == 'Packaging' ? 'selected' : '' }}>Packaging</option>
                                                        <option value="Others" {{ $supplier->material_type == 'Others' ? 'selected' : '' }}>Others</option>
                                                    </select>
                                                </div>
                                                <div class="md:col-span-2">
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Opening Balance (৳)</label>
                                                    <input type="number" step="0.01" name="opening_balance" value="{{ $supplier->opening_balance }}" class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 text-sm" placeholder="Positive = Due to them, Negative = Advance Given">
                                                    <p class="text-[10px] text-gray-500 mt-1">Note: Only adjust if fixing an initial entry error.</p>
                                                </div>
                                                <div class="md:col-span-2">
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Address</label>
                                                    <textarea name="address" rows="2" class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 text-sm">{{ $supplier->address }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="bg-gray-50 dark:bg-slate-800/80 px-6 py-4 border-t border-gray-100 dark:border-slate-700 flex justify-end gap-3">
                                            <button type="button" onclick="closeModal('editModal{{ $supplier->id }}')" class="px-4 py-2 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 rounded-lg text-gray-700 dark:text-gray-200 text-sm font-medium hover:bg-gray-50 dark:hover:bg-slate-600 transition-colors">Cancel</button>
                                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">Update Supplier</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-gray-400 print:border print:border-gray-800">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fa-solid fa-truck-field text-4xl mb-3 opacity-20 print:hidden"></i>
                                    <p>No suppliers found.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            @if($suppliers->hasPages())
                <div class="pt-5 mt-4 border-t border-gray-100 dark:border-slate-700 print:hidden">
                    {{ $suppliers->links() }}
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
                    <h3 class="text-lg leading-6 font-bold text-gray-900 dark:text-white">Add New Supplier</h3>
                    <button type="button" onclick="closeModal('addModal')" class="text-gray-400 hover:text-gray-500">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>
                <form action="{{ route('suppliers.store') }}" method="POST">
                    @csrf
                    <div class="px-6 py-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Company Name *</label>
                                <input type="text" name="company_name" required class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Contact Person</label>
                                <input type="text" name="contact_person" class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Phone Number *</label>
                                <input type="text" name="phone" required class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email Address</label>
                                <input type="email" name="email" class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Supply Category</label>
                                <select name="material_type" class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 text-sm">
                                    <option value="">Select Type</option>
                                    <option value="Raw Material">Raw Material</option>
                                    <option value="Chemicals">Chemicals</option>
                                    <option value="Packaging">Packaging</option>
                                    <option value="Others">Others</option>
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Opening Balance (৳)</label>
                                <input type="number" step="0.01" name="opening_balance" value="0" class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 text-sm" placeholder="Example: 5000">
                                <p class="text-[10px] text-gray-500 mt-1">If you owe them, put a positive amount. If you gave an advance, put a negative amount (e.g. -5000).</p>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Address</label>
                                <textarea name="address" rows="2" class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 text-sm"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-slate-800/80 px-6 py-4 border-t border-gray-100 dark:border-slate-700 flex justify-end gap-3">
                        <button type="button" onclick="closeModal('addModal')" class="px-4 py-2 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 rounded-lg text-gray-700 dark:text-gray-200 text-sm font-medium hover:bg-gray-50 dark:hover:bg-slate-600 transition-colors">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">Save Supplier</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        @media print {
            body { font-size: 12pt; background: white !important; }
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
