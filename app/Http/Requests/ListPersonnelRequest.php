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

            $this->merge([
                'unit_id' => $user->unit_id
            ]);
        }

        if ($user->is_provincial_police_officer) {
            $rules['unit_id'] = 'prohibited';
            $rules['sub_unit_id'] = 'prohibited';

            $this->merge([
                'unit_id' => $user->unit_id,
                'sub_unit_id' => $user->sub_unit_id,
            ]);
        }

        if ($user->is_municipal_police_officer) {
            $rules['unit_id'] = 'prohibited';
            $rules['sub_unit_id'] = 'prohibited';
            $rules['station_id'] = 'prohibited';

            $this->merge([
                'unit_id' => $user->unit_id,
                'sub_unit_id' => $user->sub_unit_id,
                'station_id' => $user->station_id,
            ]);
        }

        return $rules;
    }
}
