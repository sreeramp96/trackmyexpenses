<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DebtRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'contact_name' => ['required', 'string', 'max:255'],
            'direction' => ['required', Rule::in(['lent', 'borrowed'])],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'remaining_amount' => ['required', 'numeric', 'min:0', 'max:'.$this->amount],
            'due_date' => ['nullable', 'date'],
            'note' => ['nullable', 'string'],
            'is_settled' => ['nullable', 'boolean'],
        ];
    }
}
