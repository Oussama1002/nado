<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\Song;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Nado Radio — gestion du catalogue de titres (média admin).
 * Même esprit que ImgSlideController / VideoAdsController.
 */
class SongController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $songs = Song::orderBy('sort_order')->orderBy('id')->get();

        return view('admin.songs.index', [
            'songs'    => $songs,
            // Réglages Nado Radio gérés directement ici (plus dans le panneau Configuration).
            'settings' => [
                'enabled'     => (bool) settings('radio.enabled', false),
                'mini_player' => (bool) settings('radio.mini_player', false),
                'title'       => settings('radio.title', 'Nado Radio'),
                'tagline'     => settings('radio.tagline', ''),
            ],
        ]);
    }

    /** Enregistre les réglages Nado Radio (flag, mini-player, titre, tagline). */
    public function updateSettings(Request $request)
    {
        $data = $request->validate([
            'title'   => ['nullable', 'string', 'max:255'],
            'tagline' => ['nullable', 'string', 'max:255'],
        ]);

        Setting::set('radio.enabled', $request->has('enabled'));
        Setting::set('radio.mini_player', $request->has('mini_player'));
        Setting::set('radio.title', $data['title'] ?? '');
        Setting::set('radio.tagline', $data['tagline'] ?? '');

        return to_route('admin.songs.index')->with('success', 'Réglages Nado Radio mis à jour.');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.songs.form', ['song' => new Song]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate($this->rules(true), $this->messages());

        $data['file_path']  = $this->storeAudio($request, $data['title']);
        $data['cover_path'] = $this->storeCover($request, $data['title']);
        $data['active']     = $request->has('active') ? 1 : 0;
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        Song::create($data);

        return to_route('admin.songs.index')->with('success', 'Titre ajouté à Nado Radio.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Song $song)
    {
        return view('admin.songs.form', ['song' => $song]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Song $song)
    {
        $data = $request->validate($this->rules(false), $this->messages());

        if ($request->hasFile('file')) {
            $this->deleteFile($song->file_path);
            $data['file_path'] = $this->storeAudio($request, $data['title']);
        }
        if ($request->hasFile('cover')) {
            $this->deleteFile($song->cover_path);
            $data['cover_path'] = $this->storeCover($request, $data['title']);
        }

        $data['active']     = $request->has('active') ? 1 : 0;
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        $song->update($data);

        return to_route('admin.songs.index')->with('success', 'Titre mis à jour.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Song $song)
    {
        // Soft delete only : on garde les fichiers tant que la ligne peut être restaurée.
        $song->delete();

        return to_route('admin.songs.index')->with('success', 'Titre supprimé.');
    }

    /** Règles de validation ($creating = fichier audio obligatoire). */
    private function rules(bool $creating): array
    {
        return [
            'title'      => ['required', 'string', 'max:255'],
            'artist'     => ['nullable', 'string', 'max:255'],
            'duration'   => ['nullable', 'string', 'max:20'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'active'     => ['nullable'],
            'file'       => [$creating ? 'required' : 'nullable', 'file', 'mimetypes:audio/mpeg,audio/mp3,audio/ogg,audio/wav,audio/x-wav,audio/aac,audio/mp4', 'mimes:mp3,ogg,wav,aac,m4a', 'max:30720'],
            'cover'      => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ];
    }

    private function messages(): array
    {
        return [
            'file.required'  => 'Le fichier audio est obligatoire.',
            'file.mimes'     => 'Format audio accepté : MP3, OGG, WAV, AAC ou M4A.',
            'file.mimetypes' => 'Format audio accepté : MP3, OGG, WAV, AAC ou M4A.',
            'file.max'       => 'Le fichier audio est trop lourd : 30 Mo maximum.',
            'cover.image'    => 'La pochette doit être une image.',
            'cover.mimes'    => 'Format pochette accepté : JPG, PNG ou WEBP.',
            'cover.max'      => 'La pochette est trop lourde : 5 Mo maximum.',
        ];
    }

    /** Enregistre l'audio sur le disque public et renvoie son chemin relatif. */
    private function storeAudio(Request $request, string $title): string
    {
        $file = $request->file('file');
        $name = Str::slug($title, '-') . '-' . time() . '.' . $file->getClientOriginalExtension();

        return $file->storeAs('songs', $name, 'public');
    }

    /** Enregistre la pochette (optionnelle) et renvoie son chemin relatif ou null. */
    private function storeCover(Request $request, string $title): ?string
    {
        if (! $request->hasFile('cover')) {
            return null;
        }

        $cover = $request->file('cover');
        $name  = Str::slug($title, '-') . '-' . time() . '.' . $cover->getClientOriginalExtension();

        return $cover->storeAs('songs/covers', $name, 'public');
    }

    /** Supprime un fichier du disque public s'il existe. */
    private function deleteFile(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
