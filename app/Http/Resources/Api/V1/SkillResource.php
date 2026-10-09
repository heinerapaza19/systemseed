<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SkillResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id, 'name' => $this->name, 'category' => $this->category,
            'level' => $this->level, 'icon' => $this->icon,
            'sort_order' => $this->sort_order, 'visible' => $this->visible,
        ];
    }
}
