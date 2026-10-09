<?php

namespace App\Models;

use Database\Factories\CertificateFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    /** @use HasFactory<CertificateFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'institution',
        'description',
        'issued_at',
        'credential_id',
        'verification_url',
        'file_url',
        'file_type',
        'category',
        'visible',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'issued_at' => 'date',
            'visible' => 'boolean',
            'sort_order' => 'integer',
        ];
    }
}
