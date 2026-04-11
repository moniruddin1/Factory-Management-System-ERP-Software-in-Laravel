<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        // পারমিশন চেক
        if (!auth()->user()->can('view-users')) {
            abort(403);
        }

        $query = User::with('roles');

        // সার্চ লজিক (Name, Username, Phone, Email)
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('username', 'like', '%' . $searchTerm . '%')
                  ->orWhere('email', 'like', '%' . $searchTerm . '%')
                  ->orWhere('phone', 'like', '%' . $searchTerm . '%');
            });
        }

        // প্যাজিনেশন (ড্রপডাউন থেকে ভ্যালু নিলে সেটা কাজ করবে)
        $perPage = $request->get('per_page', 25);
        $users = $query->latest()->paginate($perPage);

        $roles = Role::all();

        return view('setup.users.index', compact('users', 'roles'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->can('manage-users')) {
            abort(403);
        }

        $request->validate([
            'name'      => 'required|string|max:255',
            'username'  => 'required|string|max:50|unique:users,username',
            'email'     => 'required|email|unique:users,email',
            'phone'     => 'nullable|string|max:20',
            'job_title' => 'nullable|string|max:100',
            'password'  => 'required|min:8',
            'role'      => 'required'
        ]);

        $user = User::create([
            'name'      => $request->name,
            'username'  => $request->username,
            'email'     => $request->email,
            'phone'     => $request->phone,
            'job_title' => $request->job_title,
            'password'  => Hash::make($request->password),
        ]);

        $user->assignRole($request->role);

        return back()->with('success', 'User created successfully!');
    }

    public function update(Request $request, User $user)
    {
        if (!auth()->user()->can('manage-users')) {
            abort(403);
        }

        $request->validate([
            'name'      => 'required|string|max:255',
            'username'  => ['required', 'string', 'max:50', Rule::unique('users')->ignore($user->id)],
            'email'     => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone'     => 'nullable|string|max:20',
            'job_title' => 'nullable|string|max:100',
            'role'      => 'required'
        ]);

        $user->update([
            'name'      => $request->name,
            'username'  => $request->username,
            'email'     => $request->email,
            'phone'     => $request->phone,
            'job_title' => $request->job_title,
        ]);

        // পাসওয়ার্ড ইনপুট দিলে আপডেট হবে
        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        // রোল সিঙ্ক করা (পুরাতন রোল মুছে নতুনটা বসবে)
        $user->syncRoles($request->role);

        return back()->with('success', 'User updated successfully!');
    }

    public function destroy(User $user)
    {
        if (!auth()->user()->can('delete-users')) {
            abort(403, 'আপনার ইউজার ডিলিট করার পারমিশন নেই।');
        }

        // নিজে নিজেকে ডিলিট করা এবং Super Admin ডিলিট করা রোধ
        if ($user->hasRole('Super Admin') || $user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete this user!');
        }

        $user->delete();
        return back()->with('success', 'User deleted successfully!');
    }
}
