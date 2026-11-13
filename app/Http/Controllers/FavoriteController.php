<?php

namespace App\Http\Controllers;

use App\Models\Annonces;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function index()
{
    $user = Auth::guard('client')->user();

    // Client enchère → favoris d'ENCHÈRES ; sinon favoris d'annonces.
    $isFlotte = $user->type === 'flotte&enchere';
    if ($isFlotte && method_exists($user, 'enchereFavorites')) {
        $favorites = $user->enchereFavorites()->latest()->get();
    } else {
        $favorites = $user->favorites()->latest()->get();
    }

    return view('client.favorites', compact('favorites', 'isFlotte'));
}

    public function toggle($id, \Illuminate\Http\Request $request)
    {
        $annonce = Annonces::findOrFail($id);
        $user = Auth::guard('client')->user();

        if ($user->favorites()->where('annonce_id', $id)->exists()) {
            $user->favorites()->detach($id);
            $favorited = false;
            $message = __('messages.discovery.fav_removed');
        } else {
            $user->favorites()->attach($id);
            $favorited = true;
            $message = __('messages.discovery.fav_added');
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'favorited' => $favorited, 'message' => $message]);
        }

        return back()->with('success', $message);
    }
}
