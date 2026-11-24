<?php

namespace App\Console\Commands;

use App\Models\WhatsAppTemplateDraft;
use Illuminate\Console\Command;

/**
 * Peuple la table whatsapp_template_drafts avec la bibliothèque FR + AR
 * complète (17 × 2 = 34 brouillons). Idempotent : un brouillon déjà présent
 * (même name + language) est mis à jour, pas dupliqué.
 *
 * Aucun appel à Meta : chaque brouillon doit ensuite être poussé manuellement
 * depuis /admin/whatsapp/templates via le bouton "Soumettre à Meta".
 *
 * Usage :
 *   php artisan whatsapp:seed-drafts
 *   php artisan whatsapp:seed-drafts --only=compte_approuve,rdv_essai
 */
class SeedWhatsAppDrafts extends Command
{
    protected $signature = 'whatsapp:seed-drafts
                            {--only= : Liste CSV de noms à seeder}
                            {--lang= : Restreint à une langue (fr|ar)}
                            {--replace : Écrase les brouillons existants même si déjà présents}';

    protected $description = 'Peuple la table des brouillons WhatsApp avec la bibliothèque FR + AR.';

    private const FOOTER_FR = "Repondez STOP pour ne plus recevoir ces messages.";
    private const FOOTER_AR = "أرسلوا STOP لإيقاف هذه الرسائل.";

