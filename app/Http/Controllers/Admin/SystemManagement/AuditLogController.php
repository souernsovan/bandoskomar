<?php

namespace App\Http\Controllers\Admin\SystemManagement;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    /**
     * Log theme change (called via AJAX from theme switcher).
     */
    public function logThemeChange(Request $request)
    {
        $request->validate(['theme' => 'required|in:light,dark']);
        AuditLogService::logThemeUpdate($request->theme);
        return response()->json(['ok' => true]);
    }

    /**
     * Display the audit log listing.
     */
    public function index(Request $request)
    {
        $perPage = in_array($request->input('per_page', 25), [25, 50, 100]) ? (int) $request->input('per_page', default: 25) : 25;

        $applyFilters = function ($query) use ($request) {
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('action_type', 'like', "%{$search}%")
                        ->orWhereHas('user', fn ($uq) => $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%"));
                    if (\DB::connection()->getDriverName() === 'mysql') {
                        $q->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(new_value, '$.object_changed')) LIKE ?", ["%{$search}%"])
                            ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(new_value, '$.details')) LIKE ?", ["%{$search}%"])
                            ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(old_value, '$.object_changed')) LIKE ?", ["%{$search}%"]);
                    }
                });
            }
            if ($request->filled('user_id')) {
                $query->where('user_id', $request->user_id);
            }
            if ($request->filled('action')) {
                $query->where('action_type', 'like', '%.' . $request->action);
            }
            if ($request->filled('module')) {
                $query->where('action_type', 'like', $request->module . '.%');
            }
            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }
            return $query;
        };

        $matchingBatchIds = $applyFilters(AuditLog::query())
            ->whereNotNull('batch_id')
            ->distinct()
            ->pluck('batch_id');

        $representativeQuery = AuditLog::query()->with('user')->where(function ($q) use ($applyFilters, $matchingBatchIds) {
            $q->where(function ($q2) use ($applyFilters) {
                $applyFilters($q2)->whereNull('batch_id');
            })->orWhere(function ($q2) use ($matchingBatchIds) {
                $q2->whereIn('batch_id', $matchingBatchIds)
                    ->whereIn('audit_logs.id', function ($sub) {
                        $sub->selectRaw('MIN(id)')
                            ->from('audit_logs as al_rep')
                            ->whereNotNull('al_rep.batch_id')
                            ->groupBy('al_rep.batch_id');
                    });
            });
        });

        $auditLogs = $representativeQuery->orderBy('created_at', 'desc')->paginate($perPage);

        $auditLogRows = $this->buildRowsFromRepresentatives($auditLogs->items());

        $users = User::orderBy('name')->get(['id', 'name', 'email']);

        return view('admin.system-management.audit-logs.index', compact('auditLogs', 'auditLogRows', 'perPage', 'users'));
    }

    /**
     * Build display rows from representative logs.
     * Non-batch logs become single rows; batch representatives trigger
     * a fetch of all sibling logs for that batch.
     */
    private function buildRowsFromRepresentatives(array $representatives): array
    {
        $batchIds = collect($representatives)->pluck('batch_id')->filter()->unique()->values();

        $batchMembers = [];
        if ($batchIds->isNotEmpty()) {
            $batchMembers = AuditLog::with('user')
                ->whereIn('batch_id', $batchIds)
                ->orderBy('created_at')
                ->get()
                ->groupBy('batch_id');
        }

        $rows = [];
        foreach ($representatives as $log) {
            if ($log->batch_id && isset($batchMembers[$log->batch_id])) {
                $rows[] = $batchMembers[$log->batch_id]->all();
            } else {
                $rows[] = $log;
            }
        }

        return $rows;
    }

    /**
     * Display the specified audit log entry.
     */
    public function show(Request $request, AuditLog $auditLog)
    {
        $auditLog->load('user');

        $batchLogs = null;
        if ($auditLog->batch_id && $request->boolean('batch')) {
            $batchLogs = AuditLog::where('batch_id', $auditLog->batch_id)
                ->with('user')
                ->orderBy('created_at')
                ->get();
        }

        $deviceInfo = null;
        if ($auditLog->user_agent) {
            $deviceInfo = \App\Helpers\UserAgentParser::parse($auditLog->user_agent)->toArray();
        }

        return view('admin.system-management.audit-logs.show', compact('auditLog', 'batchLogs', 'deviceInfo'));
    }
}
