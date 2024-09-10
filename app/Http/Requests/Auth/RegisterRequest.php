<?php

namespace App\Http\Requests\Auth;

use App\Traits\ResponseTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

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
     * Get the validation rules that apply to the request.
     * @return string[]
     */
    public function rules()
    {
        return [
            'name'      => 'required|min:3|max:50',
            'email'     => 'required|email|unique:users',
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
     * Modify the request data after validation passes.
     * @return void
     */
    public function passedValidation()
    {
        $this->merge([
            'password'      =>          bcrypt($this->input('password'))
        ]);
    }

    /**
     * Get custom attributes for validator errors.
     * @return string[]
     */
    public function attributes()
    {
        return [
            "name"      =>      "full name",
            "email"     =>      "email address",
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
            'required'             =>      ':attribute is required',
            'email'                =>      'Please enter a valid :attribute .',
            'unique'               =>      'This :attribute is already registered.',
            'min'                  =>      ':attribute must be at least :min characters long.',
            'confirmed'            =>      ':attribute do not match.',
            'in'                   =>      ':attribute just acceptance two value (yes OR no)'
        ];
    }
}
