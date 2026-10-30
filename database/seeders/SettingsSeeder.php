<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

/**
 * Valeurs de configuration par défaut du marketplace (doc 02 — §6.bis.2 / §6.bis.6).
 *
 * Idempotent : updateOrCreate sur la clé. Ne réécrase PAS une valeur déjà
 * modifiée en admin (seuls label/type/meta sont rafraîchis).
 *
 * Format d'une ligne : [suffixe, type, défaut, label, is_toggle, is_secret]
 * La clé stockée est "<group>.<suffixe>".
 */
class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->definitions() as $group => $rows) {
            $order = 0;
            foreach ($rows as $row) {
                [$suffix, $type, $default, $label] = $row;
                $isToggle = $row[4] ?? ($type === 'bool');
                $isSecret = $row[5] ?? false;
                $fullKey  = "$group.$suffix";

                $existing = Setting::where('key', $fullKey)->first();

                Setting::updateOrCreate(
                    ['key' => $fullKey],
                    [
                        'group'         => $group,
                        // ne pas écraser une valeur déjà saisie en admin
                        'value'         => $existing
                            ? $existing->value
                            : Setting::prepareForStorage($type, $default),
                        'type'          => $type,
                        'label'         => $label,
                        'is_toggle'     => $isToggle,
                        'is_secret'     => $isSecret,
                        'display_order' => $order++,
                    ]
                );
            }
        }

        Setting::flushCache();
    }

    private function definitions(): array
    {
        return [
            // ───────────────────────── payments ─────────────────────────
            'payments' => [
                ['cmi.enabled', 'bool', true, 'CMI activé'],
                ['cmi.merchant_id', 'string', '', 'CMI — ID marchand', false, true],
                ['cmi.store_key', 'string', '', 'CMI — Clé secrète', false, true],
                ['stripe.enabled', 'bool', false, 'Stripe activé'],
                ['stripe.key', 'string', '', 'Stripe — Clé publique', false, true],
                ['stripe.secret', 'string', '', 'Stripe — Clé secrète', false, true],
                ['cash.enabled', 'bool', true, 'Paiement cash (saisi par admin)'],
                ['bank_transfer.enabled', 'bool', true, 'Virement bancaire autorisé'],
                ['tva_rate', 'percent', 20, 'Taux TVA (%)'],
                ['currency', 'string', 'MAD', 'Devise'],
                ['test_mode', 'bool', true, 'Mode test / sandbox'],
            ],

            // ───────────────────────── packs ────────────────────────────
            'packs' => [
                ['enabled', 'bool', true, 'Système de packs activé'],
                ['free_enabled', 'bool', true, 'Pack gratuit activé'],
                ['pack1_price', 'decimal', 0, 'Pack 1 — Prix (MAD)'],
                ['pack1_duration', 'int', 7, 'Pack 1 — Durée (jours)'],
                ['pack2_price', 'decimal', 149, 'Pack 2 (Essentiel) — Prix'],
                ['pack2_duration', 'int', 10, 'Pack 2 — Durée (jours)'],
                ['pack3_price', 'decimal', 249, 'Pack 3 (Standard) — Prix'],
                ['pack3_duration', 'int', 20, 'Pack 3 — Durée (jours)'],
                ['pack4_price', 'decimal', 449, 'Pack 4 (Premium) — Prix'],
                ['pack4_duration', 'int', 30, 'Pack 4 — Durée (jours)'],
                ['pack5_price', 'decimal', 799, 'Pack 5 (Vitrine Pro) — Prix'],
                ['pack5_duration', 'int', 60, 'Pack 5 — Durée (jours)'],
                ['promo_active', 'bool', false, 'Promotion active'],
                ['promo_discount_percent', 'percent', 20, 'Remise promo (%)'],
                ['promo_ends_at', 'string', '', 'Fin de la promo (YYYY-MM-DD)', false],
            ],

            // ───────────────────────── pro_plans ────────────────────────
            'pro_plans' => [
                ['enabled', 'bool', true, 'Abonnements Pro activés'],
                ['trial_days_global', 'int', 14, 'Jours d\'essai par défaut'],
                ['auto_renew_default', 'bool', true, 'Renouvellement auto par défaut'],
                ['yearly_discount_months', 'int', 2, 'Mois offerts en facturation annuelle'],
            ],

            // ───────────────────────── boosts ───────────────────────────
            'boosts' => [
                ['enabled', 'bool', true, 'Boosts activés'],
                ['homepage_24h_price', 'decimal', 49, 'Boost homepage 24h'],
                ['homepage_7d_price', 'decimal', 249, 'Boost homepage 7j'],
                ['top_category_price', 'decimal', 149, 'Top catégorie 7j'],
                ['verified_badge_price', 'decimal', 99, 'Badge « Vérifié »'],
                ['video_feature_price', 'decimal', 199, 'Mise en avant vidéo'],
                ['pack_combo_price', 'decimal', 490, 'Pack visibilité (combo)'],
            ],

            // ───────────────────────── auctions ─────────────────────────
            'auctions' => [
                ['enabled', 'bool', true, 'Module enchères activé'],
                ['listing_fee', 'decimal', 99, 'Frais d\'inscription véhicule'],
                ['listing_fee_refundable', 'bool', true, 'Frais remboursés si vente'],
                ['seller_commission_rate', 'percent', 3, 'Commission vendeur (%)'],
                ['buyer_premium_rate', 'percent', 4, 'Prime acheteur (%)'],
                ['reserve_not_met_fee', 'decimal', 50, 'Pénalité réserve non atteinte'],
                ['highlight_price', 'decimal', 199, 'Mise en avant enchère'],
                ['warranty_7d_rate', 'percent', 1, 'Garantie 7 jours (%)'],
                ['min_bid_increment', 'decimal', 500, 'Incrément min. d\'enchère'],
                ['auto_extend_minutes', 'int', 2, 'Prolongation auto (minutes)'],
            ],

            // ───────────────────────── finance ──────────────────────────
            'finance' => [
                ['enabled', 'bool', true, 'Module financement activé'],
                ['frais_dossier', 'decimal', 2000, 'Frais de dossier (MAD)'],
                ['assurance_annuelle', 'decimal', 235, 'Assurance annuelle (MAD)'],
                ['taux_defaut', 'percent', 13, 'Taux d\'intérêt par défaut (%)'],
                ['rapport_solvabilite_enabled', 'bool', true, 'Rapport solvabilité activé'],
                ['rapport_solvabilite_price', 'decimal', 149, 'Rapport solvabilité express'],
                ['min_amount', 'decimal', 20000, 'Montant minimum (MAD)'],
                ['max_amount', 'decimal', 800000, 'Montant maximum (MAD)'],
                ['max_duree_mois', 'int', 84, 'Durée maximum (mois)'],
            ],

            // ───────────────────────── leads ────────────────────────────
            'leads' => [
                ['enabled', 'bool', false, 'Marketplace de leads activé'],
                ['buyer_price', 'decimal', 200, 'Lead acheteur (MAD)'],
                ['finance_price', 'decimal', 300, 'Lead financement (MAD)'],
                ['reprise_price', 'decimal', 150, 'Lead reprise (MAD)'],
                ['insurance_price', 'decimal', 100, 'Lead assurance (MAD)'],
                ['wallet_min_topup', 'decimal', 500, 'Recharge wallet minimum'],
                ['wallet_bonus_1000', 'percent', 5, 'Bonus wallet dès 1000 MAD (%)'],
            ],

            // ───────────────────────── ads ──────────────────────────────
            'ads' => [
                ['banners.enabled', 'bool', true, 'Bannières activées'],
                ['banners.left_1w', 'decimal', 490, 'Bannière gauche — 1 sem.'],
                ['banners.left_1m', 'decimal', 1490, 'Bannière gauche — 1 mois'],
                ['banners.right_1m', 'decimal', 1490, 'Bannière droite — 1 mois'],
                ['banners.center_1m', 'decimal', 2990, 'Bannière centre — 1 mois'],
                ['banners.homepage_card_1m', 'decimal', 1190, 'Carte homepage — 1 mois'],
                ['banners.full_package_1m', 'decimal', 4990, 'Package complet — 1 mois'],
                ['videos.enabled', 'bool', true, 'Vidéos pub activées'],
                ['videos.slot_7d', 'decimal', 390, 'Slot vidéo 7j'],
                ['videos.slot_30d', 'decimal', 1290, 'Slot vidéo 30j'],
                ['videos.production_price', 'decimal', 990, 'Production vidéo'],
                ['stories.enabled', 'bool', true, 'Stories activées'],
                ['stories.single_24h', 'decimal', 99, '1 story 24h'],
                ['stories.pack_5', 'decimal', 390, '5 stories / semaine'],
                ['stories.pack_20', 'decimal', 1290, '20 stories / mois'],
                ['social.enabled', 'bool', true, 'Social posts activés'],
                ['social.single_post', 'decimal', 149, 'Post simple'],
                ['social.reel', 'decimal', 290, 'Reel / vidéo'],
                ['social.pack_10', 'decimal', 1190, 'Pack 10 / mois'],
                ['articles.enabled', 'bool', true, 'Articles sponsorisés activés'],
                ['articles.sponsored_price', 'decimal', 490, 'Article sponsorisé'],
                ['articles.sponsored_with_social', 'decimal', 790, 'Article + partage social'],
                ['push.enabled', 'bool', true, 'Push sponsorisé activé'],
                ['push.price', 'decimal', 290, 'Push notification ciblée'],
                ['email.enabled', 'bool', true, 'Email sponsorisé activé'],
                ['email.price', 'decimal', 490, 'Email newsletter sponsor'],
            ],

            // ───────────────────────── estimation ───────────────────────
            'estimation' => [
                ['free_enabled', 'bool', true, 'Estimation gratuite activée'],
                ['pdf_report_enabled', 'bool', true, 'Rapport PDF activé'],
                ['pdf_report_price', 'decimal', 99, 'Rapport PDF signé'],
                ['inspection_enabled', 'bool', true, 'Inspection physique activée'],
                ['inspection_price', 'decimal', 390, 'Rapport + inspection'],
                ['expert_pack_enabled', 'bool', true, 'Pack reprise expert activé'],
                ['expert_pack_price', 'decimal', 690, 'Pack reprise expert'],
            ],

            // ───────────────────────── partners ─────────────────────────
            'partners' => [
                ['insurance.enabled', 'bool', false, 'Assurance activée'],
                ['insurance.commission_rate', 'percent', 12, 'Assurance — commission (%)'],
                ['warranty.enabled', 'bool', false, 'Garantie étendue activée'],
                ['warranty.commission_rate', 'percent', 15, 'Garantie — commission (%)'],
                ['transport.enabled', 'bool', false, 'Transport activé'],
                ['transport.commission_rate', 'percent', 20, 'Transport — commission (%)'],
                ['ct.enabled', 'bool', false, 'Contrôle technique activé'],
                ['ct.commission_rate', 'percent', 25, 'CT — commission (%)'],
                ['immat.enabled', 'bool', false, 'Immatriculation activée'],
                ['immat.commission_rate', 'percent', 30, 'Immat — commission (%)'],
            ],

            // ───────────────────────── global ───────────────────────────
            'global' => [
                ['marketplace_enabled', 'bool', true, 'Marketplace ouvert'],
                ['maintenance_mode', 'bool', false, 'Mode maintenance'],
                ['registration_enabled', 'bool', true, 'Inscriptions ouvertes'],
                ['auctions_module', 'bool', true, 'Module enchères'],
                ['finance_module', 'bool', true, 'Module financement'],
                ['ads_module', 'bool', true, 'Module publicité'],
                ['leads_module', 'bool', false, 'Module leads B2B'],
            ],
        ];
    }
}
