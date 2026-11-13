<?php

namespace App\Http\Controllers;

use App\Models\Annonces;
use App\Models\ModelGuide;
use App\Models\OwnerReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Guides modèles publics (community.model_guides) — bilingues, SEO.
 */
class ModelGuideController extends Controller
{
    public function index()
    {
        abort_unless(feature_on('community.model_guides'), 404);

        $guides = ModelGuide::where('is_active', 1)
            ->with(['brand', 'modele'])
            ->latest()->paginate(12);

        return view('community.guides-index', compact('guides'));
    }

    public function show($slug)
    {
        abort_unless(feature_on('community.model_guides'), 404);

        $guide = ModelGuide::where('slug', $slug)->where('is_active', 1)
            ->with(['brand', 'modele'])->firstOrFail();

        $guide->increment('views');

        // Fourchette de prix calculée sur les annonces NADO du modèle.
        $brandName = $guide->brand->name ?? null;
        $modelName = $guide->modele->name ?? null;

        $ads = Annonces::where('active', 1)
            ->where(function ($q) use ($guide, $modelName) {
                if ($guide->modele_id) $q->orWhere('modele', $guide->modele_id);
                if ($modelName) $q->orWhereRaw('UPPER(modele) = ?', [mb_strtoupper($modelName)]);
            });

        $count = (clone $ads)->count();
        $priceRange = [
            'min'   => $count ? (int) (clone $ads)->min('prix') : null,
            'max'   => $count ? (int) (clone $ads)->max('prix') : null,
            'count' => $count,
        ];

        // Avis propriétaires (modérés).
        $ownerAgg = $guide->modele_id ? OwnerReview::aggregateFor($guide->modele_id) : ['count' => 0, 'avg' => 0];
        $ownerReviews = $guide->modele_id
            ? OwnerReview::approved()->where('modele_id', $guide->modele_id)->latest()->limit(20)->get()
            : collect();

        // CTA catalogue filtré.
        $catalogUrl = route('achat.index', array_filter([
            'marque' => $brandName ? str_replace(' ', '-', $brandName) : null,
            'modele' => $modelName ? str_replace(' ', '_', $modelName) : null,
        ]));

        return view('community.guide-show', compact('guide', 'priceRange', 'ownerAgg', 'ownerReviews', 'catalogUrl'));
    }

    /** Avis propriétaire (client guard, flag). Modéré. */
    public function storeReview(Request $request, $guide)
    {
        abort_unless(feature_on('community.model_guides'), 404);

        $g = ModelGuide::findOrFail($guide);
        abort_unless($g->modele_id, 404);

        $data = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'title'  => ['nullable', 'string', 'max:120'],
            'body'   => ['nullable', 'string', 'max:2000'],
        ]);

        OwnerReview::create([
            'modele_id'          => $g->modele_id,
            'reviewer_client_id' => Auth::guard('client')->id(),
            'rating'             => $data['rating'],
            'title'              => $data['title'] ?? null,
            'body'               => $data['body'] ?? null,
            'status'             => 'pending',
        ]);

        return back()->with('success', __('messages.community.review_submitted'))->withFragment('avis');
    }
}
