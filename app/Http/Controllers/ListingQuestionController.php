<?php

namespace App\Http\Controllers;

use App\Models\Annonces;
use App\Models\Client;
use App\Models\ListingQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Q&A publiques sur les annonces (community.listing_qa). Modéré.
 * Notifications email après-réponse (QUEUE=sync).
 */
class ListingQuestionController extends Controller
{
    /** Un acheteur pose une question (client guard). */
    public function store(Request $request, $annonce)
    {
        abort_unless(feature_on('community.listing_qa'), 404);

        $ad = Annonces::findOrFail($annonce);
        $client = Auth::guard('client')->user();

        $data = $request->validate([
            'question' => ['required', 'string', 'max:500'],
        ]);

        $q = ListingQuestion::create([
            'annonce_id'      => $ad->id,
            'asker_client_id' => $client->id,
            'question'        => $data['question'],
            'status'          => 'pending',
            'is_public'       => true,
        ]);

        // Notifie le vendeur (propriétaire, retrouvé par téléphone).
        $seller = Client::where('phone', $ad->phone)->first();
        if ($seller && $seller->email) {
            $url = route('achat.detail', ['marque' => str_replace(' ', '', $ad->mark), 'modele' => str_replace(' ', '', $ad->modele), 'id' => $ad->id]);
            $this->email(
                $seller->email, $seller->name,
                __('messages.community.new_question_subject'),
                __('messages.community.new_question_subject'),
                e($data['question']),
                $url
            );
        }

        return back()->with('success', __('messages.community.question_submitted'))->withFragment('qa');
    }

    /** Le vendeur (propriétaire) ou un admin répond. */
    public function answer(Request $request, $question)
    {
        abort_unless(feature_on('community.listing_qa'), 404);

        $q = ListingQuestion::with('annonce')->findOrFail($question);
        $client = Auth::guard('client')->user();

        // Seul le propriétaire de l'annonce peut répondre (sinon admin via back-office).
        abort_unless($q->annonce && $client && $q->annonce->phone === $client->phone, 403);

        $data = $request->validate([
            'answer' => ['required', 'string', 'max:1000'],
        ]);

        // Une réponse du vendeur approuve la Q&A (contenu public utile).
        $q->update([
            'answer'      => $data['answer'],
            'answered_by' => $client->id,
            'status'      => 'approved',
        ]);

        // Notifie le demandeur.
        $asker = $q->asker;
        if ($asker && $asker->email) {
            $ad  = $q->annonce;
            $url = route('achat.detail', ['marque' => str_replace(' ', '', $ad->mark), 'modele' => str_replace(' ', '', $ad->modele), 'id' => $ad->id]);
            $this->email(
                $asker->email, $asker->name,
                __('messages.community.answer_subject'),
                __('messages.community.answer_subject'),
                e($data['answer']),
                $url
            );
        }

        return back()->with('success', __('messages.community.answer_submitted'))->withFragment('qa');
    }

    /** Email HTML simple, après-réponse, best-effort. */
    private function email(string $to, ?string $name, string $subject, string $heading, string $bodyHtml, string $url): void
    {
        $html = '<!doctype html><html><body style="margin:0;background:#f1f5f9;font-family:Arial,Helvetica,sans-serif;">'
            . '<div style="max-width:560px;margin:0 auto;padding:24px;">'
            . '<div style="background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 10px 30px -12px rgba(15,23,42,.25);">'
            . '<div style="background:#1459DD;padding:20px 24px;color:#fff;font-size:18px;font-weight:900;">' . e($heading) . '</div>'
            . '<div style="padding:24px;">'
            . '<p style="color:#334155;font-size:14px;line-height:1.6;margin:0 0 16px;white-space:pre-line;">' . $bodyHtml . '</p>'
            . '<a href="' . e($url) . '" style="display:inline-block;background:#F37021;color:#fff;text-decoration:none;font-weight:900;font-size:14px;padding:12px 22px;border-radius:12px;">NADO.ma</a>'
            . '</div></div></div></body></html>';

        dispatch(function () use ($to, $name, $subject, $html) {
            try {
                Mail::html($html, function ($m) use ($to, $name, $subject) {
                    $m->to($to, $name ?: '')->subject($subject);
                });
            } catch (\Throwable $e) {
                Log::warning('Q&A email failed: ' . $e->getMessage());
            }
        })->afterResponse();
    }
}
