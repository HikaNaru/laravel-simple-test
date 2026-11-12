<?php

namespace App\Http\Requests;

use App\Enums\CourierStatus;
use App\Enums\VehicleType;
use App\Models\Courier;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

/** Validation for creating and updating a courier; unique checks skip the courier being updated. */
class CourierRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'code' => ['required', 'alpha_dash', 'max:20', $this->unique()],
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:255', $this->unique()],
            'phone' => ['required', 'regex:/^\+?[0-9]{8,15}$/', $this->unique()],
            'national_id' => ['required', 'digits:16', $this->unique()],
            'address' => ['required', 'string', 'max:500'],
            'city' => ['required', 'string', 'max:100'],
            'vehicle_type' => ['required', Rule::enum(VehicleType::class)],
            'plate_number' => [
                Rule::requiredIf($this->input('vehicle_type') !== VehicleType::Bicycle->value),
                'nullable', 'string', 'max:15', $this->unique(),
            ],
            'level' => ['required', 'integer', 'between:'.Courier::MIN_LEVEL.','.Courier::MAX_LEVEL],
            'status' => ['required', Rule::enum(CourierStatus::class)],
            'registered_at' => ['required', 'date', 'before_or_equal:today'],
        ];
    }

    private function unique(): Unique
    {
        return Rule::unique(Courier::class)->ignore($this->route('courier'));
    }
}
