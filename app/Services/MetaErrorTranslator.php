<?php

namespace App\Services;

/**
 * Traduit les erreurs brutes de l'API Meta (Graph / WhatsApp Cloud) en
 * messages français actionnables pour l'utilisateur final.
 *
 * Fonction pure, à réutiliser à chaque appel Meta. Les codes Meta sont
 * réutilisés pour des dizaines de sous-cas donc on matche à la fois le
 * code numérique ET des sous-chaînes du message brut.
 *
 * Inspiré de MetaErrorTranslator de Brandna.
 */
class MetaErrorTranslator
{
    /**
     * @param string|null $raw        Message brut d'erreur (error.message).
     * @param int|null    $code       Code numérique (error.code).
     * @param string|null $userTitle  error.error_user_title (déjà traduit par Meta si présent).
     * @param string|null $userMsg    error.error_user_msg (idem).
     */
    public static function toFrench(?string $raw, ?int $code = null, ?string $userTitle = null, ?string $userMsg = null): string
    {
        $r = strtolower((string) $raw);

        // Meta a déjà fourni une version traduite + actionable → on s'appuie dessus.
        if ($userTitle && $userMsg) {
            return $userTitle . ' — ' . $userMsg;
        }

        // Mappings par code + mots-clés.
        switch ((int) $code) {
            case 190:
                return "Jeton d'accès Meta invalide ou expiré. Reconnectez le compte dans Paramètres → WhatsApp OTP.";

            case 200:
            case 10:
                return "Permissions Meta insuffisantes sur le jeton — vérifiez que « whatsapp_business_messaging » et « whatsapp_business_management » sont bien cochés.";

            case 4:
            case 17:
            case 32:
            case 613:
                return "Limite d'appels Meta atteinte. Réessayez dans quelques minutes.";

            case 803:
                return "Ressource Meta introuvable ou identifiant invalide (Phone Number ID / WABA ID).";

            case 1:
                return "Configuration de l'application Meta invalide (App ID / App Secret).";

            case 131030:
                return "Numéro destinataire non autorisé. Avec un numéro test Meta, ajoutez d'abord ce numéro dans la liste « À » sur developers.facebook.com → WhatsApp → API Setup.";

            case 131047:
            case 131026:
                return "Fenêtre de 24h expirée — le client n'a pas écrit dans les 24 dernières heures. Utilisez un modèle approuvé pour re-contacter.";

            case 131042:
                return "Paiement WABA manquant — ajoutez une carte bancaire dans business.facebook.com → Paramètres → Facturation.";

            case 131051:
            case 131056:
                return "Numéro destinataire invalide ou non actif sur WhatsApp.";

            case 133010:
                return "Numéro non enregistré sur la Cloud API. Allez dans WhatsApp → API Setup → « Register your phone number » (code PIN par SMS).";

            case 130472:
                return "Utilisateur dans un groupe test Meta qui refuse les templates MARKETING. Essayez une catégorie UTILITY.";

            case 132000:
            case 132001:
            case 132007:
                return "Le template n'existe pas, est en attente d'approbation, ou a été rejeté. Vérifiez-le dans « Modèles ».";
        }

        // Fallback sur des sous-chaînes.
        return match (true) {
            str_contains($r, 'rate limit')                 => "Limite d'appels Meta atteinte. Réessayez dans quelques minutes.",
            str_contains($r, 'invalid parameter')          => "Paramètre invalide dans la requête Meta. Vérifiez les valeurs saisies.",
            str_contains($r, 'not in allowed list')        => "Numéro destinataire non autorisé par Meta (ajoutez-le dans la liste « À » du compte test).",
            str_contains($r, 'invalid access token')       => "Jeton d'accès Meta invalide ou expiré.",
            str_contains($r, 'permission')                 => "Permissions Meta insuffisantes sur le jeton.",
            str_contains($r, 're-engagement message')      => "Fenêtre de 24h expirée — utilisez un modèle approuvé.",
            str_contains($r, 'template name does not exist') => "Nom de template introuvable sur Meta.",
            $raw !== null && $raw !== ''                   => "Erreur Meta : " . $raw,
            default                                        => "Erreur inconnue côté Meta.",
        };
    }

    /**
     * Extrait et traduit l'erreur d'une réponse Http Meta (array json décodé).
     * Renvoie null si pas d'erreur.
     */
    public static function fromResponseBody(mixed $body): ?string
    {
        if (! is_array($body) || ! isset($body['error'])) {
            return null;
        }
        $err = $body['error'];
        return self::toFrench(
            $err['message']           ?? null,
            isset($err['code']) ? (int) $err['code'] : null,
            $err['error_user_title']  ?? null,
            $err['error_user_msg']    ?? null,
        );
    }
}
