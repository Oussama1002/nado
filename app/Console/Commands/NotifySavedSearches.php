<?php

namespace App\Console\Commands;

use App\Models\Client;
use App\Models\SavedSearch;
use App\Support\AnnonceFilter;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Alerte « nouveaux véhicules » pour les recherches sauvegardées
 * (discovery.saved_searches). À planifier TOUTES LES HEURES (voir Kernel).
 *
 * Pour chaque recherche avec notify_new, cherche les annonces créées depuis
 * last_notified_at qui matchent via AnnonceFilter (même logique que le catalogue),
 * envoie un digest email, puis met last_notified_at à jour. Aucun match → rien.
 *
 * Cron cPanel (déjà posé si schedule:run tourne) :
 *   * * * * * php /home2/autoench/nado/artisan schedule:run >> /dev/null 2>&1
 *
 * Test : php artisan nado:notify-saved-searches
 */
class NotifySavedSearches extends Command
{
    protected $signature = 'nado:notify-saved-searches {--force : Ignorer le flag discovery.saved_searches}';

    protected $description = 'Envoie les alertes « nouveaux véhicules » des recherches sauvegardées';

    public function handle(): int
    {
        if (! $this->option('force') && ! feature_on('discovery.saved_searches')) {
            $this->info('discovery.saved_searches désactivé — rien à faire.');
            return self::SUCCESS;
        }

        $cap = (int) settings('discovery.saved_search_max_matches', 12);
        $cap = $cap > 0 ? $cap : 12;

        $searches = SavedSearch::where('notify_new', true)->get();
        $this->line("Recherches à vérifier : {$searches->count()}");

        $totalSent = 0;
        foreach ($searches as $search) {
            $client = Client::find($search->client_id);
            if (! $client || ! $client->email) {
                continue;
            }

            // Nouveaux depuis la dernière notif (sinon depuis la création de la recherche).
            $since = $search->last_notified_at ?: $search->created_at;

            $matches = AnnonceFilter::query((array) $search->params)
                ->when($since, fn ($q) => $q->where('created_at', '>', $since))
                ->orderByDesc('id')
                ->limit($cap + 1)
                ->get();

            if ($matches->isEmpty()) {
                // On avance quand même le curseur pour ne pas re-scanner le passé.
                $search->forceFill(['last_notified_at' => now()])->save();
                continue;
            }

            if ($this->sendDigest($client, $search, $matches, $cap)) {
                $totalSent++;
            }
            $search->forceFill(['last_notified_at' => now()])->save();
        }

        $this->info("Terminé : {$totalSent} email(s) envoyé(s).");
        return self::SUCCESS;
    }

    private function sendDigest(Client $client, SavedSearch $search, $matches, int $cap): bool
    {
        $shown = $matches->take($cap);
        $label = $search->displayLabel();

        $rows = '';
        foreach ($shown as $ad) {
            $brand = $ad->brand->name ?? $ad->mark;
            $model = $ad->modeleRelation->name ?? $ad->modele;
            $url   = route('achat.detail', ['marque' => str_replace(' ', '', $ad->mark), 'modele' => str_replace(' ', '', $ad->modele), 'id' => $ad->id]);
            $price = number_format($ad->prix ?? 0, 0, ',', ' ') . ' MAD';
            $rows .= '<tr>'
                . '<td style="padding:10px 12px;border-bottom:1px solid #eef2f7;color:#0f172a;font-weight:700;font-size:14px;">' . e(trim($brand . ' ' . $model)) . ' <span style="color:#64748b;font-weight:500;">· ' . e($ad->year) . '</span></td>'
                . '<td style="padding:10px 12px;border-bottom:1px solid #eef2f7;color:#1459DD;font-weight:800;font-size:14px;white-space:nowrap;">' . e($price) . '</td>'
                . '<td style="padding:10px 12px;border-bottom:1px solid #eef2f7;"><a href="' . e($url) . '" style="color:#F37021;font-weight:800;font-size:13px;text-decoration:none;">' . e(__('messages.discovery.view_vehicle')) . '</a></td>'
                . '</tr>';
        }

        $catalogUrl = route('achat.index', (array) $search->params);
        $subject = __('messages.discovery.digest_subject', ['label' => $label]);

        $html = '<!doctype html><html><body style="margin:0;background:#f1f5f9;font-family:Arial,Helvetica,sans-serif;">'
            . '<div style="max-width:600px;margin:0 auto;padding:24px;">'
            . '<div style="background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 10px 30px -12px rgba(15,23,42,.25);">'
            . '<div style="background:#1459DD;padding:20px 24px;color:#fff;">'
            . '<div style="font-size:11px;font-weight:900;letter-spacing:.15em;text-transform:uppercase;opacity:.9;">NADO.ma</div>'
            . '<div style="font-size:19px;font-weight:900;margin-top:4px;">' . e($label) . '</div>'
            . '</div>'
            . '<div style="padding:24px;">'
            . '<p style="color:#334155;font-size:14px;line-height:1.6;margin:0 0 16px;">' . e(__('messages.discovery.digest_intro')) . '</p>'
            . '<table style="width:100%;border-collapse:collapse;margin-bottom:20px;">' . $rows . '</table>'
            . '<a href="' . e($catalogUrl) . '" style="display:inline-block;background:#F37021;color:#fff;text-decoration:none;font-weight:900;font-size:14px;padding:13px 22px;border-radius:12px;">' . e(__('messages.discovery.view_results')) . '</a>'
            . '</div></div></div></body></html>';

        try {
            Mail::html($html, function ($m) use ($client, $subject) {
                $m->to($client->email, $client->name ?: '')->subject($subject);
            });
            return true;
        } catch (\Throwable $e) {
            Log::error('Digest recherche sauvegardée #' . $search->id . ' échoué : ' . $e->getMessage());
            return false;
        }
    }
}
