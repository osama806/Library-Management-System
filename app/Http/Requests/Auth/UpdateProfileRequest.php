<?php

namespace App\Http\Requests\Auth;

use App\Traits\ResponseTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class UpdateProfileRequest extends FormRequest
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
            'name'      => 'required|min:3|max:50',
            'password'  => 'required|confirmed|min:8',
            'as_admin'  => 'nullable|in:yes,no'
        ];
    }

    /**
     * Get message that errors explanation.
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
            "name"      =>      "full name",
            "password"  =>      "password",
            "as_admin"  =>      "admin"
        ];
    }


    /**
     * Get custom messages for validator errors.
     * @return string[]
     */
    public function messages()
    {
        return [
            'name.required'             =>      'Name is required',
            'password.required'         =>      'Password is required.',
            'password.min'              =>      'Password must be at least 8 characters long.',
            'password.confirmed'        =>      'Passwords do not match.',
            'as_admin.in'               =>      'Admin just acceptance two value (yes OR no)'

        ];
    }
}
