<?php

namespace App\Http\Controllers\Setup;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        // if (!auth()->user()->can('view-audit-logs')) { abort(403); }

        $query = Activity::with('causer')->latest();

        // 1. Search Logic (User name, Module name, Event)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('event', 'like', "%{$search}%")
                  ->orWhere('subject_type', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHasMorph('causer', '*', function($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // 2. Filter by Event (created, updated, deleted)
        if ($request->filled('event') && $request->event !== 'all') {
            $query->where('event', $request->event);
        }

        // 3. Filter by Date
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        // 4. Dynamic Pagination (25, 50, 100, 250)
        $perPage = $request->get('per_page', 25);

        // Appends used to keep the query string in pagination links
        $logs = $query->paginate($perPage)->appends($request->query());

        return view('setup.audit-logs.index', compact('logs'));
    }
}
