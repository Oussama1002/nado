<?php

namespace App\Console\Commands;

use App\Services\WhatsAppService;
use Illuminate\Console\Command;

/**
 * Soumet à Meta la bibliothèque complète de modèles WhatsApp FR + AR
 * décrite dans whatsapp-templates.md. Idempotent : un modèle (nom + langue)
 * déjà présent sur le WABA est sauté.
 *
 * Usage :
 *   php artisan whatsapp:seed-templates          # soumet tout
 *   php artisan whatsapp:seed-templates --dry    # liste sans soumettre
 *   php artisan whatsapp:seed-templates --only=annonce_recue,annonce_publiee
 *   php artisan whatsapp:seed-templates --lang=fr   # seulement FR (ou ar)
 */
class SeedWhatsAppTemplates extends Command
{
    protected $signature = 'whatsapp:seed-templates
                            {--dry : Affiche ce qui serait soumis sans appeler Meta}
                            {--only= : Liste CSV de noms à cibler}
                            {--lang= : Restreint à une langue (fr|ar)}';

    protected $description = 'Soumet la bibliothèque FR + AR de modèles WhatsApp à Meta (idempotent).';

    /**
     * Chaque entrée : name => [category, fr, ar]. "fr" et "ar" sont le corps du message.
     * La catégorie doit matcher ce qu'accepte Meta : UTILITY ou MARKETING.
     */
    private const TEMPLATES = [
        // ─────────────────── UTILITY ───────────────────
        'annonce_recue' => [
            'category' => 'UTILITY',
            'fr' => "Bonjour {{1}}, nous avons bien reçu votre annonce pour votre véhicule {{2}} sur autoenchere.ma. Notre équipe la validera sous 24 heures et vous recontactera au numéro {{3}} pour confirmer la mise en ligne.",
            'ar' => "مرحبا {{1}}، لقد توصلنا بإعلانكم الخاص بالسيارة {{2}} على موقع autoenchere.ma. سيقوم فريقنا بالتحقق منه خلال 24 ساعة والاتصال بكم على الرقم {{3}} لتأكيد النشر.",
        ],
        'annonce_publiee' => [
            'category' => 'UTILITY',
            'fr' => "Bonjour {{1}}, votre annonce de vente pour le véhicule {{2}} est désormais en ligne sur autoenchere.ma. Vous pouvez la consulter à l'adresse suivante : {{3}} pour vérifier son contenu.",
            'ar' => "مرحبا {{1}}، تم نشر إعلان بيع السيارة {{2}} على موقع autoenchere.ma. يمكنكم الاطلاع عليه من خلال الرابط التالي: {{3}} للتحقق من محتواه.",
        ],
        'demande_contact' => [
            'category' => 'UTILITY',
            'fr' => "Bonjour {{1}}, un acheteur est intéressé par votre véhicule {{2}}. Voici son message : \"{{3}}\". Vous pouvez le contacter directement au numéro {{4}} pour répondre à sa demande.",
            'ar' => "مرحبا {{1}}، هناك مشتر مهتم بسيارتكم {{2}}. إليكم رسالته: \"{{3}}\". يمكنكم الاتصال به مباشرة على الرقم {{4}} للرد على طلبه.",
        ],
        'rdv_essai' => [
            'category' => 'UTILITY',
            'fr' => "Bonjour {{1}}, votre rendez-vous d'essai pour le véhicule {{2}} est confirmé le {{3}} à {{4}}. Adresse du rendez-vous : {{5}}. Pour annuler ou modifier, répondez à ce message.",
            'ar' => "مرحبا {{1}}، تم تأكيد موعد تجربة السيارة {{2}} يوم {{3}} على الساعة {{4}}. عنوان الموعد: {{5}}. للإلغاء أو التعديل، يرجى الرد على هذه الرسالة.",
        ],
        'reprise_estimation' => [
            'category' => 'UTILITY',
            'fr' => "Bonjour {{1}}, l'estimation de reprise de votre véhicule {{2}} est de {{3}} MAD. Cette offre est valable 7 jours — répondez à ce message pour planifier une inspection avec notre équipe.",
            'ar' => "مرحبا {{1}}، قيمة تقدير استبدال سيارتكم {{2}} هي {{3}} درهم. هذا العرض صالح لمدة 7 أيام — ردوا على هذه الرسالة لتحديد موعد المعاينة مع فريقنا.",
        ],
        'financement_simulation' => [
            'category' => 'UTILITY',
            'fr' => "Bonjour {{1}}, votre simulation de financement pour le véhicule {{2}} s'élève à {{3}} MAD par mois sur {{4}} mois. Un conseiller vous rappellera au numéro {{5}} dans les prochaines 48 heures.",
            'ar' => "مرحبا {{1}}، محاكاة التمويل الخاصة بالسيارة {{2}} تبلغ {{3}} درهم شهريا على مدى {{4}} شهرا. سيتصل بكم أحد المستشارين على الرقم {{5}} خلال 48 ساعة القادمة.",
        ],
        'enchere_acces_approuve' => [
            'category' => 'UTILITY',
            'fr' => "Bonjour {{1}}, votre accès à la plateforme Auto-Enchère est activé. Connectez-vous via le lien suivant : {{2}}. Les prochaines enchères démarrent le {{3}} à 10h00.",
            'ar' => "مرحبا {{1}}، تم تفعيل ولوجكم إلى منصة Auto-Enchère. سجلوا الدخول عبر الرابط التالي: {{2}}. تنطلق المزادات القادمة يوم {{3}} على الساعة 10:00 صباحا.",
        ],
        'enchere_rappel' => [
            'category' => 'UTILITY',
            'fr' => "Bonjour {{1}}, votre enchère {{2}} démarre dans une heure à {{3}}. Vous retrouverez {{4}} véhicules disponibles sur la session. Rejoignez-nous sur le lien suivant : {{5}} pour participer.",
            'ar' => "مرحبا {{1}}، ينطلق مزادكم {{2}} بعد ساعة على الساعة {{3}}. ستجدون {{4}} سيارة متوفرة خلال الجلسة. انضموا إلينا عبر الرابط التالي: {{5}} للمشاركة.",
        ],
        'enchere_gagnee' => [
            'category' => 'UTILITY',
            'fr' => "Bonjour {{1}}, vous avez remporté l'enchère pour le véhicule {{2}} au montant de {{3}} MAD. Veuillez procéder au paiement sous 48 heures via le lien suivant : {{4}} pour finaliser la transaction.",
            'ar' => "مرحبا {{1}}، لقد ربحتم المزاد الخاص بالسيارة {{2}} بمبلغ {{3}} درهم. يرجى إتمام الأداء خلال 48 ساعة عبر الرابط التالي: {{4}} لإنهاء المعاملة.",
        ],
        'enchere_perdue' => [
            'category' => 'UTILITY',
            'fr' => "Bonjour {{1}}, vous avez été surenchéri sur le véhicule {{2}}. Le prix actuel est de {{3}} MAD. Vous pouvez reprendre l'enchère via le lien suivant : {{4}} pour placer une nouvelle offre.",
            'ar' => "مرحبا {{1}}، تم تجاوز عرضكم في المزاد الخاص بالسيارة {{2}}. السعر الحالي هو {{3}} درهم. يمكنكم العودة إلى المزاد عبر الرابط التالي: {{4}} لتقديم عرض جديد.",
        ],
        'paiement_recu' => [
            'category' => 'UTILITY',
            'fr' => "Bonjour {{1}}, nous avons bien reçu votre paiement de {{2}} MAD pour le véhicule {{3}}. La livraison de votre véhicule est prévue le {{4}} dans notre centre, merci de votre confiance.",
            'ar' => "مرحبا {{1}}، لقد توصلنا بأدائكم البالغ {{2}} درهم مقابل السيارة {{3}}. من المنتظر تسليم سيارتكم يوم {{4}} في مركزنا، شكرا لثقتكم.",
        ],
        'cartegrise_verifiee' => [
            'category' => 'UTILITY',
            'fr' => "Bonjour {{1}}, les documents de votre véhicule {{2}} ont été vérifiés par notre équipe. Votre annonce passe maintenant en validation finale et sera publiée sous 24 heures.",
            'ar' => "مرحبا {{1}}، تم التحقق من وثائق سيارتكم {{2}} من طرف فريقنا. سيدخل إعلانكم مرحلة المصادقة النهائية وسيتم نشره خلال 24 ساعة.",
        ],
        'compte_approuve' => [
            'category' => 'UTILITY',
            'fr' => "Bonjour {{1}}, votre compte sur autoenchere.ma est validé. Vous pouvez maintenant vous connecter via le lien suivant : {{2}} et publier votre première annonce sur notre plateforme.",
            'ar' => "مرحبا {{1}}، تم تفعيل حسابكم على موقع autoenchere.ma. يمكنكم الآن تسجيل الدخول عبر الرابط التالي: {{2}} ونشر إعلانكم الأول على منصتنا.",
        ],

        // ─────────────────── MARKETING ───────────────────
        'nouveau_modele' => [
            'category' => 'MARKETING',
            'fr' => "Bonjour {{1}}, un nouveau véhicule {{2}} au prix de {{3}} MAD vient d'être publié sur autoenchere.ma. Découvrez-le en détail sur le lien suivant : {{4}} avant qu'il ne soit vendu.",
            'ar' => "مرحبا {{1}}، تم نشر سيارة {{2}} جديدة بسعر {{3}} درهم على موقع autoenchere.ma. اكتشفوا تفاصيلها عبر الرابط التالي: {{4}} قبل أن يتم بيعها.",
        ],
        'baisse_prix' => [
            'category' => 'MARKETING',
            'fr' => "Bonjour {{1}}, le véhicule {{2}} que vous suiviez est passé de {{3}} à {{4}} MAD sur notre plateforme. Consultez l'annonce complète sur le lien suivant : {{5}} avant que le prix ne remonte.",
            'ar' => "مرحبا {{1}}، انخفض سعر السيارة {{2}} التي كنتم تتابعونها من {{3}} إلى {{4}} درهم على موقعنا. اطلعوا على الإعلان الكامل عبر الرابط التالي: {{5}} قبل أن يرتفع السعر من جديد.",
        ],
        'enchere_decouverte' => [
            'category' => 'MARKETING',
            'fr' => "Bonjour {{1}}, notre prochaine session d'enchères {{2}} aura lieu le {{3}} et proposera {{4}} véhicules sélectionnés à partir de {{5}} MAD. Inscrivez-vous dès maintenant sur le lien suivant : {{6}} pour participer.",
            'ar' => "مرحبا {{1}}، ستنعقد جلسة مزادنا القادمة {{2}} يوم {{3}} وستضم {{4}} سيارة مختارة بأسعار تنطلق من {{5}} درهم. سجلوا الآن عبر الرابط التالي: {{6}} للمشاركة.",
        ],
        'relance_vente' => [
            'category' => 'MARKETING',
            'fr' => "Bonjour {{1}}, votre annonce de vente pour le véhicule {{2}} a reçu {{3}} vues sur autoenchere.ma. Nous vous conseillons d'ajouter des photos ou de baisser le prix via ce lien : {{4}} pour attirer plus d'acheteurs.",
            'ar' => "مرحبا {{1}}، توصل إعلان بيع سيارتكم {{2}} بـ {{3}} مشاهدة على موقع autoenchere.ma. ننصحكم بإضافة صور أو تخفيض السعر عبر هذا الرابط: {{4}} لجذب المزيد من المشترين.",
        ],
    ];

