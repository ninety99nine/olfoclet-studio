<?php

namespace App\Http\Requests\Subscriptions;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class CreateSubscriptionRequest extends FormRequest
{
    public function rules()
    {
        return [
            'msisdn' => ['required', 'string', 'regex:/^267\d{8}$/'],
            'subscription_plan_id' => ['required', 'integer', 'min:1',
                Rule::exists('subscription_plans', 'id')->where(function ($query) {
                    return $query->where('project_id', request()->route('project'));
                })
            ],
        ];
    }

    public function messages()
    {
        return [
            'msisdn.required' => 'The mobile number is required',
            'msisdn.string' => 'The mobile number must be a string',
            'msisdn.regex' => 'Enter a valid Botswana mobile number with the format 267XXXXXXXX (11 digits)',
            'subscription_plan_id.required' => 'The subscription plan ID is required',
            'subscription_plan_id.integer' => 'The subscription plan ID must be an integer',
            'subscription_plan_id.min' => 'The subscription plan ID must be at least 1',
            'subscription_plan_id.exists' => 'The selected subscription plan is invalid',
        ];
    }
}
