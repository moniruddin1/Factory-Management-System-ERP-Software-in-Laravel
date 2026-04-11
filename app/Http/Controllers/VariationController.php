<?php

namespace App\Http\Controllers;

use App\Models\Variation;
use Illuminate\Http\Request;

class VariationController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user()->can('view-variations')) { abort(403, 'Unauthorized access.'); }

        $query = Variation::query();

        // Search logic
        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter by Type (Size/Color)
        if ($request->type) {
            $query->where('type', $request->type);
        }

        $variations = $query->latest()->paginate($request->per_page ?? 25);

        return view('setup.variations.index', compact('variations'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->can('manage-variations')) { abort(403, 'Unauthorized action.'); }

        $request->validate([
            'type' => 'required|in:Size,Color',
            'name' => 'required|string|max:255',
            'value' => 'nullable|string|max:50',
        ]);

        Variation::create([
            'type' => $request->type,
            'name' => $request->name,
            'value' => $request->value,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->back()->with('success', 'Variation created successfully.');
    }

    public function update(Request $request, Variation $variation)
    {
        if (!auth()->user()->can('manage-variations')) { abort(403, 'Unauthorized action.'); }

        $request->validate([
            'type' => 'required|in:Size,Color',
            'name' => 'required|string|max:255',
            'value' => 'nullable|string|max:50',
        ]);

        $variation->update([
            'type' => $request->type,
            'name' => $request->name,
            'value' => $request->value,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->back()->with('success', 'Variation updated successfully.');
    }

    public function destroy(Variation $variation)
    {
        if (!auth()->user()->can('delete-variations')) { abort(403, 'Unauthorized action.'); }

        $variation->delete();
        return redirect()->back()->with('success', 'Variation deleted successfully.');
    }
}
