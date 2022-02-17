<?php

namespace App\Http\Requests\Admin\SubUnit;

use App\Enums\SubUnitType;
use BenSampo\Enum\Rules\EnumValue;
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
            'unit_id' => 'required|exists:units,id',
            'name' => ['required',  Rule::unique('sub_units', 'name')->ignore($this->sub_unit->id)],
            'province' =>  ['required',  Rule::unique('sub_units', 'province')->ignore($this->sub_unit->id)],
            'code' =>  ['required',  Rule::unique('sub_units', 'code')->ignore($this->sub_unit->id)],
            'type' => ['required', new EnumValue(SubUnitType::class)],
            'status' => 'required|in:active,inactive'
        ];
    }
}
