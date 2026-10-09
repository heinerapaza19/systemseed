<?php

namespace App\Models;

use Database\Factories\EducationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Education extends Model
{
    /** @use HasFactory<EducationFactory> */
    use HasFactory;

    protected $attributes = ['is_current' => false, 'sort_order' => 0, 'visible' => true];

    protected $table = 'education';

    protected $fillable = ['institution', 'degree', 'field_of_study', 'description', 'start_date', 'end_date', 'is_current', 'location', 'sort_order', 'visible'];

    protected function casts(): array
    {
        return ['start_date' => 'date', 'end_date' => 'date', 'is_current' => 'boolean', 'visible' => 'boolean', 'sort_order' => 'integer'];
    }
}
