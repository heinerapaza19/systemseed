<?php

namespace App\Models;

use Database\Factories\ProjectFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Project extends Model
{
    /** @use HasFactory<ProjectFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'short_description',
        'description',
        'cover_image',
        'github_url',
        'demo_url',
        'demo_enabled',
        'demo_public',
        'is_template',
        'download_enabled',
        'source_code_enabled',
        'download_url',
        'source_code_url',
        'status',
        'featured',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'featured' => 'boolean',
            'demo_enabled' => 'boolean',
            'demo_public' => 'boolean',
            'is_template' => 'boolean',
            'download_enabled' => 'boolean',
            'source_code_enabled' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Project $project) {
            if (blank($project->slug)) {
                $project->slug = static::generateUniqueSlug($project->title);
            }
        });
    }

    public static function generateUniqueSlug(string $value, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($value) ?: 'project';
        $slug = $baseSlug;
        $suffix = 2;

        while (static::query()
            ->where('slug', $slug)
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->exists()) {
            $slug = $baseSlug.'-'.$suffix++;
        }

        return $slug;
    }

    public function technologies(): BelongsToMany
    {
        return $this->belongsToMany(Technology::class);
    }

    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }

    public function savedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'saved_templates')->withTimestamps();
    }

    public function followedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_follows')->withTimestamps();
    }

    public function updates(): HasMany
    {
        return $this->hasMany(ProjectUpdate::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProjectImage::class)->orderBy('sort_order')->orderBy('id');
    }
}
