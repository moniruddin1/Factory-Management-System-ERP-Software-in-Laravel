<x-app-layout>
    <div class="py-6">
        <div class="max-w-[90rem] mx-auto px-4 sm:px-6 lg:px-8">

            <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200">
                        <i class="fa-solid fa-file-invoice-dollar mr-2 text-blue-500"></i> Purchase Invoices
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage all your supplier purchases, payments, and dues.</p>
                </div>
                <a href="{{ route('purchases.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 px-5 rounded-lg transition shadow-md flex items-center gap-2">
                    <i class="fa-solid fa-plus"></i> Create New Purchase
                </a>
            </div>

            @if(session('success'))
                <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-t-xl border-b dark:border-slate-700 p-4">
                <form action="#" method="GET" class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-5 gap-4">
                    <div>
                        <input type="text" name="search" placeholder="Search Invoice No..." class="w-full rounded border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm focus:ring-blue-500">
                    </div>
                    <div>
                        <input type="date" name="date" class="w-full rounded border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm focus:ring-blue-500">
                    </div>
                    <div>
                        <select name="status" class="w-full rounded border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm focus:ring-blue-500">
                            <option value="">Order Status (All)</option>
                            <option value="Received">Received</option>
                            <option value="Pending">Pending</option>
                        </select>
                    </div>
                    <div>
                        <select name="payment_status" class="w-full rounded border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm focus:ring-blue-500">
                            <option value="">Payment Status (All)</option>
                            <option value="Paid">Paid</option>
                            <option value="Partial">Partial</option>
                            <option value="Due">Due</option>
                        </select>
                    </div>
                    <div>
                        <button type="submit" class="w-full bg-gray-100 dark:bg-slate-700 hover:bg-gray-200 dark:hover:bg-slate-600 text-gray-800 dark:text-gray-200 font-medium py-2 px-4 rounded transition text-sm flex items-center justify-center gap-2 border border-gray-300 dark:border-slate-600">
                            <i class="fa-solid fa-filter"></i> Filter
                        </button>
                    </div>
                </form>
            </div>

            <div class="bg-white dark:bg-slate-800 shadow-sm rounded-b-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-[1000px]">
                        <thead>
                        <tr class="bg-gray-50 dark:bg-slate-700/50 text-gray-600 dark:text-gray-300 text-xs uppercase tracking-wider">
                            <th class="p-4 border-b dark:border-slate-700 font-semibold w-32">Invoice Info</th>
                            <th class="p-4 border-b dark:border-slate-700 font-semibold w-40">Date & Type</th>
                            <th class="p-4 border-b dark:border-slate-700 font-semibold">Supplier</th>
                            <th class="p-4 border-b dark:border-slate-700 font-semibold text-right w-40">Amount Details</th>
                            <th class="p-4 border-b dark:border-slate-700 font-semibold text-center w-28">Order</th>
                            <th class="p-4 border-b dark:border-slate-700 font-semibold text-center w-28">Payment</th>
                            <th class="p-4 border-b dark:border-slate-700 font-semibold text-center w-24">Actions</th>
                        </tr>
                        </thead>
                        <tbody class="text-gray-600 dark:text-gray-400 text-sm">
                        @forelse($purchases as $purchase)
                            <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50 transition border-b dark:border-slate-700 last:border-0 align-top">

                                <td class="p-4">
                                    <a href="#" class="font-bold text-blue-600 dark:text-blue-400 hover:underline block mb-1">
                                        {{ $purchase->invoice_no }}
                                    </a>
                                    @if($purchase->reference_no)
                                        <span class="text-xs text-gray-500 dark:text-gray-400">Ref: {{ $purchase->reference_no }}</span>
                                    @endif
                                </td>

                                <td class="p-4">
                                    <div class="font-medium text-gray-900 dark:text-gray-200 mb-1">
                                        {{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d M, Y') }}
                                    </div>
                                    <span class="inline-block px-2 py-0.5 rounded text-[10px] font-medium bg-gray-100 text-gray-600 dark:bg-slate-600 dark:text-gray-300 border border-gray-200 dark:border-slate-500">
                                            {{ $purchase->invoice_type ?? 'Credit' }}
                                        </span>
                                </td>

                                <td class="p-4">
                                    <div class="font-bold text-gray-800 dark:text-gray-200">
                                        {{ $purchase->supplier->company_name ?? 'N/A' }}
                                    </div>
                                    <div class="text-xs text-gray-500 mt-0.5">
                                        {{ $purchase->supplier->contact_person ?? '' }}
                                    </div>
                                </td>

                                <td class="p-4 text-right">
                                    <div class="font-bold text-gray-900 dark:text-gray-100 mb-1 text-base">
                                        ৳ {{ number_format($purchase->grand_total, 2) }}
                                    </div>
                                    <div class="text-xs text-green-600 dark:text-green-400 font-medium">
                                        Paid: ৳ {{ number_format($purchase->paid_amount ?? 0, 2) }}
                                    </div>
                                    @if(($purchase->due_amount ?? 0) > 0)
                                        <div class="text-xs text-red-500 dark:text-red-400 font-medium mt-0.5">
                                            Due: ৳ {{ number_format($purchase->due_amount, 2) }}
                                        </div>
                                    @endif
                                </td>

                                <td class="p-4 text-center align-middle">
                                    @if($purchase->status === 'Received')
                                        <span class="inline-flex items-center gap-1 bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-400 px-2 py-1 rounded text-xs font-semibold border border-green-200 dark:border-green-800/30">
                                                <i class="fa-solid fa-check-circle text-[10px]"></i> Received
                                            </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 bg-yellow-50 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400 px-2 py-1 rounded text-xs font-semibold border border-yellow-200 dark:border-yellow-800/30">
                                                <i class="fa-solid fa-clock text-[10px]"></i> Pending
                                            </span>
                                    @endif
                                </td>

                                <td class="p-4 text-center align-middle">
                                    @if(($purchase->due_amount ?? 0) <= 0 && ($purchase->grand_total ?? 0) > 0)
                                        <span class="bg-green-500 text-white px-2 py-1 rounded text-xs font-bold shadow-sm">Paid</span>
                                    @elseif(($purchase->paid_amount ?? 0) > 0 && ($purchase->due_amount ?? 0) > 0)
                                        <span class="bg-orange-500 text-white px-2 py-1 rounded text-xs font-bold shadow-sm">Partial</span>
                                    @else
                                        <span class="bg-red-500 text-white px-2 py-1 rounded text-xs font-bold shadow-sm">Unpaid</span>
                                    @endif
                                </td>

                                <td class="p-4 text-center align-middle">
                                <td class="p-4 text-center align-middle">
                                    <div class="flex justify-center items-center gap-3">
                                        <a href="{{ route('purchases.show', $purchase->id) }}" class="text-blue-500 hover:text-blue-700 transition" title="View Details">
                                            <i class="fa-solid fa-eye text-lg"></i>
                                        </a>
                                        <a href="{{ route('purchases.show', $purchase->id) }}" class="text-gray-500 hover:text-gray-800 transition" title="Print Invoice">
                                            <i class="fa-solid fa-print text-lg"></i>
                                        </a>
                                    </div>
                                </td>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-12 text-center text-gray-500 dark:text-gray-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-16 h-16 bg-gray-100 dark:bg-slate-700 rounded-full flex items-center justify-center mb-4 text-2xl text-gray-400">
                                            <i class="fa-solid fa-receipt"></i>
                                        </div>
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-200 mb-1">No Purchases Found</h3>
                                        <p class="text-sm">You haven't created any purchase invoices yet.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                @if($purchases->hasPages())
                    <div class="p-4 border-t border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-800">
                        {{ $purchases->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
