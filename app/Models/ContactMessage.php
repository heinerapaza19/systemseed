<?php

namespace App\Models;

use Database\Factories\ContactMessageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    /** @use HasFactory<ContactMessageFactory> */
    use HasFactory;

    protected $attributes = ['is_read' => false];

    protected $fillable = ['name', 'email', 'subject', 'message', 'is_read', 'read_at'];

    protected function casts(): array
    {
        return ['is_read' => 'boolean', 'read_at' => 'datetime'];
    }
}
