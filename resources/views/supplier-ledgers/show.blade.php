<x-app-layout>
    <div class="py-6 bg-gray-50 dark:bg-[#0f172a] min-h-screen font-sans transition-colors duration-200 no-print">
        <div class="w-full mx-auto px-4 sm:px-6 lg:px-8 max-w-6xl">

            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                <a href="{{ route('supplier-ledgers.index') }}" class="bg-white dark:bg-[#1e293b] border border-gray-300 dark:border-slate-600 hover:bg-gray-50 dark:hover:bg-slate-800 text-gray-700 dark:text-white px-4 py-2 rounded-lg text-sm font-medium transition shadow-sm flex items-center">
                    <i class="fa-solid fa-arrow-left mr-2"></i> Back to Suppliers
                </a>

                <div class="flex gap-2">
                    <button onclick="window.print()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg text-sm font-medium transition shadow-sm flex items-center">
                        <i class="fa-solid fa-print mr-2"></i> Print Ledger
                    </button>
                </div>
            </div>

            <div class="bg-white dark:bg-[#1e293b] rounded-xl shadow-sm border border-gray-200 dark:border-slate-700/50 p-5 mb-6">
                <form method="GET" action="{{ route('supplier-ledgers.show', $supplier->id) }}" class="flex flex-wrap items-end gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase mb-1">Date From</label>
                        <input type="date" name="start_date" value="{{ request('start_date') }}" class="bg-gray-50 dark:bg-[#0f172a] border border-gray-300 dark:border-slate-600 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-indigo-500 px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase mb-1">Date To</label>
                        <input type="date" name="end_date" value="{{ request('end_date') }}" class="bg-gray-50 dark:bg-[#0f172a] border border-gray-300 dark:border-slate-600 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-indigo-500 px-3 py-2">
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="bg-slate-800 hover:bg-slate-900 dark:bg-indigo-600 dark:hover:bg-indigo-700 text-white px-5 py-2 rounded-lg text-sm font-medium transition shadow-sm">
                            Filter
                        </button>
                        <a href="{{ route('supplier-ledgers.show', $supplier->id) }}" class="bg-gray-200 hover:bg-gray-300 dark:bg-slate-700 dark:hover:bg-slate-600 text-gray-700 dark:text-white px-5 py-2 rounded-lg text-sm font-medium transition shadow-sm">
                            Clear
                        </a>
                    </div>
                </form>
            </div>

            <div class="bg-white dark:bg-[#1e293b] rounded-xl shadow-sm border border-gray-200 dark:border-slate-700/50 overflow-hidden">
                @include('supplier-ledgers.partials.ledger-content')
            </div>

        </div>
    </div>

    <div class="print-only-wrapper" id="printPreview">
        @include('supplier-ledgers.partials.ledger-content')
    </div>

    <style>
        .print-only-wrapper { display: none; }

        @media print {
            .no-print { display: none !important; }
            .print-only-wrapper { display: block; background: #fff; }

            body {
                background-color: white !important;
                color: black !important;
                margin: 0;
                padding: 0;
            }

            @page {
                margin: 15mm;
                size: A4 portrait;
            }

            .print-ledger-container {
                width: 100%;
                font-family: Arial, sans-serif;
                color: #000;
            }

            .print-header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
            .print-header h1 { font-size: 24px; font-weight: bold; margin: 0; text-transform: uppercase; }

            .info-table { width: 100%; margin-bottom: 20px; font-size: 14px; }
            .info-table td { padding: 2px 0; border: none !important; }

            .ledger-table { width: 100%; border-collapse: collapse; font-size: 13px; }
            .ledger-table th, .ledger-table td { border: 1px solid #000 !important; padding: 6px 8px; text-align: right; }
            .ledger-table th { background-color: #f3f4f6 !important; -webkit-print-color-adjust: exact; font-weight: bold; text-align: center;}
            .ledger-table td.text-left { text-align: left; }
            .ledger-table td.text-center { text-align: center; }

            /* Hide Dark Mode Elements in Print */
            .dark-text { color: #000 !important; }
            .dark-bg { background-color: transparent !important; border: none !important; }
        }
    </style>
</x-app-layout>
