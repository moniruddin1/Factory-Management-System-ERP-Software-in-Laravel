<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnItem;
use App\Models\Supplier;
use App\Models\Product; // আপনার প্রোডাক্ট মডেলের নাম অনুযায়ী পরিবর্তন করবেন
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseReturnController extends Controller
{
    public function index(Request $request)
    {
        $query = \App\Models\PurchaseReturn::with(['supplier', 'purchase']);

        // ১. Date to Date Filter
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('return_date', [$request->start_date, $request->end_date]);
        }

        // ২. Search Logic
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('return_no', 'like', "%{$search}%")
                  ->orWhereHas('supplier', function($q2) use ($search) {
                      $q2->where('company_name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                  })
                  ->orWhereHas('purchase', function($q3) use ($search) {
                      $q3->where('invoice_no', 'like', "%{$search}%");
                  });
            });
        }

        // ৩. Multi-filter Logic (By Supplier)
        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        // ৪. Pagination Row Control
        $limit = $request->input('limit', 25);

        $returns = $query->latest()->paginate($limit)->appends($request->all());
        $suppliers = \App\Models\Supplier::where('is_active', 1)->get();

        return view('purchase-returns.index', compact('returns', 'suppliers'));
    }

public function show($id)
    {
        // রিটার্নের ডাটা
        $return = \App\Models\PurchaseReturn::with(['supplier', 'purchase', 'items', 'creator'])->findOrFail($id);

        // কোম্পানির ডাটা (আপনার ডাটাবেজের মডেল অনুযায়ী)
        // যদি আপনার মডেলের নাম Company হয় তাহলে নিচের লাইনটি আনকমেন্ট করুন
        $company = \App\Models\CompanyInfo::first();

        // অথবা যদি আপনার মডেলের নাম Setting হয়, তবে এটি ব্যবহার করুন:
        // $company = \App\Models\Setting::first();

        // return এবং company ডাটা ভিউতে পাঠানো হচ্ছে
        return view('purchase-returns.show', compact('return', 'company'));
    }
    public function create()
    {
        $suppliers = Supplier::where('is_active', 1)->get();
        // শুধু সেই পারচেজগুলো দেখাবে যেগুলোর ডিউ আছে বা পার্শিয়াল পেইড
        $purchases = Purchase::select('id', 'invoice_no')->get();
        return view('purchase-returns.create', compact('suppliers', 'purchases'));
    }

    // AJAX এর মাধ্যমে পারচেজ আইটেম লোড করা
    // AJAX এর মাধ্যমে পারচেজ আইটেম লোড করা
        public function getPurchaseItems($id)
        {
            // রিলেশনের নাম 'items' এবং সাথে প্রোডাক্টের নাম পাওয়ার জন্য 'product' লোড করা হলো
            $purchase = Purchase::with('items.product')->findOrFail($id);

            // ডাটাগুলোকে সুন্দর করে সাজিয়ে JSON এ পাঠানো হচ্ছে
            $formattedItems = $purchase->items->map(function($item) {
                return [
                    'product_id' => $item->product_id,
                    'product_name' => optional($item->product)->name ?? 'Unknown Product',
                    'purchased_qty' => $item->quantity,
                    'unit_price' => $item->unit_price,
                ];
            });

            return response()->json($formattedItems);
        }

        // store মেথডের ভেতরের ফ্লো আপডেট
        public function store(Request $request)
        {
            $request->validate([
                'supplier_id' => 'required',
                'purchase_id' => 'required',
                'return_date' => 'required|date',
                'items'       => 'required|array',
            ]);

            try {
                DB::beginTransaction();

                // ১. রিটার্ন নম্বর জেনারেট
                $lastReturn = PurchaseReturn::orderBy('id', 'desc')->first();
                $nextId = $lastReturn ? $lastReturn->id + 1 : 1;
                $returnNo = 'PR-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

                // ২. মাস্টার ডাটা সেভ
                $purchaseReturn = PurchaseReturn::create([
                    'return_no'           => $returnNo,
                    'supplier_id'         => $request->supplier_id,
                    'purchase_id'         => $request->purchase_id,
                    'return_date'         => $request->return_date,
                    'total_return_amount' => 0,
                    'reason'              => $request->reason,
                    'note'                => $request->note,
                    'created_by'          => Auth::id(),
                ]);

                $totalReturnAmount = 0;

                foreach ($request->items as $item) {
                    // শুধু যেগুলোর রিটার্ন কোয়ান্টিটি ০ এর বেশি, সেগুলোই সেভ হবে
                    if (isset($item['return_qty']) && $item['return_qty'] > 0) {
                        $itemTotal = $item['return_qty'] * $item['unit_price'];

                        // ৩. রিটার্ন আইটেম সেভ
                        PurchaseReturnItem::create([
                            'purchase_return_id' => $purchaseReturn->id,
                            'product_id'         => $item['product_id'],
                            'product_name'       => $item['product_name'],
                            'return_qty'         => $item['return_qty'],
                            'unit_price'         => $item['unit_price'],
                            'total_price'        => $itemTotal,
                        ]);

                        // ৪. স্টক কমানোর লজিক আপাতত অফ রাখা হলো, কারণ products টেবিলে stock কলাম নেই
                        /*
                        $product = Product::find($item['product_id']);
                        if($product) {
                            $product->decrement('stock', $item['return_qty']);
                        }
                        */

                        $totalReturnAmount += $itemTotal;
                    }
                }

                // যদি কোনো আইটেম রিটার্ন না করে শুধু সাবমিট দেয়
                if ($totalReturnAmount == 0) {
                    DB::rollBack();
                    return back()->with('error', 'Please enter return quantity for at least one item.');
                }

                // ৫. মূল রিটার্ন টেবিলের টোটাল আপডেট
                $purchaseReturn->update(['total_return_amount' => $totalReturnAmount]);

                // ৬. পারচেজ ইনভয়েসের Due Amount অ্যাডজাস্ট করা
                $purchase = Purchase::find($request->purchase_id);
                if ($purchase) {
                    $purchase->due_amount -= $totalReturnAmount;

                    if ($purchase->due_amount <= 0) {
                        $purchase->due_amount = 0;
                        $purchase->status = 'Paid'; // অথবা 'Returned'
                    } else {
                        $purchase->status = 'Partial';
                    }
                    $purchase->save();
                }

                DB::commit();
                return redirect()->route('purchase-returns.index')->with('success', 'Purchase Return recorded successfully.');

            } catch (\Exception $e) {
                DB::rollBack();
                return back()->with('error', 'Something went wrong: ' . $e->getMessage());
            }
        }
}
