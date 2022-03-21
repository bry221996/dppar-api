<?php

namespace App\Http\Requests\Personnel;

use App\Enums\CheckInSubType;
use App\Enums\CheckInType;
use App\Services\Geocoder\OpenCage\OpenCageService;
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
            'image' => 'nullable|image|required_if:type,' . CheckInType::PRESENT,
            'type' => ['required', new EnumValue(CheckInType::class)],
            'sub_type' => 'required_if:type,' . CheckInType::PRESENT,
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'remarks' => 'required_if:sub_type,' . CheckInSubType::OTHERS,
            'created_at' => 'nullable|date|date_format:Y-m-d H:i:s'
        ];

        if ($this->post('type') === CheckInType::PRESENT) {
            $rules['sub_type'] = 'required|in:' . implode(',', CheckInType::getSubType($this->post('type')));
        }

        return $rules;
    }

    public function validated()
    {
        $data = parent::validated();
        $data['personnel_id'] = Auth::guard('personnels')->user()->id;

        $address = (new OpenCageService())->reverse($data['latitude'], $data['longitude']);
        $data['town'] = $address->getTown();
        $data['province'] = $address->getProvince();
        $data['address_component'] = json_encode($address->adressComponent);
        $data['from_offline_sync'] = !!$this->created_at;

        if ($this->image && $this->hasFile('image')) {
            $data['image'] = Storage::disk('do_spaces')->url($this->file('image')->store('checkins', 'do_spaces'));
        }

        return $data;
    }
}
