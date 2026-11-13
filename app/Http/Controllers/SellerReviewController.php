<?php

namespace App\Http\Controllers;

use App\Models\Annonces;
use App\Models\Client;
use App\Models\SellerReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Avis vendeurs (community.seller_reviews). Modéré : un avis reste 'pending'
 * jusqu'à validation admin. Réutilise le pattern Demandes (reply + modération).
 */
class SellerReviewController extends Controller
{
    /** Profil public d'un vendeur : note agrégée + avis approuvés + formulaire. */
    public function profile($seller)
    {
        abort_unless(feature_on('community.seller_reviews'), 404);

        $sellerClient = Client::findOrFail($seller);
        $agg = SellerReview::aggregateFor($sellerClient->id);

        $reviews = SellerReview::approved()
            ->where('seller_client_id', $sellerClient->id)
            ->with('reviewer')
            ->latest()
            ->paginate(10);

        // Quelques annonces du vendeur (contexte).
        $ads = Annonces::where('active', 1)
            ->where('phone', $sellerClient->phone)
            ->latest()->limit(6)->get();

        return view('community.seller-profile', [
            'seller'  => $sellerClient,
            'agg'     => $agg,
            'reviews' => $reviews,
            'ads'     => $ads,
        ]);
    }

    /** Dépose un avis (client guard, flag, 1 par vendeur). */
    public function store(Request $request, $seller)
    {
        abort_unless(feature_on('community.seller_reviews'), 404);

        $sellerClient = Client::findOrFail($seller);
        $reviewer = Auth::guard('client')->user();

        // Un vendeur ne peut pas s'auto-noter.
        if ($reviewer->id === $sellerClient->id) {
            return back()->with('error', __('messages.community.cannot_self_review'));
        }

        $data = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'title'  => ['nullable', 'string', 'max:120'],
            'body'   => ['nullable', 'string', 'max:2000'],
        ]);

        // "Vérifié" si le client a interagi avec le vendeur (favori sur une de ses annonces).
        $sellerAdIds = Annonces::where('phone', $sellerClient->phone)->pluck('id');
        $verified = $sellerAdIds->isNotEmpty() && DB::table('favorites')
            ->where('client_id', $reviewer->id)
            ->whereIn('annonce_id', $sellerAdIds)
            ->exists();

        // Idempotent : si un avis existe déjà, on le met à jour (repasse en pending).
        SellerReview::updateOrCreate(
            ['reviewer_client_id' => $reviewer->id, 'seller_client_id' => $sellerClient->id],
            [
                'rating'   => $data['rating'],
                'title'    => $data['title'] ?? null,
                'body'     => $data['body'] ?? null,
                'verified' => $verified,
                'status'   => 'pending',
            ]
        );

        SellerReview::flushAggregate($sellerClient->id);

        return back()->with('success', __('messages.community.review_submitted'));
    }

    /** Signalement d'abus (best-effort, réutilise le style Demandes@report). */
    public function report(Request $request, SellerReview $review)
    {
        return back()->with('info', __('messages.community.report_ack'));
    }
}
