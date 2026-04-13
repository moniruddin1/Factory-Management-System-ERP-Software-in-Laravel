<x-app-layout>
    <div class="py-6 bg-gray-50 dark:bg-[#0f172a] min-h-screen font-sans transition-colors duration-200">
        <div class="w-full mx-auto px-4 sm:px-6 lg:px-8 max-w-4xl">

            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Add New Payment</h2>
                    <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">Record a new payment voucher for a supplier</p>
                </div>
                <a href="{{ route('supplier-payments.index') }}" class="bg-white dark:bg-[#1e293b] border border-gray-200 dark:border-slate-700 hover:bg-gray-50 dark:hover:bg-slate-800 text-gray-700 dark:text-slate-300 px-4 py-2 rounded-lg text-sm font-medium transition shadow-sm flex items-center">
                    <i class="fa-solid fa-arrow-left-long mr-2"></i> Back to List
                </a>
            </div>

            @if(session('error'))
                <div class="bg-rose-100 border border-rose-400 text-rose-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Oops!</strong>
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white dark:bg-[#1e293b] rounded-xl shadow-sm dark:shadow-lg border border-gray-200 dark:border-slate-700/50 overflow-hidden transition-colors duration-200 p-6 md:p-8">
                @if(session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                        <strong class="font-bold"><i class="fa-solid fa-triangle-exclamation"></i> Oops!</strong>
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                @endif

                <form action="{{ route('supplier-payments.store') }}" method="POST">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        @if($selectedPurchase)
                            <input type="hidden" name="supplier_id" value="{{ $selectedPurchase->supplier_id }}">
                            <input type="hidden" name="purchase_id" value="{{ $selectedPurchase->id }}">

                            <div class="md:col-span-1">
                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">Supplier</label>
                                <input type="text" readonly value="{{ optional($selectedPurchase->supplier)->company_name }}" class="bg-gray-100 dark:bg-slate-800 border border-gray-300 dark:border-slate-700 text-gray-500 dark:text-slate-400 text-sm rounded-lg block w-full px-4 py-2.5 cursor-not-allowed">
                            </div>

                            <div class="md:col-span-1">
                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">Paying Against Invoice</label>
                                <input type="text" readonly value="{{ $selectedPurchase->invoice_no }} (Due: ৳{{ $selectedPurchase->due_amount }})" class="bg-gray-100 dark:bg-slate-800 border border-gray-300 dark:border-slate-700 text-rose-600 dark:text-rose-400 font-medium text-sm rounded-lg block w-full px-4 py-2.5 cursor-not-allowed">
                            </div>

                        @else
                            <div class="md:col-span-1">
                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">Supplier <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <select name="supplier_id" required class="appearance-none bg-gray-50 dark:bg-[#0f172a] border border-gray-300 dark:border-slate-700 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-4 py-2.5 pr-8 cursor-pointer transition-colors">
                                        <option value="" disabled selected>-- Select a Supplier --</option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}">{{ $supplier->company_name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500 dark:text-slate-400">
                                        <i class="fa-solid fa-chevron-down text-xs"></i>
                                    </div>
                                </div>
                                @error('supplier_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div class="md:col-span-1">
                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">Against Invoice ID (Optional)</label>
                                <input type="number" name="purchase_id" placeholder="Database ID (e.g. 15)" class="bg-gray-50 dark:bg-[#0f172a] border border-gray-300 dark:border-slate-700 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-4 py-2.5 transition-colors placeholder-gray-400 dark:placeholder-slate-500">
                                <span class="text-xs text-blue-500 dark:text-blue-400 mt-1 block">Leave blank to auto-adjust pending dues.</span>
                            </div>
                        @endif

                        <div class="md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">Amount (৳) <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-gray-500 dark:text-slate-400 font-medium">৳</div>
                                <input type="number" step="0.01" name="amount" required
                                       @if($selectedPurchase) max="{{ $selectedPurchase->due_amount }}" @endif
                                       placeholder="0.00" class="bg-gray-50 dark:bg-[#0f172a] border border-gray-300 dark:border-slate-700 text-gray-900 dark:text-white text-sm font-bold rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-8 px-4 py-2.5 transition-colors">
                            </div>
                            @error('amount') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            @if($selectedPurchase)
                                <span class="text-xs text-rose-500 mt-1 block">Max payable amount: ৳{{ $selectedPurchase->due_amount }}</span>
                            @endif
                        </div>

                        <div class="md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">Payment Date <span class="text-red-500">*</span></label>
                            <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" required class="bg-gray-50 dark:bg-[#0f172a] border border-gray-300 dark:border-slate-700 text-gray-700 dark:text-slate-300 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-4 py-2.5 transition-colors">
                            @error('payment_date') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">Payment Method <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <select name="payment_method" required class="appearance-none bg-gray-50 dark:bg-[#0f172a] border border-gray-300 dark:border-slate-700 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-4 py-2.5 pr-8 cursor-pointer transition-colors">
                                    <option value="Cash">Cash</option>
                                    <option value="Bank Transfer">Bank Transfer</option>
                                    <option value="Mobile Banking">Mobile Banking (bKash/Nagad)</option>
                                    <option value="Cheque">Cheque</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500 dark:text-slate-400">
                                    <i class="fa-solid fa-chevron-down text-xs"></i>
                                </div>
                            </div>
                            @error('payment_method') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">Transaction Ref / Cheque No.</label>
                            <input type="text" name="transaction_ref" placeholder="e.g. TrxID / Cheque Number" class="bg-gray-50 dark:bg-[#0f172a] border border-gray-300 dark:border-slate-700 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-4 py-2.5 transition-colors placeholder-gray-400 dark:placeholder-slate-500">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">Note / Remarks (Optional)</label>
                            <textarea name="note" rows="3" placeholder="Add any details about this payment..." class="bg-gray-50 dark:bg-[#0f172a] border border-gray-300 dark:border-slate-700 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-4 py-2.5 transition-colors placeholder-gray-400 dark:placeholder-slate-500"></textarea>
                        </div>

                    </div>

                    <div class="mt-8 pt-6 border-t border-gray-200 dark:border-slate-700/50 flex flex-col sm:flex-row justify-end gap-3">
                        <button type="reset" class="bg-white dark:bg-[#0f172a] border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-800 px-6 py-2.5 rounded-lg text-sm font-medium transition shadow-sm w-full sm:w-auto text-center">
                            Reset Form
                        </button>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 dark:bg-[#3b82f6] dark:hover:bg-blue-600 text-white px-6 py-2.5 rounded-lg text-sm font-medium transition shadow-sm flex items-center justify-center w-full sm:w-auto">
                            <i class="fa-solid fa-save mr-2"></i> Save Payment
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</x-app-layout>
