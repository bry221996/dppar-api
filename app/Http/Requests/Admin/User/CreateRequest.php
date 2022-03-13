<?php

namespace App\Http\Requests\Admin\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CreateRequest extends FormRequest
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

        return [
            'email' => 'required|unique:users,email|email',
            'name' => 'required',
            'role' => 'required|in:super_admin,regional_police_officer,provincial_police_officer,municipal_police_officer',
            'unit_id' => 'nullable|required_if:role,regional_police_officer,provincial_police_officer,municipal_police_officer|exists:units,id',
            'sub_unit_id' => 'nullable|required_if:role,provincial_police_officer,municipal_police_officer|exists:sub_units,id',
            'station_id' => 'nullable|required_if:role,municipal_police_officer|exists:stations,id',
            'status' => 'required|in:active,inactive',
            'is_intel' => 'required|boolean',
            'classifications' => 'required_if:role,regional_police_officer,provincial_police_officer,municipal_police_officer|array|prohibited_if:role,super_admin',
            'classifications.*' => 'required|distinct|exists:classifications,id',
            'offices' => 'array|prohibited_if:role,super_admin',
            'offices.*' => [
                'required',
                'distinct',
                Rule::exists('offices', 'id')
                    ->when($this->unit_id, function ($query) {
                        return $query->where('unit_id', $this->unit_id);
                    })
                    ->when($this->sub_unit_id, function ($query) {
                        return $query->where('sub_unit_id', $this->sub_unit_id);
                    })
                    ->when($this->station_id, function ($query) {
                        return $query->where('station_id', $this->station_id);
                    })
            ]
        ];
    }

    public function userData()
    {
        $data = $this->validated();
        unset($data['classifications']);
        unset($data['offices']);

        return $data;
    }

    public function classificationsData()
    {
        return $this->classifications;
    }

    public function officesData()
    {
        return $this->offices ? $this->offices : [];
    }
}
