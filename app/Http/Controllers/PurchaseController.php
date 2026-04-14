<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\Location;
use App\Models\InventoryStock;
use App\Models\InventoryTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // DB transaction এর জন্য
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    // ১. পারচেজ ইনভয়েস লিস্ট দেখানোর জন্য
    public function index()
    {
        // ডাটাবেজ থেকে লেটেস্ট বিলগুলো সাপ্লায়ারের তথ্যসহ আনা হলো
        $purchases = Purchase::with('supplier')->orderBy('id', 'desc')->paginate(10);
        return view('purchases.index', compact('purchases'));
    }

    // ২. নতুন ইনভয়েস তৈরি করার পেজ দেখানোর জন্য
    public function create()
    {
        // ড্রপডাউনে দেখানোর জন্য অ্যাক্টিভ সাপ্লায়ারদের লিস্ট
        $suppliers = Supplier::where('is_active', 1)->get();

        // ইনভয়েস নম্বর অটো-জেনারেট করার লজিক (যেমন: PUR-0001)
        $latestPurchase = Purchase::latest('id')->first();
        $nextInvoiceNo = $latestPurchase ? 'PUR-' . str_pad($latestPurchase->id + 1, 4, '0', STR_PAD_LEFT) : 'PUR-0001';

        return view('purchases.create', compact('suppliers', 'nextInvoiceNo'));
    }

    // ৩. AJAX এর মাধ্যমে সাপ্লায়ারের প্রোডাক্ট আনার জন্য
    public function getSupplierProducts($supplier_id)
    {
        $supplier = Supplier::with(['products' => function($query) {
            $query->with('unit'); // প্রোডাক্টের সাথে ইউনিটও আনবো
        }])->find($supplier_id);

        if (!$supplier) {
            return response()->json([]);
        }

        return response()->json($supplier->products);
    }

    // ৪. নতুন ইনভয়েস ডাটাবেজে সেভ করার জন্য
    // ৪. নতুন ইনভয়েস ডাটাবেজে সেভ করার জন্য
        public function store(Request $request)
        {
            // বেসিক ভ্যালিডেশন
            $request->validate([
                'purchase_date'      => 'required|date',
                'supplier_id'        => 'required|exists:suppliers,id',
                'items'              => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity'   => 'required|numeric|min:0.01',
                'items.*.unit_price' => 'required|numeric|min:0',
            ]);

            try {
                DB::beginTransaction();

                $location = Location::where('type', 'raw_material_store')->first();
                if (!$location) {
                    throw new \Exception('Raw Material Store location not found. Please create one in Locations setup first.');
                }

                $total_paid_by_user = $request->paid_amount ?? 0;
                $grand_total = $request->grand_total;

                // এই বিলের জন্য সর্বোচ্চ কত টাকা নেওয়া হবে (বিলের টোটাল অথবা ইউজারের দেওয়া টাকা, যেটা ছোট)
                $applied_to_current_invoice = min($total_paid_by_user, $grand_total);

                $actual_invoice_due = max(0, $grand_total - $total_paid_by_user);
                $current_status = $actual_invoice_due == 0 ? 'Paid' : ($applied_to_current_invoice > 0 ? 'Partial' : 'Unpaid');

                // মূল পারচেজ বিল তৈরি
                $purchase = Purchase::create([
                    'invoice_no'       => $request->invoice_no,
                    'supplier_id'      => $request->supplier_id,
                    'purchase_date'    => $request->purchase_date,
                    'invoice_type'     => $request->invoice_type,
                    'reference_no'     => $request->reference_no,
                    'total_amount'     => $request->total_amount,
                    'discount'         => $request->discount ?? 0,
                    'tax_amount'       => $request->tax_amount ?? 0,
                    'shipping_cost'    => $request->shipping_cost ?? 0,
                    'other_charges'    => $request->other_charges ?? 0,
                    'round_adjustment' => $request->round_adjustment ?? 0,
                    'grand_total'      => $grand_total,
                    'paid_amount'      => $applied_to_current_invoice,
                    'due_amount'       => $actual_invoice_due,
                    'payment_method'   => $request->payment_method,
                    'status'           => $current_status,
                    'note'             => $request->note,
                    'created_by'       => Auth::id(),
                ]);

                // আইটেম, পিভট টেবিল এবং স্টক আপডেট
                foreach ($request->items as $item) {
                    $total_price = ($item['quantity'] * $item['unit_price']) - ($item['discount'] ?? 0);

                    $purchase->items()->create([
                        'product_id'  => $item['product_id'],
                        'quantity'    => $item['quantity'],
                        'unit_price'  => $item['unit_price'],
                        'discount'    => $item['discount'] ?? 0,
                        'total_price' => $total_price,
                    ]);

                    DB::table('product_supplier')
                        ->where('supplier_id', $request->supplier_id)
                        ->where('product_id', $item['product_id'])
                        ->update(['last_purchase_price' => $item['unit_price']]);

                    InventoryTransaction::create([
                        'date'             => $request->purchase_date,
                        'product_id'       => $item['product_id'],
                        'location_id'      => $location->id,
                        'transaction_type' => 'Purchase',
                        'reference_type'   => get_class($purchase),
                        'reference_id'     => $purchase->id,
                        'quantity'         => $item['quantity'],
                        'unit_cost'        => $item['unit_price'],
                        'created_by'       => Auth::id(),
                    ]);

                    $stock = InventoryStock::firstOrCreate(
                        ['product_id' => $item['product_id'], 'location_id' => $location->id, 'batch_no' => $purchase->invoice_no],
                        ['quantity' => 0]
                    );
                    $stock->increment('quantity', $item['quantity']);
                }

                // ==========================================
                // PAYMENT DISTRIBUTION LOGIC (Your Logic)
                // ==========================================

                // ভাউচার নম্বর জেনারেট করার জন্য আগের লাস্ট আইডি বের করে রাখা
                $lastPayment = \App\Models\SupplierPayment::orderBy('id', 'desc')->first();
                $nextPaymentId = $lastPayment ? $lastPayment->id + 1 : 1;

                $remaining_balance = $total_paid_by_user;
                $adjusted_invoices_note = [];

                // ১. নতুন বিলের জন্য পেমেন্ট এন্ট্রি (যদি টাকা দেওয়া হয়)
                if ($applied_to_current_invoice > 0) {
                    \App\Models\SupplierPayment::create([
                        'voucher_no'     => 'PAY-' . str_pad($nextPaymentId++, 4, '0', STR_PAD_LEFT),
                        'supplier_id'    => $purchase->supplier_id,
                        'purchase_id'    => $purchase->id, // নতুন বিলের আইডি
                        'amount'         => $applied_to_current_invoice,
                        'payment_date'   => $purchase->purchase_date ?? now(),
                        'payment_method' => $request->payment_method ?? 'Cash',
                        'note'           => 'Payment for current invoice',
                        'created_by'     => Auth::id(),
                    ]);
                    $remaining_balance -= $applied_to_current_invoice;
                }

                // ২. অতিরিক্ত টাকা থাকলে পুরোনো বিল পেমেন্ট করা
                if ($remaining_balance > 0) {
                    $old_unpaid_invoices = Purchase::where('supplier_id', $request->supplier_id)
                        ->where('id', '!=', $purchase->id)
                        ->where('due_amount', '>', 0)
                        ->orderBy('purchase_date', 'asc')
                        ->get();

                    foreach ($old_unpaid_invoices as $old_inv) {
                        if ($remaining_balance <= 0) break;

                        $due = $old_inv->due_amount;
                        $pay_for_this_old_inv = min($remaining_balance, $due);

                        // পুরোনো বিল আপডেট
                        $old_inv->update([
                            'paid_amount' => $old_inv->paid_amount + $pay_for_this_old_inv,
                            'due_amount'  => $due - $pay_for_this_old_inv,
                            'status'      => ($due - $pay_for_this_old_inv) == 0 ? 'Paid' : 'Partial'
                        ]);

                        // পুরোনো বিলের জন্য পেমেন্ট টেবিলে আলাদা এন্ট্রি
                        \App\Models\SupplierPayment::create([
                            'voucher_no'     => 'PAY-' . str_pad($nextPaymentId++, 4, '0', STR_PAD_LEFT),
                            'supplier_id'    => $request->supplier_id,
                            'purchase_id'    => $old_inv->id, // পুরোনো বিলের আইডি
                            'amount'         => $pay_for_this_old_inv,
                            'payment_date'   => now(),
                            'payment_method' => $request->payment_method ?? 'Cash',
                            'note'           => 'Auto adjusted from invoice: ' . $purchase->invoice_no,
                            'created_by'     => Auth::id(),
                        ]);

                        $adjusted_invoices_note[] = $old_inv->invoice_no . ' (' . $pay_for_this_old_inv . ' Tk)';
                        $remaining_balance -= $pay_for_this_old_inv;
                    }
                }

                // ৩. এরপরও টাকা থাকলে তা Advance Payment হিসেবে রাখা
                if ($remaining_balance > 0) {
                    \App\Models\SupplierPayment::create([
                        'voucher_no'     => 'PAY-' . str_pad($nextPaymentId++, 4, '0', STR_PAD_LEFT),
                        'supplier_id'    => $request->supplier_id,
                        'purchase_id'    => null, // কোনো ইনভয়েস আইডি নেই মানে এটা অ্যাডভান্স
                        'amount'         => $remaining_balance,
                        'payment_date'   => now(),
                        'payment_method' => $request->payment_method ?? 'Cash',
                        'note'           => 'Advance payment received with invoice: ' . $purchase->invoice_no,
                        'created_by'     => Auth::id(),
                    ]);
                }

                // ৪. নতুন বিলের নোটে পুরোনো বিলের হিসাব যোগ করে দেওয়া (যাতে প্রিন্টে দেখানো যায়)
                if (count($adjusted_invoices_note) > 0 || $remaining_balance > 0) {
                    $extra_note = "\n--- Payment Distribution ---\n";
                    if (count($adjusted_invoices_note) > 0) {
                        $extra_note .= "Old Dues Paid: " . implode(', ', $adjusted_invoices_note) . ".\n";
                    }
                    if ($remaining_balance > 0) {
                        $extra_note .= "Advance Kept: " . $remaining_balance . " Tk.";
                    }

                    $purchase->update([
                        'note' => $purchase->note ? $purchase->note . "\n" . $extra_note : $extra_note
                    ]);
                }

                // সাপ্লায়ারের ব্যালেন্স আপডেট
                $supplier = Supplier::find($request->supplier_id);
                if ($supplier) {
                    $balance_impact = $grand_total - $total_paid_by_user;
                    $supplier->increment('current_balance', $balance_impact);
                }

                DB::commit();

                return redirect()->route('purchases.show', $purchase->id)
                                 ->with('success', 'Invoice created perfectly! Accounts automatically reconciled.');

            } catch (\Exception $e) {
                DB::rollBack();
                return back()->with('error', 'Something went wrong! ' . $e->getMessage())->withInput();
            }
        }

    // ৫. ইনভয়েসের বিস্তারিত দেখা (Show Invoice)
    public function show(Purchase $purchase)
    {
        // রিলেশনসহ পারচেজ ডাটা লোড করা
        $purchase->load(['supplier', 'items.product.unit', 'creator']);

        // কোম্পানির ইনফরমেশন লোড করা (ধরে নিচ্ছি টেবিলে অন্তত একটি রো আছে)
        $company = \App\Models\CompanyInfo::first();

        return view('purchases.show', compact('purchase', 'company'));
    }

    public function qrInvoicePreview($invoice_no)
    {
        // invoice_no দিয়ে ডাটাবেস থেকে পারচেজ খুঁজুন। সাথে রিলেশনগুলোও লোড করে নিন।
        $purchase = \App\Models\Purchase::with(['supplier', 'items.product.unit', 'creator'])
                    ->where('invoice_no', $invoice_no)
                    ->firstOrFail();

        // কোম্পানির ডাটা পাস করা হলো (আপনার CompanyInfo মডেল ব্যবহার করে)
        $company = \App\Models\CompanyInfo::first();

        return view('qrinvoice_public', compact('purchase', 'company'));
    }

    // ৬. ইনভয়েস এডিট করার পেজ
    public function edit(Purchase $purchase)
    {
        $suppliers = Supplier::where('is_active', 1)->get();
        $purchase->load('items.product.unit');
        return view('purchases.edit', compact('purchase', 'suppliers'));
    }
}
