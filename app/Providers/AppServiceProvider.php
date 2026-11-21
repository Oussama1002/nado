<?php

namespace App\Providers;

use App\Models\Setting;
use App\Observers\SettingObserver;
use App\Services\WhatsAppService;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Charge les helpers globaux settings()/feature_on() même sans
        // "composer dump-autoload" (certains hébergements n'ont pas composer).
        // Les fonctions sont protégées par function_exists → pas de double
        // définition si l'autoload composer les a déjà chargées.
        $helpers = base_path('app/helpers.php');
        if (is_file($helpers)) {
            require_once $helpers;
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(125);

        // Journalise chaque changement de configuration (doc 02 — §6.bis.4).
        Setting::observe(SettingObserver::class);

        // Mirror every outgoing mail to the WhatsApp admin group / numbers.
        // Gated by feature_on('whatsapp.mirror_emails'); any failure (no
        // credentials, HTTP error, mis-parsed body) is swallowed so the
        // original mail still goes out.
        Event::listen(MessageSending::class, function (MessageSending $event) {
            try {
                if (function_exists('feature_on') && ! feature_on('whatsapp.mirror_emails')) {
                    return;
                }
                $symfonyMsg = $event->message; // Symfony\Component\Mime\Email
                $subject    = (string) $symfonyMsg->getSubject();
                $recipients = array_map(fn ($a) => $a->getAddress(), $symfonyMsg->getTo() ?: []);
                $body       = $symfonyMsg->getTextBody() ?: strip_tags((string) $symfonyMsg->getHtmlBody());
                $body       = trim(preg_replace("/\n{3,}/", "\n\n", (string) $body));
                if (mb_strlen($body) > 1200) {
                    $body = mb_substr($body, 0, 1200) . '…';
                }

                $lines = [
                    '📧 *' . ($subject !== '' ? $subject : 'Nouveau message NADO') . '*',
                ];
                if (! empty($recipients)) {
                    $lines[] = '📬 ' . implode(', ', $recipients);
                }
                $lines[] = '';
                $lines[] = $body !== '' ? $body : '(aucun contenu texte)';

                app(WhatsAppService::class)
                    ->broadcastToAdmins(implode("\n", $lines));
            } catch (\Throwable $e) {
                Log::warning('WhatsApp mirror (mail) failed: ' . $e->getMessage());
            }
        });
    }
}
