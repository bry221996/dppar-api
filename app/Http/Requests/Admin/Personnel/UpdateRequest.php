<?php

namespace App\Http\Requests\Admin\Personnel;

use App\Enums\GenderType;
use App\Enums\PersonnelCategory;
use App\Enums\PersonnelClassification;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Storage;
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
            'title' => 'required',
            'qualifier' => 'nullable',
            'badge_no' => ['required',  Rule::unique('personnels', 'badge_no')->ignore($this->personnel->id)],
            'personnel_id' => ['required',  Rule::unique('personnels', 'personnel_id')->ignore($this->personnel->id)],
            'email' =>  ['required',  'email', Rule::unique('personnels', 'email')->ignore($this->personnel->id)],
            'mobile_number' =>  ['required', Rule::unique('personnels', 'mobile_number')->ignore($this->personnel->id)],
            'image' => 'nullable',
            'new_image' => 'required_if:image,null',
            'designation' => 'required',
            'category' => ['required', new EnumValue(PersonnelCategory::class)],
            'classification' => ['required', new EnumValue(PersonnelClassification::class)],
            'gender' => ['required', new EnumValue(GenderType::class)],
            'first_name' => 'required',
            'last_name' =>  'required',
            'middle_name' =>  'required',
            'birth_date' => 'required|date|date_format:Y-m-d|before:now',
            'status' => 'required|in:active,inactive'
        ];
    }

    public function validated()
    {
        $data = parent::validated();

        if ($this->hasFile('new_image') && !$this->has('image')) {
            $data['image'] = Storage::disk('s3')->url($this->file('new_image')->store('personnels', 's3'));
            unset($data['new_image']);
        }

        return $data;
    }
}