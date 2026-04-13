<div class="print-ledger-container p-6 sm:p-8">
    {{-- প্রিন্টের সময় স্ক্রলবার এবং পেজ সাইজ ঠিক করার জন্য নিচের এই ছোট স্টাইল ব্লকটি খুব জরুরি --}}
    <style>
        @media print {
            .no-print-scroll {
                overflow: visible !important;
                display: block !important;
            }
            .ledger-table {
                width: 100% !important;
                table-layout: fixed !important; /* টেবিলকে পেজের বাইরে যেতে দেবে না */
                border-collapse: collapse !important;
            }
            .ledger-table th, .ledger-table td {
                word-wrap: break-word !important;
                white-space: normal !important; /* লেখা লম্বা হলে ভেঙ্গে নিচে নামাবে */
                font-size: 10px !important;
                padding: 4px !important;
            }
            @page {
                size: auto;
                margin: 10mm;
            }
        }
    </style>

    <div class="print-header hidden print:block">
        <h1 style="text-align: center; font-size: 20px; font-weight: bold;">{{ App\Models\CompanyInfo::first()->company_name ?? 'SHOE ERP SYSTEM' }}</h1>
        <p style="margin: 5px 0; text-align: center;">Supplier Ledger Statement</p>
        @if(request('start_date') && request('end_date'))
            <p style="font-size: 13px; text-align: center;">Period: {{ date('d-M-Y', strtotime(request('start_date'))) }} to {{ date('d-M-Y', strtotime(request('end_date'))) }}</p>
        @endif
    </div>

    <table class="info-table text-sm text-gray-700 dark:text-slate-300 w-full mb-6 print:text-xs">
        <tr>
            <td style="width: 120px; font-weight: bold;">Supplier Code:</td>
            <td class="font-semibold text-indigo-600 dark:text-indigo-400">{{ $supplier->code ?? 'N/A' }}</td>
            <td style="width: 120px; font-weight: bold;">Print Date:</td>
            <td>{{ date('d-M-Y h:i A') }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Company Name:</td>
            <td class="font-bold text-gray-900 dark:text-white">{{ $supplier->company_name }}</td>
            <td style="font-weight: bold;">Contact Person:</td>
            <td>{{ $supplier->contact_person ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Phone:</td>
            <td>{{ $supplier->phone }}</td>
            <td style="font-weight: bold;">Address:</td>
            <td class="print:whitespace-normal">{{ $supplier->address ?? 'N/A' }}</td>
        </tr>
    </table>

    @php
        $openingBalance = $supplier->opening_balance ?? 0;
        $runningBalance = $openingBalance;
        $totalDebit = 0;
        $totalCredit = 0;
    @endphp

    {{-- 'no-print-scroll' ক্লাসটি অ্যাড করা হয়েছে --}}
    <div class="overflow-x-auto no-print-scroll print:w-full">
        <table class="ledger-table w-full text-left text-sm whitespace-nowrap print:whitespace-normal print:text-xs border border-gray-300 dark:border-slate-600 border-collapse">
            <thead class="bg-gray-100 dark:bg-slate-800 text-gray-700 dark:text-slate-300">
            <tr>
                <th style="width: 12%;" class="px-4 py-3 border border-gray-300 dark:border-slate-600 text-center">Date</th>
                <th style="width: 28%;" class="px-4 py-3 border border-gray-300 dark:border-slate-600 text-left">Particulars</th>
                <th style="width: 15%;" class="px-4 py-3 border border-gray-300 dark:border-slate-600 text-center">Ref / No</th>
                <th style="width: 15%;" class="px-4 py-3 border border-gray-300 dark:border-slate-600 text-right">Debit (OUT)</th>
                <th style="width: 15%;" class="px-4 py-3 border border-gray-300 dark:border-slate-600 text-right">Credit (IN)</th>
                <th style="width: 15%;" class="px-4 py-3 border border-gray-300 dark:border-slate-600 text-right font-bold">Balance (DUE)</th>
            </tr>
            </thead>
            <tbody class="text-gray-700 dark:text-slate-300">

            <tr class="bg-indigo-50/50 dark:bg-slate-800/50">
                <td class="px-4 py-2 border border-gray-300 text-center text-gray-500">
                    {{ $supplier->created_at ? date('d-M-y', strtotime($supplier->created_at)) : '-' }}
                </td>
                <td class="px-4 py-2 border border-gray-300 text-left font-semibold text-gray-900 dark:text-white">
                    Opening Balance (B/F)
                </td>
                <td class="px-4 py-2 border border-gray-300 text-center">-</td>
                <td class="px-4 py-2 border border-gray-300 text-right">-</td>
                <td class="px-4 py-2 border border-gray-300 text-right">-</td>
                <td class="px-4 py-2 border border-gray-300 text-right font-bold {{ $runningBalance > 0 ? 'text-rose-600 print:text-black' : '' }}">
                    {{ number_format(abs($runningBalance), 2) }} {{ $runningBalance > 0 ? '' : '' }}
                </td>
            </tr>

            @forelse($transactions as $txn)
                @php
                    $runningBalance += ($txn->credit - $txn->debit);
                    $totalDebit += $txn->debit;
                    $totalCredit += $txn->credit;
                @endphp
                <tr>
                    <td class="px-4 py-2 border border-gray-300 text-center">
                        {{ date('d-M-y', strtotime($txn->date)) }}
                    </td>
                    <td class="px-4 py-2 border border-gray-300 text-left">
                        {{ $txn->type }}
                    </td>
                    <td class="px-4 py-2 border border-gray-300 text-center">
                        {{ $txn->ref_no ?? '-' }}
                    </td>
                    <td class="px-4 py-2 border border-gray-300 text-right">
                        {{ $txn->debit > 0 ? number_format($txn->debit, 2) : '-' }}
                    </td>
                    <td class="px-4 py-2 border border-gray-300 text-right">
                        {{ $txn->credit > 0 ? number_format($txn->credit, 2) : '-' }}
                    </td>
                    <td class="px-4 py-2 border border-gray-300 text-right font-semibold">
                        {{ number_format(abs($runningBalance), 2) }} {{ $runningBalance > 0 ? '' : '' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-gray-500">No transactions found.</td>
                </tr>
            @endforelse

            </tbody>
            <tfoot class="bg-gray-100 dark:bg-slate-800 font-bold text-gray-900 dark:text-slate-200">
            <tr>
                <td colspan="3" class="px-4 py-3 border border-gray-300 dark:border-slate-600 text-right uppercase">
                    Total :
                </td>
                <td class="px-4 py-3 border border-gray-300 dark:border-slate-600 text-right text-emerald-600 dark:text-emerald-400">
                    ৳ {{ number_format($totalDebit, 2) }}
                </td>
                <td class="px-4 py-3 border border-gray-300 dark:border-slate-600 text-right text-rose-600 dark:text-rose-400">
                    ৳ {{ number_format($totalCredit, 2) }}
                </td>
                <td class="px-4 py-3 border border-gray-300 dark:border-slate-600 text-right text-base {{ $runningBalance > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-emerald-600 dark:text-emerald-400' }} print:text-black">
                    ৳ {{ number_format(abs($runningBalance), 2) }} {{ $runningBalance > 0 ? '(Due)' : 'Dr' }}
                </td>
            </tr>
            </tfoot>
        </table>
    </div>

    <div class="mt-16 hidden print:flex justify-between w-full text-sm font-semibold" style="border-top: 1px solid #ccc; padding-top: 10px;">
        <div style="width: 200px; text-align: center; border-top: 1px dashed #000; padding-top: 5px; margin-top: 50px;">Prepared By</div>
        <div style="width: 200px; text-align: center; border-top: 1px dashed #000; padding-top: 5px; margin-top: 50px;">Authorized Signature</div>
    </div>
</div>
