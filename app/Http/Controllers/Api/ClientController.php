<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreClientRequest;
use App\Http\Requests\Api\UpdateClientRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = User::role('cliente')
                ->select('id', 'name', 'email', 'is_active', 'created_at');

            // Filtros opcionales
            if ($request->has('status')) {
                $isActive = $request->status === 'active';
                $query->where('is_active', $isActive);
            }

            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }

            // Paginación
            $perPage = min($request->get('per_page', 15), 100);
            $clients = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Clientes obtenidos exitosamente',
                'data' => $clients
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener clientes',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClientRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            
            // Crear el cliente solo con campos disponibles
            $client = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'is_active' => ($validated['status'] ?? 'active') === 'active',
            ]);

            // Asignar rol de cliente
            $client->assignRole('cliente');

            return response()->json([
                'success' => true,
                'message' => 'Cliente creado exitosamente',
                'data' => $client->load('roles')
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear cliente',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $client = User::role('cliente')
                ->with(['roles'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Cliente obtenido exitosamente',
                'data' => $client
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cliente no encontrado'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener cliente',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClientRequest $request, string $id): JsonResponse
    {
        try {
            $client = User::role('cliente')->findOrFail($id);
            $validated = $request->validated();

            // Actualizar datos disponibles
            $updateData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'is_active' => ($validated['status'] ?? 'active') === 'active',
            ];

            // Solo actualizar password si se proporciona
            if (!empty($validated['password'])) {
                $updateData['password'] = Hash::make($validated['password']);
            }

            $client->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Cliente actualizado exitosamente',
                'data' => $client->fresh()->load('roles')
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cliente no encontrado'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar cliente',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $client = User::role('cliente')->findOrFail($id);

            // Verificar si tiene facturas asociadas
            if ($client->invoicesAsCustomer()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar el cliente porque tiene facturas asociadas'
                ], 422);
            }

            $client->delete();

            return response()->json([
                'success' => true,
                'message' => 'Cliente eliminado exitosamente'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cliente no encontrado'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar cliente',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
