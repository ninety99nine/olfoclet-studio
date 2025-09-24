<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriberResource extends JsonResource
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
            'msisdn' => $this->msisdn,
            'metadata' => $this->metadata,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
            'latestSubscription' => new SubscriptionResource(
                $this->latestSubscription
            ),
            'links' => [
                'self' => route('api.show.subscriber', ['project' => $this->project_id, 'subscriber_msisdn' => $this->msisdn]),
                'update' => route('api.update.subscriber', ['project' => $this->project_id, 'subscriber_msisdn' => $this->msisdn]),
                'delete' => route('api.delete.subscriber', ['project' => $this->project_id, 'subscriber_msisdn' => $this->msisdn])
            ]
        ];
    }
}
