<?php

namespace App\Http\Requests\Auth;

use App\Traits\ResponseTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;

class RegisterRequest extends FormRequest
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
     * Prepare the data for validation.
     * @return void
     */
    public function prepareForValidation()
    {
        $this->merge([
            'is_admin' => $this->boolean('is_admin', false) // Default to false if not provided
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     * @return string[]
     */
    public function rules()
    {
        return [
            'name'      => 'required|string|min:3|max:50',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|confirmed|min:8',
            'is_admin'  => 'nullable|boolean'
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
     * Modify the request data after validation passes.
     * @return void
     */
    public function passedValidation()
    {
        $this->merge([
            'password' => Hash::make($this->input('password'))
        ]);
    }

    /**
     * Get custom attributes for validator errors.
     * @return string[]
     */
    public function attributes()
    {
        return [
            "name"      => "Full name",
            "email"     => "Email address",
            "password"  => "Password",
            "is_admin"  => "Admin status"
        ];
    }

    /**
     * Get custom messages for validator errors.
     * @return string[]
     */
    public function messages()
    {
        return [
            'required'   => ':attribute is required.',
            'email'      => 'Please enter a valid :attribute.',
            'unique'     => 'This :attribute is already registered.',
            'min'        => ':attribute must be at least :min characters long.',
            'confirmed'  => ':attribute does not match.',
            'boolean'    => ':attribute must be true or false'
        ];
    }
}
