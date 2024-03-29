<?php

namespace App\Http\Requests\Personnel;

use App\Repositories\PersonnelRepository;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

class LoginRequest extends FormRequest
{
    protected $personnelRepository;

    public function __construct(PersonnelRepository $personnelRepository)
    {
        $this->personnelRepository = $personnelRepository;
    }

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
            'personnel_id' => 'required',
            'birth_date' => 'required_without:mpin|date|date_format:Y-m-d',
            'mpin' => 'required_without:birth_date',
            'device' => 'required'
        ];
    }

    public function authenticate()
    {
        $personnel = $this->birth_date ?
            $this->personnelRepository->getByPersonnelIdAndBirthDate($this->personnel_id, $this->birth_date) :
            $this->personnelRepository->getByPersonnelId($this->personnel_id);

        if (!$personnel) return false;

        if ($personnel->is_inactive || ($personnel && $this->mpin && !Hash::check($this->mpin, $personnel->mpin))) return false;

        // Delete previous tokens on this device.
        $personnel->tokens()->where('name', $this->device)->delete();

        return $personnel->createToken($this->device)->plainTextToken;
    }
}
