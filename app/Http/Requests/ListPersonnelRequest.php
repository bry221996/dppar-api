<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ListPersonnelRequest extends FormRequest
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
        $user = Auth::guard('admins')->user();

        $rules = [
            'unit_id' => 'integer',
            'sub_unit_id' => 'integer',
            'station_id' => 'integer',
        ];

        if ($user->is_regional_police_officer) {
            $rules['unit_id'] = 'prohibited';
        }

        if ($user->is_provincial_police_officer) {
            $rules['unit_id'] = 'prohibited';
            $rules['sub_unit_id'] = 'prohibited';
        }

        if ($user->is_municipal_police_officer) {
            $rules['unit_id'] = 'prohibited';
            $rules['sub_unit_id'] = 'prohibited';
            $rules['station_id'] = 'prohibited';
        }

        return $rules;
    }
}
