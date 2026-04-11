<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function index()
    {
        if (!auth()->user()->can('view-units')) { abort(403, 'Unauthorized access.'); }

        $units = Unit::latest()->get();
        return view('setup.units.index', compact('units'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->can('manage-units')) { abort(403, 'Unauthorized action.'); }

        $request->validate([
            'name' => 'required|string|max:255|unique:units',
            'short_name' => 'required|string|max:50|unique:units',
        ]);

        Unit::create([
            'name' => $request->name,
            'short_name' => $request->short_name,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('units.index')->with('success', 'Unit created successfully.');
    }

    public function update(Request $request, Unit $unit)
    {
        if (!auth()->user()->can('manage-units')) { abort(403, 'Unauthorized action.'); }

        $request->validate([
            'name' => 'required|string|max:255|unique:units,name,' . $unit->id,
            'short_name' => 'required|string|max:50|unique:units,short_name,' . $unit->id,
        ]);

        $unit->update([
            'name' => $request->name,
            'short_name' => $request->short_name,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('units.index')->with('success', 'Unit updated successfully.');
    }

    public function destroy(Unit $unit)
    {
        if (!auth()->user()->can('delete-units')) { abort(403, 'Unauthorized action.'); }

        // Note: Later we will add a check here to prevent deletion if the unit is used in products/inventory
        $unit->delete();

        return redirect()->route('units.index')->with('success', 'Unit deleted successfully.');
    }
}
