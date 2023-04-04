<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TopicResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if( request()->filled('fields') ) {

            /**
             *  Get the fields as an array e.g
             *
             *  Convert "id,title,content" into ["id", "title", "content"]
             *
             */
            $fields = explode(',', request()->input('fields'));

            /// Trim each field
            $fields = array_map('trim', $fields);

            /// Check if we have a matching field
            $hasMatchingField = collect($this->resource->getAttributes())->keys()->contains(function($attribute) use ($fields) {

                return collect($fields)->contains($attribute);

            });

            /// If we have a matching field
            if($hasMatchingField) {

                return collect($this->resource->getAttributes())->only($fields)->toArray();

            }

        }

        return parent::toArray($request);
    }
}
