<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NadoAutoEncheresBid extends Model
{
    use HasFactory;

    protected $table = 'nado_auto_encheres_bid';

    protected $fillable = [
        'enchere_id',
        'client_id',
        'proposition',
        'is_auto',
        'max_amount',
    ];

    protected $casts = [
        'proposition' => 'decimal:2',
        'max_amount'  => 'decimal:2',
        'is_auto'     => 'boolean',
    ];

    /**
     * Get the auction (enchere) this bid belongs to.
     */
    public function enchere()
    {
        return $this->belongsTo(NadoAutoEnchere::class, 'enchere_id');
    }
 public function brand()
    {
        return $this->belongsTo(Brand::class, 'mark'); // 'mark' stores brand id
    }

    public function modeleRelation()
    {
        return $this->belongsTo(Modele::class, 'modele'); // 'modele' stores model id
    }
    /**
     * Get the client who made the bid.
     */
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }


}
