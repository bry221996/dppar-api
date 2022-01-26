<?php

namespace App\Http\Requests\Personnel;

use App\Models\Personnel;
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
            'personnel_id' => 'required',
            'birth_date' => 'required|date|date_format:Y-m-d',
        ];
    }

    public function authenticate()
    {
        return Personnel::where('personnel_id', $this->personnel_id)
            ->where('birth_date', $this->birth_date)
            ->first();   
    }
}