    public function handle(WhatsAppService $whatsapp): int
    {
        $dry  = (bool) $this->option('dry');
        $only = array_filter(array_map('trim', explode(',', (string) $this->option('only'))));
        $lang = strtolower((string) $this->option('lang'));

        $this->line('Récupération des modèles déjà présents sur votre WABA…');
        $existing = $whatsapp->listTemplates(500);
        if (! $existing['ok']) {
            $this->error('Impossible de lister les modèles Meta : ' . ($existing['error'] ?? 'erreur inconnue'));
            return self::FAILURE;
        }
        // Index par name|language → status. On saute APPROVED/PENDING, on
        // supprime+recrée REJECTED (le seul moyen de resoumettre).
        $statusByKey = [];
        foreach ($existing['templates'] as $t) {
            $k = strtolower(($t['name'] ?? '') . '|' . ($t['language'] ?? ''));
            $statusByKey[$k] = strtoupper((string) ($t['status'] ?? ''));
        }
        $this->line('Déjà présents : ' . count($statusByKey));

        $planned = 0; $skipped = 0; $created = 0; $failed = 0;

        foreach (self::TEMPLATES as $name => $tpl) {
            if ($only && ! in_array($name, $only, true)) continue;

            foreach (['fr', 'ar'] as $langCode) {
                if ($lang && $lang !== $langCode) continue;

                $planned++;
                $key    = strtolower($name . '|' . $langCode);
                $status = $statusByKey[$key] ?? null;

                if (in_array($status, ['APPROVED', 'PENDING', 'IN_APPEAL'], true)) {
                    $this->line("  [skip] $name · $langCode — statut Meta=$status");
                    $skipped++;
                    continue;
                }

                if ($dry) {
                    $prefix = $status === 'REJECTED' ? '[redo]' : '[dry ]';
                    $this->line("  $prefix $name · $langCode · {$tpl['category']}" . ($status ? " (actuel=$status)" : ''));
                    continue;
                }

                // Supprimer avant de resoumettre s'il est REJECTED (Meta ne permet
                // pas de resoumettre un nom+langue existant, même rejeté). Le DELETE
                // de Meta sur `name` supprime toutes les variantes de langue à la
                // fois, donc on le fait une seule fois : la première langue qui
                // tombe sur REJECTED supprime tout le lot, les suivantes voient un
                // $statusByKey vide et créent normalement.
                if ($status === 'REJECTED') {
                    $del = $whatsapp->deleteTemplate($name);
                    if ($del['ok']) {
                        $this->line("  [purge] $name — ancienne version REJECTED supprimée");
                        // Invalide toutes les entrées pour ce nom (Meta supprime toutes les langues).
                        foreach ($statusByKey as $kk => $_) {
                            if (str_starts_with($kk, strtolower($name) . '|')) {
                                unset($statusByKey[$kk]);
                            }
                        }
                        usleep(300 * 1000);
                    } else {
                        $this->error("  [fail] $name · $langCode : suppression impossible : " . ($del['error'] ?? 'erreur inconnue'));
                        $failed++;
                        continue;
                    }
                }

                $res = $whatsapp->createTemplate([
                    'name'     => $name,
                    'category' => $tpl['category'],
                    'language' => $langCode,
                    'body'     => $tpl[$langCode],
                ]);

                if ($res['ok']) {
                    $this->info("  [ok  ] $name · $langCode · statut=" . ($res['status'] ?? '?'));
                    $created++;
                } else {
                    $this->error("  [fail] $name · $langCode : " . ($res['error'] ?? 'erreur inconnue'));
                    $failed++;
                }

                // Meta rate-limite les créations ; une petite pause évite les 429.
                usleep(400 * 1000);
            }
        }

        $this->newLine();
        $this->line("Planifiés : {$planned}");
        $this->line("Sautés (déjà présents) : {$skipped}");
        $this->line("Créés : {$created}");
        $this->line("Échecs : {$failed}");

        return $failed ? self::FAILURE : self::SUCCESS;
    }
}
