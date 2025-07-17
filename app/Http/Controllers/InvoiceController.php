<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class InvoiceController extends Controller
{
    /**
     * Mostrar listado de facturas
     */
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $perPage = $request->get('per_page', 15);
        
        // Validar que per_page esté en los valores permitidos
        if (!in_array($perPage, [5, 10, 15, 20, 50])) {
            $perPage = 15;
        }

        $query = Invoice::with(['user', 'customer', 'items']);

        // Aplicar filtro de búsqueda
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'LIKE', "%{$search}%")
                  ->orWhere('customer_name', 'LIKE', "%{$search}%")
                  ->orWhere('customer_email', 'LIKE', "%{$search}%")
                  ->orWhere('total', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'LIKE', "%{$search}%");
                  })
                  ->orWhereHas('customer', function ($customerQuery) use ($search) {
                      $customerQuery->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        $invoices = $query->orderBy('created_at', 'desc')->paginate($perPage);
        
        // Mantener parámetros de búsqueda en la paginación
        $invoices->appends($request->query());

        return view('invoices.index', compact('invoices', 'search', 'perPage'));
    }

    /**
     * Mostrar formulario para crear nueva factura
     */
    public function create()
    {
        $products = Product::where('stock', '>', 0)->get();
        $customers = User::role('cliente')->active()->get();
        
        return view('invoices.create', compact('products', 'customers'));
    }

    /**
     * Almacenar nueva factura
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_id' => 'nullable|exists:users,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.product_name' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.tax_rate' => 'required|in:0,15', // Solo permitir 0% o 15%
        ]);

        return DB::transaction(function () use ($request) {
            $subtotal = 0;
            $totalTax = 0;
            $items = [];

            // Validar stock y calcular totales
            foreach ($request->items as $itemData) {
                $product = Product::findOrFail($itemData['product_id']);
                
                if ($product->stock < $itemData['quantity']) {
                    throw ValidationException::withMessages([
                        'items' => "Stock insuficiente para {$product->name}. Stock disponible: {$product->stock}"
                    ]);
                }

                $quantity = $itemData['quantity'];
                $unitPrice = $product->price;
                $taxRate = $itemData['tax_rate'];
                
                $itemSubtotal = $quantity * $unitPrice;
                $itemTaxAmount = $itemSubtotal * ($taxRate / 100);
                $itemTotalWithTax = $itemSubtotal + $itemTaxAmount;
                
                $subtotal += $itemSubtotal;
                $totalTax += $itemTaxAmount;

                $items[] = [
                    'product_id' => $product->id,
                    'product_name' => $itemData['product_name'],
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'tax_rate' => $taxRate,
                    'tax_amount' => $itemTaxAmount,
                    'subtotal' => $itemSubtotal,
                    'total_price' => $itemTotalWithTax,
                ];
            }

            // Crear factura
            $invoice = Invoice::create([
                'user_id' => Auth::id(),
                'customer_id' => $request->customer_id,
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'subtotal' => $subtotal,
                'tax' => $totalTax,
                'total' => $subtotal + $totalTax,
            ]);

            // Crear items y actualizar stock
            foreach ($items as $itemData) {
                // Agregar invoice_id explícitamente
                $itemData['invoice_id'] = $invoice->id;
                
                // Crear item directamente
                InvoiceItem::create($itemData);
                
                // Reducir stock
                Product::find($itemData['product_id'])->decrement('stock', $itemData['quantity']);
            }

            return redirect()->route('invoices.show', $invoice)
                ->with('success', 'Factura creada exitosamente.');
        });
    }

    /**
     * Mostrar factura específica
     */
    public function show(Invoice $invoice)
    {
        $invoice->load(['user', 'customer', 'items.product']);
        
        return view('invoices.show', compact('invoice'));
    }

    /**
     * Mostrar formulario para editar factura
     */
    public function edit(Invoice $invoice)
    {
        // Solo se pueden editar facturas activas del mismo día
        if (!$invoice->isActive() || !$invoice->created_at->isToday()) {
            return redirect()->route('invoices.index')
                ->with('error', 'No se puede editar esta factura.');
        }

        $products = Product::all();
        $customers = User::role('cliente')->active()->get();
        
        return view('invoices.edit', compact('invoice', 'products', 'customers'));
    }

    /**
     * Actualizar factura
     */
    public function update(Request $request, Invoice $invoice)
    {
        if (!$invoice->isActive() || !$invoice->created_at->isToday()) {
            return redirect()->route('invoices.index')
                ->with('error', 'No se puede editar esta factura.');
        }

        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_id' => 'nullable|exists:users,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.product_name' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.tax_rate' => 'required|in:0,15', // Solo permitir 0% o 15%
        ]);

        return DB::transaction(function () use ($request, $invoice) {
            // Guardar items actuales para comparar
            $currentItems = $invoice->items->keyBy('id');
            $requestedItems = collect($request->items);
            
            // Reversar stock de items que serán eliminados o modificados
            foreach ($currentItems as $currentItem) {
                $requestedItem = $requestedItems->firstWhere('id', $currentItem->id);
                
                if (!$requestedItem) {
                    // Item eliminado, reversar todo el stock
                    $currentItem->product->increment('stock', $currentItem->quantity);
                } else {
                    // Item modificado, reversar stock original
                    $currentItem->product->increment('stock', $currentItem->quantity);
                }
            }

            // Eliminar todos los items anteriores
            $invoice->items()->delete();

            $subtotal = 0;
            $totalTax = 0;
            $newItems = [];

            // Validar stock y preparar nuevos items
            foreach ($requestedItems as $itemData) {
                $product = Product::findOrFail($itemData['product_id']);
                $quantity = (int) $itemData['quantity'];
                $taxRate = (float) $itemData['tax_rate'];
                
                if ($product->stock < $quantity) {
                    throw ValidationException::withMessages([
                        'items' => "Stock insuficiente para {$product->name}. Stock disponible: {$product->stock}"
                    ]);
                }

                $unitPrice = isset($itemData['price']) ? (float) $itemData['price'] : $product->price;
                $itemSubtotal = $unitPrice * $quantity;
                $itemTaxAmount = $itemSubtotal * ($taxRate / 100);
                $itemTotalWithTax = $itemSubtotal + $itemTaxAmount;
                
                $subtotal += $itemSubtotal;
                $totalTax += $itemTaxAmount;

                $newItems[] = [
                    'product_id' => $product->id,
                    'product_name' => $itemData['product_name'],
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'tax_rate' => $taxRate,
                    'tax_amount' => $itemTaxAmount,
                    'subtotal' => $itemSubtotal,
                    'total_price' => $itemTotalWithTax,
                ];
            }

            // Actualizar datos de la factura
            $invoice->update([
                'customer_id' => $request->customer_id,
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'subtotal' => $subtotal,
                'tax' => $totalTax,
                'total' => $subtotal + $totalTax,
            ]);

            // Crear nuevos items y actualizar stock
            foreach ($newItems as $itemData) {
                $invoice->items()->create($itemData);
                Product::find($itemData['product_id'])->decrement('stock', $itemData['quantity']);
            }

            return redirect()->route('invoices.show', $invoice)
                ->with('success', 'Factura actualizada exitosamente.');
        });
    }

    /**
     * Mostrar formulario de confirmación para eliminar factura
     */
    public function confirmDelete(Invoice $invoice)
    {
        if (!$invoice->canBeCancelledBy(Auth::user())) {
            return redirect()->route('invoices.index')
                ->with('error', 'No tienes permisos para cancelar esta factura.');
        }

        return view('invoices.confirm-delete', compact('invoice'));
    }

    /**
     * Cancelar factura
     */
    public function destroy(Request $request, Invoice $invoice)
    {
        if (!$invoice->canBeCancelledBy(Auth::user())) {
            return redirect()->route('invoices.index')
                ->with('error', 'No tienes permisos para cancelar esta factura.');
        }

        $request->validate([
            'password' => 'required',
            'cancellation_reason' => 'required|string|max:500',
        ]);

        // Verificar contraseña del usuario actual
        if (!Hash::check($request->password, Auth::user()->password)) {
            throw ValidationException::withMessages([
                'password' => 'La contraseña es incorrecta.',
            ]);
        }

        if ($invoice->cancel(Auth::user(), $request->cancellation_reason)) {
            return redirect()->route('invoices.index')
                ->with('success', 'Factura cancelada exitosamente. El stock ha sido restaurado.');
        }

        return redirect()->route('invoices.index')
            ->with('error', 'Error al cancelar la factura.');
    }
}
