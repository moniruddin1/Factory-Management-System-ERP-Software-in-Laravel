<x-app-layout>
    <div class="py-6" x-data="purchaseForm()">
        <div class="max-w-[90rem] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200">
                    <i class="fa-solid fa-file-invoice-dollar mr-2 text-blue-500"></i> Create Purchase Invoice
                </h2>
                <a href="{{ route('purchases.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded-lg transition">
                    <i class="fa-solid fa-arrow-left mr-1"></i> Back
                </a>
            </div>
            @if(session('error'))
                <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm">
                    {{ session('error') }}
                </div>
            @endif
            <form action="{{ route('purchases.store') }}" method="POST" class="bg-white dark:bg-slate-800 shadow-sm rounded-xl p-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Invoice No</label>
                        <input type="text" name="invoice_no" value="{{ $nextInvoiceNo }}" readonly class="w-full bg-gray-100 rounded border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-400 cursor-not-allowed text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Date <span class="text-red-500">*</span></label>
                        <input type="date" name="purchase_date" value="{{ date('Y-m-d') }}" required class="w-full rounded border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:ring-blue-500 text-sm">
                    </div>
                    <div class="lg:col-span-2">
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Supplier <span class="text-red-500">*</span></label>
                        <select name="supplier_id" x-model="supplier_id" @change="fetchProducts()" required class="w-full rounded border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:ring-blue-500 text-sm">
                            <option value="">-- Select Supplier --</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->company_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Reference No</label>
                        <input type="text" name="reference_no" placeholder="Challan/PO" class="w-full rounded border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:ring-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Invoice Type</label>
                        <select name="invoice_type" class="w-full rounded border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:ring-blue-500 text-sm">
                            <option value="Credit">Credit</option>
                            <option value="Cash">Cash</option>
                            <option value="Advance Adjusted">Advance Adjusted</option>
                        </select>
                    </div>
                </div>

                <hr class="border-gray-200 dark:border-slate-700 mb-4">

                <div class="mb-6 overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-[800px]">
                        <thead>
                        <tr class="bg-gray-50 dark:bg-slate-700 text-gray-700 dark:text-gray-300 text-xs uppercase">
                            <th class="p-2 border-b dark:border-slate-600 w-1/4">Product</th>
                            <th class="p-2 border-b dark:border-slate-600 w-24">Unit</th>
                            <th class="p-2 border-b dark:border-slate-600 w-24">Quantity</th>
                            <th class="p-2 border-b dark:border-slate-600 w-32">Unit Price</th>
                            <th class="p-2 border-b dark:border-slate-600 w-28">Item Disc.</th>
                            <th class="p-2 border-b dark:border-slate-600 w-32 text-right">Total</th>
                            <th class="p-2 border-b dark:border-slate-600 text-center w-12">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <template x-for="(item, index) in items" :key="index">
                            <tr>
                                <td class="p-1 border-b dark:border-slate-700">
                                    <select :name="'items['+index+'][product_id]'" x-model="item.product_id" @change="setProductData(index)" required class="w-full rounded border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:ring-blue-500 text-sm">
                                        <option value="">Select Product</option>
                                        <template x-for="product in availableProducts" :key="product.id">
                                            <option :value="product.id" x-text="product.name"></option>
                                        </template>
                                    </select>
                                </td>
                                <td class="p-1 border-b dark:border-slate-700">
                                    <input type="text" readonly x-model="item.unit_name" class="w-full bg-gray-50 rounded border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-gray-400 text-sm text-center">
                                </td>
                                <td class="p-1 border-b dark:border-slate-700">
                                    <input type="number" step="0.01" :name="'items['+index+'][quantity]'" x-model.number="item.quantity" @input="calculateTotal()" required class="w-full rounded border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:ring-blue-500 text-sm">
                                </td>
                                <td class="p-1 border-b dark:border-slate-700">
                                    <input type="number" step="0.01" :name="'items['+index+'][unit_price]'" x-model.number="item.price" @input="calculateTotal()" required class="w-full rounded border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:ring-blue-500 text-sm">
                                </td>
                                <td class="p-1 border-b dark:border-slate-700">
                                    <input type="number" step="0.01" :name="'items['+index+'][discount]'" x-model.number="item.discount" @input="calculateTotal()" class="w-full rounded border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:ring-blue-500 text-sm text-red-500 placeholder-red-300" placeholder="0.00">
                                </td>
                                <td class="p-1 border-b dark:border-slate-700">
                                    <input type="text" readonly :value="getItemTotal(item)" class="w-full bg-gray-100 rounded border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-gray-300 font-bold text-right text-sm">
                                </td>
                                <td class="p-1 border-b dark:border-slate-700 text-center">
                                    <button type="button" @click="removeItem(index)" class="text-red-500 hover:text-red-700 p-1">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </td>
                            </tr>
                        </template>
                        </tbody>
                    </table>

                    <div class="mt-3">
                        <button type="button" @click="addItem()" x-show="supplier_id != ''" class="bg-indigo-50 text-indigo-600 hover:bg-indigo-100 dark:bg-indigo-900/30 dark:text-indigo-400 py-1.5 px-3 rounded text-sm font-medium transition">
                            <i class="fa-solid fa-plus mr-1"></i> Add Row
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                    <div class="lg:col-span-7 space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Order Status</label>
                                <select name="status" class="w-full rounded border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                                    <option value="Received">Received</option>
                                    <option value="Pending">Pending</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Payment Method</label>
                                <select name="payment_method" class="w-full rounded border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                                    <option value="Due">Keep as Due</option>
                                    <option value="Cash">Cash</option>
                                    <option value="Bank/Cheque">Bank / Cheque</option>
                                    <option value="Mobile Banking">Mobile Banking</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Note / Remarks</label>
                            <textarea name="note" rows="3" class="w-full rounded border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm" placeholder="Any shipping details, terms, or remarks..."></textarea>
                        </div>
                    </div>

                    <div class="lg:col-span-5 bg-gray-50 dark:bg-slate-800/80 p-4 rounded-xl border border-gray-200 dark:border-slate-600 text-sm">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-gray-600 dark:text-gray-400">Sub Total:</span>
                            <span class="font-bold text-gray-800 dark:text-gray-200" x-text="subTotal.toFixed(2)">0.00</span>
                            <input type="hidden" name="total_amount" :value="subTotal">
                        </div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-gray-600 dark:text-gray-400">Overall Discount (-):</span>
                            <input type="number" step="0.01" name="discount" x-model.number="overallDiscount" @input="calculateTotal()" class="w-28 text-right rounded border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white py-1 text-sm">
                        </div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-gray-600 dark:text-gray-400">VAT / Tax (+):</span>
                            <input type="number" step="0.01" name="tax_amount" x-model.number="tax" @input="calculateTotal()" class="w-28 text-right rounded border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white py-1 text-sm">
                        </div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-gray-600 dark:text-gray-400">Shipping Cost (+):</span>
                            <input type="number" step="0.01" name="shipping_cost" x-model.number="shipping" @input="calculateTotal()" class="w-28 text-right rounded border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white py-1 text-sm">
                        </div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-gray-600 dark:text-gray-400">Other Charges (+):</span>
                            <input type="number" step="0.01" name="other_charges" x-model.number="otherCharges" @input="calculateTotal()" class="w-28 text-right rounded border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white py-1 text-sm">
                        </div>
                        <div class="flex justify-between items-center mb-3">
                            <span class="text-gray-600 dark:text-gray-400">Round Adjust (+/-):</span>
                            <input type="number" step="0.01" name="round_adjustment" x-model.number="roundAdjustment" @input="calculateTotal()" class="w-28 text-right rounded border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white py-1 text-sm" placeholder="-0.50 or 0.50">
                        </div>

                        <hr class="border-gray-300 dark:border-slate-600 my-2">

                        <div class="flex justify-between items-center mb-3">
                            <span class="text-gray-800 dark:text-white font-bold text-lg">Grand Total:</span>
                            <span class="text-xl font-black text-blue-600 dark:text-blue-400">৳ <span x-text="grandTotal.toFixed(2)"></span></span>
                            <input type="hidden" name="grand_total" :value="grandTotal">
                        </div>

                        <div class="flex justify-between items-center mb-2 bg-green-50 dark:bg-green-900/20 p-2 rounded">
                            <span class="text-green-700 dark:text-green-400 font-medium">Paid Amount:</span>
                            <input type="number" step="0.01" name="paid_amount" x-model.number="paidAmount" @input="calculateTotal()" class="w-32 text-right rounded border-green-300 dark:border-green-600 dark:bg-slate-700 dark:text-white py-1 text-sm focus:border-green-500 focus:ring-green-500">
                        </div>
                        <div class="flex justify-between items-center p-2 rounded bg-red-50 dark:bg-red-900/20">
                            <span class="text-red-700 dark:text-red-400 font-medium">Due Amount:</span>
                            <span class="font-bold text-red-600 dark:text-red-400">৳ <span x-text="dueAmount.toFixed(2)"></span></span>
                            <input type="hidden" name="due_amount" :value="dueAmount">
                        </div>
                    </div>
                </div>

                <div class="mt-8 border-t dark:border-slate-700 pt-6 flex justify-end">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-10 rounded-lg shadow-lg transition-all" :disabled="items.length === 0 || grandTotal <= 0" :class="(items.length === 0 || grandTotal <= 0) ? 'opacity-50 cursor-not-allowed' : ''">
                        <i class="fa-solid fa-floppy-disk mr-2"></i> Save Invoice
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('purchaseForm', () => ({
                supplier_id: '',
                availableProducts: [],
                items: [],

                // Calculation fields
                subTotal: 0,
                overallDiscount: 0,
                tax: 0,
                shipping: 0,
                otherCharges: 0,
                roundAdjustment: 0,
                grandTotal: 0,
                paidAmount: 0,
                dueAmount: 0,

                fetchProducts() {
                    this.availableProducts = [];
                    this.items = [];
                    this.calculateTotal();

                    if (this.supplier_id) {
                        fetch(`/get-supplier-products/${this.supplier_id}`)
                            .then(res => res.json())
                            .then(data => {
                                this.availableProducts = data;
                                if(data.length > 0) this.addItem();
                            });
                    }
                },

                addItem() {
                    this.items.push({
                        product_id: '',
                        unit_name: '',
                        quantity: 1,
                        price: 0,
                        discount: 0
                    });
                },

                removeItem(index) {
                    this.items.splice(index, 1);
                    this.calculateTotal();
                },

                setProductData(index) {
                    let selectedProductId = this.items[index].product_id;
                    let product = this.availableProducts.find(p => p.id == selectedProductId);

                    if (product) {
                        this.items[index].unit_name = product.unit ? product.unit.name : 'N/A';
                        if (product.pivot && product.pivot.last_purchase_price) {
                            this.items[index].price = parseFloat(product.pivot.last_purchase_price);
                        } else {
                            this.items[index].price = 0;
                        }
                    } else {
                        this.items[index].unit_name = '';
                        this.items[index].price = 0;
                    }
                    this.calculateTotal();
                },

                getItemTotal(item) {
                    let qty = parseFloat(item.quantity) || 0;
                    let price = parseFloat(item.price) || 0;
                    let disc = parseFloat(item.discount) || 0;
                    let total = (qty * price) - disc;
                    return total > 0 ? total.toFixed(2) : '0.00';
                },

                calculateTotal() {
                    // Calculate SubTotal
                    this.subTotal = this.items.reduce((total, item) => {
                        let qty = parseFloat(item.quantity) || 0;
                        let price = parseFloat(item.price) || 0;
                        let disc = parseFloat(item.discount) || 0;
                        return total + ((qty * price) - disc);
                    }, 0);

                    // Grand Total
                    let disc = parseFloat(this.overallDiscount) || 0;
                    let t = parseFloat(this.tax) || 0;
                    let s = parseFloat(this.shipping) || 0;
                    let o = parseFloat(this.otherCharges) || 0;
                    let r = parseFloat(this.roundAdjustment) || 0; // Can be negative

                    this.grandTotal = (this.subTotal - disc) + t + s + o + r;
                    if(this.grandTotal < 0) this.grandTotal = 0;

                    // Due Amount
                    let paid = parseFloat(this.paidAmount) || 0;
                    this.dueAmount = this.grandTotal - paid;
                }
            }));
        });
    </script>
</x-app-layout>
