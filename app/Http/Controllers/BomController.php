<?php

namespace App\Http\Controllers;

use App\Models\Bom;
use App\Models\BomItem;
use App\Models\Product;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class BomController extends Controller
{
    public function index()
    {
        $boms = Bom::with(['finishedProduct', 'creator'])->latest()->paginate(15);
        return view('inventory.boms.index', compact('boms'));
    }

    public function create()
    {
        // ফিনিশড প্রোডাক্ট এবং র-ম্যাটেরিয়ালস (আপাতত সব প্রোডাক্ট পাঠানো হচ্ছে, আপনার যদি ক্যাটাগরি করা থাকে তবে ফিল্টার করে নিতে পারবেন)
        $products = Product::all();
        $units = Unit::all();
        return view('inventory.boms.create', compact('products', 'units'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'finished_product_id' => 'required|exists:products,id',
            'name' => 'required|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.raw_material_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_id' => 'required|exists:units,id',
        ]);

        try {
            DB::beginTransaction();

            $bom = Bom::create([
                'finished_product_id' => $request->finished_product_id,
                'name' => $request->name,
                'created_by' => Auth::id(),
            ]);

            foreach ($request->items as $item) {
                BomItem::create([
                    'bom_id' => $bom->id,
                    'raw_material_id' => $item['raw_material_id'],
                    'quantity' => $item['quantity'],
                    'unit_id' => $item['unit_id'],
                ]);
            }

            DB::commit();
            return redirect()->route('boms.index')->with('success', 'BOM (Formula) created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error creating BOM: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $bom = Bom::with(['finishedProduct', 'items.rawMaterial', 'items.unit', 'creator'])->findOrFail($id);
        return view('inventory.boms.show', compact('bom'));
    }

    public function destroy($id)
    {
        $bom = Bom::findOrFail($id);
        $bom->delete();
        return redirect()->route('boms.index')->with('success', 'BOM deleted successfully!');
    }
}
