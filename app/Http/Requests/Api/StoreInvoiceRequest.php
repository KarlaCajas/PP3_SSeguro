<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class StoreInvoiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'customer_id' => 'nullable|exists:users,id',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'nullable|numeric|min:0',
            'items.*.tax_rate' => 'nullable|numeric|min:0|max:100'
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'customer_name.required' => 'El nombre del cliente es obligatorio',
            'customer_name.max' => 'El nombre del cliente no puede exceder 255 caracteres',
            'customer_email.email' => 'El formato del email no es válido',
            'customer_id.exists' => 'El cliente seleccionado no existe',
            'items.required' => 'Debe incluir al menos un item en la factura',
            'items.array' => 'Los items deben ser un array',
            'items.min' => 'Debe incluir al menos un item en la factura',
            'items.*.product_id.required' => 'El ID del producto es obligatorio',
            'items.*.product_id.exists' => 'El producto seleccionado no existe',
            'items.*.quantity.required' => 'La cantidad es obligatoria',
            'items.*.quantity.integer' => 'La cantidad debe ser un número entero',
            'items.*.quantity.min' => 'La cantidad debe ser al menos 1',
            'items.*.unit_price.numeric' => 'El precio unitario debe ser un número',
            'items.*.unit_price.min' => 'El precio unitario no puede ser negativo',
            'items.*.tax_rate.numeric' => 'La tasa de IVA debe ser un número',
            'items.*.tax_rate.min' => 'La tasa de IVA no puede ser negativa',
            'items.*.tax_rate.max' => 'La tasa de IVA no puede exceder 100%'
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->has('customer_id') && $this->customer_id) {
                // Verificar que el customer_id sea un cliente válido
                $user = \App\Models\User::find($this->customer_id);
                if ($user && !$user->hasRole('cliente')) {
                    $validator->errors()->add('customer_id', 'El usuario seleccionado no es un cliente.');
                }
            }
        });
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], 422)
        );
    }
}
