<?php

namespace App\Http\Requests\SubscriptionPlans;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSubscriptionPlanRequest extends FormRequest
{
    public function rules()
    {
        $requiredIfIsNotFolder = Rule::requiredIf(in_array(request()->input('is_folder'), [false, 0]));
        $requiredIfCanAutoBill = Rule::requiredIf($requiredIfIsNotFolder && in_array(request()->input('can_auto_bill'), [true, 1]));

        return [
            'active' => ['sometimes', 'required', 'boolean'],
            'is_folder' => ['sometimes', 'required', 'boolean'],
            'can_auto_bill' => ['sometimes', 'required', 'boolean'],
            'duration' => [$requiredIfIsNotFolder, 'sometimes', 'integer'],
            'frequency' => [$requiredIfIsNotFolder, 'sometimes', 'string'],
            'name' => ['sometimes', 'required', 'string', 'min:3', 'max:40'],
            'price' => [$requiredIfIsNotFolder, 'sometimes', 'regex:/^\d+(\.\d{1,2})?$/'],
            'description' => [$requiredIfIsNotFolder, 'sometimes', 'string', 'min:10', 'max:255'],
            'max_auto_billing_attempts' => [$requiredIfIsNotFolder, 'sometimes', 'integer', 'min:1', 'max:3'],
            'insufficient_funds_message' => [$requiredIfIsNotFolder, 'sometimes', 'string', 'min:10', 'max:255'],
            'successful_payment_sms_message' => [$requiredIfIsNotFolder, 'sometimes', 'string', 'min:10', 'max:255'],
            'next_auto_billing_reminder_sms_message' => [$requiredIfCanAutoBill, 'sometimes', 'string', 'min:10', 'max:255'],
        ];
    }

    public function messages()
    {
        return [];
    }
}
