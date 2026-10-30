<?php

namespace App\Observers;

use App\Models\Setting;
use App\Models\SettingLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

/**
 * Trace chaque changement de valeur d'un réglage (doc 02 — §6.bis.4).
 * Enregistré dans App\Providers\AppServiceProvider::boot().
 */
class SettingObserver
{
    public function updated(Setting $setting): void
    {
        // On ne journalise que les changements de valeur réelle.
        if (! $setting->wasChanged('value')) {
            return;
        }

        SettingLog::create([
            'user_id'     => Auth::guard('admin')->id() ?? Auth::id(),
            'setting_key' => $setting->key,
            'old_value'   => $setting->getOriginal('value'),
            'new_value'   => $setting->value,
            'ip'          => Request::ip(),
        ]);
    }
}
