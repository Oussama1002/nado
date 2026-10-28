<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estimation extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'brand',
        'model',
        'version',
        'year',
        'first_registration',
        'km',
        'fuel',
        'gearbox',
        'power_cv',
        'origin',
        'estimated_value',
        'range_min',
        'range_max',
        'confidence',
        'payload',
    ];

    protected $casts = [
        'payload'            => 'array',
        'first_registration' => 'date',
        'estimated_value'    => 'decimal:2',
        'range_min'          => 'decimal:2',
        'range_max'          => 'decimal:2',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
