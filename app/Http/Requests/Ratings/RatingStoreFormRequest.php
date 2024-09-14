<?php

namespace App\Http\Requests\Ratings;

use App\Traits\ResponseTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class RatingStoreFormRequest extends FormRequest
{
    use ResponseTrait;

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
            "book_id"             =>      "required|numeric|min:1",
            "rating"              =>      "required|numeric|min:1|max:5",
            "review"              =>      "nullable|string|max:256"
        ];
    }

    /**
     * Get message that errors explanation
     * @param \Illuminate\Contracts\Validation\Validator $validator
     * @throws \Illuminate\Validation\ValidationException
     * @return never
     */
    public function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, $this->getResponse("errors", $validator->errors(), 422));
    }

    /**
     * Get custom attributes for validator errors.
     * @return string[]
     */
    public function attributes()
    {
        return [
            "book_id"         =>      "book number",
            "rating"          =>      "book rating",
            "review"          =>      "book review"
        ];
    }

    /**
     * Get custom messages for validator errors.
     * @return string[]
     */
    public function messages()
    {
        return [
            'required'  => ':attribute is required.',
            'numeric'   => ':attribute must be a numeric value.',
            'min'       => ':attribute must be at least :min .',
            'max'       => ':attribute must not be greater than :max .',
        ];
    }
}
