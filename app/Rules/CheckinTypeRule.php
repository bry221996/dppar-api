<?php

namespace App\Rules;

use App\Enums\CheckInType;
use App\Models\Checkin;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class CheckinTypeRule implements Rule, DataAwareRule
{
    /**
     * All of the data under validation.
     *
     * @var array
     */
    protected $data = [];

    // ...

    /**
     * Set the data under validation.
     *
     * @param  array  $data
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $personnel  = Auth::guard('personnels')->user();

        $dateReference = isset($this->data['created_at']) && $this->data['created_at'] ?
            Carbon::parse($this->data['created_at'])->toDateString() :
            Carbon::now()->toDateString();

        if ($value === CheckInType::PRESENT) {
            return !Checkin::where('personnel_id', $personnel->id)
                ->whereIn('type', [CheckInType::LEAVE, CheckInType::OFF_DUTY])
                ->whereDate('created_at', $dateReference)
                ->exists();
        }

        return !Checkin::where('personnel_id', $personnel->id)
            ->whereDate('created_at', $dateReference)
            ->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->data['type'] === CheckInType::PRESENT ? 'Invalid checkin.' : 'Check in record already existing.';
    }
}
