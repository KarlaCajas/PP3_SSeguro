<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Controlador para la gestión de pagos desde la interfaz web
 */
class PaymentController extends Controller
{
    /**
     * Mostrar listado de pagos pendientes
     */
    public function index(): View
    {
        // Verificar que el usuario tenga rol de pagos o admin
        if (!auth()->user()->hasAnyRole(['pagos', 'admin'])) {
            abort(403, 'No tienes permisos para acceder a esta sección');
        }

        $pagosPendientes = Payment::with(['invoice', 'pagadoPor'])
            ->where('estado', 'pendiente')
            ->orderBy('created_at', 'asc')
            ->get();

        return view('payments.index', compact('pagosPendientes'));
    }

    /**
     * Mostrar detalles de un pago específico
     */
    public function show(Payment $payment): View
    {
        // Verificar que el usuario tenga rol de pagos o admin
        if (!auth()->user()->hasAnyRole(['pagos', 'admin'])) {
            abort(403, 'No tienes permisos para acceder a esta sección');
        }

        $payment->load(['invoice.items.product', 'pagadoPor', 'validadoPor']);

        return view('payments.show', compact('payment'));
    }

    /**
     * Aprobar un pago
     */
    public function aprobar(Request $request, Payment $payment): RedirectResponse
    {
        // Verificar que el usuario tenga rol de pagos o admin
        if (!auth()->user()->hasAnyRole(['pagos', 'admin'])) {
            abort(403, 'No tienes permisos para realizar esta acción');
        }

        // Verificar que el pago esté pendiente
        if (!$payment->isPendiente()) {
            return redirect()->back()
                ->with('error', 'Solo se pueden aprobar pagos pendientes');
        }

        try {
            // Aprobar el pago
            $payment->aprobar(auth()->user());

            return redirect()->route('payments.index')
                ->with('success', "Pago de {$payment->getTipoPagoNombre()} por $" . number_format($payment->monto, 2) . " aprobado exitosamente");

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al aprobar el pago: ' . $e->getMessage());
        }
    }

    /**
     * Rechazar un pago
     */
    public function rechazar(Request $request, Payment $payment): RedirectResponse
    {
        // Verificar que el usuario tenga rol de pagos o admin
        if (!auth()->user()->hasAnyRole(['pagos', 'admin'])) {
            abort(403, 'No tienes permisos para realizar esta acción');
        }

        // Verificar que el pago esté pendiente
        if (!$payment->isPendiente()) {
            return redirect()->back()
                ->with('error', 'Solo se pueden rechazar pagos pendientes');
        }

        try {
            // Rechazar el pago
            $payment->rechazar(auth()->user());

            return redirect()->route('payments.index')
                ->with('success', "Pago de {$payment->getTipoPagoNombre()} por $" . number_format($payment->monto, 2) . " rechazado");

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al rechazar el pago: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar historial de pagos procesados
     */
    public function historial(Request $request): View
    {
        // Verificar que el usuario tenga rol de pagos o admin
        if (!auth()->user()->hasAnyRole(['pagos', 'admin'])) {
            abort(403, 'No tienes permisos para acceder a esta sección');
        }

        $estado = $request->get('estado', 'todos');
        
        $query = Payment::with(['invoice', 'pagadoPor', 'validadoPor'])
            ->whereIn('estado', ['aprobado', 'rechazado']);

        if ($estado !== 'todos') {
            $query->where('estado', $estado);
        }

        $pagos = $query->orderBy('validated_at', 'desc')->paginate(15);

        return view('payments.historial', compact('pagos', 'estado'));
    }
}
