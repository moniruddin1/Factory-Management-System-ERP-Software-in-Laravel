<x-app-layout>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <style>
        .select2-container .select2-selection--single { height: 42px; border: 1px solid #d1d5db; border-radius: 0.5rem; display: flex; align-items: center; }
        .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 42px; color: #374151; padding-left: 10px; width: 100%;}
        .select2-container--default .select2-selection--single .select2-selection__arrow { height: 40px; }
        .dark .select2-container--default .select2-selection--single { background-color: #0f172a; border-color: #334155; }
        .dark .select2-container--default .select2-selection--single .select2-selection__rendered { color: #f8fafc; }
        .dark .select2-dropdown { background-color: #1e293b; border-color: #334155; }
        .dark .select2-results__option { color: #cbd5e1; }
        .dark .select2-results__option[aria-selected=true] { background-color: #334155; }
        .dark .select2-container--default .select2-results__option--highlighted[aria-selected] { background-color: #3b82f6; color: white;}
        .dark .select2-search input { background-color: #0f172a; border-color: #334155; color: white; border-radius: 4px; }
    </style>

    <div class="py-6 bg-gray-50 dark:bg-[#0f172a] min-h-screen font-sans transition-colors duration-200">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="mb-6 flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Issue Material to Production</h2>
                    <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">Generate a voucher for transferring materials based on BOM</p>
                </div>
            </div>

            @if(session('success'))
                <div class="bg-emerald-100 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg mb-4 flex items-center text-sm">
                    <i class="fa-solid fa-circle-check mr-2"></i> {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="bg-rose-100 border border-rose-200 text-rose-700 px-4 py-3 rounded-lg mb-4 flex items-center text-sm">
                    <i class="fa-solid fa-circle-exclamation mr-2"></i> {{ session('error') }}
                </div>
            @endif

            <div class="bg-white dark:bg-[#1e293b] rounded-xl shadow-sm border border-gray-200 dark:border-slate-700/50 p-6">
                <form action="{{ route('inventory.issue.store') }}" method="POST" id="issueForm">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-4 bg-gray-50 dark:bg-[#0f172a]/50 p-4 rounded-t-lg border-t border-l border-r border-gray-100 dark:border-slate-700">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Issue Date <span class="text-rose-500">*</span></label>
                            <input type="date" name="date" value="{{ date('Y-m-d') }}" required class="bg-white dark:bg-[#0f172a] border border-gray-300 dark:border-slate-700 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-blue-500 block w-full p-2.5 h-[42px]">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Issued To (Staff) <span class="text-rose-500">*</span></label>
                            <select name="issued_to" id="staff_select" class="w-full text-sm" required>
                                <option value="">Search staff...</option>
                                @foreach($staffs as $staff)
                                    <option value="{{ $staff->id }}">{{ $staff->name }} ({{ $staff->designation }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Remarks / Note</label>
                            <input type="text" name="remarks" placeholder="Optional details..." class="bg-white dark:bg-[#0f172a] border border-gray-300 dark:border-slate-700 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-blue-500 block w-full p-2.5 h-[42px]">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8 bg-indigo-50/50 dark:bg-indigo-900/10 p-4 rounded-b-lg border border-gray-100 dark:border-slate-700">
                        <div>
                            <label class="block text-sm font-medium text-indigo-800 dark:text-indigo-300 mb-1">Select BOM (Formula) <i class="fa-solid fa-magic text-indigo-500 ml-1"></i></label>
                            <select id="bom_select" class="w-full text-sm">
                                <option value="">Optional: Auto load materials via Formula...</option>
                                @foreach($boms ?? [] as $bom)
                                    <option value="{{ $bom->id }}">{{ $bom->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-indigo-800 dark:text-indigo-300 mb-1">Target Ready Product Qty</label>
                            <input type="number" id="target_qty" value="1" min="1" class="bg-white dark:bg-[#0f172a] border border-indigo-300 dark:border-indigo-700/50 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5 h-[42px] font-bold" placeholder="How many pairs to make?">
                        </div>
                    </div>

                    <div class="overflow-x-auto mb-4 border border-gray-200 dark:border-slate-700 rounded-lg">
                        <table class="w-full text-left text-sm text-gray-600 dark:text-slate-300" id="itemsTable">
                            <thead class="text-xs text-gray-500 dark:text-slate-400 uppercase bg-gray-100 dark:bg-slate-800">
                            <tr>
                                <th class="px-4 py-3 w-1/3">Product / Material <span class="text-rose-500">*</span></th>
                                <th class="px-4 py-3 w-1/3">Batch & Location <span class="text-rose-500">*</span></th>
                                <th class="px-4 py-3 w-1/5">Quantity <span class="text-rose-500">*</span></th>
                                <th class="px-4 py-3 text-center">Action</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-slate-700" id="itemRows">
                            <tr class="item-row">
                                <td class="px-4 py-3">
                                    <select name="items[0][product_id]" class="product-select w-full" required>
                                        <option value="">Select Material...</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->code }})</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-4 py-3">
                                    <select name="items[0][stock_id]" class="stock-select bg-white dark:bg-[#0f172a] border border-gray-300 dark:border-slate-700 rounded-lg w-full p-2 text-sm" required disabled>
                                        <option value="">Select Material First</option>
                                    </select>
                                </td>
                                <td class="px-4 py-3">
                                    <input type="number" step="0.01" name="items[0][quantity]" class="qty-input bg-white dark:bg-[#0f172a] border border-gray-300 dark:border-slate-700 rounded-lg w-full p-2 text-sm font-bold" required placeholder="Qty" data-base-qty="1">
                                    <p class="text-xs text-emerald-600 dark:text-emerald-400 mt-1 stock-info hidden">Available: <span></span></p>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button type="button" class="remove-row text-rose-500 hover:text-rose-700 disabled:opacity-50" disabled>
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="flex justify-between items-center pt-4">
                        <button type="button" id="addRow" class="bg-gray-100 hover:bg-gray-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-gray-800 dark:text-slate-200 px-4 py-2 rounded-lg text-sm font-medium transition">
                            <i class="fa-solid fa-plus mr-1"></i> Add Manual Item
                        </button>

                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg text-sm font-medium transition shadow-sm flex items-center">
                            <i class="fa-solid fa-file-invoice mr-2"></i> Generate Issue Voucher
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            let rowIdx = 0;

            // Product options string for dynamic rows
            const productOptions = `@foreach($products as $product)<option value="{{ $product->id }}">{{ addslashes($product->name) }} ({{ $product->code }})</option>@endforeach`;

            // Initialize Select2
            $('#staff_select, #bom_select').select2({ allowClear: true });

            function initSelect2() {
                $('.product-select').select2({ placeholder: "Search material...", allowClear: true });
            }
            initSelect2();

            // ==========================================
            // AUTO LOAD FROM BOM LOGIC
            // ==========================================
            $('#bom_select').on('change', function() {
                let bomId = $(this).val();
                let targetQty = $('#target_qty').val() || 1;

                if (bomId) {
                    // AJAX call to fetch BOM items (Using your existing endpoint)
                    $.ajax({
                        url: `/productions/get-bom/${bomId}`,
                        type: "GET",
                        success: function(response) {
                            $('#itemRows').empty(); // Clear existing rows
                            rowIdx = -1;

                            if(response.items && response.items.length > 0) {
                                response.items.forEach(function(item) {
                                    rowIdx++;
                                    let baseQty = parseFloat(item.quantity) || 0;
                                    let totalQty = (baseQty * parseFloat(targetQty)).toFixed(2);

                                    let newRow = `
                                        <tr class="item-row bg-indigo-50/20 dark:bg-indigo-900/10">
                                            <td class="px-4 py-3">
                                                <select name="items[${rowIdx}][product_id]" class="product-select w-full" required>
                                                    <option value="">Select Material...</option>
                                                    ${productOptions}
                                                </select>
                                            </td>
                                            <td class="px-4 py-3">
                                                <select name="items[${rowIdx}][stock_id]" class="stock-select bg-white dark:bg-[#0f172a] border border-gray-300 dark:border-slate-700 rounded-lg w-full p-2 text-sm" required disabled>
                                                    <option value="">Loading Batches...</option>
                                                </select>
                                            </td>
                                            <td class="px-4 py-3">
                                                <input type="number" step="0.01" name="items[${rowIdx}][quantity]" value="${totalQty}" class="qty-input bg-white dark:bg-[#0f172a] border border-indigo-300 dark:border-indigo-600 rounded-lg w-full p-2 text-sm font-bold text-indigo-700 dark:text-indigo-400" required data-base-qty="${baseQty}">
                                                <p class="text-xs text-emerald-600 dark:text-emerald-400 mt-1 stock-info hidden">Available: <span></span></p>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <button type="button" class="remove-row text-rose-500 hover:text-rose-700">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>`;

                                    let $row = $(newRow);
                                    $('#itemRows').append($row);

// Pre-select the product from BOM safely
                                    let pId = item.raw_material_id || item.product_id;
                                    let selectField = $row.find('.product-select');

// যদি কাঁচামালের স্টক না থাকার কারণে ড্রপডাউনে অপশনটি না থাকে, তবে সেটি ম্যানুয়ালি যুক্ত করে দেওয়া হলো
                                    if (selectField.find(`option[value='${pId}']`).length === 0) {
                                        let materialName = item.raw_material ? item.raw_material.name : 'Unknown Material';
                                        // Out of stock মেসেজ সহ অপশন অ্যাড করা হলো
                                        selectField.append(new Option(materialName + ' (Out of Stock)', pId, true, true));
                                    } else {
                                        selectField.val(pId);
                                    }
                                });

                                initSelect2();

                                // Trigger change to load stock batches for all new items
                                $('.product-select').trigger('change');
                            } else {
                                alert('No materials found in this formula!');
                                $('#addRow').click(); // Add an empty row
                            }
                        }
                    });
                } else {
                    // If BOM is cleared, reset to one empty row
                    $('#itemRows').empty();
                    $('#addRow').click();
                }
            });

            // ==========================================
            // DYNAMIC QUANTITY MULTIPLIER
            // ==========================================
            $('#target_qty').on('input', function() {
                let targetQty = parseFloat($(this).val()) || 1;

                // Loop through each quantity input and multiply by base BOM qty
                $('.qty-input').each(function() {
                    let baseQty = parseFloat($(this).attr('data-base-qty'));
                    if (baseQty) {
                        $(this).val((baseQty * targetQty).toFixed(2));
                    }
                });
            });


            // ==========================================
            // ADD MANUAL ROW LOGIC
            // ==========================================
            $('#addRow').on('click', function() {
                rowIdx++;
                let newRow = `
                    <tr class="item-row">
                        <td class="px-4 py-3">
                            <select name="items[${rowIdx}][product_id]" class="product-select w-full" required>
                                <option value="">Select Material...</option>
                                ${productOptions}
                            </select>
                        </td>
                        <td class="px-4 py-3">
                            <select name="items[${rowIdx}][stock_id]" class="stock-select bg-white dark:bg-[#0f172a] border border-gray-300 dark:border-slate-700 rounded-lg w-full p-2 text-sm" required disabled>
                                <option value="">Select Material First</option>
                            </select>
                        </td>
                        <td class="px-4 py-3">
                            <input type="number" step="0.01" name="items[${rowIdx}][quantity]" class="qty-input bg-white dark:bg-[#0f172a] border border-gray-300 dark:border-slate-700 rounded-lg w-full p-2 text-sm font-bold" required placeholder="Qty" data-base-qty="0">
                            <p class="text-xs text-emerald-600 dark:text-emerald-400 mt-1 stock-info hidden">Available: <span></span></p>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <button type="button" class="remove-row text-rose-500 hover:text-rose-700">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </td>
                    </tr>`;

                $('#itemRows').append(newRow);
                initSelect2();
            });

            // Remove row
            $(document).on('click', '.remove-row', function() {
                if ($('#itemRows tr').length > 1) {
                    $(this).closest('tr').remove();
                }
            });

            // ==========================================
            // FETCH BATCHES ON PRODUCT SELECT
            // ==========================================
            $(document).on('change', '.product-select', function() {
                let row = $(this).closest('tr');
                let productId = $(this).val();
                let stockSelect = row.find('.stock-select');
                let qtyInput = row.find('.qty-input');
                let stockInfo = row.find('.stock-info');

                stockSelect.empty().append('<option value="">Loading...</option>').prop('disabled', true);
                stockInfo.addClass('hidden');
                qtyInput.attr('max', '');

                if (productId) {
                    $.ajax({
                        url: "{{ route('inventory.get_stock_details') }}",
                        type: "GET",
                        data: { product_id: productId },
                        success: function(data) {
                            stockSelect.empty();
                            if (data.length > 0) {
                                stockSelect.append('<option value="">Select Batch/Location...</option>');
                                $.each(data, function(key, stock) {
                                    let locationName = stock.location ? stock.location.name : 'Unknown';
                                    let batchNo = stock.batch_no ? `(Batch: ${stock.batch_no})` : '';
                                    stockSelect.append(`<option value="${stock.id}" data-qty="${stock.quantity}">${locationName} ${batchNo} - Stock: ${stock.quantity}</option>`);
                                });
                                stockSelect.prop('disabled', false);
                            } else {
                                stockSelect.append('<option value="">No stock available</option>');
                            }
                        }
                    });
                } else {
                    stockSelect.empty().append('<option value="">Select Material First</option>');
                }
            });

            // Update Max Quantity when Batch is selected
            $(document).on('change', '.stock-select', function() {
                let row = $(this).closest('tr');
                let selectedOption = $(this).find(':selected');
                let maxQty = selectedOption.data('qty');
                let qtyInput = row.find('.qty-input');
                let stockInfo = row.find('.stock-info');

                if (maxQty) {
                    stockInfo.find('span').text(maxQty);
                    stockInfo.removeClass('hidden');
                    qtyInput.attr('max', maxQty);
                } else {
                    stockInfo.addClass('hidden');
                    qtyInput.removeAttr('max');
                }
            });
        });
    </script>
</x-app-layout>
