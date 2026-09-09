<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCourierRequest extends FormRequest
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
        $courier = $this->route('courier');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'phone_number' => [
                'required',
                'string',
                'regex:/^\+?[0-9]{10,15}$/',
                Rule::unique('couriers', 'phone_number')
                    ->ignore($courier),
            ],

            'email' => [
                'nullable',
                'email',
                Rule::unique('couriers', 'email')
                    ->ignore($courier),
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
