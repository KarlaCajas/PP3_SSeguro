<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Controlador para gestión de categorías
 * Solo accesible por usuarios con rol 'bodega'
 */
class CategoryController extends Controller
{
    /**
     * Mostrar listado de categorías
     */
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $perPage = $request->get('per_page', 10);
        
        // Validar que per_page esté en los valores permitidos
        if (!in_array($perPage, [5, 10, 15, 20, 50])) {
            $perPage = 10;
        }

        $query = Category::withCount('products');

        // Aplicar filtro de búsqueda
        if ($search) {
            $query->where('name', 'LIKE', "%{$search}%");
        }

        $categories = $query->orderBy('name')->paginate($perPage);
        
        // Mantener parámetros de búsqueda en la paginación
        $categories->appends($request->query());

        return view('categories.index', compact('categories', 'search', 'perPage'));
    }

    /**
     * Mostrar formulario para crear nueva categoría
     */
    public function create()
    {
        return view('categories.create');
    }

    /**
     * Almacenar nueva categoría
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ]);

        Category::create([
            'name' => $request->name,
        ]);

        return redirect()->route('categories.index')
            ->with('success', 'Categoría creada exitosamente');
    }

    /**
     * Mostrar formulario para editar categoría
     */
    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    /**
     * Actualizar categoría existente
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('categories')->ignore($category->id)],
        ]);

        $category->update([
            'name' => $request->name,
        ]);

        return redirect()->route('categories.index')
            ->with('success', 'Categoría actualizada exitosamente');
    }

    /**
     * Eliminar categoría
     */
    public function destroy(Category $category)
    {
        // Verificar si la categoría puede ser eliminada
        if (!$category->canBeDeleted()) {
            return back()->withErrors([
                'error' => 'No se puede eliminar esta categoría: ' . $category->getDeletionBlockReason()
            ]);
        }

        $categoryName = $category->name;
        $category->delete();

        return redirect()->route('categories.index')
            ->with('success', "Categoría '{$categoryName}' eliminada exitosamente");
    }
}