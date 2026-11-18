<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Envoyée au client « flotte&enchere » quand un admin valide son accès
 * Auto Enchère (clients.ae_approved passe à true dans ClientController@approveEnchere).
 */
class EnchereAccessApprovedNotification extends Notification
{
    use Queueable;

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Votre accès Auto Enchère a été validé')
            ->greeting('Bonjour ' . ($notifiable->name ?: '') . ',')
            ->line('Bonne nouvelle : votre accès à l’espace Auto Enchère de NADO vient d’être validé par notre équipe.')
            ->line('Vous pouvez désormais vous connecter et participer aux enchères réservées aux clients Flotte & Enchère.')
            ->action('Accéder à Auto Enchère', url('/nadoautoenchere/login'))
            ->line('Merci pour votre confiance.');
    }
}
