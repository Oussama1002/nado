<?php

namespace App\Services;

use App\Models\Client;
use App\Models\NadoAutoEnchere;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Notifications admin liées à l'accès Auto Enchère.
 */
class EnchereNotifier
{
    /**
     * Prévient tous les clients « flotte&enchere » approuvés qu'une enchère
     * ouvre bientôt (rappel envoyé ~24 h avant l'ouverture), avec tous les
     * détails du véhicule. Un envoi raté n'interrompt pas les autres.
     *
     * @return int Nombre d'emails envoyés.
     */
    public static function openingSoon(NadoAutoEnchere $enchere): int
    {
        $clients = Client::where('type', 'flotte&enchere')
            ->where('ae_approved', 1)
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->get();

        if ($clients->isEmpty()) {
            Log::info('Rappel enchère #' . $enchere->id . ' : aucun client flotte&enchere approuvé à notifier.');
            return 0;
        }

        $subject = 'Enchère NADO — ouverture dans moins de 24 h : '
            . ($enchere->titre ?: trim(($enchere->mark ?? '') . ' ' . ($enchere->modele ?? '')) ?: ('#' . $enchere->id));
        $html = self::buildEnchereHtml($enchere);

        $sent = 0;
        foreach ($clients as $client) {
            try {
                Mail::html($html, function ($m) use ($client, $subject) {
                    $m->to($client->email, $client->name ?: '')->subject($subject);
                });
                $sent++;
            } catch (\Throwable $e) {
                Log::error('Rappel enchère #' . $enchere->id . ' échoué pour client #' . $client->id . ' : ' . $e->getMessage());
            }
        }

        Log::info('Rappel enchère #' . $enchere->id . " envoyé à {$sent}/{$clients->count()} client(s) flotte&enchere.");
        return $sent;
    }

    /**
     * Construit le corps HTML du rappel d'enchère à partir des détails du véhicule.
     */
    private static function buildEnchereHtml(NadoAutoEnchere $enchere): string
    {
        $fmtDate = fn ($d) => $d ? \Illuminate\Support\Carbon::parse($d)->format('d/m/Y à H:i') : '—';
        $fmtNum  = fn ($n) => ($n === null || $n === '') ? '—' : number_format((float) $n, 0, ',', ' ');

        $rows = [
            'Véhicule'        => $enchere->titre ?: trim(($enchere->mark ?? '') . ' ' . ($enchere->modele ?? '')) ?: '—',
            'Marque'          => $enchere->mark ?: '—',
            'Modèle'          => $enchere->modele ?: '—',
            'Année'           => $enchere->year ?: '—',
            'Kilométrage'     => $enchere->km !== null ? $fmtNum($enchere->km) . ' km' : '—',
            'Carburant'       => $enchere->carburant ?: '—',
            'Boîte'           => $enchere->box ?: '—',
            'Puissance'       => $enchere->cv ? $enchere->cv . ' CV' : '—',
            'Ville'           => $enchere->city ?: '—',
            'Mise à prix'     => $enchere->prix ? $fmtNum($enchere->prix) . ' MAD' : '—',
            'Ouverture'       => $fmtDate($enchere->starts_at),
            'Clôture'         => $fmtDate($enchere->ends_at),
        ];

        $url = route('nadoautoenchere.show', $enchere->id);

        $rowsHtml = '';
        foreach ($rows as $label => $value) {
            $rowsHtml .= '<tr>'
                . '<td style="padding:8px 12px;border-bottom:1px solid #eef2f7;color:#64748b;font-size:13px;font-weight:700;">' . e($label) . '</td>'
                . '<td style="padding:8px 12px;border-bottom:1px solid #eef2f7;color:#0f172a;font-size:14px;font-weight:700;">' . e($value) . '</td>'
                . '</tr>';
        }

        return '<!doctype html><html><body style="margin:0;background:#f1f5f9;font-family:Arial,Helvetica,sans-serif;">'
            . '<div style="max-width:560px;margin:0 auto;padding:24px;">'
            . '<div style="background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 10px 30px -12px rgba(15,23,42,.25);">'
            . '<div style="background:#F37021;padding:20px 24px;color:#fff;">'
            . '<div style="font-size:11px;font-weight:900;letter-spacing:.15em;text-transform:uppercase;opacity:.9;">NADO Auto Enchère</div>'
            . '<div style="font-size:20px;font-weight:900;margin-top:4px;">Une enchère ouvre dans moins de 24 h</div>'
            . '</div>'
            . '<div style="padding:24px;">'
            . '<p style="color:#334155;font-size:14px;line-height:1.6;margin:0 0 16px;">Bonjour,<br>Une nouvelle enchère réservée aux clients <strong>Flotte &amp; Enchère</strong> ouvre bientôt. Voici tous les détails du véhicule :</p>'
            . '<table style="width:100%;border-collapse:collapse;margin-bottom:20px;">' . $rowsHtml . '</table>'
            . '<a href="' . e($url) . '" style="display:inline-block;background:#1459DD;color:#fff;text-decoration:none;font-weight:900;font-size:14px;padding:14px 24px;border-radius:12px;">Voir l\'enchère</a>'
            . '<p style="color:#94a3b8;font-size:12px;line-height:1.6;margin:20px 0 0;">Vous recevez cet email car votre compte NADO est approuvé pour l\'Auto Enchère.</p>'
            . '</div></div></div></body></html>';
    }


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

            Log::info('Notification validation enchère envoyée à contact@nado.ma (client #' . $client->id . ')');
        } catch (\Throwable $e) {
            Log::error('Notification validation enchère échouée: ' . $e->getMessage());
        }
    }
}
