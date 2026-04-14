<?php

namespace App\Http\Controllers;

use App\Models\InventoryStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ProductionIssue;
use App\Models\ProductionIssueItem;

class InventoryController extends Controller
{
    // স্টক রিপোর্ট দেখানোর জন্য
    public function stockReport(Request $request)
        {
            $query = \App\Models\Product::with(['unit', 'category', 'stocks.location'])
                ->withSum('stocks as total_stock', 'quantity')
                ->whereHas('stocks', function($q) {
                    $q->where('quantity', '>', 0); // শুধুমাত্র যেগুলোর স্টক আছে সেগুলোই দেখাবে
                });

            // Search Filter
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%");
                });
            }

            $products = $query->paginate($request->get('per_page', 25));

            // মোট স্টকের ভ্যালু (Optional, for Top Cards)
            $totalItems = $products->total();

            return view('inventory.stock', compact('products', 'totalItems'));
        }

    // আইটেম লেজার (হিস্ট্রি) দেখানোর জন্য
        public function ledger(Request $request)
        {
            // ড্রপডাউনের জন্য সব প্রোডাক্ট লোড করা হচ্ছে
            $products = \App\Models\Product::orderBy('name')->get();

            $transactions = [];
            $selectedProduct = null;

            // যদি ইউজার কোনো প্রোডাক্ট সিলেক্ট করে সার্চ করে
            if ($request->filled('product_id')) {
                $selectedProduct = \App\Models\Product::find($request->product_id);

                $query = \App\Models\InventoryTransaction::with(['location'])
                            ->where('product_id', $request->product_id);

                // Date Filter
                if ($request->filled('start_date')) {
                    $query->whereDate('date', '>=', $request->start_date);
                }
                if ($request->filled('end_date')) {
                    $query->whereDate('date', '<=', $request->end_date);
                }

                // লেজার সাধারণত লেটেস্ট ট্রানজেকশন আগে দেখায়
                $transactions = $query->orderBy('date', 'desc')
                                      ->orderBy('created_at', 'desc')
                                      ->paginate(25);
            }

            return view('inventory.ledger', compact('products', 'transactions', 'selectedProduct'));
        }

// ইস্যু ফর্ম দেখানোর জন্য
    public function issueCreate()
    {
        // যে প্রোডাক্টগুলোর স্টক ০ এর বেশি আছে, শুধু সেগুলোই ফর্মে দেখাবে
        $products = \App\Models\Product::whereHas('stocks', function($q) {
            $q->where('quantity', '>', 0);
        })->get();



                // অ্যাকটিভ স্টাফদের ডাটা লোড করা
                $staffs = \App\Models\Staff::where('is_active', 1)->orderBy('name')->get();

                return view('inventory.issue', compact('products', 'staffs'));


    }

    // AJAX রিকোয়েস্টের মাধ্যমে নির্দিষ্ট প্রোডাক্টের ব্যাচ/লোকেশন অনুযায়ী স্টক বের করা
    public function getStockDetails(Request $request)
    {
        $stocks = \App\Models\InventoryStock::with('location')
                    ->where('product_id', $request->product_id)
                    ->where('quantity', '>', 0)
                    ->get();
        return response()->json($stocks);
    }
// ইস্যু ভাউচারগুলোর লিস্ট দেখার জন্য
    public function issueIndex()
    {
        $issues = \App\Models\ProductionIssue::with('creator')->latest()->paginate(20);

        // স্টাফদের ডাটা লোড করে পাঠানো (যাতে লিস্টে আইডি'র বদলে নাম দেখানো যায়)
        $staffs = \App\Models\Staff::pluck('name', 'id')->toArray();

        return view('inventory.issue_index', compact('issues', 'staffs'));
    }


