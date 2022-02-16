<?php

namespace App\Http\Requests\Admin\Station;

use Illuminate\Foundation\Http\FormRequest;

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
        return [
            'sub_unit_id' => 'required|exists:sub_units,id',
            'name' => 'required|unique:stations,name',
            'municipality' => 'required|unique:stations,municipality',
            'code' => 'required|unique:stations,code',
        ];
    }
}
