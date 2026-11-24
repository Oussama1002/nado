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
            'fr' => "Bonjour {{1}}, nous avons bien reçu votre annonce pour votre {{2}}. Notre équipe la validera sous 24h et vous recontactera au {{3}}. Merci — autoenchere.ma",
            'ar' => "مرحبا {{1}}، لقد توصلنا بإعلانكم الخاص بـ {{2}}. سيقوم فريقنا بالتحقق منه خلال 24 ساعة والاتصال بكم على الرقم {{3}}. شكرا لثقتكم — autoenchere.ma",
        ],
        'annonce_publiee' => [
            'category' => 'UTILITY',
            'fr' => "Bonjour {{1}}, votre annonce {{2}} est en ligne sur autoenchere.ma. Lien : {{3}}. Bonnes ventes !",
            'ar' => "مرحبا {{1}}، تم نشر إعلانكم {{2}} على autoenchere.ma. الرابط: {{3}}. نتمنى لكم بيعا موفقا.",
        ],
        'demande_contact' => [
            'category' => 'UTILITY',
            'fr' => "Bonjour {{1}}, un acheteur est intéressé par votre {{2}}. Son message : \"{{3}}\". Répondez-lui au {{4}}.",
            'ar' => "مرحبا {{1}}، هناك مشتر مهتم بـ {{2}}. رسالته: \"{{3}}\". يمكنكم الرد عليه عبر الرقم {{4}}.",
        ],
        'rdv_essai' => [
            'category' => 'UTILITY',
            'fr' => "Bonjour {{1}}, votre essai de la {{2}} est confirmé le {{3}} à {{4}}. Adresse : {{5}}. Pour annuler, répondez à ce message.",
            'ar' => "مرحبا {{1}}، تم تأكيد موعد تجربة {{2}} يوم {{3}} على الساعة {{4}}. العنوان: {{5}}. للإلغاء، يرجى الرد على هذه الرسالة.",
        ],
        'reprise_estimation' => [
            'category' => 'UTILITY',
            'fr' => "Bonjour {{1}}, l'estimation de reprise de votre {{2}} est de {{3}} MAD. Offre valable 7 jours. Répondez pour planifier l'inspection.",
            'ar' => "مرحبا {{1}}، قيمة تقدير استبدال سيارتكم {{2}} هي {{3}} درهم. العرض صالح لمدة 7 أيام. ردوا على هذه الرسالة لتحديد موعد المعاينة.",
        ],
        'financement_simulation' => [
            'category' => 'UTILITY',
            'fr' => "Bonjour {{1}}, votre simulation de financement pour la {{2}} : {{3}} MAD/mois sur {{4}} mois. Un conseiller vous rappellera au {{5}}.",
            'ar' => "مرحبا {{1}}، محاكاة التمويل الخاصة بـ {{2}}: {{3}} درهم شهريا على مدى {{4}} شهرا. سيتصل بكم أحد المستشارين على الرقم {{5}}.",
        ],
        'enchere_acces_approuve' => [
            'category' => 'UTILITY',
            'fr' => "Bonjour {{1}}, votre accès Auto-Enchère est activé. Connectez-vous : {{2}}. Les prochaines enchères démarrent le {{3}}.",
            'ar' => "مرحبا {{1}}، تم تفعيل ولوجكم إلى منصة Auto-Enchère. سجلوا الدخول عبر: {{2}}. تنطلق المزادات القادمة يوم {{3}}.",
        ],
        'enchere_rappel' => [
            'category' => 'UTILITY',
            'fr' => "Bonjour {{1}}, l'enchère {{2}} démarre dans 1h ({{3}}). {{4}} véhicules disponibles. Rejoindre : {{5}}",
            'ar' => "مرحبا {{1}}، ينطلق المزاد {{2}} بعد ساعة ({{3}}). {{4}} سيارة متوفرة. للمشاركة: {{5}}",
        ],
        'enchere_gagnee' => [
            'category' => 'UTILITY',
            'fr' => "Félicitations {{1}} ! Vous avez remporté le {{2}} pour {{3}} MAD. Procédez au paiement sous 48h : {{4}}",
            'ar' => "مبروك {{1}}! لقد ربحتم مزاد {{2}} بمبلغ {{3}} درهم. يرجى إتمام الأداء خلال 48 ساعة عبر: {{4}}",
        ],
        'enchere_perdue' => [
            'category' => 'UTILITY',
            'fr' => "Bonjour {{1}}, vous avez été surenchéri sur {{2}}. Prix actuel : {{3}} MAD. Reprendre l'enchère : {{4}}",
            'ar' => "مرحبا {{1}}، تم تجاوز عرضكم في المزاد {{2}}. السعر الحالي: {{3}} درهم. للعودة إلى المزاد: {{4}}",
        ],
        'paiement_recu' => [
            'category' => 'UTILITY',
            'fr' => "Bonjour {{1}}, nous avons bien reçu votre paiement de {{2}} MAD pour la {{3}}. Livraison prévue le {{4}}.",
            'ar' => "مرحبا {{1}}، لقد توصلنا بأدائكم البالغ {{2}} درهم مقابل {{3}}. من المنتظر تسليم السيارة يوم {{4}}.",
        ],
        'cartegrise_verifiee' => [
            'category' => 'UTILITY',
            'fr' => "Bonjour {{1}}, les documents de votre {{2}} ont été vérifiés. Votre annonce passe en validation finale.",
            'ar' => "مرحبا {{1}}، تم التحقق من وثائق سيارتكم {{2}}. سيدخل إعلانكم مرحلة المصادقة النهائية.",
        ],
        'compte_approuve' => [
            'category' => 'UTILITY',
            'fr' => "Bonjour {{1}}, votre compte sur autoenchere.ma est validé. Vous pouvez maintenant vous connecter : {{2}}",
            'ar' => "مرحبا {{1}}، تم تفعيل حسابكم على autoenchere.ma. يمكنكم الآن تسجيل الدخول عبر: {{2}}",
        ],

        // ─────────────────── MARKETING ───────────────────
        'nouveau_modele' => [
            'category' => 'MARKETING',
            'fr' => "Bonjour {{1}}, une nouvelle {{2}} à {{3}} MAD vient d'être publiée sur autoenchere.ma. La voir : {{4}}",
            'ar' => "مرحبا {{1}}، تم نشر {{2}} جديدة بسعر {{3}} درهم على autoenchere.ma. للاطلاع: {{4}}",
        ],
        'baisse_prix' => [
            'category' => 'MARKETING',
            'fr' => "Bonjour {{1}}, la {{2}} que vous suiviez est passée de {{3}} à {{4}} MAD. Voir : {{5}}",
            'ar' => "مرحبا {{1}}، انخفض سعر {{2}} التي تتابعونها من {{3}} إلى {{4}} درهم. للاطلاع: {{5}}",
        ],
        'enchere_decouverte' => [
            'category' => 'MARKETING',
            'fr' => "Bonjour {{1}}, enchère {{2}} le {{3}} : {{4}} véhicules à partir de {{5}} MAD. S'inscrire : {{6}}",
            'ar' => "مرحبا {{1}}، مزاد {{2}} يوم {{3}}: {{4}} سيارة انطلاقا من {{5}} درهم. للتسجيل: {{6}}",
        ],
        'relance_vente' => [
            'category' => 'MARKETING',
            'fr' => "Bonjour {{1}}, votre annonce {{2}} a reçu {{3}} vues. Ajoutez des photos ou baissez le prix pour attirer plus d'acheteurs : {{4}}",
            'ar' => "مرحبا {{1}}، توصل إعلانكم {{2}} بـ {{3}} مشاهدة. أضيفوا صورا أو خفضوا السعر لجذب المزيد من المشترين: {{4}}",
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
        $seen = collect($existing['templates'])
            ->map(fn ($t) => strtolower(($t['name'] ?? '') . '|' . ($t['language'] ?? '')))
            ->flip();
        $this->line('Déjà présents : ' . $seen->count());

        $planned = 0; $skipped = 0; $created = 0; $failed = 0;

        foreach (self::TEMPLATES as $name => $tpl) {
            if ($only && ! in_array($name, $only, true)) continue;

            foreach (['fr', 'ar'] as $langCode) {
                if ($lang && $lang !== $langCode) continue;

                $planned++;
                $key = strtolower($name . '|' . $langCode);
                if ($seen->has($key)) {
                    $this->line("  [skip] $name · $langCode — déjà sur Meta");
                    $skipped++;
                    continue;
                }

                if ($dry) {
                    $this->line("  [dry ] $name · $langCode · {$tpl['category']}");
                    continue;
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
