<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EditUserRequest extends FormRequest
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
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',

            'username' => ['nullable', 'max:255', Rule::unique('users')->where(function ($query) {
                return $query->where('username', request()->input('username'));
            })->ignore(request('id')) ],

            'phone_number' => ['nullable', 'string', Rule::unique('users')->where(function ($query) {
                return $query->where('phone_number', request()->input('phone_number'));
            })->ignore(request('id')) ],

            'image' => 'nullable|mimes:jpg,bmp,png',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'phone_number.string' => trans('validation.phone_number_should_be_string'),
            'phone_number.unique' => trans('validation.phone_number_should_be_unique'),
            'email.email' => trans('validation.email_should_be_real_email'),
            'email.max' => trans('validation.email_should_be_max_of_255_characters'),
            'email.unique' => trans('validation.email_should_be_unique'),
            'username.string' => trans('validation.username_should_be_string'),
            'username.max' => trans('validation.username_should_be_max_of_255_characters'),
            'username.unique' => trans('validation.username_should_be_unique'),
            'first_name.string' => trans('validation.first_name_should_be_string'),
            'last_name.string' => trans('validation.last_name_should_be_string'),
            'birth_date.string' => trans('validation.birth_date_should_be_date_format'),
            'bio.string' => trans('validation.bio_should_be_string'),
            'gender.in' => trans('validation.gender_should_be_in'),
        ];
    }
}
