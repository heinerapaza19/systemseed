<?php

namespace App\Models;

use Database\Factories\ExperienceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Experience extends Model
{
    /** @use HasFactory<ExperienceFactory> */
    use HasFactory;

    protected $attributes = ['is_current' => false, 'sort_order' => 0, 'visible' => true];

    protected $fillable = ['company', 'position', 'description', 'start_date', 'end_date', 'is_current', 'location', 'sort_order', 'visible'];

    protected function casts(): array
    {
        return ['start_date' => 'date', 'end_date' => 'date', 'is_current' => 'boolean', 'visible' => 'boolean', 'sort_order' => 'integer'];
    }
}
