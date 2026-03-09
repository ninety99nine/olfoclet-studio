<?php

declare(strict_types=1);

namespace App\Http\Requests\Subscribers;

use Illuminate\Foundation\Http\FormRequest;

class ListSubscribersRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'msisdn' => ['nullable', 'string', 'max:20'],
            'status' => ['nullable', 'string', 'in:active,inactive'],
            'billingStatus' => ['nullable', 'string', 'in:successful,unsuccessful'],
            'autoBillingStatus' => ['nullable', 'string', 'in:successful,unsuccessful'],
            'spendStatus' => ['nullable', 'string', 'in:has_spent,has_not_spent'],
            'scheduledBilling' => ['nullable', 'string', 'in:scheduled,past_due'],
            'scheduledSms' => ['nullable', 'string', 'in:scheduled,past_due'],
            'cancelledAutoBilling' => ['nullable', 'string', 'in:yes,no'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'sort' => ['nullable', 'string', 'regex:/^[\w_]+:(asc|desc)$/'],
        ];
    }
}
