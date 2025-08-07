<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Invoice;

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
