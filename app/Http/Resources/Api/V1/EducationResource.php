<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EducationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id, 'institution' => $this->institution, 'degree' => $this->degree,
            'field_of_study' => $this->field_of_study, 'description' => $this->description,
            'start_date' => $this->start_date?->toDateString(), 'end_date' => $this->end_date?->toDateString(),
            'is_current' => $this->is_current, 'location' => $this->location,
            'sort_order' => $this->sort_order, 'visible' => $this->visible,
        ];
    }
}
