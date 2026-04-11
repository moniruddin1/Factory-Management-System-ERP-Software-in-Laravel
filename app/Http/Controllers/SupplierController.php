<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $query = Supplier::query();

        // Search Filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%");
            });
        }

        // Material Type Filter
        if ($request->filled('material_type') && $request->material_type !== 'all') {
            $query->where('material_type', $request->material_type);
        }

        // Pagination setup (25, 50, 100, 250)
        $perPage = $request->input('per_page', 25);
        $suppliers = $query->latest()->paginate($perPage)->appends($request->all());

        return view('suppliers.index', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'required|string|max:20|unique:suppliers,phone',
            'email' => 'nullable|email|max:255|unique:suppliers,email',
            'address' => 'nullable|string',
            'material_type' => 'nullable|string|max:100',
            'opening_balance' => 'nullable|numeric',
        ]);

        Supplier::create($validated);

        return redirect()->route('suppliers.index')->with('success', 'Supplier added successfully.');
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'required|string|max:20|unique:suppliers,phone,' . $supplier->id,
            'email' => 'nullable|email|max:255|unique:suppliers,email,' . $supplier->id,
            'address' => 'nullable|string',
            'material_type' => 'nullable|string|max:100',
            // Opening balance usually shouldn't be edited after transactions start, but keeping it editable for initial setup
            'opening_balance' => 'nullable|numeric',
        ]);

        // Adjust current balance if opening balance changes (Basic logic for setup phase)
        if (isset($validated['opening_balance']) && $validated['opening_balance'] != $supplier->opening_balance) {
            $difference = $validated['opening_balance'] - $supplier->opening_balance;
            $supplier->current_balance += $difference;
        }

        $supplier->update($validated);

        return redirect()->route('suppliers.index')->with('success', 'Supplier updated successfully.');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return redirect()->route('suppliers.index')->with('success', 'Supplier deleted successfully.');
    }
}
