<?php

namespace App\Http\Requests\Admin\Office;

use App\Enums\OfficeClassification;
use App\Enums\OfficeType;
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
            'name' => ['required',  Rule::unique('offices', 'name')->ignore($this->office->id)],
            'status' => 'required|in:active,inactive',
            'type' => ['required', new EnumValue(OfficeType::class)],
            'classification' => ['required', new EnumValue(OfficeClassification::class)],
            'unit_id' => 'required|exists:units,id',
            'sub_unit_id' => 'nullable|exists:sub_units,id',
            'station_id' => 'nullable|exists:stations,id',
        ];
    }
}
