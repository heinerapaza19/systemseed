<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class CertificateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'institution' => $this->institution,
            'description' => $this->description,
            'issued_at' => $this->issued_at?->toDateString(),
            'credential_id' => $this->credential_id,
            'verification_url' => $this->verification_url,
            'file_url' => $this->file_url ? Storage::disk(config('filesystems.default'))->url($this->file_url) : null,
            'file_type' => $this->file_type,
            'category' => $this->category,
            'visible' => $this->visible,
            'sort_order' => $this->sort_order,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
