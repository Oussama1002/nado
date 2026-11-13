<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Episode;
use Illuminate\Http\Request;

/**
 * CRUD admin des épisodes Nado Radio (mirror SongController).
 */
class EpisodeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:admin', 'role:admin']);
    }

    public function index()
    {
        return view('admin.community.episodes-index', [
            'episodes' => Episode::latest()->paginate(20),
        ]);
    }

    public function create()
    {
        return view('admin.community.episode-form', ['episode' => new Episode()]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        if ($request->hasFile('audio')) {
            $data['audio_path'] = $request->file('audio')->store('episodes', 'public');
        }
        if ($request->hasFile('cover')) {
            $data['cover_path'] = $request->file('cover')->store('episodes/covers', 'public');
        }
        Episode::create($data);

        return to_route('admin.community.episodes.index')->with('success', 'Épisode créé ✅');
    }

    public function edit(Episode $episode)
    {
        return view('admin.community.episode-form', compact('episode'));
    }

    public function update(Request $request, Episode $episode)
    {
        $data = $this->validateData($request);
        if ($request->hasFile('audio')) {
            $data['audio_path'] = $request->file('audio')->store('episodes', 'public');
        }
        if ($request->hasFile('cover')) {
            $data['cover_path'] = $request->file('cover')->store('episodes/covers', 'public');
        }
        $episode->update($data);

        return to_route('admin.community.episodes.index')->with('success', 'Épisode mis à jour ✅');
    }

    public function destroy(Episode $episode)
    {
        $episode->delete();
        return back()->with('success', 'Épisode supprimé.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'title'        => ['required', 'string', 'max:180'],
            'description'  => ['nullable', 'string', 'max:2000'],
            'duration'     => ['nullable', 'string', 'max:12'],
            'published_at' => ['nullable', 'date'],
            'sort_order'   => ['nullable', 'integer'],
            'audio'        => ['nullable', 'file', 'mimes:mp3,m4a,aac,wav,ogg', 'max:51200'],
            'cover'        => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]) + ['is_active' => $request->boolean('is_active', true)];
    }
}
