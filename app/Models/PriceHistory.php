<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceHistory extends Model
{
    protected $table = 'price_history';

    protected $fillable = ['annonce_id', 'old_price', 'new_price', 'changed_at', 'notified'];

    protected $casts = [
        'old_price'  => 'decimal:2',
        'new_price'  => 'decimal:2',
        'changed_at' => 'datetime',
        'notified'   => 'boolean',
    ];

    public function annonce()
    {
        return $this->belongsTo(Annonces::class, 'annonce_id');
    }

    /** La variation est-elle une baisse ? */
    public function isDrop(): bool
    {
        return $this->old_price !== null && $this->new_price !== null
            && (float) $this->new_price < (float) $this->old_price;
    }
}
