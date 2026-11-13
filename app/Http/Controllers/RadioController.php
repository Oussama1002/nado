<?php

namespace App\Http\Controllers;

use App\Models\Song;
use App\Models\Episode;

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

        // Épisodes / podcast (community.radio_episodes).
        $episodes = collect();
        $latestEpisode = null;
        if (feature_on('community.radio_episodes')) {
            $episodes = Episode::published()
                ->orderByDesc('published_at')->orderByDesc('id')
                ->get();
            $latestEpisode = $episodes->first();
        }

        return view('radio.index', [
            'songs'         => $songs,
            'episodes'      => $episodes,
            'latestEpisode' => $latestEpisode,
            'title'         => settings('radio.title', 'Nado Radio'),
            'tagline'       => settings('radio.tagline', ''),
        ]);
    }
}
