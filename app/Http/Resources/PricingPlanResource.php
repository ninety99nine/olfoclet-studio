<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PricingPlanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /**
         *  Note that some values here are important for the following Wodget:
         *
         *  resources/js/Pages/Subscriptions/List/Partials/ManageSubscriptionModal.vue
         *
         *  This Modal Widget will make an API call to retrieve the pricing plans whenever
         *  we want to create a new subscription directly on the dashboard. The values that
         *  are particularly important for this Widget are the "active", "isFolder" and
         *  the "childrenCount"
         */
        return [
            'id' => $this->id,
            'name' => $this->name,
            'tags' => $this->tags,
            'price' => $this->price,
            'active' => $this->active,                  //  Important for the dashboard
            'duration' => $this->duration,
            'isFolder' => $this->is_folder,             //  Important for the dashboard
            'frequency' => $this->frequency,
            'description' => $this->description,
            'billingType' => $this->billing_type,
            'canAutoBill' => $this->can_auto_bill,
            'childrenCount' => $this->children_count    //  Important for the dashboard
        ];
    }
}
