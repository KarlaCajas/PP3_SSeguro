<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

/**
 * Controlador para gestión de productos
 * Solo accesible por usuarios con rol 'bodega'
 */
class ProductController extends Controller
{
    /**
     * Mostrar listado de productos con sus categorías
     */
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $perPage = $request->get('per_page', 10);
        
        // Validar que per_page esté en los valores permitidos
        if (!in_array($perPage, [5, 10, 15, 20, 50])) {
            $perPage = 10;
        }

        $query = Product::with('category');

        // Aplicar filtro de búsqueda
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhereHas('category', function ($categoryQuery) use ($search) {
                      $categoryQuery->where('name', 'LIKE', "%{$search}%");
                  })
                  ->orWhere('price', 'LIKE', "%{$search}%")
                  ->orWhere('stock', 'LIKE', "%{$search}%");
            });
        }

        $products = $query->orderBy('name')->paginate($perPage);
        
        // Mantener parámetros de búsqueda en la paginación
        $products->appends($request->query());

        return view('products.index', compact('products', 'search', 'perPage'));
    }

    /**
     * Mostrar formulario para crear nuevo producto
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('products.create', compact('categories'));
    }

    /**
     * Almacenar nuevo producto
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0|max:999999.99',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
        ]);

        Product::create([
            'name' => $request->name,
            'price' => $request->price,
            'stock' => $request->stock,
            'category_id' => $request->category_id,
        ]);

        return redirect()->route('products.index')
            ->with('success', 'Producto creado exitosamente');
    }

    /**
     * Mostrar formulario para editar producto
     */
    public function edit(Product $product)
    {
        // Cargar relaciones necesarias
        $product->load(['category', 'sales']);
        $categories = Category::orderBy('name')->get();
        
        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * Actualizar producto existente
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0|max:999999.99',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
        ]);

        $product->update([
            'name' => $request->name,
            'price' => $request->price,
            'stock' => $request->stock,
            'category_id' => $request->category_id,
        ]);

        return redirect()->route('products.index')
            ->with('success', 'Producto actualizado exitosamente');
    }

    /**
     * Eliminar producto (soft delete)
     */
    public function destroy(Request $request, Product $product)
    {
        $request->validate([
            'deletion_reason' => 'required|string|max:500',
        ]);

        // Verificar si el producto puede ser eliminado
        if (!$product->canBeDeleted()) {
            return back()->withErrors([
                'error' => 'No se puede eliminar este producto: ' . $product->getDeletionBlockReason()
            ]);
        }

        $productName = $product->name;
        
        // Realizar soft delete
        $product->update([
            'deleted_by' => auth()->id(),
            'deletion_reason' => $request->deletion_reason,
        ]);
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', "Producto '{$productName}' eliminado exitosamente");
    }

    /**
     * Mostrar papelera de productos eliminados
     */
    public function trash(Request $request)
    {
        $search = $request->get('search', '');
        $perPage = $request->get('per_page', 10);
        
        // Validar que per_page esté en los valores permitidos
        if (!in_array($perPage, [5, 10, 15, 20, 50])) {
            $perPage = 10;
        }

        $query = Product::onlyTrashed()
            ->with(['category', 'deletedBy']);

        // Aplicar filtro de búsqueda
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('price', 'LIKE', "%{$search}%")
                  ->orWhere('stock', 'LIKE', "%{$search}%")
                  ->orWhere('deletion_reason', 'LIKE', "%{$search}%")
                  ->orWhereHas('category', function ($categoryQuery) use ($search) {
                      $categoryQuery->where('name', 'LIKE', "%{$search}%");
                  })
                  ->orWhereHas('deletedBy', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        $deletedProducts = $query->orderBy('deleted_at', 'desc')->paginate($perPage);
        
        // Mantener parámetros de búsqueda en la paginación
        $deletedProducts->appends($request->query());
            
        return view('products.trash', compact('deletedProducts', 'search', 'perPage'));
    }

    /**
     * Restaurar producto eliminado
     */
    public function restore(Request $request, $id)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
        ]);

        $product = Product::onlyTrashed()->findOrFail($id);
        
        $productName = $product->name;
        $product->restore();

        return redirect()->route('products.trash')
            ->with('success', "Producto '{$productName}' restaurado exitosamente");
    }

    /**
     * Eliminar producto permanentemente
     */
    public function forceDelete(Request $request, $id)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
        ]);

        $product = Product::onlyTrashed()->findOrFail($id);
        
        // Verificar si el producto tiene ventas registradas
        if ($product->sales()->count() > 0) {
            return back()->withErrors([
                'error' => 'No se puede eliminar permanentemente un producto que tiene ventas registradas en el historial.'
            ]);
        }

        $productName = $product->name;
        $product->forceDelete();

        return redirect()->route('products.trash')
            ->with('success', "Producto '{$productName}' eliminado permanentemente");
    }
}