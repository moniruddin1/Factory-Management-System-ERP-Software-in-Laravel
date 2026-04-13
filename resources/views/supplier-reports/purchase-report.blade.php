<x-app-layout>
    <style>
        /* --- Professional Print Logic --- */
        @media print {
            /* পেজ সাইজ এবং মার্জিন সেট করা */
            @page { size: A4 portrait; margin: 15mm 10mm; }

            /* 🔴 স্ক্রলবার কিলার: যেকোনো এলিমেন্টের স্ক্রলবার এবং হাইট লিমিটেশন বন্ধ করা 🔴 */
            *, html, body, .main-content, .overflow-x-auto, .overflow-y-auto, .overflow-auto {
                overflow: visible !important;
                height: auto !important;
                min-height: 0 !important;
                max-height: none !important;
            }

            html, body {
                background: white !important;
                color: black !important;
            }

            /* সাইডবার, নেভিগেশন এবং অপ্রয়োজনীয় অংশ হাইড করার অ্যাডভান্সড রুল */
            nav, aside, header, footer, .print-hidden,
            #sidebar, .sidebar, .main-sidebar, [data-sidebar],
            [class*="sidebar"], .fixed.z-50, .fixed.inset-y-0 {
                display: none !important;
                width: 0 !important;
                margin: 0 !important;
                padding: 0 !important;
                opacity: 0 !important;
                visibility: hidden !important;
            }

            /* সাইডবারের জন্য রাখা ফাঁকা স্পেস (Margin/Padding) রিমুভ করা */
            .sm\:ml-64, .md\:ml-64, .lg\:ml-64, .xl\:ml-64,
            .sm\:pl-64, .md\:pl-64, .lg\:pl-64, .xl\:pl-64,
            main, #main-content {
                margin-left: 0 !important;
                padding-left: 0 !important;
            }

            /* মেইন কন্টেন্টকে পুরো পেজ জুড়ে দেওয়া */
            .main-content {
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
                display: block !important;
                box-shadow: none !important;
            }

            /* প্রিন্ট হেডার */
            .print-header {
                display: block !important;
                text-align: center;
                margin-bottom: 25px;
                border-bottom: 2px solid #000;
                padding-bottom: 15px;
            }

            /* টেবিলের স্টাইল */
            table { width: 100% !important; border-collapse: collapse !important; border: 1px solid #000 !important; }
            th { background-color: #f0f0f0 !important; color: #000 !important; border: 1px solid #000 !important; padding: 10px !important; font-size: 13px; }
            td { border: 1px solid #000 !important; padding: 8px 10px !important; font-size: 13px !important; color: #000 !important; }

            /* ডার্ক মোড কালার রিমুভ করা */
            .text-emerald-600, .text-emerald-700, .text-gray-500, .dark\:text-white { color: #000 !important; }

            /* সিগনেচার সেকশন পজিশন */
            .signature-section {
                display: flex !important;
                justify-content: space-between;
                margin-top: 80px !important;
                page-break-inside: avoid;
            }
        }
    </style>

    <div class="py-6 bg-gray-50 dark:bg-[#0f172a] min-h-screen font-sans main-content">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Action & Filter Bar (Hidden in Print) --}}
            <div class="bg-white dark:bg-[#1e293b] p-5 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 mb-6 print-hidden">
                <form method="GET" action="{{ url()->current() }}" class="flex flex-wrap items-end gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase mb-1">Start Date</label>
                        <input type="date" name="start_date" value="{{ $startDate }}" class="bg-gray-50 dark:bg-slate-800 border border-gray-300 dark:border-slate-600 text-gray-800 dark:text-gray-200 text-sm rounded-lg block p-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase mb-1">End Date</label>
                        <input type="date" name="end_date" value="{{ $endDate }}" class="bg-gray-50 dark:bg-slate-800 border border-gray-300 dark:border-slate-600 text-gray-800 dark:text-gray-200 text-sm rounded-lg block p-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg text-sm font-bold shadow-md transition flex items-center gap-2">
                            <i class="fa-solid fa-filter"></i> Filter
                        </button>
                        <button type="button" onclick="window.print()" class="bg-gray-800 hover:bg-gray-900 text-white px-5 py-2.5 rounded-lg text-sm font-bold shadow-md transition flex items-center gap-2">
                            <i class="fa-solid fa-print"></i> Print
                        </button>
                    </div>
                    <div class="ml-auto">
                        <a href="{{ route('supplier-reports.index') }}" class="flex items-center gap-2 text-sm font-bold text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 transition-colors bg-indigo-50 dark:bg-indigo-900/30 px-4 py-2 rounded-lg">
                            <i class="fa-solid fa-arrow-left"></i> Back to Reports
                        </a>
                    </div>
                </form>
            </div>

            {{-- Printable Report Area --}}
            <div class="bg-white dark:bg-[#1e293b] rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6 md:p-8">

                {{-- Print Header (Only visible during print) --}}
                <div class="text-center border-b border-gray-200 dark:border-slate-700 pb-4 mb-6 print-header hidden">
                    <h1 class="text-3xl font-black uppercase text-gray-900 dark:text-white">
                        {{ App\Models\CompanyInfo::first()->invoice_title ?? 'SHOE ERP SYSTEM' }}
                    </h1>
                    <h2 class="text-xl font-bold text-gray-600 dark:text-slate-300 mt-1">Purchase Summary Report</h2>
                    <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">
                        Period: <strong>{{ date('d-M-Y', strtotime($startDate)) }}</strong> to <strong>{{ date('d-M-Y', strtotime($endDate)) }}</strong>
                    </p>
                </div>

                {{-- Screen Header --}}
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 print-hidden">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800 dark:text-white tracking-tight">Purchase Summary</h2>
                        <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">
                            Showing records from <span class="font-semibold text-gray-700 dark:text-slate-300">{{ date('d-M-Y', strtotime($startDate)) }}</span> to <span class="font-semibold text-gray-700 dark:text-slate-300">{{ date('d-M-Y', strtotime($endDate)) }}</span>
                        </p>
                    </div>
                    <div class="text-right bg-emerald-50 dark:bg-emerald-900/20 px-5 py-3 rounded-xl border border-emerald-200 dark:border-emerald-800/50 shadow-sm">
                        <p class="text-xs font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-1">Total Purchase</p>
                        <h3 class="text-2xl font-black text-emerald-700 dark:text-emerald-300">৳ {{ number_format($totalPurchase, 2) }}</h3>
                    </div>
                </div>

                {{-- Data Table --}}
                <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-slate-700">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead>
                        <tr class="bg-gray-100 dark:bg-slate-800 text-gray-700 dark:text-slate-300 uppercase text-xs font-bold border-b border-gray-200 dark:border-slate-700">
                            <th class="p-4 whitespace-nowrap">Sl.</th>
                            <th class="p-4 whitespace-nowrap">Date</th>
                            <th class="p-4 whitespace-nowrap">Invoice No</th>
                            <th class="p-4 whitespace-nowrap">Supplier Name</th>
                            <th class="p-4 text-right whitespace-nowrap">Amount</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-slate-700 bg-white dark:bg-[#1e293b]">
                        @forelse($purchases as $index => $purchase)
                            <tr class="hover:bg-gray-50 dark:hover:bg-slate-800 transition-colors">
                                <td class="p-4 text-gray-500 dark:text-slate-400 font-medium">{{ $index + 1 }}</td>
                                <td class="p-4 text-gray-800 dark:text-slate-200 whitespace-nowrap font-medium">
                                    {{ date('d-M-Y', strtotime($purchase->purchase_date)) }}
                                </td>
                                <td class="p-4">
                                        <span class="font-mono text-xs font-bold bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-slate-300 px-2 py-1 rounded">
                                            {{ $purchase->invoice_no ?? 'N/A' }}
                                        </span>
                                </td>
                                <td class="p-4 font-bold text-gray-900 dark:text-white">
                                    {{ $purchase->supplier->company_name ?? 'Unknown Supplier' }}
                                </td>
                                <td class="p-4 text-right font-black text-gray-800 dark:text-slate-200 text-base">
                                    {{ number_format($purchase->grand_total, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-10 text-center">
                                    <div class="flex flex-col items-center justify-center text-gray-400 dark:text-slate-500">
                                        <i class="fa-solid fa-file-invoice-dollar text-4xl mb-3 opacity-50"></i>
                                        <p class="font-medium text-lg">No purchase records found</p>
                                        <p class="text-sm mt-1">Try adjusting the date range to find more records.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                        <tfoot class="bg-gray-100 dark:bg-slate-800 border-t-2 border-gray-300 dark:border-slate-600">
                        <tr>
                            <td colspan="4" class="p-4 font-black text-right text-gray-800 dark:text-white uppercase tracking-wider text-sm">
                                Grand Total:
                            </td>
                            <td class="p-4 text-right font-black text-xl text-emerald-600 dark:text-emerald-400">
                                {{ number_format($totalPurchase, 2) }}
                            </td>
                        </tr>
                        </tfoot>
                    </table>
                </div>

                {{-- Signatures for Print --}}
                <div class="hidden print-header signature-section">
                    <div class="border-t border-black pt-2 w-48 text-center text-sm font-bold text-black">
                        Prepared By
                    </div>
                    <div class="border-t border-black pt-2 w-48 text-center text-sm font-bold text-black">
                        Authorized Signature
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
