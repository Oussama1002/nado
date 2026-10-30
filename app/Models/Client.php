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
        'otp_code',
        'otp_expires_at',
    ];

    protected $casts = [
        'otp_expires_at' => 'datetime',
    ];

    // ✅ Si tu utilises des mots de passe, ajoute aussi un cast
    protected $hidden = [
        'password',
    ];
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
