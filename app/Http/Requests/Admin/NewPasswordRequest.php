<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class NewPasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'token' => [
                'required',
                Rule::exists('password_resets')->where(function ($query) {
                    return $query->where('email', $this->email)
                        ->where('expires_at', '>=', now());
                })
            ],
            'email' => 'required',
            'password' => 'required|min:6|confirmed'
        ];
    }
}
