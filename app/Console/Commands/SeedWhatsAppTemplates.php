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
                            {--lang= : Restreint à une langue (fr|ar)}
                            {--no-purge : N\'essaie pas de recréer les REJECTED (saute-les simplement)}
                            {--purge-wait=130 : Secondes d\'attente une fois les REJECTED supprimés avant recréation}';

    protected $description = 'Soumet la bibliothèque FR + AR de modèles WhatsApp à Meta (idempotent).';

    /**
     * Chaque entrée : name => [category, fr, ar]. "fr" et "ar" sont le corps du message.
     * La catégorie doit matcher ce qu'accepte Meta : UTILITY ou MARKETING.
     */
    /** Opt-out footer appliqué à tous les modèles MARKETING (recommandé Meta). */
    private const FOOTER_FR = "Repondez STOP pour ne plus recevoir ces messages.";
    private const FOOTER_AR = "أرسلوا STOP لإيقاف هذه الرسائل.";

    private const TEMPLATES = [
        // ─────────────────── UTILITY ───────────────────
        'annonce_recue' => [
            'category' => 'UTILITY',
            'fr' => "Bonjour {{1}}, nous confirmons la bonne reception de votre annonce pour le vehicule {{2}} sur notre plateforme. Notre equipe la validera sous 24 heures et vous appellera au numero {{3}} pour confirmer la mise en ligne.",
            'ar' => "مرحبا {{1}}، نؤكد توصلنا بإعلانكم الخاص بالسيارة {{2}} على منصتنا. سيقوم فريقنا بالتحقق منه خلال 24 ساعة وسيتصل بكم على الرقم {{3}} لتأكيد النشر.",
        ],
        'annonce_publiee' => [
            'category' => 'UTILITY',
            'fr' => "Bonjour {{1}}, votre annonce pour le vehicule {{2}} est desormais en ligne sur autoenchere.ma. Vous pouvez la consulter dans votre espace personnel ou en recherchant la reference {{3}} sur le site.",
            'ar' => "مرحبا {{1}}، تم نشر إعلانكم الخاص بالسيارة {{2}} على موقع autoenchere.ma. يمكنكم الاطلاع عليه من خلال حسابكم الشخصي أو بالبحث عن المرجع {{3}} على الموقع.",
        ],
        'demande_contact' => [
            'category' => 'UTILITY',
            'fr' => "Bonjour {{1}}, un acheteur interesse par votre vehicule {{2}} vient de vous ecrire. Son message est le suivant : {{3}}. Vous pouvez le contacter directement au numero {{4}} pour repondre a sa demande.",
            'ar' => "مرحبا {{1}}، تلقيتم رسالة من مشتر مهتم بسيارتكم {{2}}. محتوى الرسالة هو كالتالي: {{3}}. يمكنكم الاتصال به مباشرة على الرقم {{4}} للرد على طلبه.",
        ],
        'rdv_essai' => [
            'category' => 'UTILITY',
            'fr' => "Bonjour {{1}}, votre rendez-vous pour l'essai du vehicule {{2}} est confirme pour le {{3}} a {{4}}. Adresse du rendez-vous : {{5}}. Pour annuler ou modifier, repondez simplement a ce message.",
            'ar' => "مرحبا {{1}}، تم تأكيد موعدكم لتجربة السيارة {{2}} يوم {{3}} على الساعة {{4}}. عنوان الموعد هو: {{5}}. للإلغاء أو التعديل، يرجى الرد على هذه الرسالة.",
        ],
        'reprise_estimation' => [
            'category' => 'UTILITY',
            'fr' => "Bonjour {{1}}, l'estimation de reprise de votre vehicule {{2}} s'eleve a {{3}} MAD selon les criteres soumis. Cette offre est valable 7 jours. Repondez a ce message pour planifier une inspection avec notre equipe.",
            'ar' => "مرحبا {{1}}، قيمة تقدير استبدال سيارتكم {{2}} هي {{3}} درهم وفقا للمعطيات المقدمة. هذا العرض صالح لمدة 7 أيام. يرجى الرد على هذه الرسالة لتحديد موعد المعاينة مع فريقنا.",
        ],
        'financement_simulation' => [
            'category' => 'UTILITY',
            'fr' => "Bonjour {{1}}, votre simulation de financement pour le vehicule {{2}} s'eleve a {{3}} MAD par mois sur une duree de {{4}} mois. Un conseiller vous rappellera au numero {{5}} dans les prochaines 48 heures.",
            'ar' => "مرحبا {{1}}، محاكاة التمويل الخاصة بالسيارة {{2}} تبلغ {{3}} درهم شهريا على مدى {{4}} شهرا. سيتصل بكم أحد المستشارين على الرقم {{5}} خلال 48 ساعة القادمة.",
        ],
        'enchere_acces_approuve' => [
            'category' => 'UTILITY',
            'fr' => "Bonjour {{1}}, votre acces a la plateforme Auto-Enchere est maintenant actif. Vous pouvez vous connecter avec votre identifiant habituel. Les prochaines encheres demarrent le {{2}} a 10 heures du matin.",
            'ar' => "مرحبا {{1}}، تم تفعيل ولوجكم إلى منصة Auto-Enchère. يمكنكم تسجيل الدخول بمعرفكم المعتاد. تنطلق المزادات القادمة يوم {{2}} على الساعة 10 صباحا.",
        ],
        'enchere_rappel' => [
            'category' => 'UTILITY',
            'fr' => "Bonjour {{1}}, votre enchere {{2}} demarre dans une heure a {{3}}. Vous retrouverez {{4}} vehicules disponibles sur la session. Connectez-vous des maintenant pour participer a l'enchere.",
            'ar' => "مرحبا {{1}}، ينطلق مزادكم {{2}} بعد ساعة على الساعة {{3}}. ستجدون {{4}} سيارة متوفرة خلال الجلسة. سجلوا الدخول الآن للمشاركة في المزاد.",
        ],
        'enchere_gagnee' => [
            'category' => 'UTILITY',
            'fr' => "Bonjour {{1}}, vous avez remporte l'enchere pour le vehicule {{2}} au montant de {{3}} MAD. Veuillez proceder au paiement sous 48 heures depuis votre espace personnel pour finaliser la transaction.",
            'ar' => "مرحبا {{1}}، لقد ربحتم المزاد الخاص بالسيارة {{2}} بمبلغ {{3}} درهم. يرجى إتمام الأداء خلال 48 ساعة من حسابكم الشخصي لإنهاء المعاملة.",
        ],
        'enchere_perdue' => [
            'category' => 'UTILITY',
            'fr' => "Bonjour {{1}}, votre offre sur le vehicule {{2}} a ete depassee. Le prix actuel est de {{3}} MAD. Vous pouvez reprendre l'enchere depuis votre espace personnel pour placer une nouvelle offre.",
            'ar' => "مرحبا {{1}}، تم تجاوز عرضكم على السيارة {{2}}. السعر الحالي هو {{3}} درهم. يمكنكم العودة إلى المزاد من حسابكم الشخصي لتقديم عرض جديد.",
        ],
        'paiement_recu' => [
            'category' => 'UTILITY',
            'fr' => "Bonjour {{1}}, nous avons bien recu votre paiement de {{2}} MAD pour le vehicule {{3}}. La livraison de votre vehicule est prevue le {{4}} dans notre centre, merci de votre confiance.",
            'ar' => "مرحبا {{1}}، لقد توصلنا بأدائكم البالغ {{2}} درهم مقابل السيارة {{3}}. من المنتظر تسليم سيارتكم يوم {{4}} في مركزنا، شكرا لثقتكم.",
        ],
        'cartegrise_verifiee' => [
            'category' => 'UTILITY',
            'fr' => "Bonjour {{1}}, les documents de votre vehicule {{2}} ont ete verifies par notre equipe. Votre annonce passe maintenant en validation finale et sera publiee sous 24 heures sur notre plateforme.",
            'ar' => "مرحبا {{1}}، تم التحقق من وثائق سيارتكم {{2}} من طرف فريقنا. سيدخل إعلانكم مرحلة المصادقة النهائية وسيتم نشره خلال 24 ساعة على منصتنا.",
        ],
        'compte_approuve' => [
            'category' => 'UTILITY',
            'fr' => "Bonjour {{1}}, votre compte sur autoenchere.ma est maintenant valide. Vous pouvez vous connecter avec vos identifiants habituels et publier votre premiere annonce sur notre plateforme.",
            'ar' => "مرحبا {{1}}، تم تفعيل حسابكم على موقع autoenchere.ma. يمكنكم الآن تسجيل الدخول بمعرفاتكم المعتادة ونشر إعلانكم الأول على منصتنا.",
        ],

        // ─────────────────── MARKETING ───────────────────
        'nouveau_modele' => [
            'category' => 'MARKETING',
            'fr' => "Bonjour {{1}}, un nouveau vehicule {{2}} au prix de {{3}} MAD vient d'etre publie sur autoenchere.ma. Rendez-vous sur notre site pour decouvrir toutes les caracteristiques avant qu'il ne soit vendu.",
            'ar' => "مرحبا {{1}}، تم نشر سيارة {{2}} جديدة بسعر {{3}} درهم على موقع autoenchere.ma. تفضلوا إلى موقعنا لاكتشاف كافة المواصفات قبل أن يتم بيعها.",
        ],
        'baisse_prix' => [
            'category' => 'MARKETING',
            'fr' => "Bonjour {{1}}, le vehicule {{2}} que vous suiviez est passe de {{3}} a {{4}} MAD sur notre plateforme. Consultez l'annonce complete sur autoenchere.ma avant que le prix ne remonte.",
            'ar' => "مرحبا {{1}}، انخفض سعر السيارة {{2}} التي كنتم تتابعونها من {{3}} إلى {{4}} درهم على موقعنا. اطلعوا على الإعلان الكامل على autoenchere.ma قبل أن يرتفع السعر من جديد.",
        ],
        'enchere_decouverte' => [
            'category' => 'MARKETING',
            'fr' => "Bonjour {{1}}, notre prochaine session d'encheres {{2}} aura lieu le {{3}} et proposera {{4}} vehicules selectionnes a partir de {{5}} MAD. Inscrivez-vous sur autoenchere.ma pour participer a cette session.",
            'ar' => "مرحبا {{1}}، ستنعقد جلسة مزادنا القادمة {{2}} يوم {{3}} وستضم {{4}} سيارة مختارة بأسعار تنطلق من {{5}} درهم. سجلوا على autoenchere.ma للمشاركة في هذه الجلسة.",
        ],
        'relance_vente' => [
            'category' => 'MARKETING',
            'fr' => "Bonjour {{1}}, votre annonce pour le vehicule {{2}} a recu {{3}} vues sur autoenchere.ma. Nous vous conseillons d'ajouter davantage de photos ou de baisser le prix pour attirer plus d'acheteurs interesses.",
            'ar' => "مرحبا {{1}}، توصل إعلان بيع سيارتكم {{2}} بـ {{3}} مشاهدة على موقع autoenchere.ma. ننصحكم بإضافة المزيد من الصور أو تخفيض السعر لجذب المزيد من المشترين المهتمين.",
        ],
    ];

    public function handle(WhatsAppService $whatsapp): int
    {
        $dry      = (bool) $this->option('dry');
        $only     = array_filter(array_map('trim', explode(',', (string) $this->option('only'))));
        $lang     = strtolower((string) $this->option('lang'));
        $noPurge  = (bool) $this->option('no-purge');
        $purgeWait = max(60, (int) $this->option('purge-wait'));

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

        // Phase 1 : supprimer en lot les REJECTED ciblés (un seul DELETE par nom,
        // Meta supprime les deux langues) puis attendre une fois pour toutes que
        // Meta libère les slots. Évite 34 × 65 s de pauses séquentielles.
        if (! $dry && ! $noPurge) {
            $toPurge = [];
            foreach (self::TEMPLATES as $name => $tpl) {
                if ($only && ! in_array($name, $only, true)) continue;
                foreach (['fr', 'ar'] as $langCode) {
                    if ($lang && $lang !== $langCode) continue;
                    $k = strtolower($name . '|' . $langCode);
                    if (($statusByKey[$k] ?? null) === 'REJECTED') {
                        $toPurge[$name] = true;
                    }
                }
            }
            if ($toPurge) {
                $this->line('Phase 1 : suppression de ' . count($toPurge) . ' modèles REJECTED…');
                foreach (array_keys($toPurge) as $name) {
                    $del = $whatsapp->deleteTemplate($name);
                    if ($del['ok']) {
                        $this->line("  [purge] $name supprimé");
                        foreach ($statusByKey as $kk => $_) {
                            if (str_starts_with($kk, strtolower($name) . '|')) {
                                unset($statusByKey[$kk]);
                            }
                        }
                    } else {
                        $this->error("  [purge-fail] $name : " . ($del['error'] ?? 'erreur inconnue'));
                    }
                    usleep(300 * 1000);
                }
                $this->line("Attente {$purgeWait}s pour que Meta libère les slots…");
                sleep($purgeWait);
            }
        }

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

                // Les REJECTED ont déjà été supprimés en phase 1 (sauf --no-purge),
                // donc ici on ne devrait plus en rencontrer sauf si --no-purge est passé.
                if ($status === 'REJECTED') {
                    $this->line("  [skip] $name · $langCode — statut REJECTED (utilisez une exécution sans --no-purge pour le resoumettre)");
                    $skipped++;
                    continue;
                }

                // Les modèles MARKETING reçoivent un footer d'opt-out (recommandé Meta).
                $footer = null;
                if ($tpl['category'] === 'MARKETING') {
                    $footer = $langCode === 'ar' ? self::FOOTER_AR : self::FOOTER_FR;
                }

                $res = $whatsapp->createTemplate([
                    'name'     => $name,
                    'category' => $tpl['category'],
                    'language' => $langCode,
                    'body'     => $tpl[$langCode],
                    'footer'   => $footer,
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
