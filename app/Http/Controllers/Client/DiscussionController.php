<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Discussion;
use App\Models\DiscussionMessage;
use App\Models\Annonces;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DiscussionController extends Controller
{
    private function client()
    {
        return Auth::guard('client')->user();
    }

    /**
     * The logged-in client's role in a discussion:
     *  'buyer'  — they opened it (discussion.client_id)
     *  'seller' — it is about their own particulier annonce (they answer it)
     *  null     — not theirs
     */
    private function roleIn(Discussion $discussion): ?string
    {
        $client = $this->client();

        if ((int) $discussion->client_id === (int) $client->id) {
            return 'buyer';
        }

        $seller = $discussion->sellerClient();
        if ($seller && (int) $seller->id === (int) $client->id) {
            return 'seller';
        }

        return null;
    }

    /** sender_type the viewer writes with. */
    private function ownSenderType(string $role): string
    {
        return $role === 'seller' ? 'seller' : 'client';
    }

    public function index()
    {
        $client = $this->client();

        // Discussions they opened as a buyer + discussions buyers opened about
        // their own annonces (annonces are linked by phone, compared on the last
        // 9 digits; NADO's certifié annonces stay with the admin)
        $phoneKey = Discussion::phoneKey($client->phone);

        $discussions = Discussion::with(['annonce.brand', 'annonce.modeleRelation', 'lastMessage', 'client'])
            ->where(function ($q) use ($client, $phoneKey) {
                $q->where('client_id', $client->id);
                if ($phoneKey) {
                    $q->orWhereHas('annonce', function ($a) use ($phoneKey) {
                        $a->where(function ($c) {
                              $c->where('certifie', '!=', 1)->orWhereNull('certifie');
                          })
                          ->whereRaw(Discussion::PHONE_SQL . ' LIKE ?', ['%' . $phoneKey]);
                    });
                }
            })
            ->orderByDesc('last_message_at')
            ->paginate(20);

        // Role + unread count (messages from the other side) for each row
        foreach ($discussions as $d) {
            $d->viewer_role  = (int) $d->client_id === (int) $client->id ? 'buyer' : 'seller';
            $d->unread_count = $d->messages()
                ->where('sender_type', '!=', $this->ownSenderType($d->viewer_role))
                ->whereNull('read_at')
                ->count();
        }

        return view('client.discussions.index', compact('discussions'));
    }

    public function show(Discussion $discussion)
    {
        $role = $this->roleIn($discussion);
        abort_if($role === null, 403);

        $discussion->load(['annonce.brand', 'annonce.modeleRelation', 'messages', 'client']);

        // Mark the other side's messages as read
        $discussion->messages()
            ->where('sender_type', '!=', $this->ownSenderType($role))
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $sellerManaged = $discussion->isSellerManaged();

        return view('client.discussions.show', compact('discussion', 'role', 'sellerManaged'));
    }

    public function startOrGet(Request $request)
    {
        $client = $this->client();
        $annonceId = $request->annonce_id;

        // A seller can't open a discussion with themselves about their own annonce
        $annonce = Annonces::find($annonceId);
        if ($annonce && (int) $annonce->certifie !== 1
            && Discussion::phoneKey($annonce->phone) !== null
            && Discussion::phoneKey($annonce->phone) === Discussion::phoneKey($client->phone)) {
            $error = 'Il s’agit de votre propre annonce : les acheteurs intéressés vous contacteront dans « Mes Discussions ».';
            if ($request->expectsJson()) {
                return response()->json(['error' => $error], 422);
            }
            return redirect()->route('client.discussions.index')->with('error', $error);
        }

        $discussion = Discussion::firstOrCreate(
            ['annonce_id' => $annonceId, 'client_id' => $client->id],
            ['status' => 'open', 'last_message_at' => now()]
        );

        if ($request->expectsJson()) {
            $discussion->load('messages');

            // Mark NADO / seller messages as read
            $discussion->messages()
                ->where('sender_type', '!=', 'client')
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            $messages = $discussion->messages->map(fn($m) => $this->msgToArray($m, 'buyer'));

            return response()->json([
                'id'       => $discussion->id,
                'status'   => $discussion->status,
                'messages' => $messages,
                'send_url' => route('client.discussions.send', $discussion->id),
                'poll_url' => route('client.discussions.messages', $discussion->id),
            ]);
        }

        return redirect()->route('client.discussions.show', $discussion->id);
    }

    public function sendMessage(Request $request, Discussion $discussion)
    {
        $client = $this->client();
        $role = $this->roleIn($discussion);
        abort_if($role === null, 403);
        $senderType = $this->ownSenderType($role);

        // Detect "first client message ever in this discussion"
        $isFirstClientMessage = !$discussion->messages()
            ->where('sender_type', 'client')
            ->exists();

        if ($request->hasFile('audio')) {
            $request->validate([
                'audio' => 'required|file|max:10240',
                // Recorded length in seconds, measured by the browser while recording.
                // WebM from MediaRecorder carries no duration header, so the player shows
                // 0:00 until the whole file is buffered - storing it avoids the guesswork.
                'duration' => 'nullable|integer|min:0|max:3600',
            ]);

            $file = $request->file('audio');
            $ext  = strtolower($file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'webm');
            if ($ext === 'bin' || $ext === '') {
                $mime = $file->getMimeType();
                $ext  = match (true) {
                    str_contains($mime, 'webm') => 'webm',
                    str_contains($mime, 'ogg')  => 'ogg',
                    str_contains($mime, 'mp4')  => 'mp4',
                    str_contains($mime, 'mpeg') => 'mp3',
                    str_contains($mime, 'wav')  => 'wav',
                    default                     => 'webm',
                };
            }
            $filename = \Illuminate\Support\Str::random(40) . '.' . $ext;
            $path = $file->storeAs('discussion_audio', $filename, 'public');

            $msg = DiscussionMessage::create([
                'discussion_id' => $discussion->id,
                'sender_type'   => $senderType,
                'sender_id'     => $client->id,
                'message'       => '[Message vocal]',
                'type'          => 'audio',
                'attachment'    => $path,
                'duration'      => $request->integer('duration') ?: null,
            ]);
        } else {
            $request->validate(['message' => 'required|string|max:2000']);

            $msg = DiscussionMessage::create([
                'discussion_id' => $discussion->id,
                'sender_type'   => $senderType,
                'sender_id'     => $client->id,
                'message'       => $request->message,
                'type'          => 'text',
            ]);
        }

        $discussion->update(['last_message_at' => now(), 'status' => 'open']);

        // Auto-reply if this was the very first client message
        // NADO auto-replies only answer the buyer, not the seller's replies
        if ($role === 'buyer' && $isFirstClientMessage) {
            DiscussionMessage::create([
                'discussion_id' => $discussion->id,
                'sender_type'   => 'admin',
                'sender_id'     => 0,
                // No emoji here until discussion_messages is converted to utf8mb4: the
                // column is utf8mb3 and rejects 4-byte characters, which 500s the whole
                // request (and made the client's own message come back as "NADO · undefined").
                'message'       => "Bonjour ! Merci pour votre message. Un de nos vendeurs NADO va vous répondre dans quelques minutes. Merci de patienter.",
                'type'          => 'text',
            ]);
            $discussion->update(['last_message_at' => now()]);
        }

        // Answer common questions (prix, options, disponibilite, caracteristiques)
        // immediately so the client is not left waiting for a human. Only for typed
        // messages - we cannot read a voice note.
        if ($role === 'buyer' && $msg->type === 'text') {
            $answer = $this->botAnswer((string) $request->message, $discussion);
            if ($answer) {
                DiscussionMessage::create([
                    'discussion_id' => $discussion->id,
                    'sender_type'   => 'admin',
                    'sender_id'     => 0,
                    'message'       => $answer,
                    'type'          => 'text',
                ]);
                $discussion->update(['last_message_at' => now()]);
            }
        }

        if ($request->expectsJson()) {
            return response()->json($this->msgToArray($msg, $role));
        }

        return back();
    }

    public function messages(Discussion $discussion)
    {
        $role = $this->roleIn($discussion);
        abort_if($role === null, 403);
        $own = $this->ownSenderType($role);

        // New messages from the other side (buyer sees NADO + seller, seller sees buyer + NADO)
        $since = request('since');
        $query = $discussion->messages()->where('sender_type', '!=', $own);

        if ($since) {
            $query->where('id', '>', $since);
        }

        $messages = $query->get()->map(fn($m) => $this->msgToArray($m, $role));

        // Mark as read
        $discussion->messages()
            ->where('sender_type', '!=', $own)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json($messages);
    }

    /* =======================
     * AUTO-REPLY BOT
     * =======================
     * Scripted answers, transcribed from the "chat bot.docx" brief. Returns null when
     * nothing matches, so a human answers as before.
     *
     * Order matters: the most specific intents are tested first (e.g. "documents pour
     * le credit" before "credit", "frais de dossier" before "documents").
     */
    private function botAnswer(string $question, Discussion $discussion): ?string
    {
        $annonce = Annonces::find($discussion->annonce_id);
        if (!$annonce) {
            return null;
        }

        // Accent- and case-insensitive haystack.
        $q = mb_strtolower($question, 'UTF-8');
        $q = strtr($q, [
            'à' => 'a', 'â' => 'a', 'ä' => 'a', 'é' => 'e', 'è' => 'e', 'ê' => 'e',
            'ë' => 'e', 'î' => 'i', 'ï' => 'i', 'ô' => 'o', 'ö' => 'o', 'û' => 'u',
            'ù' => 'u', 'ü' => 'u', 'ç' => 'c', '’' => "'",
        ]);

        $has = function (array $words) use ($q) {
            foreach ($words as $w) {
                if (str_contains($q, $w)) {
                    return true;
                }
            }
            return false;
        };

        $status   = mb_strtolower(trim((string) ($annonce->status ?? 'disponible')));
        $isSold   = str_contains($status, 'vendu') || str_contains($status, 'sold');
        $hasGarantie = !empty($annonce->garantie);

        // 1. Frais de dossier / mutation / carte grise
        if ($has(['frais', 'mutation', 'carte grise', 'carte-grise'])) {
            return "Le prix affiché ne comprend pas les frais de mutation (carte grise). "
                . "Un conseiller Nado peut vous détailler l'ensemble des frais administratifs lors de votre visite.";
        }

        // 2. Documents pour le crédit
        if ($has(['document', 'papier', 'piece', 'justificatif', 'dossier de credit'])) {
            return "Pour le dossier de crédit, il vous faut généralement : une CIN, un spécimen de chèque, "
                . "vos 3 derniers bulletins de salaire (ou justificatifs de revenus si indépendant) "
                . "et un justificatif de domicile.\n"
                . "Voir la simulation rapide de vos mensualités sur notre site.";
        }

        // 3. Vente à crédit
        if ($has(['credit', 'financ', 'mensualite', 'leasing', 'traite'])) {
            return "Tout à fait ! Ce véhicule est éligible au crédit auto avec nos partenaires financiers.\n"
                . "Voir la simulation rapide de vos mensualités sur notre site.";
        }

        // 4. Prix / négociation
        if ($has(['negoci', 'dernier prix', 'prix', 'combien', 'coute', 'cout', 'tarif', 'cher', 'remise'])) {
            $answer = "Nos véhicules affichent les meilleurs prix du marché, rigoureusement étudiés. "
                . "Une légère marge de négociation reste toutefois possible directement après l'inspection "
                . "du véhicule dans notre showroom ou lors d'une offre ferme. Vous pouvez faire une offre d'achat.";

            if ($annonce->prix) {
                $answer = "Prix affiché : " . number_format((float) $annonce->prix, 0, ',', ' ') . " MAD\n\n" . $answer;
            }

            return $answer;
        }

        // 5. Garantie
        if ($has(['garantie', 'garanti'])) {
            return $hasGarantie
                ? "Oui ! Tous nos véhicules éligibles bénéficient d'une garantie Nado "
                    . "(moteur et boîte de vitesses) de 3 mois pour rouler en toute sérénité.\n"
                    . "Voir nos conditions Garantie NADO Sérénité."
                : "Seules les voitures avec le tag Garantie sont sous Garantie NADO Sérénité.";
        }

        // 6. Kilométrage / état / certification / rapport d'expertise
        if ($has(['kilometr', 'km', 'etat', 'certifi', 'rapport', 'expertise', 'inspection', 'bonne'])) {
            $km = $annonce->km
                ? number_format((float) $annonce->km, 0, ',', ' ')
                : '—';

            return "Ce véhicule affiche {$km} km au compteur. Il a passé avec succès notre inspection "
                . "minutieuse sur 100 points de contrôle Nado Expert.\n"
                . "Voir le rapport d'inspection complet en bas de l'annonce.";
        }

        // 7. Options / équipements (not in the brief, but useful)
        if ($has(['option', 'equipement', 'accessoire', 'confort', 'securite', 'finition'])) {
            $eq = $this->equipementsFor($annonce);
            if (empty($eq)) {
                return "Un conseiller Nado vous détaillera les équipements de ce véhicule.";
            }
            return "Options et équipements (" . count($eq) . ") :\n- " . implode("\n- ", $eq);
        }

        // 8. Visite / essai / localisation (" voir" is deliberately broad, so it runs
        //    AFTER the more specific intents above)
        if ($has([' voir', 'venir', 'voir la voiture', 'essai', 'essaye', 'essayer', 'ou la voir', 'ou est', 'adresse',
                  'localisation', 'showroom', 'visite', 'rendez-vous', 'rdv'])) {
            return "Elle vous attend dans notre espace d'exposition à Jnane Californie, Casablanca.\n"
                . "Localisation : https://maps.app.goo.gl/RuP9P8wv898XNAAJ9";
        }

        // 9. Réservation
        if ($has(['reserv', 'acompte', 'bloquer', 'garder'])) {
            return "Oui, vous pouvez poser une option de réservation temporaire en versant un acompte sécurisé.\n"
                . "Un conseiller peut vous assister dans cette démarche.";
        }

        // 10. Reprise / échange
        if ($has(['reprise', 'reprendre', 'echange', 'echanger', 'ancienne voiture', 'ancien vehicule'])) {
            return "Oui, tout à fait ! Nous pouvons reprendre votre ancien véhicule pour l'utiliser comme apport "
                . "lors de l'achat de cette voiture.\n"
                . "Voir notre section reprise : https://www.nado.ma/reprises/formulaire";
        }

        // 11. Disponibilité (also "je veux cette voiture")
        if ($has(['dispo', 'disponible', 'vendu', 'encore', 'toujours', 'je veux cette voiture', 'je la veux',
                  'interesse', 'libre'])) {
            if ($isSold) {
                return "Malheureusement, ce véhicule a déjà trouvé preneur.\n"
                    . "Vous pouvez choisir d'autres modèles similaires sur Nado.ma";
            }
            if (str_contains($status, 'reserv')) {
                return "Ce véhicule est actuellement réservé. Un conseiller vous confirmera s'il se libère.";
            }
            return "Bonjour ! Oui, cette voiture est actuellement disponible dans notre showroom.";
        }

        return null;
    }


    /**
     * Equipment labels that are switched on for this annonce.
     */
    private function equipementsFor($annonce): array
    {
        $map = [
            'climatisation' => 'Climatisation',
            'gps' => 'GPS',
            'camera_recul' => 'Camera de recul',
            'ecran_tactile' => 'Ecran tactile',
            'bluetooth' => 'Bluetooth',
            'regulateur_vitesse' => 'Regulateur de vitesse',
            'aide_stationnement' => 'Aide au stationnement',
            'toit_panoramique' => 'Toit panoramique',
            'sieges_electriques' => 'Sieges electriques',
            'phares_full_led' => 'Phares full LED',
            'apple_carplay_android_auto' => 'Apple CarPlay / Android Auto',
            'chargeur_sans_fil' => 'Chargeur sans fil',
            'jantesA' => 'Jantes alliage',
            'volant_cuir' => 'Volant cuir',
            'airbags' => 'Airbags',
            'abs' => 'ABS',
            'esp' => 'ESP',
            'detecteur_angle_mort' => 'Detecteur d\'angle mort',
            'start_stop' => 'Start & Stop',
            'cockpit_digital' => 'Cockpit digital',
        ];

        $out = [];
        foreach ($map as $col => $label) {
            if (!empty($annonce->$col)) {
                $out[] = $label;
            }
        }

        return $out;
    }

    /** Who wrote a message, as shown to the other participants. */
    public static function senderLabel(string $senderType): string
    {
        return match ($senderType) {
            'seller' => 'Vendeur',
            'client' => 'Acheteur',
            default  => 'NADO',
        };
    }

    private function msgToArray(DiscussionMessage $m, string $role = 'buyer'): array
    {
        return [
            'id'             => $m->id,
            'sender_type'    => $m->sender_type,
            // Whether the viewer wrote it (right-hand bubble) + sender name otherwise
            'mine'           => $m->sender_type === $this->ownSenderType($role),
            'label'          => self::senderLabel($m->sender_type),
            'type'           => $m->type ?? 'text',
            'message'        => $m->message,
            'attachment_url' => $m->attachment ? parse_url(Storage::url($m->attachment), PHP_URL_PATH) : null,
            'duration'       => $m->duration ?? null,
            // Guard against a null created_at: the front-end prints this directly and
            // used to render "NADO · undefined".
            'time'           => optional($m->created_at)->format('H:i') ?? '',
        ];
    }
}
