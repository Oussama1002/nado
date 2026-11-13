<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SellerReview;
use Illuminate\Http\Request;

/**
 * Modération des avis vendeurs (mirror DemandeAdminController).
 */
class SellerReviewController extends Controller
{
    public function index(Request $request)
    {
        $q = SellerReview::with(['reviewer', 'seller'])->latest();
        if ($status = $request->get('status')) {
            $q->where('status', $status);
        }

        return view('admin.community.seller-reviews', [
            'reviews' => $q->paginate(20)->withQueryString(),
            'status'  => $status,
        ]);
    }

    public function approve(SellerReview $review)
    {
        $review->update(['status' => 'approved']);
        SellerReview::flushAggregate($review->seller_client_id);
        return back()->with('success', 'Avis approuvé ✅');
    }

    public function reject(SellerReview $review)
    {
        $review->update(['status' => 'rejected']);
        SellerReview::flushAggregate($review->seller_client_id);
        return back()->with('success', 'Avis rejeté.');
    }

    public function destroy(SellerReview $review)
    {
        $seller = $review->seller_client_id;
        $review->delete();
        SellerReview::flushAggregate($seller);
        return back()->with('success', 'Avis supprimé.');
    }
}
