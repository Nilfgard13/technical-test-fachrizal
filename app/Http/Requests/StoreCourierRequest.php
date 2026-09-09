<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCourierRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],

            'phone_number' => [
                'required',
                'string',
                'unique:couriers,phone_number',
                'regex:/^\+?[0-9]{10,15}$/',
            ],

            'email' => [
                'nullable',
                'email',
                'unique:couriers,email',
            ],

            'address' => [
                'nullable',
                'string',
            ],

            'level' => [
                'required',
                'integer',
                'between:1,5',
            ],

            'status' => [
                'nullable',
                'in:active,inactive',
            ],

            'joined_at' => [
                'required',
                'date',
            ],
        ];
    }
}
