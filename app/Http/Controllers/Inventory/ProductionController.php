<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Production;
use App\Models\ProductionItem;
use App\Models\Bom;
use App\Models\Product;
use App\Models\ProductionIssue;
use App\Models\InventoryStock;
use App\Models\InventoryTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ProductionController extends Controller
{
    // ... index, show methods (আগের মতোই থাকবে) ...
public function show($id)
    {
        // Eager load all necessary relationships
        $production = \App\Models\Production::with(['issue', 'finishedProduct', 'items.rawMaterial'])->findOrFail($id);

        return view('inventory.productions.show', compact('production'));
    }
    public function create()
    {
        $boms = Bom::with('finishedProduct')->get();
        // যেসব ভাউচার এখনো প্রোডাকশনে কনভার্ট হয়নি সেগুলো আনা হচ্ছে
        $usedIssueIds = Production::whereNotNull('production_issue_id')->pluck('production_issue_id');
        $issues = ProductionIssue::whereNotIn('id', $usedIssueIds)->latest()->get();

        return view('inventory.productions.create', compact('boms', 'issues'));
    }

    // AJAX: Issue Voucher এর আইটেম আনার জন্য
    public function getIssueDetails($id)
    {
        $issue = ProductionIssue::with(['items.product', 'items.stock'])->findOrFail($id);
        return response()->json($issue);
    }

// AJAX: BOM (Formula) এর আইটেম আনার জন্য
    public function getBomDetails($id)
    {
        $bom = \App\Models\Bom::with(['items.rawMaterial', 'items.unit'])->findOrFail($id);
        return response()->json($bom);
    }

public function index()
    {
        // Eager load relationships properly
        $productions = Production::with(['issue', 'finishedProduct'])->latest()->paginate(10);

        return view('inventory.productions.index', compact('productions'));
    }
    public function store(Request $request)
        {
            $request->validate([
                'production_issue_id' => 'required|exists:production_issues,id',
                'bom_id' => 'required|exists:boms,id',
                'target_quantity' => 'required|numeric|min:1',
                'production_date' => 'required|date',
                'items' => 'required|array',
                'items.*.raw_material_id' => 'required|exists:products,id',
                'items.*.actual_qty' => 'required|numeric|min:0',
                'items.*.return_qty' => 'required|numeric|min:0', // <--- NEW VALIDATION
                'labor_cost' => 'nullable|numeric|min:0',
                            'overhead_cost' => 'nullable|numeric|min:0',
            ]);

            $bom = Bom::findOrFail($request->bom_id);
            $finishedProduct = Product::findOrFail($bom->finished_product_id);
            $issue = ProductionIssue::findOrFail($request->production_issue_id);
            $staffId = $issue->issued_to;
            $currentIssueId = (int) $issue->id;

            // ==========================================
            // STEP 1: PRE-VALIDATION (STAFF WIP CHECK)
            // ==========================================
            foreach ($request->items as $item) {
                $rawMaterialId = $item['raw_material_id'];
                $actualQty = (float) $item['actual_qty'];
                $returnQty = (float) $item['return_qty']; // <--- GET RETURN QTY
                $totalClaimedQty = $actualQty + $returnQty; // <--- TOTAL DEDUCTION FROM WIP

                $totalIssuedToStaff = (float) DB::table('production_issues')
                    ->join('production_issue_items', 'production_issues.id', '=', 'production_issue_items.production_issue_id')
                    ->where('production_issues.issued_to', $staffId)
                    ->where('production_issue_items.product_id', $rawMaterialId)
                    ->sum('production_issue_items.quantity');

                $totalConsumedByStaff = (float) DB::table('productions')
                    ->join('production_items', 'productions.id', '=', 'production_items.production_id')
                    ->join('production_issues', 'productions.production_issue_id', '=', 'production_issues.id')
                    ->where('production_issues.issued_to', $staffId)
                    ->where('production_items.raw_material_id', $rawMaterialId)
                    ->sum('production_items.actual_qty');

                $totalReturnedByStaff = (float) DB::table('inventory_transactions')
                    ->join('productions', 'inventory_transactions.reference_id', '=', 'productions.id')
                    ->join('production_issues', 'productions.production_issue_id', '=', 'production_issues.id')
                    ->where('inventory_transactions.reference_type', 'Production Return')
                    ->where('inventory_transactions.transaction_type', 'production_return_in')
                    ->where('production_issues.issued_to', $staffId)
                    ->where('inventory_transactions.product_id', $rawMaterialId)
                    ->sum('inventory_transactions.quantity');

                $availableStaffWip = $totalIssuedToStaff - $totalConsumedByStaff - $totalReturnedByStaff;

                // Check if consumed + returned is greater than what staff actually has
                if (round($totalClaimedQty, 2) > round($availableStaffWip, 2)) {
                    $productName = Product::find($rawMaterialId)->name ?? 'Unknown Material';
                    return back()->withInput()->withErrors(['error' => "Shortage! '{$productName}' available with staff is {$availableStaffWip}, but you entered {$actualQty} (Consumed) + {$returnQty} (Return). Not enough WIP stock."]);
                }
            }

            // ==========================================
            // STEP 2: SAVE PRODUCTION
            // ==========================================
            try {
                DB::beginTransaction();

                $latestProduction = Production::latest('id')->first();
                $nextId = $latestProduction ? $latestProduction->id + 1 : 1;
                $referenceNo = 'PRD-' . date('Ym') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
                $batchNo = 'BATCH-' . date('Ym') . '-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);

                $production = Production::create([
                    'reference_no' => $referenceNo,
                    'batch_no' => $batchNo,
                    'production_issue_id' => $issue->id,
                    'bom_id' => $bom->id,
                    'finished_product_id' => $finishedProduct->id,
                    'target_quantity' => $request->target_quantity,
                    'production_date' => $request->production_date,
                    'total_cost' => 0,
                    'unit_cost' => 0,
                    'notes' => $request->notes,
                    'created_by' => Auth::id(),

                                'total_material_cost' => 0,
                                'labor_cost' => $request->labor_cost ?? 0,
                                'overhead_cost' => $request->overhead_cost ?? 0,
                                'final_total_cost' => 0,
                                'unit_cost' => 0,
                                'notes' => $request->notes,
                                'created_by' => Auth::id(),
                ]);

                $totalCost = 0;

                foreach ($request->items as $item) {
                    $rawMaterialId = $item['raw_material_id'];
                    $requiredQty = (float) $item['actual_qty'];
                    $returnQty = (float) $item['return_qty']; // <--- GET MANUAL RETURN QTY

                    // ১. Base Unit Cost বের করা (ফলব্যাক হিসেবে)
                    $productModel = Product::find($rawMaterialId);
                    $baseUnitCost = (float) ($productModel->purchase_price ?? 0);
                    if ($baseUnitCost <= 0) {
                        $lastTx = DB::table('inventory_transactions')
                            ->where('product_id', $rawMaterialId)
                            ->where('unit_cost', '>', 0)
                            ->latest('date')
                            ->first();
                        $baseUnitCost = $lastTx ? (float) $lastTx->unit_cost : 0;
                    }

                    $remainingToConsume = $requiredQty;
                    $materialSubtotal = 0;

                    // ২. WIP (Location 2) থেকে মাল মাইনাস করা এবং কস্ট ক্যালকুলেট করা (Actual Consume এর জন্য)
                    if ($requiredQty > 0) {
                        $wipStocks = InventoryStock::where('product_id', $rawMaterialId)
                            ->where('location_id', 2)
                            ->where('quantity', '>', 0)
                            ->orderBy('created_at', 'asc')
                            ->get();

                        foreach ($wipStocks as $wStock) {
                            if ($remainingToConsume <= 0) break;

                            $consumeQty = min((float) $wStock->quantity, $remainingToConsume);

                            // এই ব্যাচের আসল দাম বের করার চেষ্টা
                            $issueItem = DB::table('production_issue_items as pii')
                                ->join('production_issues as pi', 'pii.production_issue_id', '=', 'pi.id')
                                ->where('pi.issued_to', $staffId)
                                ->where('pii.product_id', $rawMaterialId)
                                ->whereIn('pii.stock_id', function($q) use ($wStock) {
                                    $q->select('id')->from('inventory_stocks')->where('batch_no', $wStock->batch_no);
                                })
                                ->first();

                            $unitCost = $issueItem ? (float) $issueItem->unit_cost : $baseUnitCost;
                            if ($unitCost <= 0) $unitCost = $baseUnitCost;

                            $materialSubtotal += ($consumeQty * $unitCost);
                            $remainingToConsume -= $consumeQty;

                            $wStock->decrement('quantity', $consumeQty);

                            InventoryTransaction::create([
                                'date' => $request->production_date,
                                'product_id' => $rawMaterialId,
                                'location_id' => 2,
                                'transaction_type' => 'production_consume',
                                'reference_type' => 'Production',
                                'reference_id' => $production->id,
                                'quantity' => $consumeQty,
                                'unit_cost' => $unitCost,
                                'created_by' => Auth::id(),
                            ]);
                        }

                        // Force Consume if WIP stock mismatch
                        if ($remainingToConsume > 0) {
                            $materialSubtotal += ($remainingToConsume * $baseUnitCost);

                            $genericWip = InventoryStock::firstOrCreate(
                                ['product_id' => $rawMaterialId, 'location_id' => 2, 'batch_no' => null],
                                ['quantity' => 0]
                            );
                            $genericWip->decrement('quantity', $remainingToConsume);

                            InventoryTransaction::create([
                                'date' => $request->production_date,
                                'product_id' => $rawMaterialId,
                                'location_id' => 2,
                                'transaction_type' => 'production_consume',
                                'reference_type' => 'Production',
                                'reference_id' => $production->id,
                                'quantity' => $remainingToConsume,
                                'unit_cost' => $baseUnitCost,
                                'created_by' => Auth::id(),
                            ]);
                        }
                    }

                    // ৩. রিটার্ন (Savings) লজিক - ম্যানুয়াল ইনপুটের উপর ভিত্তি করে (অটো বাদ)
                    if ($returnQty > 0) {
                        // বর্তমান ইস্যু ভাউচার থেকে ব্যাচ এবং কস্ট বের করা
                        $currentIssueItem = DB::table('production_issue_items')
                            ->where('production_issue_id', $currentIssueId)
                            ->where('product_id', $rawMaterialId)
                            ->first();

                        $returnBatchNo = null;
                        $returnUnitCost = $baseUnitCost;

                        if ($currentIssueItem) {
                            $returnUnitCost = (float) $currentIssueItem->unit_cost > 0 ? (float) $currentIssueItem->unit_cost : $baseUnitCost;
                            $stockRef = DB::table('inventory_stocks')->where('id', $currentIssueItem->stock_id)->first();
                            if ($stockRef) $returnBatchNo = $stockRef->batch_no;
                        }

                        // মেইন গোডাউনে (Location 1) ফেরত পাঠানো
                        $mainStock = InventoryStock::firstOrCreate(
                            ['product_id' => $rawMaterialId, 'location_id' => 1, 'batch_no' => $returnBatchNo],
                            ['quantity' => 0]
                        );
                        $mainStock->increment('quantity', $returnQty);

                        // WIP থেকে রিমুভ করা
                        $wipStockToReduce = InventoryStock::where('product_id', $rawMaterialId)
                            ->where('location_id', 2)
                            ->where('quantity', '>=', $returnQty) // Try to find sufficient stock
                            ->first();

                        if ($wipStockToReduce) {
                            $wipStockToReduce->decrement('quantity', $returnQty);
                        } else {
                            // Fallback generic WIP reduction
                            $fallbackWip = InventoryStock::firstOrCreate(
                                ['product_id' => $rawMaterialId, 'location_id' => 2, 'batch_no' => null],
                                ['quantity' => 0]
                            );
                            $fallbackWip->decrement('quantity', $returnQty);
                        }

                        InventoryTransaction::create([
                            'date' => $request->production_date,
                            'product_id' => $rawMaterialId,
                            'location_id' => 1,
                            'transaction_type' => 'production_return_in',
                            'reference_type' => 'Production Return',
                            'reference_id' => $production->id,
                            'quantity' => $returnQty,
                            'unit_cost' => $returnUnitCost,
                            'created_by' => Auth::id(),
                        ]);
                    }

                    // ৪. Production Items Table এন্ট্রি (কস্ট সহ)
                    $avgUnitCost = $requiredQty > 0 ? (float) ($materialSubtotal / $requiredQty) : 0;

                    ProductionItem::create([
                        'production_id' => $production->id,
                        'raw_material_id' => $rawMaterialId,
                        'estimated_qty' => $item['estimated_qty'] ?? 0,
                        'actual_qty' => $requiredQty,
                        'unit_cost' => $avgUnitCost,
                        'subtotal_cost' => $materialSubtotal,
                    ]);

                    $totalCost += $materialSubtotal;
                }
                $laborCost = (float) ($request->labor_cost ?? 0);
                $overheadCost = (float) ($request->overhead_cost ?? 0);

                $finalTotalCost = $totalCost + $laborCost + $overheadCost;
                $finalUnitCost = $request->target_quantity > 0 ? (float) ($finalTotalCost / $request->target_quantity) : 0;
                // প্রোডাকশন আপডেট (Costing Update)
                $production->update([
                                'total_cost' => $totalCost, // শুধু ম্যাটেরিয়াল কস্ট
                                'total_material_cost' => $totalCost,
                                'final_total_cost' => $finalTotalCost,
                                'unit_cost' => $finalUnitCost, // ম্যাটেরিয়াল + লেবার + ওভারহেড মিলিয়ে পার পিস রেট
                            ]);

                            // ... production cost calculation seshe ...
                            $production->unit_cost = $finalUnitCost;
                            $production->save();

                            // Ready Production Location (3)-e stock update kora
                            InventoryStock::updateOrCreate(
                                [
                                    'product_id' => $production->finished_product_id,
                                    'location_id' => 3,
                                    'batch_no' => $production->batch_no,
                                ],
                                [
                                    'quantity' => DB::raw("quantity + $request->target_quantity"),
                                    'unit_cost' => $finalUnitCost, // <--- এটি এখন সেভ হবে
                                    'created_by' => Auth::id(),
                                ]
                            );

                            // মনে রাখবেন: নিচে থাকা 'finishedStock->increment' লাইনটি ডিলিট করে দিন কারণ উপরের updateOrCreate ই স্টক বাড়িয়ে দিচ্ছে।



                // ঘ) ফিনিশড জুতো Location 3 (Ready Products) এ যোগ করা
                $finishedStock = InventoryStock::firstOrCreate(
                    ['product_id' => $finishedProduct->id, 'location_id' => 3, 'batch_no' => $batchNo],
                    ['quantity' => 0]

                );
//                 $finishedStock->increment('quantity', $request->target_quantity);

                InventoryTransaction::create([
                                'date' => $request->production_date,
                                'product_id' => $finishedProduct->id,
                                'location_id' => 3,
                                'transaction_type' => 'finished_good_in',
                                'reference_type' => 'Production Final',
                                'reference_id' => $production->id,
                                'quantity' => $request->target_quantity,
                                'unit_cost' => $finalUnitCost, // <--- এখানে $production->unit_cost এর বদলে $finalUnitCost দিন
                                'created_by' => Auth::id(),
                            ]);

                DB::commit();
                return redirect()->route('productions.index')->with('success', 'Production entry successful! Costs & Stocks are perfectly mapped.');

            } catch (\Exception $e) {
                DB::rollBack();
                return back()->withInput()->withErrors(['error' => 'Error: ' . $e->getMessage()]);
            }
        }
    public function analytics()
    {
        // Eager load issue and production items
        $productions = Production::with(['items', 'issue', 'finishedProduct'])->get();

        // স্টাফদের নাম এবং আইডি নিয়ে আসা
        $staffs = \App\Models\Staff::pluck('name', 'id');

        $totalWastageCost = 0;
        $totalSavingsCost = 0;

        foreach($productions as $prd) {
            $variance = $prd->material_variance;
            if($variance > 0) $totalWastageCost += $variance;
            else $totalSavingsCost += abs($variance);
        }

        $topWastageBatches = $productions->sortByDesc(function($prd) {
            return $prd->material_variance;
        })->take(20); // এখন ৫টির বদলে ১০টি দেখালে অ্যানালাইসিস ভালো হবে

        return view('inventory.productions.analytics', compact('totalWastageCost', 'totalSavingsCost', 'topWastageBatches', 'staffs'));
    }
}
