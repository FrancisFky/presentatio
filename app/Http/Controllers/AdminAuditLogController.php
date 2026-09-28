<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\AdminAuditLog;
use Illuminate\Http\Request;

/**
 * Journal d'activité des administrateurs.
 */
class AdminAuditLogController extends Controller
{
    public function index(Request $request)
    {
        $logs = AdminAuditLog::with('admin')
            ->when($request->filled('admin'), fn ($query) => $query->where('admin_id', $request->integer('admin')))
            ->when($request->filled('search'), fn ($query) => $query->where('action', 'like', '%' . addcslashes($request->input('search'), '%_\\') . '%'))
            ->latest('created_at')
            ->paginate(50)
            ->withQueryString();

        $admins = Admin::orderBy('name')->get(['id', 'name'])
            ->map(fn ($admin) => ['value' => $admin->id, 'label' => $admin->name]);

        return view('admins.audit', compact('logs', 'admins'));
    }
}
