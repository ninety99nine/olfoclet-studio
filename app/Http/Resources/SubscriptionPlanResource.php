<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionPlanResource extends JsonResource
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
            'name' => $this->name,
            'tags' => $this->tags,
            'price' => $this->price,
            'active' => $this->active,
            'duration' => $this->duration,
            'frequency' => $this->frequency,
            'description' => $this->description,
            'canAutoBill' => $this->can_auto_bill,
        ];
    }
}
