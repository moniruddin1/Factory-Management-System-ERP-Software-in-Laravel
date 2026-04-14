<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffController extends Controller
{
    public function index()
    {
        $staffs = Staff::with('creator')->latest()->get();
        return view('staffs.index', compact('staffs'));
    }

    public function create()
    {
        return view('staffs.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|unique:staffs,phone',
            'designation' => 'required|string|max:255',
            'base_salary' => 'nullable|numeric|min:0',
        ]);

        Staff::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'designation' => $request->designation,
            'base_salary' => $request->base_salary ?? 0,
            'is_active' => $request->has('is_active'),
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('staffs.index')->with('success', 'Staff added successfully.');
    }

    public function edit(Staff $staff)
    {
        return view('staffs.edit', compact('staff'));
    }

    public function update(Request $request, Staff $staff)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|unique:staffs,phone,' . $staff->id,
            'designation' => 'required|string|max:255',
            'base_salary' => 'nullable|numeric|min:0',
        ]);

        $staff->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'designation' => $request->designation,
            'base_salary' => $request->base_salary ?? 0,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('staffs.index')->with('success', 'Staff updated successfully.');
    }

    public function destroy(Staff $staff)
    {
        $staff->delete();
        return redirect()->route('staffs.index')->with('success', 'Staff deleted successfully.');
    }
}
