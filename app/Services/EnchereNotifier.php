<?php

namespace App\Services;

use App\Models\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Notifications admin liées à l'accès Auto Enchère.
 */
class EnchereNotifier
{
    /**
     * Informe contact@nado.ma qu'un client attend la validation de son accès
     * Auto Enchère. N'interrompt jamais le flux si l'envoi échoue.
     */
    public static function adminPending(Client $client): void
    {
        try {
            $lines = [
                'Un client attend la validation de son accès Auto Enchère.',
                '',
                'Nom      : ' . ($client->name ?: '—'),
                'Email    : ' . ($client->email ?: '—'),
                'Téléphone: ' . ($client->phone ?: '—'),
                'Rôle     : ' . ($client->ae_role ?: '—'),
                'Société  : ' . ($client->company ?: '—'),
                '',
                'À valider dans : Admin → Clients Auto Enchère.',
                '',
                '---',
                'Demande du ' . now()->format('d/m/Y H:i'),
                'ID interne : #' . $client->id,
            ];

            Mail::raw(implode("\n", $lines), function ($m) use ($client) {
                $m->to('contact@nado.ma')
                  ->subject('Auto Enchère — validation en attente : ' . ($client->name ?: ('#' . $client->id)));
                if ($client->email) {
                    $m->replyTo($client->email, $client->name ?: '');
                }
            });
        } catch (\Throwable $e) {
            Log::error('Notification validation enchère échouée: ' . $e->getMessage());
        }
    }
}
