<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'unit'])->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        $perPage = $request->get('per_page', 25);
        $products = $query->paginate($perPage)->appends($request->query());

        $categories = Category::all();
        $units = Unit::all();

        return view('products.index', compact('products', 'categories', 'units'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'code'           => 'nullable|string|max:50|unique:products,code',
            'type'           => 'required|in:raw_material,finished_good',
            'category_id'    => 'required|exists:categories,id',
            'unit_id'        => 'required|exists:units,id',
            'purchase_price' => 'nullable|numeric|min:0',
            'selling_price'  => 'nullable|numeric|min:0',
            'alert_quantity' => 'nullable|integer|min:0',
            'image'          => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'description'    => 'nullable|string'
        ]);

        $data = $request->all();

        // Image Upload Logic
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        Product::create($data);

        return back()->with('success', 'Product added successfully!');
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'code'           => ['nullable', 'string', 'max:50', Rule::unique('products')->ignore($product->id)],
            'type'           => 'required|in:raw_material,finished_good',
            'category_id'    => 'required|exists:categories,id',
            'unit_id'        => 'required|exists:units,id',
            'purchase_price' => 'nullable|numeric|min:0',
            'selling_price'  => 'nullable|numeric|min:0',
            'alert_quantity' => 'nullable|integer|min:0',
            'image'          => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'description'    => 'nullable|string'
        ]);

        $data = $request->all();

        // Image Upload Logic
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        return back()->with('success', 'Product updated successfully!');
    }

    public function destroy(Product $product)
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        $product->delete();
        return back()->with('success', 'Product deleted successfully!');
    }
}
