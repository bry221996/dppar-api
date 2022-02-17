<?php

namespace App\Http\Requests\Admin\SubUnit;

use App\Enums\SubUnitType;
use BenSampo\Enum\Rules\EnumValue;
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
            'unit_id' => 'required|exists:units,id',
            'name' => 'required|unique:sub_units,name',
            'province' => 'required|unique:sub_units,province',
            'code' => 'required|unique:sub_units,code',
            'type' => ['required', new EnumValue(SubUnitType::class)],
            'status' => 'required|in:active,inactive'
        ];
    }
}
