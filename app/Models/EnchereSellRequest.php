<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnchereSellRequest extends Model
{
    protected $fillable = [
        'client_id', 'name', 'phone', 'message', 'vehicles', 'status',
    ];

    protected $casts = [
        'vehicles' => 'array',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    /** Nombre de véhicules soumis. */
    public function vehicleCount(): int
    {
        return is_array($this->vehicles) ? count($this->vehicles) : 0;
    }
}
