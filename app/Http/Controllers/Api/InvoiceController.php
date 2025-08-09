<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreInvoiceRequest;
use App\Http\Requests\Api\UpdateInvoiceRequest;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Invoice::with(['user', 'customer', 'items.product']);

            // Filtros opcionales
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            if ($request->has('customer_id')) {
                $query->where('customer_id', $request->customer_id);
            }

            if ($request->has('user_id')) {
                $query->where('user_id', $request->user_id);
            }

            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('invoice_number', 'like', "%{$search}%")
                      ->orWhere('customer_name', 'like', "%{$search}%")
                      ->orWhere('customer_email', 'like', "%{$search}%");
                });
            }

            if ($request->has('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->has('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            // Ordenar por fecha más reciente
            $query->orderBy('created_at', 'desc');

            // Paginación
            $perPage = min($request->get('per_page', 15), 100);
            $invoices = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Facturas obtenidas exitosamente',
                'data' => $invoices
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener facturas',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInvoiceRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $validated = $request->validated();
            $user = $request->user();

            // Crear la factura
            $invoice = Invoice::create([
                'user_id' => $user->id,
                'customer_id' => $validated['customer_id'] ?? null,
                'customer_name' => $validated['customer_name'],
                'customer_email' => $validated['customer_email'] ?? null,
                'subtotal' => 0,
                'tax' => 0,
                'total' => 0,
                'status' => 'active'
            ]);

            $subtotal = 0;
            $totalTax = 0;

            // Agregar items a la factura
            foreach ($validated['items'] as $itemData) {
                $product = Product::findOrFail($itemData['product_id']);
                
                // Verificar stock
                if ($product->stock < $itemData['quantity']) {
                    throw new \Exception("Stock insuficiente para el producto: {$product->name}. Stock disponible: {$product->stock}");
                }

                $unitPrice = $itemData['unit_price'] ?? $product->price;
                $taxRate = $itemData['tax_rate'] ?? $product->tax_rate ?? 0;
                $itemSubtotal = $unitPrice * $itemData['quantity'];
                $itemTax = $itemSubtotal * ($taxRate / 100);
                $itemTotal = $itemSubtotal + $itemTax;

                // Crear item de factura
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $product->id,
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $unitPrice,
                    'tax_rate' => $taxRate,
                    'total_price' => $itemTotal
                ]);

                // Actualizar stock del producto
                $product->decrement('stock', $itemData['quantity']);

                $subtotal += $itemSubtotal;
                $totalTax += $itemTax;
            }

            // Actualizar totales de la factura
            $invoice->update([
                'subtotal' => $subtotal,
                'tax' => $totalTax,
                'total' => $subtotal + $totalTax
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Factura creada exitosamente',
                'data' => $invoice->load(['user', 'customer', 'items.product'])
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al crear factura',
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
            $invoice = Invoice::with(['user', 'customer', 'items.product', 'payments'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Factura obtenida exitosamente',
                'data' => $invoice
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Factura no encontrada'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener factura',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateInvoiceRequest $request, string $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $invoice = Invoice::findOrFail($id);
            
            // Solo se pueden actualizar facturas activas
            if ($invoice->status !== 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'Solo se pueden actualizar facturas con estado activo'
                ], 422);
            }

            $validated = $request->validated();

            // Restaurar stock de productos antiguos
            foreach ($invoice->items as $oldItem) {
                $oldItem->product->increment('stock', $oldItem->quantity);
            }

            // Eliminar items antiguos
            $invoice->items()->delete();

            $subtotal = 0;
            $totalTax = 0;

            // Agregar nuevos items
            foreach ($validated['items'] as $itemData) {
                $product = Product::findOrFail($itemData['product_id']);
                
                // Verificar stock
                if ($product->stock < $itemData['quantity']) {
                    throw new \Exception("Stock insuficiente para el producto: {$product->name}. Stock disponible: {$product->stock}");
                }

                $unitPrice = $itemData['unit_price'] ?? $product->price;
                $taxRate = $itemData['tax_rate'] ?? $product->tax_rate ?? 0;
                $itemSubtotal = $unitPrice * $itemData['quantity'];
                $itemTax = $itemSubtotal * ($taxRate / 100);
                $itemTotal = $itemSubtotal + $itemTax;

                // Crear item de factura
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $product->id,
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $unitPrice,
                    'tax_rate' => $taxRate,
                    'total_price' => $itemTotal
                ]);

                // Actualizar stock del producto
                $product->decrement('stock', $itemData['quantity']);

                $subtotal += $itemSubtotal;
                $totalTax += $itemTax;
            }

            // Actualizar la factura
            $invoice->update([
                'customer_id' => $validated['customer_id'] ?? $invoice->customer_id,
                'customer_name' => $validated['customer_name'],
                'customer_email' => $validated['customer_email'] ?? $invoice->customer_email,
                'subtotal' => $subtotal,
                'tax' => $totalTax,
                'total' => $subtotal + $totalTax
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Factura actualizada exitosamente',
                'data' => $invoice->fresh()->load(['user', 'customer', 'items.product'])
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Factura no encontrada'
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar factura',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $invoice = Invoice::findOrFail($id);

            // Solo se pueden cancelar facturas activas o pendientes
            if (!in_array($invoice->status, ['active', 'pending'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Solo se pueden cancelar facturas activas o pendientes'
                ], 422);
            }

            // Verificar si tiene pagos asociados
            if ($invoice->payments()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede cancelar una factura con pagos asociados'
                ], 422);
            }

            // Restaurar stock de los productos
            foreach ($invoice->items as $item) {
                $item->product->increment('stock', $item->quantity);
            }

            // Marcar como cancelada
            $invoice->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancelled_by' => request()->user()->id,
                'cancellation_reason' => 'Cancelada via API'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Factura cancelada exitosamente'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Factura no encontrada'
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al cancelar factura',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
