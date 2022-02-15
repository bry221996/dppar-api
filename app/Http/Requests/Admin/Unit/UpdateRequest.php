<?php

namespace App\Http\Requests\Admin\Unit;

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
            'name' => ['required',  Rule::unique('units', 'name')->ignore($this->unit->id)],
            'region' =>  ['required',  Rule::unique('units', 'region')->ignore($this->unit->id)],
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ];
    }
}
