<?php

namespace App\Http\Controllers;

use App\Models\Song;

/**
 * Nado Radio — page publique de lecture (opt-in, lifestyle).
 * Entièrement pilotée par le flag radio.enabled : OFF → 404.
 */
class RadioController extends Controller
{
    public function index()
    {
        abort_unless(feature_on('radio.enabled'), 404);

        $songs = Song::activeOrdered()->get();

        return view('radio.index', [
            'songs'   => $songs,
            'title'   => settings('radio.title', 'Nado Radio'),
            'tagline' => settings('radio.tagline', ''),
        ]);
    }
}
