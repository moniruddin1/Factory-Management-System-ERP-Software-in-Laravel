<x-app-layout>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        /* Select2 Light/Dark Mode Fix */
        .select2-container .select2-selection--single {
            height: 42px !important;
            border: 1px solid #d1d5db !important;
            border-radius: 0.5rem !important;
            display: flex;
            align-items: center;
            background-color: #f9fafb !important;
        }
        .dark .select2-container .select2-selection--single {
            background-color: #0f172a !important;
            border-color: #475569 !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #111827 !important;
        }
        .dark .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #f8fafc !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px !important;
        }
        .dark .select2-dropdown {
            background-color: #1e293b !important;
            border-color: #475569 !important;
            color: white;
        }
        .dark .select2-container--default .select2-search--dropdown .select2-search__field {
            background-color: #0f172a !important;
            color: white !important;
            border-color: #475569 !important;
        }
        .dark .select2-container--default .select2-results__option--selected { background-color: #334155 !important; }
        .dark .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable { background-color: #3b82f6 !important; }

        @media print {
            body * { visibility: hidden; }
            .print\:hidden { display: none !important; }
            .print-area, .print-area * { visibility: visible; }
            .print-area { position: absolute; left: 0; top: 0; width: 100%; }
        }
    </style>

    <div class="py-6 bg-gray-50 dark:bg-[#0f172a] min-h-screen font-sans transition-colors duration-200">
        <div class="w-full mx-auto px-4 sm:px-6 lg:px-8 max-w-[100%]">

            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                <div class="flex items-center gap-3 text-gray-800 dark:text-white">
                    <div class="bg-blue-100 dark:bg-blue-900/30 p-2.5 rounded-lg text-blue-600 dark:text-blue-500">
                        <i class="fa-solid fa-truck-ramp-box text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-semibold">Purchase Returns</h2>
                        <p class="text-sm text-gray-500 dark:text-slate-400">Manage all returned products and debit notes</p>
                    </div>
                </div>
                <div class="flex gap-2 print:hidden">
                    <button type="button" onclick="window.print()" class="bg-white dark:bg-[#1e293b] border border-gray-300 dark:border-slate-600 hover:bg-gray-50 dark:hover:bg-slate-800 text-gray-700 dark:text-white px-4 py-2 rounded-lg text-sm font-medium transition flex items-center shadow-sm">
                        <i class="fa-solid fa-print mr-2"></i> Print List
                    </button>
                    <a href="{{ route('purchase-returns.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition shadow-sm flex items-center">
                        <i class="fa-solid fa-plus mr-2"></i> Add New Return
                    </a>
                </div>
            </div>

            <div class="bg-white dark:bg-[#1e293b] rounded-xl shadow-sm border border-gray-200 dark:border-slate-700/50 overflow-hidden transition-colors duration-200">

                <div class="p-5 border-b border-gray-200 dark:border-slate-700/50 print:hidden bg-gray-50/50 dark:bg-transparent">
                    <form method="GET" action="{{ route('purchase-returns.index') }}">
                        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">

                            <div class="md:col-span-1 relative">
                                <label class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase mb-1 block">Search</label>
                                <div class="absolute inset-y-0 left-0 top-6 flex items-center pl-3 pointer-events-none">
                                    <i class="fa-solid fa-magnifying-glass text-gray-400 dark:text-slate-500 text-sm"></i>
                                </div>
                                <input type="text" name="search" value="{{ request('search') }}" class="bg-gray-50 dark:bg-[#0f172a] border border-gray-300 dark:border-slate-600 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 transition-colors" placeholder="Voucher, Invoice...">
                            </div>

                            <div class="md:col-span-1">
                                <label class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase mb-1 block">Supplier</label>
                                <select name="supplier_id" class="select2-supplier w-full">
                                    <option value="">All Suppliers</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                            {{ $supplier->company_name }} ({{ $supplier->phone }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="md:col-span-1">
                                <label class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase mb-1 block">Date From</label>
                                <input type="date" name="start_date" value="{{ request('start_date') }}" class="bg-gray-50 dark:bg-[#0f172a] border border-gray-300 dark:border-slate-600 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 transition-colors">
                            </div>

                            <div class="md:col-span-1">
                                <label class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase mb-1 block">Date To</label>
                                <input type="date" name="end_date" value="{{ request('end_date') }}" class="bg-gray-50 dark:bg-[#0f172a] border border-gray-300 dark:border-slate-600 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 transition-colors">
                            </div>

                            <div class="md:col-span-1 flex items-end gap-2">
                                <button type="submit" class="w-full bg-slate-800 hover:bg-slate-900 dark:bg-blue-600 dark:hover:bg-blue-700 text-white px-4 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
                                    <i class="fa-solid fa-filter mr-1"></i> Filter
                                </button>
                                <a href="{{ route('purchase-returns.index') }}" class="w-full text-center bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 hover:bg-gray-100 dark:hover:bg-slate-600 text-gray-700 dark:text-white px-4 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
                                    Clear
                                </a>
                            </div>
                        </div>

                        <div class="mt-4 flex items-center justify-end gap-2 text-sm text-gray-600 dark:text-slate-400">
                            <span>Show</span>
                            <select name="limit" onchange="this.form.submit()" class="bg-gray-50 dark:bg-[#0f172a] border border-gray-300 dark:border-slate-600 text-gray-900 dark:text-white text-sm rounded focus:ring-blue-500 focus:border-blue-500 px-2 py-1">
                                <option value="25" {{ request('limit') == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ request('limit') == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ request('limit') == 100 ? 'selected' : '' }}>100</option>
                                <option value="250" {{ request('limit') == 250 ? 'selected' : '' }}>250</option>
                            </select>
                            <span>Entries</span>
                        </div>
                    </form>
                </div>

                <div class="overflow-x-auto print-area">
                    <table class="w-full text-left text-sm whitespace-nowrap">
                        <thead class="text-xs text-gray-500 dark:text-slate-400 uppercase border-b border-gray-200 dark:border-slate-700/50 bg-gray-50 dark:bg-[#1e293b]">
                        <tr>
                            <th class="px-6 py-4 font-semibold">Return Info</th>
                            <th class="px-6 py-4 font-semibold">Contact Details</th>
                            <th class="px-6 py-4 font-semibold text-center">Invoice Ref.</th>
                            <th class="px-6 py-4 font-semibold text-right">Return Amount</th>
                            <th class="px-6 py-4 font-semibold text-center print:hidden">Action</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-slate-700/50">
                        @forelse($returns as $return)
                            <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/30 transition-colors duration-200">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900 dark:text-white">{{ $return->return_no }}</div>
                                    <div class="text-xs text-gray-500 dark:text-slate-400 mt-0.5"><i class="fa-regular fa-calendar mr-1"></i> {{ date('d M, Y', strtotime($return->return_date)) }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-blue-600 dark:text-blue-400">{{ optional($return->supplier)->company_name ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-500 dark:text-slate-400 mt-0.5"><i class="fa-solid fa-phone text-[10px] mr-1"></i> {{ optional($return->supplier)->phone ?? '' }}</div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-800 dark:bg-slate-800 dark:text-slate-300 border border-gray-200 dark:border-slate-600">
                                            {{ optional($return->purchase)->invoice_no ?? 'N/A' }}
                                        </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="font-bold text-rose-600 dark:text-rose-400">৳ {{ number_format($return->total_return_amount, 2) }}</div>
                                </td>
                                <td class="px-6 py-4 text-center print:hidden">
                                    <div class="flex items-center justify-center gap-3">
                                        <a href="{{ route('purchase-returns.show', $return->id) }}" class="text-gray-400 hover:text-blue-600 dark:text-slate-400 dark:hover:text-blue-400 transition" title="View / Print">
                                            <i class="fa-regular fa-eye text-lg"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500 dark:text-slate-500">
                                    <i class="fa-solid fa-box-open text-4xl mb-3 text-gray-400 dark:text-slate-600"></i>
                                    <p>No purchase returns found matching your criteria.</p>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                @if($returns->hasPages())
                    <div class="p-5 border-t border-gray-200 dark:border-slate-700/50 bg-white dark:bg-[#1e293b] print:hidden">
                        {{ $returns->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2-supplier').select2({
                placeholder: "Search by Name/Phone",
                allowClear: true,
                width: '100%'
            });
        });
    </script>
</x-app-layout>
