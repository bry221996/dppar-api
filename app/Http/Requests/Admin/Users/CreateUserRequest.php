<?php

namespace App\Http\Requests\Admin\Users;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
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
            'email' => 'required|unique:users,email|email',
            'name' => 'required',
            'role' => 'required|in:super_admin,regional_police_officer,provincial_police_officer,municipal_police_officer',
            'unit_id' => 'prohibited_if:role,super_admin|required_if:role,regional_police_officer,provincial_police_officer,municipal_police_officer|exists:units,id',
            'sub_unit_id' => 'prohibited_if:role,super_admin,regional_police_officer|required_if:role,provincial_police_officer,municipal_police_officer|exists:sub_units,id',
            'station_id' => 'prohibited_if:role,super_admin,regional_police_officer,provincial_police_officer|required_if:role,municipal_police_officer|exists:stations,id',
        ];
    }
}