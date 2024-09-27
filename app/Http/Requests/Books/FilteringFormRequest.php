<?php

namespace App\Http\Requests\Books;

use App\Traits\ResponseTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class FilteringFormRequest extends FormRequest
{
    use ResponseTrait;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
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
            'author'            =>    'nullable|string|min:3',
            'available_books'   =>    'nullable|string|in:checked',
            'category'          =>    'nullable|string|min:2|exists:categories,name'
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param Validator $validator
     * @throws ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException(
            $validator,
            $this->getResponse('errors', $validator->errors(), 400)
        );
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'author'          =>    'Author name',
            'available_books' =>    'Availability status',
            'category'        =>    'Category name'
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'min'             =>     ':attribute must be at least :min characters long.',
            'in'              =>     ':attribute must be just "checked".',
            'string'          =>     ':attribute must be a string.',
            'exists'          =>     'Selected :attribute does not exist.',
        ];
    }
}
