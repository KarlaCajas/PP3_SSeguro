<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Mostrar logs de actividad del sistema
     */
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $perPage = $request->get('per_page', 20);
        
        // Validar que per_page esté en los valores permitidos
        if (!in_array($perPage, [5, 10, 15, 20, 50])) {
            $perPage = 20;
        }

        $query = ActivityLog::with(['user'])
            ->orderBy('created_at', 'desc');

        // Aplicar filtro de búsqueda
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('model_type', 'LIKE', "%{$search}%")
                  ->orWhere('action', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'LIKE', "%{$search}%")
                               ->orWhere('email', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Filtros
        if ($request->filled('model_type')) {
            $query->where('model_type', $request->model_type);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate($perPage);
        
        // Mantener parámetros de búsqueda en la paginación
        $logs->appends($request->query());

        // Datos para filtros
        $modelTypes = [
            'App\Models\User' => 'Usuarios',
            'App\Models\Product' => 'Productos',
            'App\Models\Sale' => 'Ventas',
            'App\Models\Category' => 'Categorías',
        ];

        $actions = [
            'create' => 'Creado',
            'update' => 'Actualizado',
            'soft_delete' => 'Eliminado (Lógico)',
            'hard_delete' => 'Eliminado (Permanente)',
            'restore' => 'Restaurado',
        ];

        $users = \App\Models\User::orderBy('name')->get();

        return view('activity-logs.index', compact('logs', 'modelTypes', 'actions', 'users', 'search', 'perPage'));
    }

    /**
     * Mostrar detalles de un log específico
     */
    public function show(ActivityLog $activityLog)
    {
        $activityLog->load(['user']);
        return view('activity-logs.show', compact('activityLog'));
    }
}
