<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        // View Permission Check
        if (!auth()->user()->can('view-categories')) {
            abort(403, 'Unauthorized action.');
        }

        $query = Category::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $perPage = $request->input('per_page', 25);
        $categories = $query->latest()->paginate($perPage);

        return view('master.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        // Manage Permission Check
        if (!auth()->user()->can('manage-categories')) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|unique:categories,name',
            'type' => 'required'
        ]);

        Category::create($request->all());

        return back()->with('success', 'Category has been created successfully!');
    }

    public function update(Request $request, Category $category)
    {
        // Manage Permission Check
        if (!auth()->user()->can('manage-categories')) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|unique:categories,name,' . $category->id,
            'type' => 'required'
        ]);

        $category->update($request->all());
        return redirect()->back()->with('success', 'Category updated successfully!');
    }

    public function destroy(Category $category)
    {
        // Manage Permission Check
        if (!auth()->user()->can('manage-categories')) {
            abort(403);
        }
if (!auth()->user()->can('delete-categories')) {
                return back()->with('error', 'আপনার ক্যাটাগরি ডিলিট করার পারমিশন নেই।');
            }

            $category->delete();
            return back()->with('success', 'Category has been removed successfully!');



    }
}
