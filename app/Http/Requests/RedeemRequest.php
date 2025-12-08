<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class RedeemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'hash' => [
                'required',
                'string',
                'size:6',
                'regex:/^[a-zA-Z0-9]+$/'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'hash.required' => 'The invitation hash is required.',
            'hash.string'   => 'The invitation hash must be a string.',
            'hash.size'     => 'The invitation hash must be exactly 6 characters long.',
            'hash.regex'    => 'Invalid hash format. Only alphanumeric characters are allowed.',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors'  => $validator->errors(),
        ], 422));
    }
}
