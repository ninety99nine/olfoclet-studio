<?php

declare(strict_types=1);

namespace App\Http\Requests\Subscriptions;

use Illuminate\Foundation\Http\FormRequest;

class ListSubscriptionsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'msisdn' => ['nullable', 'string', 'max:20'],
            'status' => ['nullable', 'string', 'in:active,inactive'],
            'pricing_plan_id' => ['nullable', 'integer', 'exists:pricing_plans,id'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'sort' => ['nullable', 'string', 'regex:/^[\w_]+:(asc|desc)$/'],
        ];
    }
}
