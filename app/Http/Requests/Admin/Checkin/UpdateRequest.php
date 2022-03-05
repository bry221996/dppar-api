<?php

namespace App\Http\Requests\Admin\Checkin;

use App\Enums\CheckInSubType;
use App\Enums\CheckInType;
use Illuminate\Foundation\Http\FormRequest;

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
            'ids' => 'required|array',
            'ids.*' => 'required|exists:checkins,id',
            'type' => 'required|string|in:' . CheckInType::ABSENT,
            'remarks' => 'required',
            'sub_type' => 'required|in:' . implode(',',  CheckInType::getSubType(CheckInType::ABSENT)),
        ];
    }
}
