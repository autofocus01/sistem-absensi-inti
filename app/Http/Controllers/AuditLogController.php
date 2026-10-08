<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    /**
     * Audit Trail
     *
     * Akses:
     * - HR
     * - Administrator Sistem
     *
     * Data audit bersifat read-only dari halaman ini.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        abort_unless(
            $user && (
                $user->isHrAdmin() ||
                $user->isAdminSistem()
            ),
            403
        );

        $query = AuditLog::query()
            ->with('user')
            ->latest();

        /*
        |--------------------------------------------------------------------------
        | FILTER ACTION
        |--------------------------------------------------------------------------
        */
        if ($request->filled('action')) {
            $query->where(
                'action',
                $request->string('action')
            );
        }

        /*
        |--------------------------------------------------------------------------
        | FILTER SUBJECT TYPE
        |--------------------------------------------------------------------------
        */
        if ($request->filled('subject_type')) {
            $query->where(
                'subject_type',
                $request->string('subject_type')
            );
        }

        /*
        |--------------------------------------------------------------------------
        | FILTER DATE FROM
        |--------------------------------------------------------------------------
        */
        if ($request->filled('from')) {
            $query->whereDate(
                'created_at',
                '>=',
                $request->input('from')
            );
        }

        /*
        |--------------------------------------------------------------------------
        | FILTER DATE UNTIL
        |--------------------------------------------------------------------------
        */
        if ($request->filled('until')) {
            $query->whereDate(
                'created_at',
                '<=',
                $request->input('until')
            );
        }

        /*
        |--------------------------------------------------------------------------
        | PAGINATION
        |--------------------------------------------------------------------------
        */
        $logs = $query
            ->paginate(25)
            ->withQueryString();

        return view('audit-logs.index', [
            'logs' => $logs,

            'filters' => $request->only([
                'action',
                'subject_type',
                'from',
                'until',
            ]),
        ]);
    }
}