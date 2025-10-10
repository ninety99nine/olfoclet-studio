<?php

namespace App\Http\Requests\PricingPlans;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePricingPlanRequest extends FormRequest
{
    public function rules()
    {
        $requiredIfIsNotFolder = Rule::requiredIf(in_array(request()->input('is_folder'), [false, 0]));
        $requiredIfCanAutoBill = Rule::requiredIf($requiredIfIsNotFolder && in_array(request()->input('can_auto_bill'), [true, 1]));

        return [
            'active' => ['sometimes', 'required', 'boolean'],
            'is_folder' => ['sometimes', 'required', 'boolean'],
            'can_auto_bill' => ['sometimes', 'required', 'boolean'],
            'billing_type' => ['sometimes', 'string', Rule::in(['one time', 'subscription'])],
            'duration' => [$requiredIfIsNotFolder, 'integer'],
            'frequency' => [$requiredIfIsNotFolder, 'string'],
            'name' => ['sometimes', 'required', 'string', 'min:3', 'max:40'],
            'billing_product_id' => ['sometimes', 'string', 'min:1', 'max:500'],
            'price' => [$requiredIfIsNotFolder, 'regex:/^\d+(\.\d{1,2})?$/'],
            'trial_days' => [$requiredIfIsNotFolder, 'integer', 'min:0'],
            'billing_purchase_category_code' => ['sometimes', 'string', 'min:1', 'max:500'],
            'description' => [$requiredIfIsNotFolder, 'string', 'min:10', 'max:255'],
            'max_auto_billing_attempts' => [$requiredIfIsNotFolder, 'integer', 'min:0'],
            'insufficient_funds_message' => [$requiredIfIsNotFolder, 'nullable', 'string', 'min:10', 'max:500'],
            'trial_started_sms_message' => ['nullable', 'string', 'min:10', 'max:500'],
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
