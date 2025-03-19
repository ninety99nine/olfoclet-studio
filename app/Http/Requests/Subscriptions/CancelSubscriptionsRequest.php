<?php

namespace App\Http\Requests\Subscriptions;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class CancelSubscriptionsRequest extends FormRequest
{
    public function rules()
    {
        return [
            'msisdn' => ['required', 'string', 'regex:/^267\d{8}$/'],
            'tags' => ['sometimes', 'array'],
            'tags.*' => ['string']
        ];
    }

    public function messages()
    {
        return [
            'msisdn.required' => 'The mobile number is required',
            'msisdn.string' => 'The mobile number must be a string',
            'msisdn.regex' => 'Enter a valid Botswana mobile number with the format 267XXXXXXXX (11 digits)'
        ];
    }
}
