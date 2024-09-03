<?php

namespace App\Http\Requests\BorrowRecords;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBorrowRecordFormRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "book_id"           =>      "required|numeric|min:1",
            "borrowed_at"       =>      "required|date",
            "returned_at"       =>      "required|date|after:borrowed_at "
        ];
    }

    /**
     * Get custom attributes for validator errors.
     * @return string[]
     */
    public function attributes()
    {
        return [
            "book_id"       =>      "Book number",
            'borrowed_at'   =>      'Book borrowed date',
            "returned_at"   =>      "Book returned date"
        ];
    }

    /**
     * Get custom messages for validator errors.
     * @return string[]
     */
    public function messages()
    {
        return [
            "book_id.required"          =>      "Book number is required",
            "book_id.numeric"           =>      "Book number must be at number",
            "book_id.min"               =>      "Book number must be at minimum 1 character",
            'borrowed_at.date'          =>      'The borrowed date must be a valid date format.',
            'returned_at.date'          =>      'The returned date must be a valid date format.',
            'returned_at.after'         =>      'The returned date must be after the borrowed date.',
        ];
    }
}
