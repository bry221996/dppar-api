<?php

namespace App\Http\Requests\Admin\Personnel;

use App\Enums\GenderType;
use App\Enums\PersonnelCategory;
use App\Enums\PersonnelClassification;
use BenSampo\Enum\Rules\EnumValue;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

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
            'title' => 'required',
            'qualifier' => 'nullable',
            'badge_no' => 'required|unique:personnels,badge_no',
            'personnel_id' => 'required|unique:personnels,personnel_id',
            'designation' => 'required',
            'category' => ['required', new EnumValue(PersonnelCategory::class)],
            'classification' => ['required', new EnumValue(PersonnelClassification::class)],
            'gender' => ['required', new EnumValue(GenderType::class)],
            'first_name' => 'required',
            'last_name' =>  'required',
            'middle_name' =>  'required',
            'birth_date' => 'required|date|date_format:Y-m-d|before:now',
            'mobile_number' => 'required',
            'email' => 'required|email|unique:personnels,email',
            'assignment' => 'required|array',
            'assignment.unit_id' => 'required|exists:units,id',
            'assignment.sub_unit_id' => 'nullable|exists:sub_units,id',
            'assignment.station_id' => 'nullable|exists:stations,id',
        ];
    }

    public function personnelData(): array
    {
        $data = $this->validated();
        $data['mpin'] = Hash::make(Carbon::parse($this->birth_date)->format('Ymd'));

        unset($data['assignment']);
        return $data;
    }

    public function assignmentData(): array
    {
        return $this->validated()['assignment'];
    }
}