    private const TEMPLATES = [
        'annonce_recue' => [
            'category' => 'UTILITY',
            'fr' => "Annonce recue : nous avons bien enregistre votre {{1}}. Notre equipe la valide sous 24h et vous rappelle au {{2}} depuis autoenchere.ma.",
            'ar' => "توصلنا بإعلانكم: تم تسجيل سيارتكم {{1}} بنجاح. سيتحقق فريقنا منه خلال 24 ساعة وسنتصل بكم على الرقم {{2}} عبر موقع autoenchere.ma.",
        ],
        'annonce_publiee' => [
            'category' => 'UTILITY',
            'fr' => "Annonce en ligne : votre {{1}} est maintenant visible sur autoenchere.ma. Reference de suivi : {{2}}. Modifiez-la depuis votre espace personnel.",
            'ar' => "إعلان منشور: سيارتكم {{1}} أصبحت ظاهرة على موقع autoenchere.ma. مرجع المتابعة: {{2}}. يمكنكم تعديل الإعلان من حسابكم الشخصي.",
        ],
        'demande_contact' => [
            'category' => 'UTILITY',
            'fr' => "Nouvelle demande acheteur sur votre {{1}} : {{2}}. Rappelez-le au {{3}} pour repondre, ou consultez le message complet sur autoenchere.ma.",
            'ar' => "طلب جديد من مشتر مهتم بسيارتكم {{1}}: {{2}}. اتصلوا به على الرقم {{3}} للرد، أو اطلعوا على الرسالة كاملة على موقع autoenchere.ma.",
        ],
        'rdv_essai' => [
            'category' => 'UTILITY',
            'fr' => "Confirmation : votre essai de la {{1}} est fixe le {{2}}. Pour annuler ou deplacer le rendez-vous, repondez a ce message sur autoenchere.ma.",
            'ar' => "تأكيد موعد: تجربة السيارة {{1}} محددة يوم {{2}}. للإلغاء أو التعديل، ردوا على هذه الرسالة على موقع autoenchere.ma.",
        ],
        'reprise_estimation' => [
            'category' => 'UTILITY',
            'fr' => "Estimation reprise : votre {{1}} est evaluee a {{2}} MAD. Offre valable 7 jours, repondez pour prendre rendez-vous d'inspection sur autoenchere.ma.",
            'ar' => "تقدير الاستبدال: سيارتكم {{1}} مقدرة بـ {{2}} درهم. العرض صالح لمدة 7 أيام، ردوا على هذه الرسالة لتحديد موعد المعاينة على autoenchere.ma.",
        ],
        'financement_simulation' => [
            'category' => 'UTILITY',
            'fr' => "Simulation financement : {{1}} finance a {{2}} MAD par mois sur {{3}} mois. Un conseiller autoenchere.ma vous rappelle sous 48h.",
            'ar' => "محاكاة التمويل: السيارة {{1}} بقيمة {{2}} درهم شهريا على مدى {{3}} شهرا. سيتصل بكم مستشار من موقع autoenchere.ma خلال 48 ساعة.",
        ],
        'enchere_acces_approuve' => [
            'category' => 'UTILITY',
            'fr' => "Acces Auto-Enchere active. Vos identifiants autoenchere.ma fonctionnent des maintenant. Prochaine session : {{1}} a 10h00 du matin.",
            'ar' => "تم تفعيل ولوجكم إلى منصة Auto-Enchère. يمكنكم استعمال معرفاتكم المعتادة على موقع autoenchere.ma. الجلسة القادمة: {{1}} على الساعة 10:00 صباحا.",
        ],
        'enchere_rappel' => [
            'category' => 'UTILITY',
            'fr' => "Rappel enchere : la session {{1}} demarre dans une heure avec {{2}} vehicules. Connectez-vous sur autoenchere.ma pour participer en direct.",
            'ar' => "تذكير بالمزاد: جلسة {{1}} ستنطلق بعد ساعة وتضم {{2}} سيارة. سجلوا الدخول على موقع autoenchere.ma للمشاركة مباشرة.",
        ],
        'enchere_gagnee' => [
            'category' => 'UTILITY',
            'fr' => "Felicitations, vous avez remporte le {{1}} pour {{2}} MAD. Reglez sous 48h depuis votre espace autoenchere.ma pour finaliser l'achat.",
            'ar' => "مبروك، لقد ربحتم السيارة {{1}} بمبلغ {{2}} درهم. يرجى الأداء خلال 48 ساعة من حسابكم على موقع autoenchere.ma لإتمام الشراء.",
        ],
        'enchere_perdue' => [
            'category' => 'UTILITY',
            'fr' => "Offre depassee sur le {{1}}. Prix actuel : {{2}} MAD. Reprenez la main depuis votre espace autoenchere.ma pour placer une nouvelle offre.",
            'ar' => "تم تجاوز عرضكم على السيارة {{1}}. السعر الحالي: {{2}} درهم. يمكنكم تقديم عرض جديد من حسابكم على موقع autoenchere.ma.",
        ],
        'paiement_recu' => [
            'category' => 'UTILITY',
            'fr' => "Paiement confirme : {{1}} MAD recus pour la {{2}}. Livraison prevue le {{3}} dans notre centre, suivi complet sur autoenchere.ma.",
            'ar' => "تم تأكيد الأداء: توصلنا بمبلغ {{1}} درهم مقابل السيارة {{2}}. من المنتظر تسليمها يوم {{3}} في مركزنا، تتبعوا الطلب على موقع autoenchere.ma.",
        ],
        'cartegrise_verifiee' => [
            'category' => 'UTILITY',
            'fr' => "Documents verifies : la carte grise de votre {{1}} a ete validee par notre equipe. L'annonce sera publiee sous 24h sur autoenchere.ma.",
            'ar' => "تم التحقق من الوثائق: صودق على البطاقة الرمادية لسيارتكم {{1}} من طرف فريقنا. سيُنشر الإعلان خلال 24 ساعة على موقع autoenchere.ma.",
        ],
        'compte_approuve' => [
            'category' => 'UTILITY',
            'fr' => "Bienvenue {{1}}, votre compte autoenchere.ma est actif. Connectez-vous avec vos identifiants pour publier votre premiere annonce.",
            'ar' => "مرحبا {{1}}، تم تفعيل حسابكم على موقع autoenchere.ma. سجلوا الدخول بمعرفاتكم المعتادة لنشر إعلانكم الأول.",
        ],

        // ─────────────────── MARKETING ───────────────────
        'nouveau_modele' => [
            'category' => 'MARKETING',
            'fr' => "Nouveau sur autoenchere.ma : une {{1}} proposee a {{2}} MAD. Decouvrez les photos et caracteristiques completes avant qu'elle ne parte.",
            'ar' => "جديد على موقع autoenchere.ma: سيارة {{1}} معروضة بـ {{2}} درهم. اكتشفوا الصور والمواصفات الكاملة قبل أن يتم بيعها.",
        ],
        'baisse_prix' => [
            'category' => 'MARKETING',
            'fr' => "Baisse de prix sur autoenchere.ma : la {{1}} suivie passe de {{2}} a {{3}} MAD. Consultez l'annonce complete avant que le prix ne remonte.",
            'ar' => "انخفاض في السعر على موقع autoenchere.ma: السيارة {{1}} التي تتابعونها انتقلت من {{2}} إلى {{3}} درهم. اطلعوا على الإعلان كاملا قبل أن يرتفع السعر.",
        ],
        'enchere_decouverte' => [
            'category' => 'MARKETING',
            'fr' => "Prochaine enchere autoenchere.ma : le {{1}}, {{2}} vehicules a partir de {{3}} MAD. Inscrivez-vous sur notre site pour participer a la session.",
            'ar' => "مزاد قادم على موقع autoenchere.ma: يوم {{1}}، {{2}} سيارة انطلاقا من {{3}} درهم. سجلوا على الموقع للمشاركة في الجلسة.",
        ],
        'relance_vente' => [
            'category' => 'MARKETING',
            'fr' => "Votre annonce {{1}} totalise {{2}} vues sur autoenchere.ma. Ajoutez des photos ou baissez le prix pour attirer plus d'acheteurs interesses.",
            'ar' => "إعلانكم الخاص بـ {{1}} حقق {{2}} مشاهدة على موقع autoenchere.ma. أضيفوا صورا أو خفضوا السعر لجذب المزيد من المشترين المهتمين.",
        ],
    ];

