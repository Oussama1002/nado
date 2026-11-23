@extends('layouts.app')

@section('title', 'Politique de confidentialité — NADO')

@section('content')
<style>
    .legal-wrap {
        max-width: 820px;
        margin: 40px auto;
        padding: 0 20px;
        font-family: 'Inter', -apple-system, 'Segoe UI', Roboto, sans-serif;
        line-height: 1.65;
        color: #1f2937;
    }
    .legal-wrap h1 { font-size: 28px; margin-bottom: 4px; }
    .legal-wrap h2 { font-size: 18px; margin-top: 28px; color: #111827; }
    .legal-wrap p, .legal-wrap li { font-size: 15px; }
    .legal-wrap .muted { color: #6b7280; font-size: 13px; }
    .legal-wrap a { color: #4f46e5; }
</style>

<div class="legal-wrap">
    <h1>Politique de confidentialité</h1>
    <p class="muted">Dernière mise à jour : {{ date('d/m/Y') }}</p>

    <p>
        NADO (autoenchere.ma / www.nado.ma) attache une grande importance à la protection de votre
        vie privée. Cette politique explique quelles informations nous collectons, comment nous les
        utilisons et quels sont vos droits.
    </p>

    <h2>1. Responsable du traitement</h2>
    <p>
        NADO, plateforme marocaine de petites annonces automobiles, contactable à l'adresse
        <a href="mailto:contact@nado.ma">contact@nado.ma</a>.
    </p>

    <h2>2. Données collectées</h2>
    <ul>
        <li><strong>Compte utilisateur</strong> : nom, téléphone, e-mail, mot de passe chiffré.</li>
        <li><strong>Annonces</strong> : informations sur votre véhicule, photos, prix, ville.</li>
        <li><strong>Communications</strong> : messages échangés avec les vendeurs/acheteurs, WhatsApp
            (via l'API officielle Meta), e-mails.</li>
        <li><strong>Techniques</strong> : adresse IP, type de navigateur, cookies de session.</li>
        <li><strong>Paiements</strong> : traitement via nos partenaires (CMI, Stripe), nous ne
            stockons jamais le numéro complet de votre carte.</li>
    </ul>

    <h2>3. Finalités</h2>
    <ul>
        <li>Créer et gérer votre compte, publier et modérer vos annonces.</li>
        <li>Faciliter la mise en relation entre acheteurs et vendeurs.</li>
        <li>Vous envoyer des notifications utiles (validation d'annonce, nouveau message, alerte prix).</li>
        <li>Prévenir la fraude et respecter nos obligations légales.</li>
    </ul>

    <h2>4. WhatsApp &amp; API Meta</h2>
    <p>
        NADO utilise l'API WhatsApp Business de Meta pour certaines communications
        (code de vérification OTP, confirmation d'annonce, suivi de demande). Votre numéro
        n'est utilisé qu'à ces fins et n'est jamais revendu.
    </p>

    <h2>5. Partage</h2>
    <p>
        Vos données ne sont partagées qu'avec les prestataires strictement nécessaires
        au fonctionnement du service (hébergement, paiement, messagerie). Nous ne vendons
        jamais vos données à des tiers.
    </p>

    <h2>6. Conservation</h2>
    <p>
        Les données de compte sont conservées tant que votre compte est actif.
        À la suppression de votre compte, elles sont effacées dans un délai de 30 jours,
        sauf obligation légale de conservation (facturation, lutte anti-fraude).
    </p>

    <h2>7. Vos droits</h2>
    <p>
        Conformément à la loi 09-08 relative à la protection des personnes physiques à l'égard du
        traitement des données à caractère personnel, vous disposez d'un droit d'accès, de
        rectification, d'opposition et de suppression. Écrivez à
        <a href="mailto:contact@nado.ma">contact@nado.ma</a> pour exercer ces droits.
    </p>

    <h2>8. Cookies</h2>
    <p>
        NADO utilise des cookies strictement nécessaires (session, panier, préférence de langue)
        et des cookies analytiques anonymes. Vous pouvez les désactiver depuis les paramètres
        de votre navigateur.
    </p>

    <h2>9. Contact</h2>
    <p>
        Pour toute question : <a href="mailto:contact@nado.ma">contact@nado.ma</a>.
    </p>

    <p class="muted" style="margin-top:40px;">
        © {{ date('Y') }} NADO. Cette politique peut être mise à jour ; consultez-la régulièrement.
    </p>
</div>
@endsection
