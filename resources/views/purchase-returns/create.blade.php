<x-app-layout>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <div class="py-6 bg-gray-50 dark:bg-[#0f172a] min-h-screen font-sans">
        <div class="w-full mx-auto px-4 sm:px-6 lg:px-8 max-w-5xl">

            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Create Purchase Return</h2>
                    <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">Return products to supplier</p>
                </div>
                <a href="{{ route('purchase-returns.index') }}" class="bg-white dark:bg-[#1e293b] border border-gray-200 dark:border-slate-700 hover:bg-gray-50 text-gray-700 dark:text-slate-300 px-4 py-2 rounded-lg text-sm font-medium transition shadow-sm">
                    <i class="fa-solid fa-list mr-2"></i> Return List
                </a>
            </div>

            @if(session('error'))
                <div class="bg-rose-100 border border-rose-400 text-rose-700 px-4 py-3 rounded relative mb-4">
                    <strong>Oops!</strong> {{ session('error') }}
                </div>
            @endif

            <div class="bg-white dark:bg-[#1e293b] rounded-xl shadow-sm border border-gray-200 dark:border-slate-700/50 p-6">
                <form action="{{ route('purchase-returns.store') }}" method="POST">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">Supplier <span class="text-red-500">*</span></label>
                            <select name="supplier_id" id="supplier_id" required class="w-full rounded-lg border-gray-300">
                                <option value="" disabled selected>-- Select Supplier --</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->company_name }} ({{ $supplier->phone }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">Invoice / Purchase <span class="text-red-500">*</span></label>
                            <select name="purchase_id" id="purchase_id" required class="w-full rounded-lg border-gray-300">
                                <option value="" disabled selected>-- Select Invoice --</option>
                                @foreach($purchases as $purchase)
                                    <option value="{{ $purchase->id }}">{{ $purchase->invoice_no }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">Return Date <span class="text-red-500">*</span></label>
                            <input type="date" name="return_date" value="{{ date('Y-m-d') }}" required class="w-full rounded-lg border border-gray-300 dark:border-slate-700 bg-gray-50 dark:bg-[#0f172a] text-gray-900 dark:text-white">
                        </div>
                    </div>

                    <div class="overflow-x-auto mb-6 border border-gray-200 dark:border-slate-700 rounded-lg">
                        <table class="w-full text-left text-sm text-gray-600 dark:text-slate-300">
                            <thead class="bg-gray-50 dark:bg-slate-800 border-b border-gray-200 dark:border-slate-700">
                            <tr>
                                <th class="px-4 py-3">Product Name</th>
                                <th class="px-4 py-3 text-center">Purchased Qty</th>
                                <th class="px-4 py-3 text-right">Unit Price (৳)</th>
                                <th class="px-4 py-3 text-center" style="width: 150px;">Return Qty</th>
                                <th class="px-4 py-3 text-right">Return Total (৳)</th>
                            </tr>
                            </thead>
                            <tbody id="items_table_body">
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500">Please select an invoice to load items.</td>
                            </tr>
                            </tbody>
                            <tfoot class="bg-gray-50 dark:bg-slate-800 border-t border-gray-200 dark:border-slate-700 font-bold">
                            <tr>
                                <td colspan="4" class="px-4 py-3 text-right text-gray-900 dark:text-white">Grand Total Return:</td>
                                <td class="px-4 py-3 text-right text-rose-600 dark:text-rose-400">৳ <span id="grand_total">0.00</span></td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">Return Reason</label>
                            <input type="text" name="reason" placeholder="e.g. Damaged, Wrong Item" class="w-full rounded-lg border border-gray-300 dark:border-slate-700 bg-gray-50 dark:bg-[#0f172a] text-gray-900 dark:text-white p-2.5">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">Notes</label>
                            <input type="text" name="note" placeholder="Any additional notes..." class="w-full rounded-lg border border-gray-300 dark:border-slate-700 bg-gray-50 dark:bg-[#0f172a] text-gray-900 dark:text-white p-2.5">
                        </div>
                    </div>

                    <div class="text-right">
                        <button type="submit" id="submit_btn" disabled class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg font-medium transition disabled:opacity-50 disabled:cursor-not-allowed">
                            <i class="fa-solid fa-save mr-2"></i> Submit Return
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#supplier_id, #purchase_id').select2();

            // When Purchase Invoice is selected
            $('#purchase_id').on('change', function() {
                let purchaseId = $(this).val();
                let tableBody = $('#items_table_body');

                tableBody.html('<tr><td colspan="5" class="text-center py-4"><i class="fa-solid fa-spinner fa-spin text-blue-500 text-2xl"></i> Loading items...</td></tr>');

                $.ajax({
                    url: '/purchase-returns/get-purchase-items/' + purchaseId,
                    type: 'GET',
                    success: function(data) {
                        tableBody.empty();
                        if(data.length === 0) {
                            tableBody.html('<tr><td colspan="5" class="text-center py-4 text-red-500">No items found in this invoice.</td></tr>');
                            $('#submit_btn').prop('disabled', true);
                            return;
                        }

                        let html = '';
                        $.each(data, function(index, item) {
                            html += `
                                <tr class="border-b border-gray-100 dark:border-slate-700/50">
                                    <td class="px-4 py-3">
                                        <input type="hidden" name="items[${index}][product_id]" value="${item.product_id}">
                                        <input type="hidden" name="items[${index}][product_name]" value="${item.product_name}">
                                        <input type="hidden" name="items[${index}][unit_price]" value="${item.unit_price}">
                                        <span class="font-medium text-gray-900 dark:text-white">${item.product_name}</span>
                                    </td>
                                    <td class="px-4 py-3 text-center bg-gray-50 dark:bg-slate-800 text-gray-500">${item.purchased_qty}</td>
                                    <td class="px-4 py-3 text-right">৳ ${parseFloat(item.unit_price).toFixed(2)}</td>
                                    <td class="px-4 py-3">
                                        <input type="number" name="items[${index}][return_qty]"
                                            class="return-qty w-full text-center border border-gray-300 dark:border-slate-600 rounded bg-white dark:bg-[#0f172a] text-gray-900 dark:text-white p-1 focus:ring-rose-500 focus:border-rose-500"
                                            min="0" max="${item.purchased_qty}" value="0" step="0.01"
                                            data-price="${item.unit_price}">
                                    </td>
                                    <td class="px-4 py-3 text-right font-bold text-gray-900 dark:text-white item-total">৳ 0.00</td>
                                </tr>
                            `;
                        });

                        tableBody.html(html);
                        calculateGrandTotal();
                    },
                    error: function() {
                        tableBody.html('<tr><td colspan="5" class="text-center py-4 text-red-500">Failed to load items.</td></tr>');
                    }
                });
            });

            // Calculate totals dynamically
            $(document).on('input', '.return-qty', function() {
                let maxQty = parseFloat($(this).attr('max'));
                let currentQty = parseFloat($(this).val()) || 0;

                // Prevent returning more than purchased
                if(currentQty > maxQty) {
                    $(this).val(maxQty);
                    currentQty = maxQty;
                }

                let price = parseFloat($(this).data('price'));
                let total = currentQty * price;

                $(this).closest('tr').find('.item-total').text('৳ ' + total.toFixed(2));
                calculateGrandTotal();
            });

            function calculateGrandTotal() {
                let grandTotal = 0;
                $('.return-qty').each(function() {
                    let qty = parseFloat($(this).val()) || 0;
                    let price = parseFloat($(this).data('price')) || 0;
                    grandTotal += (qty * price);
                });

                $('#grand_total').text(grandTotal.toFixed(2));

                // Enable/Disable submit button
                if(grandTotal > 0) {
                    $('#submit_btn').prop('disabled', false);
                } else {
                    $('#submit_btn').prop('disabled', true);
                }
            }
        });
    </script>
</x-app-layout>
