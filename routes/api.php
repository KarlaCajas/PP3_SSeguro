<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Invoice;
use App\Http\Controllers\Api\PaymentController;

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
    // Obtener facturas pendientes de pago del cliente
    Route::get('/facturas-pendientes', [PaymentController::class, 'getFacturasPendientes']);
    
    // Registrar un nuevo pago
    Route::post('/pagos', [PaymentController::class, 'store']);
    
    // Obtener historial de pagos del cliente
    Route::get('/mis-pagos', [PaymentController::class, 'getHistorialPagos']);
});

// Rutas para pagos (API REST con tokens en texto plano - INSEGURO)
Route::middleware('auth.plaintext')->prefix('v2')->group(function () {
    // Obtener facturas pendientes de pago del cliente
    Route::get('/facturas-pendientes', [PaymentController::class, 'getFacturasPendientes']);
    
    // Registrar un nuevo pago
    Route::post('/pagos', [PaymentController::class, 'store']);
    
    // Obtener historial de pagos del cliente
    Route::get('/mis-pagos', [PaymentController::class, 'getHistorialPagos']);
    
    // Ruta para obtener información del usuario
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    // Ruta para obtener facturas
    Route::get('/facturas', function (Request $request) {
        $user = $request->user();
        $facturas = Invoice::where('customer_id', $user->id)
            ->with(['customer', 'items.product'])
            ->orderBy('created_at', 'asc')
            ->get();
        return response()->json($facturas);
    });
});
