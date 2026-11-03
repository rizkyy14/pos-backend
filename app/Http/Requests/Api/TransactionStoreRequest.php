<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class TransactionStoreRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'items'               => ['required', 'array', 'min:1'],
            'items.*.product_id'  => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity'    => ['required', 'integer', 'min:1'],
            'paid'                => ['required', 'numeric', 'min:0'],
            'payment_method'      => ['nullable', 'string', 'in:cash,qris,debit,transfer'],
            'customer_name'       => ['nullable', 'string', 'max:255'],
            'note'                => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'items.required'            => 'Minimal satu item harus dibeli.',
            'items.min'                 => 'Minimal satu item harus dibeli.',
            'items.*.product_id.exists' => 'Salah satu produk tidak ditemukan.',
            'items.*.quantity.min'      => 'Jumlah item minimal 1.',
            'paid.required'             => 'Jumlah bayar wajib diisi.',
        ];
    }
}
