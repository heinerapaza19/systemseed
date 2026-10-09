<?php

namespace App\Models;

use Database\Factories\SkillFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    /** @use HasFactory<SkillFactory> */
    use HasFactory;

    protected $attributes = ['sort_order' => 0, 'visible' => true];

    protected $fillable = ['name', 'category', 'level', 'icon', 'sort_order', 'visible'];

    protected function casts(): array
    {
        return ['visible' => 'boolean', 'sort_order' => 'integer'];
    }
}
