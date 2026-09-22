<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()?->isHrAdmin(), 403);
        $query = AuditLog::query()->with('user')->latest();
        if ($request->filled('action')) $query->where('action', $request->string('action'));
        if ($request->filled('subject_type')) $query->where('subject_type', $request->string('subject_type'));
        if ($request->filled('from')) $query->whereDate('created_at', '>=', $request->input('from'));
        if ($request->filled('until')) $query->whereDate('created_at', '<=', $request->input('until'));
        return view('audit-logs.index', ['logs' => $query->paginate(25)->withQueryString(), 'filters' => $request->only(['action','subject_type','from','until'])]);
    }
}
