<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Invoice;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\InvoiceController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/facturas', function (Request $request) {
    $user = $request->user();
    $facturas = Invoice::where('customer_id', $user->id)
        ->with(['customer', 'items.product'])
        ->orderBy('created_at', 'asc')
        ->get();
    return response()->json($facturas);
})->middleware('auth:sanctum');

// Rutas para pagos (API REST con Sanctum - hasheado)
Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    // === PAGOS ===
    Route::get('/facturas-pendientes', [PaymentController::class, 'getFacturasPendientes']);
    Route::post('/pagos', [PaymentController::class, 'store']);
    Route::get('/mis-pagos', [PaymentController::class, 'getHistorialPagos']);
    
    // === CLIENTES (Solo admin y ventas) ===
    Route::middleware('role:admin,ventas')->group(function () {
        Route::apiResource('clientes', ClientController::class);
    });
    
    // === PRODUCTOS (Solo admin y ventas) ===
    Route::middleware('role:admin,ventas')->group(function () {
        Route::apiResource('productos', ProductController::class);
        Route::get('/categorias', [ProductController::class, 'categories']);
    });
    
    // === FACTURAS (Solo admin y ventas) ===
    Route::middleware('role:admin,ventas')->group(function () {
        Route::apiResource('facturas', InvoiceController::class);
    });
});

// Rutas para pagos (API REST con tokens en texto plano - INSEGURO)
Route::middleware('auth.plaintext')->prefix('v2')->group(function () {
    // === PAGOS (Todos los usuarios autenticados) ===
    Route::get('/facturas-pendientes', [PaymentController::class, 'getFacturasPendientes']);
    Route::post('/pagos', [PaymentController::class, 'store']);
    Route::get('/mis-pagos', [PaymentController::class, 'getHistorialPagos']);
    
    // === CLIENTES (Solo admin y ventas) ===
    Route::middleware('role:admin,ventas')->group(function () {
        Route::apiResource('clientes', ClientController::class);
    });
    
    // === PRODUCTOS (Solo admin y ventas) ===
    Route::middleware('role:admin,ventas')->group(function () {
        Route::apiResource('productos', ProductController::class);
        Route::get('/categorias', [ProductController::class, 'categories']);
    });
    
    // === FACTURAS (Solo admin y ventas) ===
    Route::middleware('role:admin,ventas')->group(function () {
        Route::apiResource('facturas', InvoiceController::class);
    });
    
    // === INFORMACIÓN DE USUARIO (Todos los autenticados) ===
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    // === FACTURAS DEL CLIENTE (Solo clientes ven sus propias facturas) ===
    Route::middleware('role:cliente')->group(function () {
        Route::get('/mis-facturas', function (Request $request) {
            $user = $request->user();
            $facturas = Invoice::where('customer_id', $user->id)
                ->with(['customer', 'items.product'])
                ->orderBy('created_at', 'desc')
                ->get();
            return response()->json([
                'success' => true,
                'message' => 'Facturas obtenidas exitosamente',
                'data' => $facturas
            ]);
        });
    });
});
