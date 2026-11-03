<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Http\FormRequest;

class ProductUpdateRequest extends FormRequest
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
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:100', Rule::unique('products', 'sku')->ignore($this->route('product'))],
            'description' => ['nullable', 'string'],
            'price' => ['sometimes','required', 'numeric', 'min:0'],
            'stock' => ['sometimes','required', 'integer', 'min:0'],
            'category' => ['nullable', 'string', 'max:100'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'sku.unique' => 'SKU sudah digunakan oleh produk lain.',
            'price.numeric' => 'Harga produk harus berupa angka.',
            'stock.integer' => 'Stok produk harus berupa bilangan bulat.',  
        ];
    }
}
