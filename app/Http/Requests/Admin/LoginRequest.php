<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
            'email' => 'required',
            'password' => 'required',
        ];
    }

    public function authenticate()
    {
        $token = auth()->attempt([
            'email' => $this->email,
            'password' => $this->password,
            'status' => 'active'
        ]);

        if (!$token) abort(401, 'Invalid Credentials');

        return $token;
    }
}
