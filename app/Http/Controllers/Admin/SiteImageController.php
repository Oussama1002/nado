<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Admin — gestion des images illustratives du site (hero Vente, hero Reprise,
 * section "Notre expertise" Auto-Enchère, image "Estimation" de la page d'accueil).
 *
 * Chaque emplacement est stocké sur le disque `public` (storage/app/public/site-images/)
 * et son chemin relatif est persisté dans la table `settings` sous la clé
 * site_images.<slot>. Les Blades lisent via le helper site_image($slot, $default).
 */
class SiteImageController extends Controller
{
    /**
     * Les emplacements configurables. Chaque entrée décrit où l'image apparaît
     * (pour aider l'admin) et donne l'URL par défaut si aucun upload n'a été fait.
     */
    public const SLOTS = [
        'vente_hero' => [
            'label'   => 'Vente — Hero',
            'desc'    => 'Image à droite du hero sur /vente/formulaire.',
            'default' => 'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?w=900&q=80',
        ],
        'ae_expertise' => [
            'label'   => 'Auto-Enchère — Notre expertise',
            'desc'    => 'Image de la section « NOTRE EXPERTISE » sur /nadoautoenchere/prelogin.',
            'default' => 'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?auto=format&fit=crop&q=80&w=1200',
        ],
        'reprise_hero' => [
            'label'   => 'Reprise — Hero',
            'desc'    => 'Image du hero sur /reprises/formulaire.',
            'default' => 'https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?auto=format&fit=crop&q=80&w=1200',
        ],
        'front_estimation' => [
            'label'   => 'Accueil — Estimation',
            'desc'    => 'Image de la section « NADO PRICE » (estimer mon véhicule) sur la page d\'accueil.',
            'default' => 'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?auto=format&fit=crop&q=80&w=1200',
        ],
    ];

    public function index()
    {
        $values = [];
        foreach (array_keys(self::SLOTS) as $slot) {
            $values[$slot] = settings('site_images.' . $slot);
        }

        return view('admin.site-images.index', [
            'slots'  => self::SLOTS,
            'values' => $values,
        ]);
    }

    public function update(Request $request, string $slot)
    {
        abort_unless(array_key_exists($slot, self::SLOTS), 404);

        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png,webp,gif|max:8192',
        ]);

        $file = $request->file('image');
        $ext  = strtolower($file->getClientOriginalExtension() ?: 'jpg');
        $name = $slot . '-' . Str::random(8) . '.' . $ext;
        $path = $file->storeAs('site-images', $name, 'public');

        $this->rememberSlot($slot, $path);

        // Si on remplace, supprimer l'ancienne copie (uniquement si c'était bien
        // un fichier sous site-images/, pas une URL par défaut).
        if ($old = $request->input('previous')) {
            if (is_string($old) && str_starts_with($old, 'site-images/')) {
                Storage::disk('public')->delete($old);
            }
        }

        return redirect()->route('admin.site-images.index')
            ->with('success', 'Image « ' . self::SLOTS[$slot]['label'] . ' » mise à jour.');
    }

    public function destroy(string $slot)
    {
        abort_unless(array_key_exists($slot, self::SLOTS), 404);

        $current = settings('site_images.' . $slot);
        if (is_string($current) && str_starts_with($current, 'site-images/')) {
            Storage::disk('public')->delete($current);
        }

        $this->rememberSlot($slot, null);

        return redirect()->route('admin.site-images.index')
            ->with('success', 'Image « ' . self::SLOTS[$slot]['label'] . ' » réinitialisée.');
    }

    /** Upsert direct (Setting::set ne crée pas la ligne si elle n'existe pas). */
    private function rememberSlot(string $slot, ?string $path): void
    {
        $key = 'site_images.' . $slot;
        Setting::updateOrCreate(
            ['key' => $key],
            [
                'group'         => 'site_images',
                'value'         => $path,
                'type'          => 'string',
                'label'         => self::SLOTS[$slot]['label'] ?? $slot,
                'is_secret'     => false,
                'display_order' => 0,
            ]
        );
        Cache::forget("setting.$key");
    }
}
