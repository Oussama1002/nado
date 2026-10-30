<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Journal d'audit des modifications de configuration (doc 02 — §6.bis.4).
 * Rempli automatiquement par App\Observers\SettingObserver.
 */
class SettingLog extends Model
{
    protected $fillable = [
        'user_id', 'setting_key', 'old_value', 'new_value', 'ip',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
