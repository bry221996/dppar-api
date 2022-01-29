<?php

namespace App\Http\Requests\Personnel;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

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
        return [
            'image' => 'required|image',
            'type' => 'required|in:regular_checkin,out_of_area_of_responsibility,leave_of_absence,off_duty',
            'out_of_area_of_responsibility_type' => 'required_if:type,out_of_area_of_responsibility|in:hospital,travel,under_instruction,official_mission,conference,others',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'remarks' => 'required',
            'admin_remarks' => 'nullable'
        ];
    }

    public function validated()
    {
        $data = parent::validated();
        $data['personnel_id'] = Auth::guard('personnels')->user()->id;
        $data['image'] = Storage::disk('s3')->url($this->file('image')->store('checkins', 's3'));

        return $data;
    }
}
