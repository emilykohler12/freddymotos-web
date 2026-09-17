<?php

namespace App\Http\Requests;

use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:40'],
            'email' => ['nullable', 'email', 'max:160'],
            'address' => ['nullable', 'string', 'max:255', 'required_if:delivery_method,' . Order::DELIVERY_ENVIO],
            'delivery_method' => ['required', Rule::in([Order::DELIVERY_RETIRO, Order::DELIVERY_ENVIO])],
            'payment_method' => ['required', Rule::in([Order::PAYMENT_MERCADOPAGO, Order::PAYMENT_WHATSAPP])],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nombre',
            'phone' => 'teléfono',
            'address' => 'dirección',
            'delivery_method' => 'método de entrega',
            'payment_method' => 'método de pago',
            'notes' => 'aclaraciones',
        ];
    }
}
