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

    public function autoencheres()
    {
        return $this->hasMany(Autoenchere::class);
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
    ];

    protected $casts = [
        'otp_expires_at'           => 'datetime',
        'ae_approved'              => 'boolean',
        'caution_reminder_sent_at' => 'datetime',
    ];

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
