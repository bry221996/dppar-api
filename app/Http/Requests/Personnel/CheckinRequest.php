<?php

namespace App\Http\Requests\Personnel;

use App\Enums\CheckInSubType;
use App\Enums\CheckInType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use BenSampo\Enum\Rules\EnumValue;

class CheckinRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::guard('personnels')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'image' => 'image|required_if:type,' . CheckInType::PRESENT,
            'type' => ['required', new EnumValue(CheckInType::class)],
            'sub_type' => 'required_unless:type,' . CheckInType::OFF_DUTY,
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'remarks' => 'required_if:sub_type,' . CheckInSubType::OTHERS,
        ];

        if ($this->post('type') === CheckInType::PRESENT || $this->post('type') === CheckInType::ABSENT) {
            $rules['sub_type'] = 'required|in:' . implode(',', CheckInType::getSubType($this->post('type')));
        }

        return $rules;
    }

    public function validated()
    {
        $data = parent::validated();
        $data['personnel_id'] = Auth::guard('personnels')->user()->id;
        $data['image'] = Storage::disk('s3')->url($this->file('image')->store('checkins', 's3'));

        return $data;
    }
}
