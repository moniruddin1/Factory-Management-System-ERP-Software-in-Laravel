<?php

namespace App\Http\Controllers;

use App\Models\InventoryStock;
use Illuminate\Http\Request;

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
}
