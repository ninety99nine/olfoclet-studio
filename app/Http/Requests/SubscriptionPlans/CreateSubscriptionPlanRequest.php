<?php

namespace App\Http\Requests\SubscriptionPlans;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class CreateSubscriptionPlanRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:20',
                Rule::unique('subscription_plans')->where(function ($query) {

                    // Make sure that this project does not already have this subscription plan
                    return $query->where('project_id', request()->route('project'));

                })
            ],
            'price' => ['required', 'regex:/^\d+(\.\d{1,2})?$/'],
            'frequency' => ['required', 'string'],
            'duration' => ['required', 'integer']
        ];
    }

    public function messages()
    {
        return [];
    }
}
