<x-app-layout>
    <style>
        /* --- Professional Print System --- */
        @media print {
            @page { size: A4 portrait; margin: 10mm; }
            html, body { overflow: visible !important; height: auto !important; background: white !important; }
            nav, aside, .print-hidden, header, footer { display: none !important; }
            .main-content { padding: 0 !important; margin: 0 !important; width: 100% !important; display: block !important; }

            table { width: 100% !important; border-collapse: collapse !important; border: 1px solid #000 !important; }
            th { background-color: #f2f2f2 !important; border: 1px solid #000 !important; padding: 6px !important; font-size: 12px; color: #000 !important; }
            td { border: 1px solid #000 !important; padding: 6px !important; font-size: 12px !important; color: #000 !important; }
            .print-header { display: block !important; text-align: center; margin-bottom: 20px; }
            .text-rose-600, .text-emerald-600 { color: black !important; font-weight: bold !important; }
        }
    </style>

    <div class="py-6 bg-gray-50 dark:bg-[#0f172a] min-h-screen font-sans main-content">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Print Header --}}
            <div class="hidden print-header">
                <h1 class="text-2xl font-black uppercase">{{ App\Models\CompanyInfo::first()->company_name ?? 'SHOE ERP SYSTEM' }}</h1>
                <p class="font-bold text-lg">All Supplier Due & Balance Report</p>
                <p class="text-sm">Printed on: {{ date('d-M-Y h:i A') }}</p>
                <hr class="my-3 border-black">
            </div>

            <div class="flex justify-between items-center mb-6 print-hidden">
                <div class="flex items-center gap-3">
                    <a href="{{ route('supplier-reports.index') }}" class="bg-gray-200 dark:bg-slate-700 text-gray-700 dark:text-white px-3 py-2 rounded-lg hover:bg-gray-300 dark:hover:bg-slate-600 transition">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white tracking-tight">Due & Balance Report</h2>
                </div>
                <button onclick="window.print()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg text-sm font-bold shadow-md transition flex items-center gap-2">
                    <i class="fa-solid fa-print"></i> Print Summary
                </button>
            </div>

            {{-- Summary Cards (Hidden in Print for cleaner list, or you can adjust to show) --}}
            <div class="grid grid-cols-2 gap-4 mb-6 print-hidden">
                <div class="bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800/50 rounded-xl p-5">
                    <p class="text-sm font-bold text-rose-600 dark:text-rose-400 uppercase">Total Payable (We Owe)</p>
                    <h3 class="text-2xl font-black text-rose-700 dark:text-rose-300">৳ {{ number_format($totalPayable, 2) }}</h3>
                </div>
                <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800/50 rounded-xl p-5">
                    <p class="text-sm font-bold text-emerald-600 dark:text-emerald-400 uppercase">Total Advance (We Paid)</p>
                    <h3 class="text-2xl font-black text-emerald-700 dark:text-emerald-300">৳ {{ number_format(abs($totalAdvance), 2) }}</h3>
                </div>
            </div>

            {{-- Data Table --}}
            <div class="bg-white dark:bg-[#1e293b] rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden">
                <table class="w-full text-left text-sm border-collapse">
                    <thead>
                    <tr class="bg-gray-100 dark:bg-slate-800/80 text-gray-600 dark:text-slate-300 uppercase text-xs font-bold">
                        <th class="px-6 py-4 border-b border-gray-200 dark:border-slate-700">Sl.</th>
                        <th class="px-6 py-4 border-b border-gray-200 dark:border-slate-700">Supplier Info</th>
                        <th class="px-6 py-4 border-b border-gray-200 dark:border-slate-700">Contact</th>
                        <th class="px-6 py-4 text-right border-b border-gray-200 dark:border-slate-700">Balance Amount</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-slate-700/50">
                    @forelse($suppliers as $index => $supplier)
                        <tr class="hover:bg-gray-50 dark:hover:bg-slate-800/30 transition-colors">
                            <td class="px-6 py-4 text-gray-500 dark:text-slate-400 font-medium">{{ $index + 1 }}</td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900 dark:text-white">{{ $supplier->company_name }}</div>
                                <div class="text-xs font-mono text-gray-500 dark:text-slate-400">{{ $supplier->code }}</div>
                            </td>
                            <td class="px-6 py-4 text-gray-600 dark:text-slate-300 text-xs">
                                <span class="font-bold">{{ $supplier->contact_person }}</span><br>
                                {{ $supplier->phone }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="text-base font-black {{ $supplier->current_balance > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-emerald-600 dark:text-emerald-400' }}">
                                    ৳ {{ number_format(abs($supplier->current_balance), 2) }}
                                </div>
                                <span class="text-[10px] font-bold uppercase {{ $supplier->current_balance > 0 ? 'text-rose-500' : 'text-emerald-500' }}">
                                        {{ $supplier->current_balance > 0 ? 'Payable (Cr)' : 'Advance (Dr)' }}
                                    </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-gray-400 font-medium">No pending balances found.</td>
                        </tr>
                    @endforelse
                    </tbody>
                    <tfoot class="bg-gray-50 dark:bg-slate-800">
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-right font-black uppercase text-gray-800 dark:text-white text-sm">Net Payable Balance:</td>
                        <td class="px-6 py-4 text-right font-black text-lg {{ ($totalPayable + $totalAdvance) > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                            ৳ {{ number_format(abs($totalPayable + $totalAdvance), 2) }}
                        </td>
                    </tr>
                    </tfoot>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>
