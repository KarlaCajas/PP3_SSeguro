<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rule;

/**
 * Controlador para gestión de usuarios
 * Maneja CRUD completo de usuarios según roles
 */
class UserController extends Controller
{
    /**
     * Mostrar listado de usuarios
     */
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $perPage = $request->get('per_page', 10);
        
        // Validar que per_page esté en los valores permitidos
        if (!in_array($perPage, [5, 10, 15, 20, 50])) {
            $perPage = 10;
        }

        $query = User::with('roles');

        // Aplicar filtro de búsqueda
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhereHas('roles', function ($roleQuery) use ($search) {
                      $roleQuery->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        $users = $query->orderBy('name')->paginate($perPage);
        
        // Mantener parámetros de búsqueda en la paginación
        $users->appends($request->query());

        return view('users.index', compact('users', 'search', 'perPage'));
    }

    /**
     * Mostrar formulario para crear nuevo usuario
     */
    public function create()
    {
        $availableRoles = $this->getAvailableRoles();
        return view('users.create', compact('availableRoles'));
    }

    /**
     * Almacenar nuevo usuario
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users',
                'regex:/^[a-zA-Z0-9._%+-]+@barespe\.com$/'
            ],
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|exists:roles,name',
            'is_active' => 'boolean',
        ], [
            'email.regex' => 'El correo electrónico debe tener el dominio @barespe.com'
        ]);

        if (!$this->canAssignRole($request->role)) {
            return back()->withErrors(['role' => 'No tienes permisos para asignar este rol']);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_active' => $request->has('is_active') ? (bool)$request->is_active : true,
        ]);

        $user->assignRole($request->role);

        return redirect()->route('users.index')
            ->with('success', 'Usuario creado exitosamente');
    }

    /**
     * Mostrar formulario para editar usuario
     */
    public function edit(User $user)
    {
        // Verificar permisos para editar este usuario
        if (!$this->canEditUser($user)) {
            abort(403, 'No tienes permisos para editar este usuario');
        }

        $availableRoles = $this->getAvailableRoles();
        $currentRole = $user->roles->first()?->name;
        
        return view('users.edit', compact('user', 'availableRoles', 'currentRole'));
    }

    /**
     * Actualizar usuario existente
     */
    public function update(Request $request, User $user)
    {
        // Verificar permisos para editar este usuario
        if (!$this->canEditUser($user)) {
            abort(403, 'No tienes permisos para editar este usuario');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
                'regex:/^[a-zA-Z0-9._%+-]+@barespe\.com$/'
            ],
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|string|exists:roles,name',
            'is_active' => 'boolean',
        ], [
            'email.regex' => 'El correo electrónico debe tener el dominio @barespe.com'
        ]);

        // Verificar si puede asignar el nuevo rol
        if (!$this->canAssignRole($request->role)) {
            return back()->withErrors(['role' => 'No tienes permisos para asignar este rol']);
        }

        //No permitir que usuarios cambien su propio rol
        $currentRole = $user->roles->first()?->name;
        if ($user->id === auth()->id() && $currentRole !== $request->role) {
            return back()->withErrors(['role' => 'No puedes cambiar tu propio rol']);
        }

