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
        .tab-active { border-bottom-color: #3b82f6; color: #3b82f6; }
        .dark .tab-active { border-bottom-color: #60a5fa; color: #60a5fa; }

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
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Payments & Dues</h2>
                    <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">Manage your supplier payments and pending dues</p>
                </div>
                <div class="flex items-center gap-3">
                    <button type="button" onclick="window.print()" class="bg-white dark:bg-[#1e293b] border border-gray-200 dark:border-slate-700 hover:bg-gray-50 dark:hover:bg-slate-800 text-gray-700 dark:text-slate-300 px-4 py-2 rounded-lg text-sm font-medium transition shadow-sm flex items-center">
                        <i class="fa-solid fa-print mr-2"></i> Print
                    </button>
                    <a href="{{ route('supplier-payments.create') }}" class="bg-blue-600 hover:bg-blue-700 dark:bg-[#3b82f6] dark:hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition shadow-sm flex items-center">
                        <i class="fa-solid fa-plus mr-2"></i> Add Payment
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6 no-print">
                <div class="bg-white dark:bg-[#1e293b] rounded-xl p-5 border border-gray-200 dark:border-slate-700/50 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-slate-400">Total Paid to Suppliers</p>
                        <h3 class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">৳ {{ number_format($totalPaid ?? 0, 2) }}</h3>
                    </div>
                    <div class="h-12 w-12 rounded-full bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center text-emerald-600 dark:text-emerald-400 text-xl"><i class="fa-solid fa-money-bill-wave"></i></div>
                </div>
                <div class="bg-white dark:bg-[#1e293b] rounded-xl p-5 border border-gray-200 dark:border-slate-700/50 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-slate-400">Total Pending Dues</p>
                        <h3 class="text-2xl font-bold text-rose-600 dark:text-rose-400 mt-1">৳ {{ number_format($totalDue ?? 0, 2) }}</h3>
                    </div>
                    <div class="h-12 w-12 rounded-full bg-rose-100 dark:bg-rose-500/20 flex items-center justify-center text-rose-600 dark:text-rose-400 text-xl"><i class="fa-solid fa-file-invoice-dollar"></i></div>
                </div>
            </div>

            @if(session('success'))
                <div class="bg-emerald-100 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 text-emerald-700 dark:text-emerald-400 px-4 py-3 rounded-lg mb-4 flex items-center text-sm no-print">
                    <i class="fa-solid fa-circle-check mr-2"></i> {{ session('success') }}
                </div>
            @endif

            <div class="bg-white dark:bg-[#1e293b] rounded-xl shadow-sm border border-gray-200 dark:border-slate-700/50 overflow-hidden">

                <div class="border-b border-gray-200 dark:border-slate-700 no-print">
                    <ul class="flex flex-wrap -mb-px text-sm font-medium text-center text-gray-500 dark:text-slate-400">
                        <li class="mr-2">
                            <button onclick="switchTab('payments')" id="tab-btn-payments" class="inline-flex items-center p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                                <i class="fa-solid fa-list-check mr-2"></i> Payment History
                            </button>
                        </li>
                        <li class="mr-2">
                            <button onclick="switchTab('dues')" id="tab-btn-dues" class="inline-flex items-center p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                                <i class="fa-solid fa-clock-rotate-left mr-2"></i> Pending Dues
                                @if(isset($dueInvoices) && $dueInvoices->total() > 0)
                                    <span class="ml-2 bg-rose-100 text-rose-600 dark:bg-rose-500/20 dark:text-rose-400 text-xs px-2 py-0.5 rounded-full">{{ $dueInvoices->total() }}</span>
                                @endif
                            </button>
                        </li>
                    </ul>
                </div>

                <form method="GET" action="{{ route('supplier-payments.index') }}" id="filter-form" class="p-4 border-b border-gray-200 dark:border-slate-700/50 flex flex-col xl:flex-row justify-between items-center gap-4 no-print bg-gray-50/50 dark:bg-[#0f172a]/30">
                    <input type="hidden" name="tab" id="current_tab" value="{{ request('tab', 'payments') }}">

                    <div class="flex items-center text-sm text-gray-600 dark:text-slate-300 w-full xl:w-auto">
                        <span>Show</span>
                        <div class="relative mx-2">
                            <select name="per_page" onchange="document.getElementById('filter-form').submit()" class="bg-white dark:bg-[#0f172a] border border-gray-300 dark:border-slate-700 text-gray-700 dark:text-white text-sm rounded-lg focus:ring-blue-500 block w-full px-3 py-1.5 pr-8 cursor-pointer">
                                <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                                <option value="250" {{ request('per_page') == 250 ? 'selected' : '' }}>250</option>
                            </select>
                        </div>
                        <span>entries</span>
                    </div>

                    <div class="flex flex-wrap items-center gap-3 w-full xl:w-auto">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none"><i class="fa-solid fa-search text-gray-400"></i></div>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Inv, Vch, Phone, Name..." class="bg-white dark:bg-[#0f172a] border border-gray-300 dark:border-slate-700 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-blue-500 block pl-9 p-2 w-52">
                        </div>

                        <div class="flex items-center gap-1">
                            <input type="date" name="start_date" value="{{ request('start_date') }}" class="bg-white dark:bg-[#0f172a] border border-gray-300 dark:border-slate-700 text-gray-700 dark:text-slate-300 text-sm rounded-lg p-2 focus:ring-blue-500 w-36">
                            <span class="text-gray-400 dark:text-slate-500">-</span>
                            <input type="date" name="end_date" value="{{ request('end_date') }}" class="bg-white dark:bg-[#0f172a] border border-gray-300 dark:border-slate-700 text-gray-700 dark:text-slate-300 text-sm rounded-lg p-2 focus:ring-blue-500 w-36">
                        </div>

                        <div class="w-48">
                            <select name="supplier_id" id="supplier_select" onchange="document.getElementById('filter-form').submit()" class="w-full text-sm">
                                <option value="">All Suppliers</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->company_name }} ({{ $supplier->phone ?? 'N/A' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="hidden"></button>
                    </div>
                </form>

                <div id="printable-area">
                    <div id="tab-content-payments" class="hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm text-gray-600 dark:text-slate-300">
                                <thead class="text-xs text-gray-500 dark:text-slate-400 uppercase bg-gray-50 dark:bg-[#1e293b] border-b border-gray-200 dark:border-slate-700">
                                <tr>
                                    <th class="px-6 py-4">#</th>
                                    <th class="px-6 py-4">Date</th>
                                    <th class="px-6 py-4">Voucher No</th>
                                    <th class="px-6 py-4">Supplier</th>
                                    <th class="px-6 py-4">Ref/Invoice</th>
                                    <th class="px-6 py-4 text-right">Amount</th>
                                    <th class="px-6 py-4">Method</th>
                                </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-slate-700/50">
                                @forelse($payments as $index => $payment)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/20">
                                        <td class="px-6 py-4">{{ $payments->firstItem() + $index }}</td>
                                        <td class="px-6 py-4">{{ \Carbon\Carbon::parse($payment->payment_date)->format('d M, Y') }}</td>
                                        <td class="px-6 py-4 text-blue-600 dark:text-blue-400 font-medium">{{ $payment->voucher_no }}</td>
                                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ optional($payment->supplier)->company_name }}</td>
                                        <td class="px-6 py-4 text-gray-500 dark:text-slate-400">{{ $payment->purchase_id ? optional($payment->purchase)->invoice_no : '-' }}</td>
                                        <td class="px-6 py-4 text-right font-bold text-gray-900 dark:text-white">৳ {{ number_format($payment->amount, 2) }}</td>
                                        <td class="px-6 py-4"><span class="border border-purple-200 dark:border-purple-500/30 text-purple-700 dark:text-purple-400 bg-purple-50 dark:bg-purple-500/10 rounded px-2 py-1 text-xs">{{ $payment->payment_method }}</span></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="px-6 py-8 text-center text-gray-500">No payments found matching your filter</td></tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if($payments->hasPages())
                            <div class="px-6 py-4 border-t border-gray-200 dark:border-slate-700 bg-gray-50/50 dark:bg-transparent no-print">{{ $payments->appends(request()->except('payment_page'))->links() }}</div>
                        @endif
                    </div>

                    <div id="tab-content-dues" class="hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm text-gray-600 dark:text-slate-300">
                                <thead class="text-xs text-gray-500 dark:text-slate-400 uppercase bg-gray-50 dark:bg-[#1e293b] border-b border-gray-200 dark:border-slate-700">
                                <tr>
                                    <th class="px-6 py-4">#</th>
                                    <th class="px-6 py-4 font-medium">Invoice No</th>
                                    <th class="px-6 py-4 font-medium">Supplier</th>
                                    <th class="px-6 py-4 font-medium">Purchase Date</th>
                                    <th class="px-6 py-4 font-medium text-right">Total Bill</th>
                                    <th class="px-6 py-4 font-medium text-right text-rose-500">Due Amount</th>
                                    <th class="px-6 py-4 font-medium text-center no-print">Action</th>
                                </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-slate-700/50">
                                @forelse($dueInvoices as $index => $due)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/20">
                                        <td class="px-6 py-4">{{ $dueInvoices->firstItem() + $index }}</td>
                                        <td class="px-6 py-4 text-gray-900 dark:text-white font-medium">{{ $due->invoice_no }}</td>
                                        <td class="px-6 py-4">{{ optional($due->supplier)->company_name }}</td>
                                        <td class="px-6 py-4">{{ \Carbon\Carbon::parse($due->purchase_date)->format('d M, Y') }}</td>
                                        <td class="px-6 py-4 text-right">৳ {{ number_format($due->grand_total, 2) }}</td>
                                        <td class="px-6 py-4 text-right font-bold text-rose-600 dark:text-rose-400">৳ {{ number_format($due->due_amount, 2) }}</td>
                                        <td class="px-6 py-4 text-center no-print">
                                            <a href="{{ route('supplier-payments.create', ['purchase_id' => $due->id, 'supplier_id' => $due->supplier_id, 'due_amount' => $due->due_amount]) }}" class="bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-white px-3 py-1.5 rounded text-xs font-medium inline-flex items-center">
                                                <i class="fa-solid fa-money-bill-wave mr-1.5"></i> Pay Now
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-12 text-center text-emerald-500 dark:text-emerald-400">
                                            <i class="fa-solid fa-face-smile-beam text-4xl mb-3"></i>
                                            <p class="text-base font-medium mt-2">Awesome! No pending dues found.</p>
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if($dueInvoices->hasPages())
                            <div class="px-6 py-4 border-t border-gray-200 dark:border-slate-700 bg-gray-50/50 dark:bg-transparent no-print">{{ $dueInvoices->appends(request()->except('due_page'))->links() }}</div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#supplier_select').select2({
                placeholder: "Search Supplier...",
                allowClear: true
            });
        });

        function switchTab(tab) {
            // Hide all tab contents
            document.getElementById('tab-content-payments').classList.add('hidden');
            document.getElementById('tab-content-dues').classList.add('hidden');

            // Reset tab button styles
            document.getElementById('tab-btn-payments').classList.remove('tab-active', 'border-blue-500', 'dark:border-blue-400');
            document.getElementById('tab-btn-dues').classList.remove('tab-active', 'border-blue-500', 'dark:border-blue-400');

            // Show active tab and update styles
            document.getElementById('tab-content-' + tab).classList.remove('hidden');
            document.getElementById('tab-btn-' + tab).classList.add('tab-active', 'border-blue-500', 'dark:border-blue-400');

            // Update hidden input so filter form remembers the tab
            document.getElementById('current_tab').value = tab;
        }

        // Initialize active tab based on Request
        document.addEventListener('DOMContentLoaded', function() {
            let activeTab = "{{ request('tab', 'payments') }}";
            switchTab(activeTab);
        });
    </script>
</x-app-layout>
