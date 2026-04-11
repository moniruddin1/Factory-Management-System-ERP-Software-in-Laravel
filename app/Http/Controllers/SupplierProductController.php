<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\Product;
use Illuminate\Http\Request;

class SupplierProductController extends Controller
{
    public function index(Request $request)
    {
        // সব অ্যাক্টিভ সাপ্লায়ার ডাটাবেজ থেকে নেওয়া
        $suppliers = Supplier::where('is_active', 1)->get();

        // প্রোডাক্টের সাথে ক্যাটাগরি এবং ইউনিট রিলেশনগুলোও নিয়ে আসা হলো (N+1 Query Issue এড়াতে)
        $products = Product::with(['category', 'unit'])->get();

        $selectedSupplier = null;
        $mappedProductIds = [];

        // যদি ইউজার ড্রপডাউন থেকে কোনো সাপ্লায়ার সিলেক্ট করে
        if ($request->filled('supplier_id')) {
            $selectedSupplier = Supplier::with('products')->find($request->supplier_id);
            if ($selectedSupplier) {
                // এই সাপ্লায়ারের আগে থেকে ম্যাপ করা প্রোডাক্টের আইডিগুলো আলাদা করা হলো
                $mappedProductIds = $selectedSupplier->products->pluck('id')->toArray();
            }
        }

        return view('supplier-products.index', compact('suppliers', 'products', 'selectedSupplier', 'mappedProductIds'));
    }

    public function store(Request $request, Supplier $supplier)
    {
        $request->validate([
            'products' => 'nullable|array',
            'products.*' => 'exists:products,id'
        ]);

        // sync() মেথডটি নতুন আইডিগুলো সেভ করবে, আগের যেগুলো আনচেক করা হয়েছে তা ডিলিট করবে।
        $supplier->products()->sync($request->products ?? []);

        return redirect()->route('supplier-products.index', ['supplier_id' => $supplier->id])
                         ->with('success', 'Supplier items mapped successfully!');
    }
}
