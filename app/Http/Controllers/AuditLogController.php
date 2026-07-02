<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AuditLogController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->input('search');
        $module = $request->input('module');
        $action = $request->input('action');
        $userId = $request->input('user_id');

        $auditLogs = AuditLog::query()
            ->with('user')
            ->when($search, function ($query, string $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('description', 'like', "%{$search}%")
                        ->orWhere('module', 'like', "%{$search}%")
                        ->orWhere('actiion', 'like', "%{$search}%")
                        ->orWhere('auditable_type', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($query) use ($search) {
                            $query->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            })
            ->when($module, function ($query, string $module) {
                $query->where('module', $module);
            })
            ->when($action, function ($query, string $action) {
                $query->where('action', $action);
            })
            ->when($userId, function ($query, string $userId) {
                $query->where('user_id', $userId);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $modules = AuditLog::query()
            ->select('module')
            ->distinct()
            ->orderBy('module')
            ->pluck('module');

        $actions = AuditLog::query()
            ->select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');
            
        $users = User::query()
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return Inertia::render('AuditLogs/Index', [
            'auditLogs' => $auditLogs,
            'modules' => $modules,
            'actions' => $actions,
            'users' => $users,
            'filters' => [
                'search' => $search,
                'module' => $module,
                'action' => $action,
                'user_id' => $userId,
            ]
        ]);
    }

    public function show(AuditLog $auditLog): Response
    {
        $auditLog->load('user');    

        return Inertia::render('AuditLogs/Show', [
            'auditLog' => $auditLog,
        ]);
    }
}