    public function handle(): int
    {
        $only    = array_filter(array_map('trim', explode(',', (string) $this->option('only'))));
        $lang    = strtolower((string) $this->option('lang'));
        $replace = (bool) $this->option('replace');

        $created = 0; $updated = 0; $skipped = 0;

        foreach (self::TEMPLATES as $name => $tpl) {
            if ($only && ! in_array($name, $only, true)) continue;

            foreach (['fr', 'ar'] as $langCode) {
                if ($lang && $lang !== $langCode) continue;

                $footer = null;
                if ($tpl['category'] === 'MARKETING') {
                    $footer = $langCode === 'ar' ? self::FOOTER_AR : self::FOOTER_FR;
                }

                $existing = WhatsAppTemplateDraft::where('name', $name)
                    ->where('language', $langCode)
                    ->first();

                if ($existing && ! $replace) {
                    $this->line("  [skip] $name · $langCode — brouillon déjà présent");
                    $skipped++;
                    continue;
                }

                if ($existing) {
                    $existing->update([
                        'category' => $tpl['category'],
                        'body'     => $tpl[$langCode],
                        'footer'   => $footer,
                    ]);
                    $this->line("  [upd ] $name · $langCode");
                    $updated++;
                } else {
                    WhatsAppTemplateDraft::create([
                        'name'     => $name,
                        'language' => $langCode,
                        'category' => $tpl['category'],
                        'body'     => $tpl[$langCode],
                        'footer'   => $footer,
                    ]);
                    $this->line("  [new ] $name · $langCode");
                    $created++;
                }
            }
        }

        $this->newLine();
        $this->line("Créés : {$created}");
        $this->line("Mis à jour : {$updated}");
        $this->line("Sautés : {$skipped}");
        $this->info('Ouvrez /admin/whatsapp/templates : chaque brouillon a un bouton "Soumettre à Meta".');

        return self::SUCCESS;
    }
}
