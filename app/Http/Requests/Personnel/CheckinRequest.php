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
        $rules = [
            'image' => 'required|image',
            'type' => 'required|in:present,absent',
            'sub_type' => 'required',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'remarks' => 'required',
            'admin_remarks' => 'nullable'
        ];

        if ($this->post('type') === 'present') {
            $rules['sub_type'] = 'required|in:duty,under_instruction,conference,schooling,travel,off_duty';
        }

        if ($this->post('type') === 'absent') {
            $rules['sub_type'] = 'required|in:leave,confined_in_hospital,sick,suspended';
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
