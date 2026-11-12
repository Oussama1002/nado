<?php

namespace App\Console\Commands;

use App\Models\Annonces;
use App\Models\Client;
use App\Models\PriceHistory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Alerte « baisse de prix » aux clients ayant mis le véhicule en favori
 * (discovery.price_alerts). À planifier TOUTES LES HEURES (voir Kernel).
 *
 * Parcourt les lignes price_history non encore notifiées qui sont des baisses,
 * notifie les clients qui ont favorisé l'annonce, puis marque la ligne notifiée.
 *
 * Test : php artisan nado:notify-price-drops
 */
class NotifyPriceDrops extends Command
{
    protected $signature = 'nado:notify-price-drops {--force : Ignorer le flag discovery.price_alerts}';

    protected $description = 'Notifie les clients d\'une baisse de prix sur un véhicule favori';

    public function handle(): int
    {
        if (! $this->option('force') && ! feature_on('discovery.price_alerts')) {
            $this->info('discovery.price_alerts désactivé — rien à faire.');
            return self::SUCCESS;
        }

        // Baisses non notifiées (new_price < old_price).
        $drops = PriceHistory::where('notified', false)
            ->whereColumn('new_price', '<', 'old_price')
            ->orderBy('id')
            ->limit(200)
            ->get();

        $this->line("Baisses à traiter : {$drops->count()}");
        $sent = 0;

        foreach ($drops as $drop) {
            $annonce = Annonces::find($drop->annonce_id);

            // Annonce absente/inactive → on marque traité et on passe.
            if (! $annonce || ! $annonce->active) {
                $drop->forceFill(['notified' => true])->save();
                continue;
            }

            // Clients ayant favorisé cette annonce.
            $clientIds = DB::table('favorites')->where('annonce_id', $drop->annonce_id)->pluck('client_id');
            foreach ($clientIds as $cid) {
                $client = Client::find($cid);
                if ($client && $client->email && $this->sendDrop($client, $annonce, $drop)) {
                    $sent++;
                }
            }

            $drop->forceFill(['notified' => true])->save();
        }

        $this->info("Terminé : {$sent} email(s) envoyé(s).");
        return self::SUCCESS;
    }

    private function sendDrop(Client $client, Annonces $annonce, PriceHistory $drop): bool
    {
        $brand = $annonce->brand->name ?? $annonce->mark;
        $model = $annonce->modeleRelation->name ?? $annonce->modele;
        $label = trim($brand . ' ' . $model) ?: ('#' . $annonce->id);
        $url   = route('achat.detail', ['marque' => str_replace(' ', '', $annonce->mark), 'modele' => str_replace(' ', '', $annonce->modele), 'id' => $annonce->id]);

        $oldP = number_format((float) $drop->old_price, 0, ',', ' ') . ' MAD';
        $newP = number_format((float) $drop->new_price, 0, ',', ' ') . ' MAD';
        $diff = number_format((float) $drop->old_price - (float) $drop->new_price, 0, ',', ' ') . ' MAD';

        $html = '<!doctype html><html><body style="margin:0;background:#f1f5f9;font-family:Arial,Helvetica,sans-serif;">'
            . '<div style="max-width:560px;margin:0 auto;padding:24px;">'
            . '<div style="background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 10px 30px -12px rgba(15,23,42,.25);">'
            . '<div style="background:#16a34a;padding:20px 24px;color:#fff;">'
            . '<div style="font-size:11px;font-weight:900;letter-spacing:.15em;text-transform:uppercase;opacity:.9;">NADO.ma</div>'
            . '<div style="font-size:19px;font-weight:900;margin-top:4px;">' . e(__('messages.discovery.drop_subject')) . '</div>'
            . '</div>'
            . '<div style="padding:24px;">'
            . '<p style="color:#334155;font-size:14px;line-height:1.6;margin:0 0 12px;">' . e(__('messages.discovery.drop_intro')) . '</p>'
            . '<p style="font-size:16px;font-weight:900;color:#0f172a;margin:0 0 6px;">' . e($label) . '</p>'
            . '<p style="font-size:14px;margin:0 0 18px;"><span style="color:#94a3b8;text-decoration:line-through;">' . e($oldP) . '</span> '
            . '&nbsp;<strong style="color:#16a34a;">' . e($newP) . '</strong> <span style="color:#16a34a;font-weight:700;">(−' . e($diff) . ')</span></p>'
            . '<a href="' . e($url) . '" style="display:inline-block;background:#1459DD;color:#fff;text-decoration:none;font-weight:900;font-size:14px;padding:13px 22px;border-radius:12px;">' . e(__('messages.discovery.view_vehicle')) . '</a>'
            . '<p style="color:#94a3b8;font-size:12px;line-height:1.6;margin:20px 0 0;">Vous recevez cet email car ce véhicule est dans vos favoris NADO.</p>'
            . '</div></div></div></body></html>';

        try {
            Mail::html($html, function ($m) use ($client, $label) {
                $m->to($client->email, $client->name ?: '')
                  ->subject('NADO.ma — ' . __('messages.discovery.drop_subject') . ' : ' . $label);
            });
            return true;
        } catch (\Throwable $e) {
            Log::error('Alerte baisse de prix (annonce #' . $annonce->id . ', client #' . $client->id . ') échouée : ' . $e->getMessage());
            return false;
        }
    }
}
