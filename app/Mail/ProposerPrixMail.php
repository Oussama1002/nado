<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProposerPrixMail extends Mailable
{
    use Queueable, SerializesModels;

    public $client;
    public $enchere;
    public $montant;

    public function __construct($client, $enchere, $montant)
    {
        $this->client = $client;
        $this->enchere = $enchere;
        $this->montant = $montant;
    }

    public function build()
    {
        $subject = __('messages.mail.pp_subject_hdr')
                 . " {$this->enchere->id} - {$this->enchere->brand?->name} {$this->enchere->modeleRelation?->name} - "
                 . __('messages.mail.pp_thanks');

        return $this->subject($subject)
                    ->view('mail.proposer_prix');
    }
}
