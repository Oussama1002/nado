<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DemandeExpert extends Model
{
    protected $table = 'demande_experts';

    protected $fillable = [
        'name', 'email', 'phone', 'ville',
        'subject', 'message',
        'source', 'status', 'ip',
    ];
}
