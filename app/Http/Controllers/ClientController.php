<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Controlador para la interfaz de clientes
 */
class ClientController extends Controller
{
    /**
     * Dashboard del cliente
     */
    public function dashboard(): View
    {
        // Verificar que el usuario tenga rol de cliente
        if (!auth()->user()->hasRole('cliente')) {
            abort(403, 'No tienes permisos para acceder a esta sección');
        }

        $user = auth()->user();

        // Obtener estadísticas del cliente
        $totalFacturas = Invoice::where('customer_id', $user->id)->count();
        
        $facturasPendientes = Invoice::where('customer_id', $user->id)
            ->whereIn('status', ['active', 'pending'])
            ->count();
            
        $facturasPagadas = Invoice::where('customer_id', $user->id)
            ->where('status', 'paid')
            ->count();

        $totalDeuda = Invoice::where('customer_id', $user->id)
            ->whereIn('status', ['active', 'pending'])
            ->sum('total');

        $pagosPendientes = Payment::where('pagado_por', $user->id)
            ->where('estado', 'pendiente')
            ->count();

        // Obtener tokens del usuario
        $tokens = $user->tokens;

        return view('client.dashboard', compact(
            'totalFacturas',
            'facturasPendientes', 
            'facturasPagadas',
            'totalDeuda',
            'pagosPendientes',
            'tokens'
        ));
    }

    /**
     * Mostrar las facturas del cliente
     */
    public function facturas(Request $request): View
    {
        // Verificar que el usuario tenga rol de cliente
        if (!auth()->user()->hasRole('cliente')) {
            abort(403, 'No tienes permisos para acceder a esta sección');
        }

        $user = auth()->user();
        $status = $request->get('status', 'todas');

        $query = Invoice::where('customer_id', $user->id)
            ->with(['payments' => function($q) {
                $q->where('estado', 'aprobado');
            }]);

        // Filtrar por estado si se especifica
        if ($status !== 'todas') {
            $query->where('status', $status);
        }

        $facturas = $query->orderBy('created_at', 'desc')->paginate(10);

        // Calcular información adicional para cada factura
        $facturas->getCollection()->transform(function ($factura) {
            $totalPagado = $factura->getTotalPagosAprobados();
            $factura->saldo_pendiente = $factura->total - $totalPagado;
            $factura->tiene_pagos_pendientes = $factura->hasPagosPendientes();
            return $factura;
        });

        return view('client.facturas', compact('facturas', 'status'));
    }

    /**
     * Mostrar detalles de una factura específica
     */
    public function mostrarFactura(Invoice $invoice): View
    {
        // Verificar que el usuario tenga rol de cliente
        if (!auth()->user()->hasRole('cliente')) {
            abort(403, 'No tienes permisos para acceder a esta sección');
        }

        // Verificar que la factura pertenezca al cliente
        if ($invoice->customer_id !== auth()->user()->id) {
            abort(403, 'No tienes permisos para ver esta factura');
        }

        $invoice->load(['items.product', 'payments.validadoPor']);

        // Calcular información de pagos
        $totalPagado = $invoice->getTotalPagosAprobados();
        $saldoPendiente = $invoice->total - $totalPagado;

        return view('client.factura-detalle', compact('invoice', 'totalPagado', 'saldoPendiente'));
    }

    /**
     * Mostrar los pagos del cliente
     */
    public function pagos(Request $request): View
    {
        // Verificar que el usuario tenga rol de cliente
        if (!auth()->user()->hasRole('cliente')) {
            abort(403, 'No tienes permisos para acceder a esta sección');
        }

        $user = auth()->user();
        $estado = $request->get('estado', 'todos');

        $query = Payment::where('pagado_por', $user->id)
            ->with(['invoice', 'validadoPor']);

        // Filtrar por estado si se especifica
        if ($estado !== 'todos') {
            $query->where('estado', $estado);
        }

        $pagos = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('client.pagos', compact('pagos', 'estado'));
    }

    public function regenerarToken()
    {
        // Eliminar todos los tokens existentes del usuario actual
        auth()->user()->tokens()->delete();
        
        // Crear un nuevo token
        $newToken = auth()->user()->createToken('API Token Cliente')->plainTextToken;
        
        return redirect()->back()->with('nuevo_token', $newToken);
    }

    public function tokens()
    {
        $tokens = auth()->user()->tokens()->orderBy('created_at', 'desc')->get();
        
        return view('clientes.tokens', compact('tokens'));
    }

    /**
     * Mostrar detalles de un pago específico
     */
    public function mostrarPago(Payment $payment): View
    {
        // Verificar que el usuario tenga rol de cliente
        if (!auth()->user()->hasRole('cliente')) {
            abort(403, 'No tienes permisos para acceder a esta sección');
        }

        // Verificar que el pago pertenezca al cliente
        if ($payment->pagado_por !== auth()->user()->id) {
            abort(403, 'No tienes permisos para ver este pago');
        }

        $payment->load(['invoice.items.product', 'validadoPor']);

        return view('client.pago-detalle', compact('payment'));
    }

    /**
     * Mostrar facturas pendientes de pago
     */
    public function facturasPendientesPago(): View
    {
        // Verificar que el usuario tenga rol de cliente
        if (!auth()->user()->hasRole('cliente')) {
            abort(403, 'No tienes permisos para acceder a esta sección');
        }

        $user = auth()->user();

        $facturas = Invoice::where('customer_id', $user->id)
            ->whereIn('status', ['active', 'pending'])
            ->with(['payments' => function($query) {
                $query->where('estado', 'aprobado');
            }])
            ->get()
            ->map(function($factura) {
                $totalPagado = $factura->getTotalPagosAprobados();
                $saldoPendiente = $factura->total - $totalPagado;
                
                $factura->total_pagado = $totalPagado;
                $factura->saldo_pendiente = $saldoPendiente;
                $factura->tiene_pagos_pendientes = $factura->hasPagosPendientes();
                
                return $factura;
            })
            ->filter(function($factura) {
                return $factura->saldo_pendiente > 0;
            });

        // Obtener tokens del usuario para mostrar en la API
        $tokens = $user->tokens;

        return view('client.facturas-pendientes', compact('facturas', 'tokens'));
    }
}
