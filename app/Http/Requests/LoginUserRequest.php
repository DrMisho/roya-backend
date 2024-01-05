<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;

class LoginUserRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'is_phone_number' => 'required|boolean',
            'username' => ['required_if:is_phone_number,0', 'max:255', 'exists:users,username'],
            'phone_number' => ['required_if:is_phone_number,1', 'max:255', 'exists:users,phone_number'],
            'password' => ['required'],
        ];
    }

}
