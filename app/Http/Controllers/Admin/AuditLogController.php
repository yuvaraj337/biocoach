<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminAuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AdminAuditLog::with('admin')->latest();

        if ($request->filled('action')) {
            $query->where('action', $request->string('action')->toString());
        }

        if ($request->filled('q')) {
            $term = trim((string) $request->q);
            $query->where(function ($sub) use ($term) {
                $sub->where('subject_type', 'like', '%'.$term.'%')
                    ->orWhere('reason', 'like', '%'.$term.'%')
                    ->orWhere('action', 'like', '%'.$term.'%');
            });
        }

        $logs = $query->paginate(30)->withQueryString();
        $actions = AdminAuditLog::query()->select('action')->distinct()->orderBy('action')->pluck('action');

        return view('admin.audit.index', compact('logs', 'actions'));
    }
}
