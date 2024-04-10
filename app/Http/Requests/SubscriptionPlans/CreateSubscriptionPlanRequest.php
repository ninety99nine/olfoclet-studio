<?php

namespace App\Http\Requests\SubscriptionPlans;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class CreateSubscriptionPlanRequest extends FormRequest
{
    public function rules()
    {
        $requiredIfIsNotFolder = Rule::requiredIf(in_array(request()->input('is_folder'), [false, 0]));

        return [
            'active' => ['required', 'boolean'],
            'is_folder' => ['required', 'boolean'],
            'name' => ['required', 'string', 'min:3', 'max:40'],
            'duration' => [$requiredIfIsNotFolder, 'sometimes', 'integer'],
            'frequency' => [$requiredIfIsNotFolder, 'sometimes', 'string'],
            'can_auto_bill' => [$requiredIfIsNotFolder, 'sometimes', 'boolean'],
            'price' => [$requiredIfIsNotFolder, 'sometimes', 'regex:/^\d+(\.\d{1,2})?$/'],
            'description' => [$requiredIfIsNotFolder, 'sometimes', 'string', 'min:10', 'max:255'],
            'max_auto_billing_attempts' => [$requiredIfIsNotFolder, 'sometimes', 'integer', 'min:1', 'max:10'],
            'insufficient_funds_message' => [$requiredIfIsNotFolder, 'sometimes', 'string', 'min:10', 'max:255'],
            'successful_payment_sms_message' => [$requiredIfIsNotFolder, 'sometimes', 'string', 'min:10', 'max:255'],
            'auto_billing_disabled_sms_message' => [$requiredIfIsNotFolder, 'sometimes', 'string', 'min:10', 'max:255'],
            'next_auto_billing_reminder_sms_message' => [$requiredIfIsNotFolder, 'sometimes', 'string', 'min:10', 'max:255'],
            'successful_auto_billing_payment_sms_message' => [$requiredIfIsNotFolder, 'sometimes', 'string', 'min:10', 'max:255'],
        ];
    }

    public function messages()
    {
        return [];
    }
}
