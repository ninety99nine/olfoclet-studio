<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'endAt' => $this->end_at,
            'startAt' => $this->start_at,
            'isActive' => $this->is_active,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
            'cancelledAt' => $this->cancelled_at,
            'createdUsingAutoBilling' => $this->created_using_auto_billing,
            'subscriptionPlan' => new SubscriptionPlanResource(
                $this->subscriptionPlan
            ),
            'links' => [
                'self' => route('api.show.subscription', ['project' => $this->project_id, 'subscription' => $this->id]),
                'cancelSubscription' => route('api.cancel.subscription', ['project' => $this->project_id, 'subscription' => $this->id]),
                'uncancelSubscription' => route('api.uncancel.subscription', ['project' => $this->project_id, 'subscription' => $this->id]),
            ],
        ];
    }
}
