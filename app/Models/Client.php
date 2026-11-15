<?php

namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable; // ✅ correct
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Authenticatable
{
    use HasFactory;
    use HasRoles;
    use Notifiable;

    protected $guard_name = 'web'; // important for Spatie to know which guard

    /**
     * Grant the one-time token signup bonus on account creation, from ONE place
     * so every registration path is covered (email, phone, admin). Idempotent —
     * TokenService checks the ledger before granting, so it never double-grants.
     */
    protected static function booted(): void
    {
        static::created(function (Client $client) {
            try {
                \App\Services\TokenService::grantSignupBonus($client);
            } catch (\Throwable $e) {
                // Never let a token hiccup block account creation.
                \Illuminate\Support\Facades\Log::warning(
                    'Token signup bonus failed for client '.$client->id.': '.$e->getMessage()
                );
            }
        });
    }

    public function autoencheres()
    {
        return $this->hasMany(Autoenchere::class);
    }

    /** Token ledger (source of truth for the balance). */
    public function tokenTransactions()
    {
        return $this->hasMany(TokenTransaction::class)->latest();
    }

    /** Token top-up / payment attempts. */
    public function tokenPurchases()
    {
        return $this->hasMany(TokenPurchase::class)->latest();
    }

    /** Spendable token balance (cached column; ledger is authoritative). */
    public function tokens(): int
    {
        return (int) $this->tokens_balance;
    }
    


    // ✅ Ajoute les champs que tu veux autoriser à remplir automatiquement
    protected $fillable = [
        'name',
        'email',
        'password',
        'type',
        'ae_role',
        'phone',
        'logo',
        'company',
        'is_approved',
        'is_verified',
        'ae_approved',
        'caution_reminder_sent_at',
        'otp_code',
        'otp_expires_at',
        'tokens_balance',
        'unlimited_tokens',
    ];

    protected $casts = [
        'otp_expires_at'           => 'datetime',
        'ae_approved'              => 'boolean',
        'caution_reminder_sent_at' => 'datetime',
        'tokens_balance'           => 'integer',
        'unlimited_tokens'         => 'boolean',
    ];

    /** Admin-granted full access: paid features never debit this client. */
    public function hasUnlimitedTokens(): bool
    {
        return (bool) $this->unlimited_tokens;
    }

    // ✅ Si tu utilises des mots de passe, ajoute aussi un cast
    protected $hidden = [
        'password',
    ];
    /**
     * PHASE 3 (stub) — Caution / dépôt Auto-Enchère.
     * Hook d'éligibilité pour le plafonnement par dépôt (cf. AuctionService +
     * réglage enchere.deposit_threshold). Tant que la caution n'est pas modélisée
     * (montant / statut de dépôt), on considère le pro approuvé comme couvert.
     * À implémenter : vérifier un dépôt validé (table cautions / solde).
     */
    public function hasValidAuctionDeposit(): bool
    {
        return (bool) $this->ae_approved;
    }

    public function favorites()
    {
        return $this->belongsToMany(
            Annonces::class,   // Modèle cible
            'favorites',       // Nom de la table pivot
            'client_id',       // clé étrangère dans favorites vers client
            'annonce_id'       // clé étrangère dans favorites vers annonce
        )->withTimestamps();
    }

    /** Favoris d'ENCHÈRES (table pivot enchere_favorites, distincte des favoris d'annonces). */
    public function enchereFavorites()
    {
        return $this->belongsToMany(
            \App\Models\NadoAutoEnchere::class,
            'enchere_favorites',
            'client_id',
            'enchere_id'
        )->withTimestamps();
    }
    
    public function annonces()
    {
        return $this->hasMany(Annonces::class, 'phone', 'phone');
    }

    public function sendPasswordResetNotification($token)
    {
        $url = route('client.password.reset', ['token' => $token, 'email' => $this->email]);
        $this->notify(new \App\Notifications\ClientResetPasswordNotification($url));
    }
}
