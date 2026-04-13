<div class="print-ledger-container p-6 sm:p-8">

    <div class="print-header hidden print:block">
        <h1>{{ App\Models\Company::first()->invoice_title ?? 'SHOE ERP SYSTEM' }}</h1>
        <p style="margin: 5px 0;">Supplier Ledger Statement</p>
        @if(request('start_date') && request('end_date'))
            <p style="font-size: 13px;">Period: {{ date('d-M-Y', strtotime(request('start_date'))) }} to {{ date('d-M-Y', strtotime(request('end_date'))) }}</p>
        @endif
    </div>

    <table class="info-table text-sm text-gray-700 dark:text-slate-300 w-full mb-6">
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
            <td>{{ $supplier->address ?? 'N/A' }}</td>
        </tr>
    </table>

    @php
        // Calculation Logic
        $openingBalance = $supplier->opening_balance ?? 0;
        $runningBalance = $openingBalance;
        $totalDebit = 0;
        $totalCredit = 0;
    @endphp

    <div class="overflow-x-auto">
        <table class="ledger-table w-full text-left text-sm whitespace-nowrap border border-gray-300 dark:border-slate-600">
            <thead class="bg-gray-100 dark:bg-slate-800 text-gray-700 dark:text-slate-300">
            <tr>
                <th class="px-4 py-3 border border-gray-300 dark:border-slate-600 text-center w-24">Date</th>
                <th class="px-4 py-3 border border-gray-300 dark:border-slate-600 text-left">Particulars / Details</th>
                <th class="px-4 py-3 border border-gray-300 dark:border-slate-600 text-center w-32">Ref / Voucher No</th>
                <th class="px-4 py-3 border border-gray-300 dark:border-slate-600 text-right w-32">Debit (Dr) <br><small class="font-normal text-xs text-gray-500">(Payment/Return)</small></th>
                <th class="px-4 py-3 border border-gray-300 dark:border-slate-600 text-right w-32">Credit (Cr) <br><small class="font-normal text-xs text-gray-500">(Purchase)</small></th>
                <th class="px-4 py-3 border border-gray-300 dark:border-slate-600 text-right w-32 font-bold">Balance <br><small class="font-normal text-xs text-gray-500">(Due Amount)</small></th>
            </tr>
            </thead>
            <tbody class="text-gray-700 dark:text-slate-300">

            <tr class="bg-indigo-50/50 dark:bg-slate-800/50">
                <td class="px-4 py-2 border border-gray-300 dark:border-slate-600 text-center text-gray-500">
                    {{ $supplier->created_at ? date('d-M-y', strtotime($supplier->created_at)) : '-' }}
                </td>
                <td class="px-4 py-2 border border-gray-300 dark:border-slate-600 text-left font-semibold text-gray-900 dark:text-white">
                    Opening Balance (B/F)
                </td>
                <td class="px-4 py-2 border border-gray-300 dark:border-slate-600 text-center">-</td>
                <td class="px-4 py-2 border border-gray-300 dark:border-slate-600 text-right">-</td>
                <td class="px-4 py-2 border border-gray-300 dark:border-slate-600 text-right">-</td>
                <td class="px-4 py-2 border border-gray-300 dark:border-slate-600 text-right font-bold {{ $runningBalance > 0 ? 'text-rose-600 dark:text-rose-400' : '' }}">
                    {{ number_format($runningBalance, 2) }} {{ $runningBalance > 0 ? 'Cr' : '' }}
                </td>
            </tr>

            @forelse($transactions as $txn)
                @php
                    // Purchase = Credit (We owe them more)
                    // Payment/Return = Debit (We owe them less)
                    $runningBalance += ($txn->credit - $txn->debit);
                    $totalDebit += $txn->debit;
                    $totalCredit += $txn->credit;
                @endphp
                <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/30">
                    <td class="px-4 py-2 border border-gray-300 dark:border-slate-600 text-center text-left">
                        {{ date('d-M-y', strtotime($txn->date)) }}
                    </td>
                    <td class="px-4 py-2 border border-gray-300 dark:border-slate-600 text-left text-left">
                        {{ $txn->type }}
                        @if($txn->type == 'Payment')
                            <span class="text-xs text-green-600 dark:text-green-400 ml-2">(Paid)</span>
                        @elseif($txn->type == 'Purchase Return')
                            <span class="text-xs text-rose-600 dark:text-rose-400 ml-2">(Goods Returned)</span>
                        @endif
                    </td>
                    <td class="px-4 py-2 border border-gray-300 dark:border-slate-600 text-center text-center">
                        {{ $txn->ref_no ?? '-' }}
                    </td>
                    <td class="px-4 py-2 border border-gray-300 dark:border-slate-600 text-right">
                        {{ $txn->debit > 0 ? number_format($txn->debit, 2) : '-' }}
                    </td>
                    <td class="px-4 py-2 border border-gray-300 dark:border-slate-600 text-right">
                        {{ $txn->credit > 0 ? number_format($txn->credit, 2) : '-' }}
                    </td>
                    <td class="px-4 py-2 border border-gray-300 dark:border-slate-600 text-right font-semibold">
                        {{ number_format(abs($runningBalance), 2) }} {{ $runningBalance > 0 ? 'Cr' : 'Dr' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-gray-500">No transactions found for this supplier.</td>
                </tr>
            @endforelse

            </tbody>
            <tfoot class="bg-gray-100 dark:bg-slate-800 text-gray-900 dark:text-white font-bold">
            <tr>
                <td colspan="3" class="px-4 py-3 border border-gray-300 dark:border-slate-600 text-right uppercase">Total :</td>
                <td class="px-4 py-3 border border-gray-300 dark:border-slate-600 text-right">৳ {{ number_format($totalDebit, 2) }}</td>
                <td class="px-4 py-3 border border-gray-300 dark:border-slate-600 text-right">৳ {{ number_format($totalCredit, 2) }}</td>
                <td class="px-4 py-3 border border-gray-300 dark:border-slate-600 text-right text-rose-600 dark:text-rose-400 text-base">
                    ৳ {{ number_format(abs($runningBalance), 2) }} {{ $runningBalance > 0 ? 'Cr' : 'Dr' }}
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
