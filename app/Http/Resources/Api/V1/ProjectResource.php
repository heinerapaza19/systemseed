<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class ProjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $user = $request->user() ?? Auth::guard('sanctum')->user();
        $user = $user?->is_active ? $user : null;
        $isAdmin = $user?->isAdmin() ?? false;
        $isPublished = $this->status === 'published';
        $canUseDemo = $this->demo_enabled
            && ($isAdmin || ($isPublished && ($this->demo_public || $user !== null)));
        $canDownload = $this->download_enabled && ($isAdmin || ($isPublished && $user !== null));
        $canViewSource = $this->source_code_enabled && ($isAdmin || ($isPublished && $user !== null));

        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'short_description' => $this->short_description,
            'description' => $this->description,
            'cover_image' => $this->cover_image,
            'github_url' => $this->github_url,
            'demo_enabled' => $this->demo_enabled,
            'demo_public' => $this->demo_public,
            'demo_url' => $this->when($isAdmin || $canUseDemo, $this->demo_url),
            'is_template' => $this->is_template,
            'download_enabled' => $this->download_enabled,
            'download_url' => $this->when($isAdmin || $canDownload, $this->download_url),
            'source_code_enabled' => $this->source_code_enabled,
            'source_code_url' => $this->when($isAdmin || $canViewSource, $this->source_code_url),
            'status' => $this->status,
            'featured' => $this->featured,
            'published_at' => $this->published_at?->toIso8601String(),
            'technologies' => TechnologyResource::collection($this->whenLoaded('technologies')),
            'images' => ProjectImageResource::collection($this->whenLoaded('images')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
