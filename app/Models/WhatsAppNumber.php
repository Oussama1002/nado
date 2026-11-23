<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsAppNumber extends Model
{
    protected $table = 'whatsapp_numbers';

    protected $fillable = [
        'display_number', 'phone_id', 'waba_id', 'label',
        'is_default', 'is_active',
        'verified_name', 'quality_rating',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active'  => 'boolean',
    ];
}
