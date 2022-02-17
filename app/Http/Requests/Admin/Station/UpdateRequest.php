<?php

namespace App\Http\Requests\Admin\Station;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
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
            'name' => ['required',  Rule::unique('stations', 'name')->ignore($this->station->id)],
            'municipality' => ['required',  Rule::unique('stations', 'municipality')->ignore($this->station->id)],
            'code' => ['required',  Rule::unique('stations', 'code')->ignore($this->station->id)],
            'status' => 'required|in:active,inactive'
        ];
    }
}
