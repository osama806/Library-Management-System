<?php

namespace App\Http\Requests\Books;

use App\Traits\ResponseTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class UpdateBookRequest extends FormRequest
{
    use ResponseTrait;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->is_admin == true;
    }

    /**
     * Get response for user hasn't permission
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     * @return never
     */
    public function failedAuthorization()
    {
        throw new HttpResponseException($this->getResponse('error', "Can't access to this permission", 400));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "title"             =>      "nullable|string|unique:books,title|min:2|max:100",
            "author"            =>      "nullable|string|min:3|max:100",
            "description"       =>      "nullable|string|min:2|max:256",
            "category_id"       =>      "nullable|numeric|min:1|exists:categories,id"
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
            "title"         =>      "Book title",
            "author"        =>      "Author name",
            "description"   =>      "Book description",
            "category_id"   =>      "Category number"
        ];
    }

    /**
     * Get custom messages for validator errors.
     * @return string[]
     */
    public function messages()
    {
        return [
            "unique"                  =>      ":attribute must be unique",
            "min"                     =>      ":attribute must be at minimum :min characters",
            "max"                     =>      ":attribute must be at maximum :max characters",
            'numeric'                 =>      ':attribute must be a valid number.',
            'exists'                  =>      'Selected :attribute does not exist.',
        ];
    }
}