        // Actualizar datos básicos
        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'is_active' => $request->has('is_active') ? (bool)$request->is_active : $user->is_active,
        ];

        // Solo actualizar contraseña si se proporciona
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $user->update($userData);

        // Actualizar rol si cambió
        if ($currentRole !== $request->role) {
            $user->syncRoles([$request->role]);
        }

        return redirect()->route('users.index')
            ->with('success', 'Usuario actualizado exitosamente');
    }

    /**
     * Eliminar usuario (soft delete)
     */
    public function destroy(Request $request, User $user)
    {
        // Verificar permisos para eliminar este usuario
        if (!$this->canDeleteUser($user)) {
            abort(403, 'No tienes permisos para eliminar este usuario');
        }

        // No permitir auto-eliminación
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'No puedes eliminarte a ti mismo']);
        }

        // Verificar si el usuario puede ser eliminado
        if (!$user->canBeDeleted()) {
            return back()->withErrors([
                'error' => 'No se puede eliminar este usuario: ' . $user->getDeletionBlockReason()
            ]);
        }

        $request->validate([
            'deletion_reason' => 'required|string|max:500',
        ]);

        $userName = $user->name;
        
        // Realizar soft delete
        $user->update([
            'deleted_by' => auth()->id(),
            'deletion_reason' => $request->deletion_reason,
        ]);
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', "Usuario '{$userName}' eliminado exitosamente");
    }

    /**
     * Mostrar papelera de usuarios eliminados
     */
    public function trash(Request $request)
    {
        $search = $request->get('search', '');
        $perPage = $request->get('per_page', 10);
        
        // Validar que per_page esté en los valores permitidos
        if (!in_array($perPage, [5, 10, 15, 20, 50])) {
            $perPage = 10;
        }

        $query = User::onlyTrashed()
            ->with(['roles', 'deletedBy']);

        // Aplicar filtro de búsqueda
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('deletion_reason', 'LIKE', "%{$search}%")
                  ->orWhereHas('roles', function ($roleQuery) use ($search) {
                      $roleQuery->where('name', 'LIKE', "%{$search}%");
                  })
                  ->orWhereHas('deletedBy', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        $deletedUsers = $query->orderBy('deleted_at', 'desc')->paginate($perPage);
        
        // Mantener parámetros de búsqueda en la paginación
        $deletedUsers->appends($request->query());
            
        return view('users.trash', compact('deletedUsers', 'search', 'perPage'));
    }

    /**
     * Restaurar usuario eliminado
     */
    public function restore(Request $request, $id)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
        ]);

        $user = User::onlyTrashed()->findOrFail($id);
        
        // Verificar permisos
        if (!$this->canRestoreUser($user)) {
            abort(403, 'No tienes permisos para restaurar este usuario');
        }

        $userName = $user->name;
        $user->restore();

        return redirect()->route('users.trash')
            ->with('success', "Usuario '{$userName}' restaurado exitosamente");
    }

    /**
     * Eliminar usuario permanentemente
     */
    public function forceDelete(Request $request, $id)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
        ]);

        $user = User::onlyTrashed()->findOrFail($id);
        
        // Verificar permisos
        if (!$this->canForceDeleteUser($user)) {
            abort(403, 'No tienes permisos para eliminar permanentemente este usuario');
        }

        // Verificar si el usuario tiene ventas o facturas registradas
        if (($user->sales && $user->sales->count() > 0) || ($user->invoices && $user->invoices->count() > 0)) {
            return back()->withErrors(['error' => 'No se puede eliminar permanentemente un usuario que tiene ventas o facturas registradas']);
        }

        $userName = $user->name;
        $user->forceDelete();

        return redirect()->route('users.trash')
            ->with('success', "Usuario '{$userName}' eliminado permanentemente");
    }

    /**
     * Obtener roles disponibles según el usuario autenticado
     */
    private function getAvailableRoles()
    {
        $user = auth()->user();
        
        if ($user->hasRole('admin')) {
            return Role::all();
        } elseif ($user->hasRole('secre')) {
            return Role::where('name', '!=', 'admin')->get();
        }
        
        return collect();
    }

    /**
     * Verificar si el usuario puede asignar un rol específico
     */
    private function canAssignRole(string $role): bool
    {
        $user = auth()->user();
        
        if ($user->hasRole('admin')) {
            return true;
        } elseif ($user->hasRole('secre')) {
            return $role !== 'admin';
        }
        
        return false;
    }

    /**
     * Verificar si el usuario puede editar otro usuario
     */
    private function canEditUser(User $targetUser): bool
    {
        $currentUser = auth()->user();
        
        // Admin puede editar a cualquiera
        if ($currentUser->hasRole('admin')) {
            return true;
        }
        
        // Secre puede editar usuarios que no sean admin
        if ($currentUser->hasRole('secre')) {
            return !$targetUser->hasRole('admin');
        }
        
        return false;
    }

    /**
     * Verificar si el usuario puede eliminar otro usuario
     */
    private function canDeleteUser(User $targetUser): bool
    {
        $currentUser = auth()->user();
        
        // Admin puede eliminar a cualquiera (excepto a sí mismo)
        if ($currentUser->hasRole('admin')) {
            return $targetUser->id !== $currentUser->id;
        }
        
        // Secre puede eliminar usuarios que no sean admin
        if ($currentUser->hasRole('secre')) {
            return !$targetUser->hasRole('admin') && $targetUser->id !== $currentUser->id;
        }
        
        return false;
    }

    /**
     * Verificar si el usuario puede restaurar otro usuario
     */
    private function canRestoreUser(User $targetUser): bool
    {
        return $this->canEditUser($targetUser);
    }

    /**
     * Verificar si el usuario puede eliminar permanentemente otro usuario
     */
    private function canForceDeleteUser(User $targetUser): bool
    {
        return $this->canDeleteUser($targetUser);
    }

    public function crearTokenAcceso(User $user)
    {
        $token = $user->createToken('API Token')->plainTextToken;

        // Guardar el token en la sesión como mensaje flash
        session()->flash('token_message', "Token de acceso generado: {$token} para el usuario {$user->name}");

        return redirect()->route('users.index');
    }
}