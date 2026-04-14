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
        ]);

        try {
            DB::beginTransaction();

            $bom = Bom::findOrFail($request->bom_id);
            $finishedProduct = Product::findOrFail($bom->finished_product_id);
            $issue = ProductionIssue::findOrFail($request->production_issue_id);

            // রেফারেন্স নম্বর তৈরি
            $latestProduction = Production::latest('id')->first();
            $nextId = $latestProduction ? $latestProduction->id + 1 : 1;
            $referenceNo = 'PRD-' . date('Ym') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

            $totalCost = 0;
            $productionItems = [];
            $nextId = Production::max('id') + 1;
            $batchNo = 'BATCH-' . date('Ym') . '-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
            // ১. মেইন প্রডাকশন রেকর্ড তৈরি
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
            ]);

            // ২. লজিক: ইস্যু করা আইটেম এবং আসল খরচের হিসাব
            foreach ($request->items as $item) {
                $rawMaterialId = $item['raw_material_id'];
                $actualQty = $item['actual_qty'];
                $estimatedQty = $item['estimated_qty'] ?? 0;

                // এই আইটেমটি ভাউচারে কতটুকু ইস্যু করা হয়েছিল তা বের করা
                $issuedItems = $issue->items()->where('product_id', $rawMaterialId)->get();
                $totalIssuedQty = $issuedItems->sum('quantity');

                $unitCost = $issuedItems->first()->unit_cost ?? (Product::find($rawMaterialId)->purchase_price ?? 0);
                $subtotalCost = $actualQty * $unitCost;
                $totalCost += $subtotalCost;

                $productionItems[] = [
                    'production_id' => $production->id,
                    'raw_material_id' => $rawMaterialId,
                    'estimated_qty' => $estimatedQty,
                    'actual_qty' => $actualQty,
                    'unit_cost' => $unitCost,
                    'subtotal_cost' => $subtotalCost,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                // **********************************************
                // আপনার অসাধারণ ইনভেন্টরি লজিক ইমপ্লিমেন্টেশন
                // **********************************************

                // ক) Location 2 (Production Floor/WIP) থেকে মাল কমানো
                $wipStock = InventoryStock::where('product_id', $rawMaterialId)
                                          ->where('location_id', 2)
                                          ->first();

                if ($wipStock) {
                    $deductFromWip = min($actualQty, $totalIssuedQty);
                    $wipStock->decrement('quantity', $deductFromWip);

                    InventoryTransaction::create([
                        'date' => $request->production_date,
                        'product_id' => $rawMaterialId,
                        'location_id' => 2,
                        'transaction_type' => 'production_consume',
                        'reference_type' => 'Production',
                        'reference_id' => $production->id,
                        'quantity' => $deductFromWip,
                        'unit_cost' => $unitCost,
                        'created_by' => Auth::id(),
                    ]);
                }

                // খ) যদি মাল কম লাগে (Savings), তবে বেঁচে যাওয়া মাল Location 1 (Main Godown)-এ ফেরত যাবে
                if ($actualQty < $totalIssuedQty) {
                    $returnQty = $totalIssuedQty - $actualQty;

                    // Main Godown এ যোগ করা
                    $mainStock = InventoryStock::firstOrCreate(
                        ['product_id' => $rawMaterialId, 'location_id' => 1],
                        ['quantity' => 0]
                    );
                    $mainStock->increment('quantity', $returnQty);

                    // লেজার এন্ট্রি (Return)
                    InventoryTransaction::create([
                        'date' => $request->production_date,
                        'product_id' => $rawMaterialId,
                        'location_id' => 1,
                        'transaction_type' => 'production_return_in',
                        'reference_type' => 'Production Return',
                        'reference_id' => $production->id,
                        'quantity' => $returnQty,
                        'unit_cost' => $unitCost,
                        'created_by' => Auth::id(),
                    ]);
                }

                // গ) যদি মাল বেশি লাগে (Extra Usage), তবে Location 1 (Main Godown) থেকে FIFO সিস্টেমে কাটবে
                if ($actualQty > $totalIssuedQty) {
                    $extraNeeded = $actualQty - $totalIssuedQty;

                    // FIFO লজিক: Main Godown (1) এর পুরোনো স্টক আগে ব্যবহার হবে
                    $availableStocks = InventoryStock::where('product_id', $rawMaterialId)
                                                     ->where('location_id', 1)
                                                     ->where('quantity', '>', 0)
                                                     ->orderBy('created_at', 'asc') // First In
                                                     ->lockForUpdate()
                                                     ->get();

                    foreach ($availableStocks as $stockLine) {
                        if ($extraNeeded <= 0) break;

                        $qtyToDeduct = min($stockLine->quantity, $extraNeeded);
                        $stockLine->decrement('quantity', $qtyToDeduct);
                        $extraNeeded -= $qtyToDeduct;

                        InventoryTransaction::create([
                            'date' => $request->production_date,
                            'product_id' => $rawMaterialId,
                            'location_id' => 1,
                            'transaction_type' => 'extra_material_issue',
                            'reference_type' => 'Production Extra',
                            'reference_id' => $production->id,
                            'quantity' => $qtyToDeduct,
                            'unit_cost' => $unitCost,
                            'created_by' => Auth::id(),
                        ]);
                    }
                }
            }

            ProductionItem::insert($productionItems);

            $production->update([
                'total_cost' => $totalCost,
                'unit_cost' => $totalCost / $request->target_quantity,
            ]);

            // ঘ) ফিনিশড জুতো Location 3 (Ready Products) এ যোগ করা
            $finishedStock = InventoryStock::firstOrCreate(
                ['product_id' => $finishedProduct->id, 'location_id' => 3],
                ['quantity' => 0]
            );
            $finishedStock->increment('quantity', $request->target_quantity);

            InventoryTransaction::create([
                'date' => $request->production_date,
                'product_id' => $finishedProduct->id,
                'location_id' => 3,
                'transaction_type' => 'finished_good_in',
                'reference_type' => 'Production Final',
                'reference_id' => $production->id,
                'quantity' => $request->target_quantity,
                'unit_cost' => $production->unit_cost,
                'created_by' => Auth::id(),
            ]);

            DB::commit();
            return redirect()->route('productions.index')->with('success', 'Production entry successful! Stocks (WIP, Returns & Main) reconciled accurately.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Error: ' . $e->getMessage()]);
        }
    }
public function analytics()
{
    $productions = Production::with('items')->get();

    $totalWastageCost = 0;
    $totalSavingsCost = 0;

    foreach($productions as $prd) {
        $variance = $prd->material_variance;
        if($variance > 0) $totalWastageCost += $variance;
        else $totalSavingsCost += abs($variance);
    }

    // টপ ৫টি ওয়েস্টেজ হওয়া ব্যাচ
    $topWastageBatches = $productions->sortByDesc(function($prd) {
        return $prd->material_variance;
    })->take(5);

    return view('inventory.productions.analytics', compact('totalWastageCost', 'totalSavingsCost', 'topWastageBatches'));
}
}
