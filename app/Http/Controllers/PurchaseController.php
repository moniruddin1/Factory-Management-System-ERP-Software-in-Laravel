<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // DB transaction এর জন্য

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

            // মূল পারচেজ বিল তৈরি করা
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
                'grand_total'      => $request->grand_total,
                'paid_amount'      => $request->paid_amount ?? 0,
                'due_amount'       => $request->due_amount ?? 0,
                'payment_method'   => $request->payment_method,
                'status'           => $request->status,
                'note'             => $request->note,
                'created_by'       => auth()->id(), // যে ইউজার লগইন করা আছে
            ]);

            // বিলে থাকা প্রতিটি প্রোডাক্ট (Purchase Items) সেভ করা
            foreach ($request->items as $item) {
                // প্রতিটি আইটেমের টোটাল হিসাব করা
                $total_price = ($item['quantity'] * $item['unit_price']) - ($item['discount'] ?? 0);

                $purchase->items()->create([
                    'product_id'  => $item['product_id'],
                    'quantity'    => $item['quantity'],
                    'unit_price'  => $item['unit_price'],
                    'discount'    => $item['discount'] ?? 0,
                    'total_price' => $total_price,
                ]);

                // সাপ্লায়ার ম্যাপিং পিভট টেবিলে 'last_purchase_price' আপডেট করে দেওয়া
                DB::table('product_supplier')
                    ->where('supplier_id', $request->supplier_id)
                    ->where('product_id', $item['product_id'])
                    ->update(['last_purchase_price' => $item['unit_price']]);
            }

            // পারচেস সেভ করার পর... (আপনার আগের কোডের সাথে মিলিয়ে নিন)
            if ($request->paid_amount > 0) {
                $lastPayment = \App\Models\SupplierPayment::orderBy('id', 'desc')->first();
                $nextId = $lastPayment ? $lastPayment->id + 1 : 1;

                \App\Models\SupplierPayment::create([
                    'voucher_no'     => 'PAY-' . str_pad($nextId, 4, '0', STR_PAD_LEFT),
                    'supplier_id'    => $purchase->supplier_id,
                    'purchase_id'    => $purchase->id,
                    'amount'         => $request->paid_amount,
                    'payment_date'   => $purchase->purchase_date ?? now(),
                    'payment_method' => $request->payment_method ?? 'Cash', // আপনার ফর্ম থেকে আসা মেথড
                    'note'           => 'Initial payment during purchase',
                    'created_by'     => \Illuminate\Support\Facades\Auth::id(),
                ]);
            }

            DB::commit();

            // store মেথডের শেষে এটি পরিবর্তন করুন:
            return redirect()->route('purchases.show', $purchase->id)
                             ->with('success', 'Invoice created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            // কোনো এরর হলে আগের পেজেই মেসেজসহ ফেরত পাঠাবে
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
