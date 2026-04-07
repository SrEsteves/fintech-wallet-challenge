<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class TransferRequest extends FormRequest
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
            'email' => 'required|email|exists:users,email',
            'amount' => 'required|numeric|min:0.01',
        ];
    }

    public function messages(): array
    {
        return[
            'email.exists' => 'O usuário destinatário não foi encontrado.',
            'amount.min' => 'O valor da transferência deve ser maior que zero.',
        ];
    }
}
