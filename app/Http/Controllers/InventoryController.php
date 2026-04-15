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
            // ১. BOM (Formula) গুলো ডাটাবেস থেকে আনা
            $boms = \App\Models\Bom::all();

            // ২. শুধুমাত্র 'Raw Material' এবং যেগুলোর মেইন গোডাউনে (location_id = 1) স্টক আছে সেগুলো আনা
            $products = \App\Models\Product::where('type', 'raw_material')
                ->whereHas('stocks', function($q) {
                    $q->where('quantity', '>', 0)
                      ->where('location_id', 1); // 1 = Main Godown
                })->get();

            // ৩. অ্যাকটিভ স্টাফদের ডাটা লোড করা
            $staffs = \App\Models\Staff::where('is_active', 1)->orderBy('name')->get();

            // ৪. boms ভ্যারিয়েবলটি compact এর মাধ্যমে ফর্মে পাঠানো হলো
            return view('inventory.issue', compact('products', 'staffs', 'boms'));
        }

    // AJAX রিকোয়েস্টের মাধ্যমে নির্দিষ্ট প্রোডাক্টের ব্যাচ/লোকেশন অনুযায়ী স্টক বের করা
    // AJAX রিকোয়েস্টের মাধ্যমে নির্দিষ্ট প্রোডাক্টের ব্যাচ/লোকেশন অনুযায়ী স্টক বের করা
        public function getStockDetails(Request $request)
        {
            $stocks = \App\Models\InventoryStock::with('location')
                        ->where('product_id', $request->product_id)
                        ->where('quantity', '>', 0)
                        ->where('location_id', 1) // <--- শুধুমাত্র Main Godown (Location 1) এর স্টক দেখাবে
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
    // Ready Production (Location 3) এর স্টক দেখানো
        public function readyProducts()
        {
            $stocks = \App\Models\InventoryStock::with(['product.category', 'product.unit', 'production']) // production যোগ করুন
                        ->where('location_id', 3)
                        ->where('quantity', '>', 0)
                        ->orderBy('created_at', 'desc')
                        ->get();

            return view('inventory.ready_products', compact('stocks'));
        }




        // Location 3 (Ready Production) থেকে Location 4 (Store) এ পাঠানো এবং Price Set করা
        public function transferToStore(Request $request)
        {
            $request->validate([
                'stock_id' => 'required|exists:inventory_stocks,id',
                'transfer_qty' => 'required|numeric|min:0.01',
                'wholesale_price' => 'required|numeric|min:0',
                'retail_price' => 'required|numeric|min:0',
            ]);

            DB::beginTransaction();
            try {
                $storeLocationId = 4;
                $sourceStock = \App\Models\InventoryStock::lockForUpdate()->findOrFail($request->stock_id);

                if ($request->transfer_qty > $sourceStock->quantity) {
                    throw new \Exception('Transfer quantity cannot be greater than available stock!');
                }

                // কস্ট বের করা
                $prodData = \App\Models\Production::where('batch_no', $sourceStock->batch_no)->first();
                $unitCost = $prodData ? (float) $prodData->unit_cost : ($sourceStock->unit_cost ?? 0);

                // ১. সোর্স থেকে কমানো
                $sourceStock->decrement('quantity', $request->transfer_qty);

                // ২. ডেস্টিনেশন (Location 4) এ আপডেট
                $storeStock = \App\Models\InventoryStock::updateOrCreate(
                    [
                        'product_id' => $sourceStock->product_id,
                        'location_id' => $storeLocationId,
                        'batch_no' => $sourceStock->batch_no
                    ],
                    [
                        'unit_cost' => $unitCost,
                        'wholesale_price' => $request->wholesale_price,
                        'retail_price' => $request->retail_price,
                        'created_by' => auth()->id(),
                    ]
                );

                // আলাদা করে ইনক্রিমেন্ট করা যাতে আগের পরিমাণের সাথে যোগ হয়
                $storeStock->increment('quantity', $request->transfer_qty);

                // ট্রানজেকশন রেকর্ড (আপনার আগের কোড অনুযায়ী থাকবে)
                \App\Models\InventoryTransaction::create([
                    'date' => now()->format('Y-m-d'),
                    'product_id' => $sourceStock->product_id,
                    'location_id' => 3,
                    'batch_no' => $sourceStock->batch_no,
                    'transaction_type' => 'transfer_out',
                    'reference_type' => 'Store Transfer',
                    'reference_id' => $sourceStock->id,
                    'quantity' => $request->transfer_qty,
                    'unit_cost' => $unitCost,
                    'created_by' => auth()->id(),
                ]);

                \App\Models\InventoryTransaction::create([
                    'date' => now()->format('Y-m-d'),
                    'product_id' => $sourceStock->product_id,
                    'location_id' => $storeLocationId,
                    'batch_no' => $sourceStock->batch_no,
                    'transaction_type' => 'transfer_in',
                    'reference_type' => 'Store Transfer',
                    'reference_id' => $storeStock->id,
                    'quantity' => $request->transfer_qty,
                    'unit_cost' => $unitCost,
                    'created_by' => auth()->id(),
                ]);

                DB::commit();
                return back()->with('success', 'Transfer and Pricing updated successfully!');

            } catch (\Exception $e) {
                DB::rollback();
                return back()->with('error', 'Error: ' . $e->getMessage());
            }
        }
    //Store stock data
    public function storeStock(Request $request)
    {
        $query = \App\Models\InventoryStock::with(['product.category', 'product.unit'])
                    ->where('location_id', 4) // Sudhu Main Store
                    ->where('quantity', '>', 0); // Sudhu stock-e ache emon gulo

        // Product Name search
        if ($request->filled('search')) {
            $query->whereHas('product', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        // Category filter
        if ($request->filled('category_id')) {
            $query->whereHas('product', function($q) use ($request) {
                $q->where('category_id', $request->category_id);
            });
        }

        $stocks = $query->latest()->paginate(15);
        $categories = \App\Models\Category::all();

        return view('inventory.store_stock', compact('stocks', 'categories'));
    }

// ১. মেইন সামারি টেবিল: প্রোডাকশন ফ্লোরে (Location 2) কোন আইটেম মোট কতটুকু আছে
    public function productionInventory(Request $request)
    {
        $summaryStocks = InventoryStock::with(['product.category', 'product.unit'])
            ->where('location_id', 2) // নিশ্চিতভাবে প্রোডাকশন লোকেশন
            ->select('product_id', DB::raw('SUM(quantity) as total_qty'))
            ->groupBy('product_id')
            ->having('total_qty', '>', 0)
            ->get();

        return view('inventory.production_stock', compact('summaryStocks'));
    }

    // ২. স্টাফ অনুযায়ী নির্দিষ্ট আইটেমের ডিটেইল ব্রেকডাউন
    public function productionItemDetails($product_id)
    {
        $product = Product::with('unit')->findOrFail($product_id);

        // স্টাফদের কাছে কোন ব্যাচের মাল কতটুকু আছে তা বের করা
        $staffStocks = DB::table('inventory_stocks as s')
            ->join('production_issue_items as pii', 's.id', '=', 'pii.stock_id')
            ->join('production_issues as pi', 'pii.production_issue_id', '=', 'pi.id')
            ->join('staffs', 'pi.issued_to', '=', 'staffs.id')
            ->where('s.product_id', $product_id)
            ->where('s.location_id', 2) // শুধুমাত্র প্রোডাকশন লোকেশন ২ এর ডাটা
            ->where('s.quantity', '>', 0)
            ->select(
                'staffs.name as staff_name',
                's.batch_no',
                's.quantity',
                'pi.date as issue_date',      // আপনার DB কলাম অনুযায়ী
                'pi.voucher_no as issue_ref'  // আপনার DB কলাম অনুযায়ী
            )
            ->get();

        return view('inventory.production_item_details', compact('product', 'staffStocks'));
    }
}