// নির্দিষ্ট একটি ভাউচার প্রিন্ট/দেখার জন্য
    public function issueShow($id)
    {
        $issue = \App\Models\ProductionIssue::with(['items.product', 'items.stock.location', 'creator'])->findOrFail($id);
        $staff = \App\Models\Staff::find($issue->issued_to); // স্টাফের বিস্তারিত তথ্য

        // ডাটাবেস থেকে কোম্পানির ইনফরমেশন ফেচ করা হচ্ছে
        $company = \App\Models\CompanyInfo::first();
        // (বিঃদ্রঃ আপনার ডাটাবেসে যদি কোম্পানির মডেলের নাম 'Company' না হয়ে 'Setting' বা অন্য কিছু হয়, তবে সেটি এখানে পরিবর্তন করে নেবেন)

        return view('inventory.issue_show', compact('issue', 'staff', 'company'));
    }

    // ইস্যু ডাটা সেভ করা এবং স্টক থেকে কমানো ও প্রোডাকশন ফ্লোরে পাঠানো (WIP)
        public function issueStore(Request $request)
        {
            $request->validate([
                'date' => 'required|date',
                'issued_to' => 'required|string|max:255',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.stock_id' => 'required|exists:inventory_stocks,id',
                'items.*.quantity' => 'required|numeric|min:0.01',
            ]);

            DB::beginTransaction();
            try {
                // ভাউচার নম্বর জেনারেট (e.g., PI-20260414-001)
                $latestIssue = ProductionIssue::latest('id')->first();
                $nextId = $latestIssue ? $latestIssue->id + 1 : 1;
                $voucherNo = 'PI-' . date('Ymd') . '-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);

                // ১. Issue Parent Record তৈরি
                $issue = ProductionIssue::create([
                    'voucher_no' => $voucherNo,
                    'date' => $request->date,
                    'issued_to' => $request->issued_to,
                    'remarks' => $request->remarks,
                    'created_by' => auth()->id(),
                ]);

                // ২. প্রতিটি আইটেম লুপ করে সেভ করা এবং লোকেশন ট্রান্সফার করা
                foreach ($request->items as $item) {
                    $stock = \App\Models\InventoryStock::with('product')->lockForUpdate()->find($item['stock_id']);

                    if ($item['quantity'] > $stock->quantity) {
                        throw new \Exception("Quantity exceeds available stock for product: " . $stock->product->name);
                    }

                    // [স্টেপ ১]: মেইন গোডাউন থেকে স্টক কমানো
                    $stock->quantity -= $item['quantity'];
                    $stock->save();

                    // Issue Item সেভ করা
                    ProductionIssueItem::create([
                        'production_issue_id' => $issue->id,
                        'product_id' => $item['product_id'],
                        'stock_id' => $item['stock_id'],
                        'quantity' => $item['quantity'],
                        'unit_cost' => $stock->product->purchase_price ?? 0,
                    ]);

                    // [স্টেপ ২]: লেজার ট্রানজেকশন (OUT from Main Godown)
                    \App\Models\InventoryTransaction::create([
                        'date' => $request->date,
                        'product_id' => $item['product_id'],
                        'location_id' => $stock->location_id, // সাধারণত 1 (Main Godown)
                        'transaction_type' => 'issue_to_production_out', // স্পেসিফিক টাইপ
                        'reference_type' => 'Production Issue',
                        'reference_id' => $issue->id,
                        'quantity' => $item['quantity'],
                        'unit_cost' => $stock->product->purchase_price ?? 0,
                        'created_by' => auth()->id(),
                    ]);

                    // [স্টেপ ৩]: *ম্যাজিক লজিক* - ফ্যাক্টরি ফ্লোরে (Location 2) স্টক প্লাস করা (Work In Progress)
                    $productionStock = \App\Models\InventoryStock::firstOrCreate(
                        [
                            'product_id' => $item['product_id'],
                            'location_id' => 2, // 2 = Production Floor
                            'batch_no' => $stock->batch_no // আগের ব্যাচ নাম্বারটাই রাখছি যাতে হিসেব মেলাতে সুবিধা হয়
                        ],
                        ['quantity' => 0]
                    );
                    $productionStock->increment('quantity', $item['quantity']);

                    // [স্টেপ ৪]: লেজার ট্রানজেকশন (IN to Production Floor)
                    \App\Models\InventoryTransaction::create([
                        'date' => $request->date,
                        'product_id' => $item['product_id'],
                        'location_id' => 2, // Production Floor
                        'transaction_type' => 'issue_to_production_in',
                        'reference_type' => 'Production Issue',
                        'reference_id' => $issue->id,
                        'quantity' => $item['quantity'],
                        'unit_cost' => $stock->product->purchase_price ?? 0,
                        'created_by' => auth()->id(),
                    ]);
                }

                DB::commit();
                return back()->with('success', 'Materials issued and transferred to Factory Floor! Voucher: ' . $voucherNo);
            } catch (\Exception $e) {
                DB::rollback();
                return back()->with('error', $e->getMessage());
            }
        }




    }
