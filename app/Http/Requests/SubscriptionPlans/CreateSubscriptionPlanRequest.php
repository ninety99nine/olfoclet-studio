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
            'billing_product_id' => ['sometimes', 'string', 'min:1', 'max:500'],
            'price' => [$requiredIfIsNotFolder, 'sometimes', 'regex:/^\d+(\.\d{1,2})?$/'],
            'billing_purchase_category_code' => ['sometimes', 'string', 'min:1', 'max:500'],
            'description' => [$requiredIfIsNotFolder, 'sometimes', 'string', 'min:10', 'max:255'],
            'max_auto_billing_attempts' => [$requiredIfIsNotFolder, 'sometimes', 'integer', 'min:1', 'max:10'],
            'insufficient_funds_message' => ['nullable', 'string', 'min:10', 'max:500'],
            'successful_payment_sms_message' => ['nullable', 'string', 'min:10', 'max:500'],
            'auto_billing_disabled_sms_message' => ['nullable', 'string', 'min:10', 'max:500'],
            'next_auto_billing_reminder_sms_message' => ['nullable', 'string', 'min:10', 'max:500'],
            'successful_auto_billing_payment_sms_message' => ['nullable', 'string', 'min:10', 'max:500'],
        ];
    }

    public function messages()
    {
        return [];
    }
}
