<x-app-layout>
    {{-- Select2 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        /* --- Select2 Dark/Light Mode Styling --- */
        .select2-container--default .select2-selection--single {
            background-color: #f9fafb; border: 1px solid #d1d5db; height: 42px; padding-top: 6px; border-radius: 8px; transition: all 0.2s;
        }
        .dark .select2-container--default .select2-selection--single {
            background-color: #0f172a !important; border-color: #334155 !important;
        }
        .dark .select2-selection__rendered { color: #f8fafc !important; }
        .dark .select2-container--default .select2-results__option { background-color: #1e293b; color: #f8fafc; }
        .dark .select2-dropdown { background-color: #1e293b; border-color: #334155; }
        .dark .select2-search__field { background-color: #0f172a !important; color: white; }

        /* --- Hover Fix: Light & Dark Mode --- */
        .supplier-row:hover { background-color: rgba(79, 70, 229, 0.05) !important; }
        .dark .supplier-row:hover { background-color: rgba(255, 255, 255, 0.03) !important; }

        /* --- Professional Print Logic (Scrollbar & Layout Fix) --- */
        @media print {
            @page { size: A4 portrait; margin: 10mm; }

            /* স্ক্রলবার এবং অতিরিক্ত ব্যাকগ্রাউন্ড রিমুভ */
            html, body {
                overflow: visible !important;
                height: auto !important;
                margin: 0 !important;
                padding: 0 !important;
                background: white !important;
            }

            /* নেভিগেশন, সাইডবার এবং ফিল্টার হাইড */
            nav, aside, header, footer, .print-hidden, .select2-container, button {
                display: none !important;
            }

            /* মেইন কন্টেন্ট ফিক্স */
            .main-content {
                position: static !important;
                display: block !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                overflow: visible !important;
            }

            /* টেবিল ফিক্স */
            table {
                width: 100% !important;
                border-collapse: collapse !important;
                border: 1px solid #000 !important;
                table-layout: auto !important;
            }
            th {
                background-color: #f0f0f0 !important;
                color: #000 !important;
                border: 1px solid #000 !important;
                padding: 8px !important;
                font-size: 12px;
            }
            td {
                border: 1px solid #000 !important;
                padding: 8px !important;
                font-size: 11px !important;
                color: #000 !important;
                background: transparent !important;
            }

            .print-header { display: block !important; text-align: center; margin-bottom: 20px; }
            .balance-text { color: black !important; font-weight: 800 !important; }
        }
    </style>

    <div class="py-6 bg-gray-50 dark:bg-[#0f172a] min-h-screen font-sans transition-colors duration-200 main-content">
        <div class="w-full mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Print Header (Only visible on paper) --}}
            <div class="hidden print-header">
                <h1 class="text-2xl font-bold uppercase">{{ App\Models\CompanyInfo::first()->company_name ?? 'SHOE ERP SYSTEM' }}</h1>
                <p class="font-bold text-lg">Supplier Balance Report</p>
                <p class="text-sm">Printed on: {{ date('d-M-Y h:i A') }}</p>
                <div style="border-bottom: 2px solid black; margin-top: 10px;"></div>
            </div>

            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4 print-hidden">
                <div class="flex items-center gap-3 text-gray-800 dark:text-white">
                    <div class="bg-indigo-600 p-2.5 rounded-lg text-white shadow-lg">
                        <i class="fa-solid fa-file-invoice-dollar text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold tracking-tight">Supplier Ledgers</h2>
                        <p class="text-sm text-gray-500 dark:text-slate-400">Manage transaction history and dues</p>
                    </div>
                </div>

                <button onclick="window.print()" class="flex items-center gap-2 bg-white dark:bg-slate-800 text-gray-700 dark:text-slate-200 px-5 py-2.5 rounded-lg border border-gray-300 dark:border-slate-600 hover:bg-gray-50 dark:hover:bg-slate-700 transition shadow-sm font-bold text-sm">
                    <i class="fa-solid fa-print"></i> Print Report
                </button>
            </div>

            {{-- Filter Section --}}
            <div class="bg-white dark:bg-[#1e293b] rounded-xl shadow-sm border border-gray-200 dark:border-slate-700/50 mb-6 print-hidden">
                <form method="GET" action="{{ route('supplier-ledgers.index') }}" class="p-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase mb-2 ml-1">Search</label>
                            <input type="text" name="search" value="{{ request('search') }}" class="bg-gray-50 dark:bg-[#0f172a] border border-gray-300 dark:border-slate-600 text-gray-900 dark:text-white text-sm rounded-lg block w-full p-2.5" placeholder="Name, Phone, Code...">
                        </div>

                        <div class="supplier-dropdown-wrapper">
                            <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase mb-2 ml-1">Supplier</label>
                            <select name="supplier_id" class="searchable-select block w-full">
                                <option value="">-- All Suppliers --</option>
                                @foreach($allSuppliers as $s)
                                    <option value="{{ $s->id }}" {{ request('supplier_id') == $s->id ? 'selected' : '' }}>{{ $s->company_name }} ({{ $s->code }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase mb-2 ml-1">Limit</label>
                            <select name="limit" class="bg-gray-50 dark:bg-[#0f172a] border border-gray-300 dark:border-slate-600 text-gray-900 dark:text-white text-sm rounded-lg block w-full p-2.5">
                                <option value="25" {{ request('limit') == 25 ? 'selected' : '' }}>25 Rows</option>
                                <option value="500" {{ request('limit') == 500 ? 'selected' : '' }}>Full Report (All)</option>
                            </select>
                        </div>

                        <div class="flex gap-2">
                            <button type="submit" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-lg text-sm font-bold shadow-md">
                                <i class="fa-solid fa-filter mr-1"></i> Filter
                            </button>
                            <a href="{{ route('supplier-ledgers.index') }}" class="bg-gray-200 dark:bg-slate-700 text-gray-700 dark:text-white px-4 py-2.5 rounded-lg text-sm font-bold hover:bg-gray-300">
                                <i class="fa-solid fa-rotate-left"></i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Table --}}
            <div class="bg-white dark:bg-[#1e293b] rounded-xl shadow-sm border border-gray-200 dark:border-slate-700/50 overflow-hidden">
                <table class="w-full text-left text-sm border-collapse">
                    <thead>
                    <tr class="text-xs font-bold text-gray-500 dark:text-slate-400 uppercase bg-gray-50 dark:bg-slate-800/50 border-b border-gray-200 dark:border-slate-700">
                        <th class="px-6 py-4">Code</th>
                        <th class="px-6 py-4">Company Details</th>
                        <th class="px-6 py-4">Contact Person</th>
                        <th class="px-6 py-4 text-right">Current Balance</th>
                        <th class="px-6 py-4 text-center print-hidden">Action</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-slate-700/50">
                    @forelse($suppliers as $supplier)
                        <tr class="supplier-row transition-colors">
                            <td class="px-6 py-4 font-mono font-bold text-gray-600 dark:text-slate-400">
                                {{ $supplier->code ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900 dark:text-white text-base">{{ $supplier->company_name }}</div>
                                <div class="text-[10px] uppercase font-black text-indigo-500 dark:text-indigo-400">
                                    {{ $supplier->material_type ?? 'N/A' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-600 dark:text-slate-300">
                                <span class="font-medium">{{ $supplier->contact_person }}</span> <br>
                                <small class="text-xs">{{ $supplier->phone }}</small>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="text-base font-black balance-text {{ $supplier->current_balance > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                                    ৳ {{ number_format(abs($supplier->current_balance ?? 0), 2) }}
                                </div>
                                <span class="text-[10px] font-bold uppercase {{ $supplier->current_balance > 0 ? 'text-rose-500' : 'text-emerald-500' }}">
                                        {{ ($supplier->current_balance ?? 0) > 0 ? 'Payable (Cr)' : 'Advance (Dr)' }}
                                    </span>
                            </td>
                            <td class="px-6 py-4 text-center print-hidden">
                                <a href="{{ route('supplier-ledgers.show', $supplier->id) }}" class="inline-flex items-center bg-indigo-50 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-600 hover:text-white px-4 py-2 rounded-lg text-xs font-bold transition-all border border-indigo-100 dark:border-indigo-800">
                                    <i class="fa-solid fa-eye mr-1.5"></i> Ledger
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-10 text-center text-gray-400">No data found.</td></tr>
                    @endforelse
                    </tbody>
                </table>
                <div class="p-4 print-hidden">{{ $suppliers->links() }}</div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.searchable-select').select2({
                placeholder: "-- Search Supplier --",
                allowClear: true,
                width: '100%',
                dropdownParent: $('.supplier-dropdown-wrapper')
            });
        });
    </script>
</x-app-layout>
