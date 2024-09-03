<?php

namespace App\Http\Requests\Books;

use App\Traits\ResponseTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class BookFormRequest extends FormRequest
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
            "title"             =>      "required|string|unique:books,title|min:2|max:100",
            "author"            =>      "required|string|min:3|max:100",
            "description"       =>      "required",
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
            "title"         =>      "book title",
            "author"        =>      "author name",
            "description"   =>      "book description",
        ];
    }

    /**
     * Get custom messages for validator errors.
     * @return string[]
     */
    public function messages()
    {
        return [
            "title.required"                =>      "Book title is required",
            "title.unique"                  =>      "Book title must be unique",
            "title.min"                     =>      "Book title must be at minimum 2 characters",
            "title.max"                     =>      "Book title must be at maximum 100 characters",
            "author.required"               =>      "Author name is required",
            "author.min"                    =>      "Author name must be at minimum 3 characters",
            "author.max"                    =>      "Author name must be at maximum 100 characters",
            "description.required"          =>      "Book description is required",
        ];
    }
}
