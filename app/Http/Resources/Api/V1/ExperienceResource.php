<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExperienceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id, 'company' => $this->company, 'position' => $this->position,
            'description' => $this->description, 'start_date' => $this->start_date?->toDateString(),
            'end_date' => $this->end_date?->toDateString(), 'is_current' => $this->is_current,
            'location' => $this->location, 'sort_order' => $this->sort_order, 'visible' => $this->visible,
        ];
    }
}
