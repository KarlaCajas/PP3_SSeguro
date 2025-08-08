<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

/**
 * Controlador API para gestión de pagos
 */
class PaymentController extends Controller
{
    /**
     * Registrar un nuevo pago (API REST - Sanctum)
     */
    public function store(Request $request): JsonResponse
    {
        // Validar que el usuario autenticado tenga rol de cliente
        if (!$request->user()->hasRole('cliente')) {
            return response()->json([
                'message' => 'No tienes permisos para realizar esta acción',
                'error' => 'Unauthorized'
            ], 403);
        }

        // Validar los datos del pago
        $validated = $request->validate([
            'invoice_id' => [
                'required',
                'integer',
                'exists:invoices,id'
            ],
            'tipo_pago' => [
                'required',
                Rule::in(['efectivo', 'tarjeta', 'transferencia', 'cheque'])
            ],
            'monto' => [
                'required',
                'numeric',
                'min:0.01',
                'max:999999.99'
            ],
            'numero_transaccion' => [
                'required',
                'string',
                'max:255'
            ],
            'observacion' => [
                'nullable',
                'string',
                'max:1000'
            ]
        ]);

        // Verificar que la factura pertenezca al cliente autenticado
        $invoice = Invoice::find($validated['invoice_id']);
        
        if ($invoice->customer_id !== $request->user()->id) {
            return response()->json([
                'message' => 'No tienes permisos para pagar esta factura',
                'error' => 'Forbidden'
            ], 403);
        }

        // Verificar que la factura no esté ya pagada o cancelada
        if ($invoice->status === 'paid') {
            return response()->json([
                'message' => 'Esta factura ya ha sido pagada',
                'error' => 'Invoice already paid'
            ], 400);
        }

        if ($invoice->status === 'cancelled') {
            return response()->json([
                'message' => 'No se puede pagar una factura cancelada',
                'error' => 'Invoice cancelled'
            ], 400);
        }

        // Verificar que el monto no exceda el total de la factura
        $totalPagado = $invoice->getTotalPagosAprobados();
        $montoRestante = $invoice->total - $totalPagado;

        if ($validated['monto'] > $montoRestante) {
            return response()->json([
                'message' => 'El monto del pago excede el saldo pendiente de la factura',
                'error' => 'Amount exceeds pending balance',
                'saldo_pendiente' => $montoRestante
            ], 400);
        }

        try {
            // Crear el pago
            $payment = Payment::create([
                'invoice_id' => $validated['invoice_id'],
                'tipo_pago' => $validated['tipo_pago'],
                'monto' => $validated['monto'],
                'numero_transaccion' => $validated['numero_transaccion'],
                'observacion' => $validated['observacion'],
                'estado' => 'pendiente',
                'pagado_por' => $request->user()->id,
            ]);

            return response()->json([
                'message' => 'Pago registrado exitosamente. Está pendiente de validación.',
                'data' => [
                    'payment_id' => $payment->id,
                    'invoice_id' => $payment->invoice_id,
                    'monto' => $payment->monto,
                    'tipo_pago' => $payment->getTipoPagoNombre(),
                    'numero_transaccion' => $payment->numero_transaccion,
                    'estado' => $payment->getEstadoNombre(),
                    'created_at' => $payment->created_at
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al registrar el pago',
                'error' => 'Internal server error'
            ], 500);
        }
    }

    /**
     * Obtener las facturas pendientes de pago del cliente autenticado
     */
    public function getFacturasPendientes(Request $request): JsonResponse
    {
        // Validar que el usuario autenticado tenga rol de cliente
        if (!$request->user()->hasRole('cliente')) {
            return response()->json([
                'message' => 'No tienes permisos para realizar esta acción',
                'error' => 'Unauthorized'
            ], 403);
        }

        try {
            $facturas = Invoice::where('customer_id', $request->user()->id)
                ->whereIn('status', ['active', 'pending'])
                ->with(['payments' => function($query) {
                    $query->where('estado', 'aprobado');
                }])
                ->get()
                ->map(function($invoice) {
                    $totalPagado = $invoice->getTotalPagosAprobados();
                    $saldoPendiente = $invoice->total - $totalPagado;
                    
                    return [
                        'id' => $invoice->id,
                        'invoice_number' => $invoice->invoice_number,
                        'total' => $invoice->total,
                        'total_pagado' => $totalPagado,
                        'saldo_pendiente' => $saldoPendiente,
                        'status' => $invoice->status,
                        'created_at' => $invoice->created_at,
                        'tiene_pagos_pendientes' => $invoice->hasPagosPendientes()
                    ];
                })
                ->filter(function($invoice) {
                    return $invoice['saldo_pendiente'] > 0;
                });

            return response()->json([
                'message' => 'Facturas pendientes obtenidas exitosamente',
                'data' => $facturas->values()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener las facturas pendientes',
                'error' => 'Internal server error'
            ], 500);
        }
    }

    /**
     * Obtener el historial de pagos del cliente autenticado
     */
    public function getHistorialPagos(Request $request): JsonResponse
    {
        // Validar que el usuario autenticado tenga rol de cliente
        if (!$request->user()->hasRole('cliente')) {
            return response()->json([
                'message' => 'No tienes permisos para realizar esta acción',
                'error' => 'Unauthorized'
            ], 403);
        }

        try {
            $pagos = Payment::where('pagado_por', $request->user()->id)
                ->with(['invoice:id,invoice_number,total', 'validadoPor:id,name'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($payment) {
                    return [
                        'id' => $payment->id,
                        'invoice_number' => $payment->invoice->invoice_number,
                        'monto' => $payment->monto,
                        'tipo_pago' => $payment->getTipoPagoNombre(),
                        'numero_transaccion' => $payment->numero_transaccion,
                        'estado' => $payment->getEstadoNombre(),
                        'observacion' => $payment->observacion,
                        'created_at' => $payment->created_at,
                        'validated_at' => $payment->validated_at,
                        'validado_por' => $payment->validadoPor?->name
                    ];
                });

            return response()->json([
                'message' => 'Historial de pagos obtenido exitosamente',
                'data' => $pagos
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener el historial de pagos',
                'error' => 'Internal server error'
            ], 500);
        }
    }
}
