<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Modele;
use App\Services\CarteGriseExtractor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Endpoint de pré-remplissage IA depuis une carte grise.
 * Gated : auth:client (route) + feature-flag ai.cartegrise_enabled + rate-limit.
 * Ne renvoie QUE des champs véhicule — aucune donnée personnelle.
 */
class CarteGriseController extends Controller
{
    public function extract(Request $request, CarteGriseExtractor $extractor)
    {
        abort_unless(feature_on('ai.cartegrise_enabled'), 404);

        $client = Auth::guard('client')->user();
        abort_unless($client, 403);

        // Rate-limit par client / heure (réglable en admin).
        $perHour = (int) settings('ai.rate_limit_per_hour', 10);
        $key = 'cartegrise:' . $client->id;
        if (RateLimiter::tooManyAttempts($key, max(1, $perHour))) {
            return response()->json([
                'success' => false,
                'message' => 'Trop de tentatives. Réessayez dans une heure.',
            ], 429);
        }

        $maxMb = (int) settings('ai.max_upload_mb', 8);
        $request->validate([
            'images'   => ['required', 'array', 'min:1', 'max:2'],
            'images.*' => ['file', 'mimes:jpg,jpeg,png,pdf', 'max:' . ($maxMb * 1024)],
        ], [
            'images.required' => 'Ajoutez une photo de la carte grise.',
            'images.*.mimes'  => 'Formats acceptés : JPG, PNG ou PDF.',
            'images.*.max'    => "Fichier trop lourd : {$maxMb} Mo maximum.",
        ]);

        RateLimiter::hit($key, 3600);

        $files = $request->file('images');

        // Rétention optionnelle sur un disque PRIVÉ (jamais public_html/storage).
        if ((bool) settings('ai.retain_image', false)) {
            foreach ($files as $file) {
                $name = 'cg-' . $client->id . '-' . time() . '-' . Str::random(6) . '.' . $file->getClientOriginalExtension();
                $file->storeAs('cartegrise', $name, 'local'); // storage/app/cartegrise (hors web)
            }
        }

        $result = $extractor->extract($files);

        if (! ($result['ok'] ?? false)) {
            $reason = $result['error'] ?? 'unknown';
            $message = match ($reason) {
                'ocr_unavailable' => "OCR indisponible sur le serveur (Tesseract non installé).",
                default           => "Nous n'avons pas pu lire la carte grise, remplissez manuellement.",
            };

            return response()->json([
                'success' => false,
                'reason'  => $reason,   // diagnostic (config / api / request / parse / no_image)
                'message' => $message,
            ], 200);
        }

        return response()->json($this->mapForForm($result['data'], $result['confidence']));
    }

    /**
     * Traduit les champs extraits vers les noms de champs du formulaire vente
     * et résout marque/modèle sur les tables Brand/Modele.
     */
    private function mapForForm(array $data, array $confidence): array
    {
        $threshold = ((float) settings('ai.min_confidence', 50)) / 100;

        [$markName, $markConf] = $this->matchBrand($data['marque'] ?? null, $confidence['marque'] ?? 0);
        [$modeleName, $modeleConf] = $this->matchModele($markName, $data['modele'] ?? null, $confidence['modele'] ?? 0);

        $year = $this->resolveYear($data['annee'] ?? null, $data['date_premiere_circulation'] ?? null);

        $fields = [
            'mark'      => $markName,
            'modele'    => $modeleName,
            'year'      => $year,
            'carburant' => $this->normalizeCarburant($data['carburant'] ?? null),
            'cv'        => is_numeric($data['puissance_fiscale'] ?? null) ? (int) $data['puissance_fiscale'] : null,
            'matricule' => $data['matricule'] ?? null,
            'vin'       => $data['vin'] ?? null,
        ];

        // Confiance par champ du formulaire (0..1).
        $conf = [
            'mark'      => $markConf,
            'modele'    => $modeleConf,
            'year'      => max((float) ($confidence['annee'] ?? 0), (float) ($confidence['date_premiere_circulation'] ?? 0)),
            'carburant' => (float) ($confidence['carburant'] ?? 0),
            'cv'        => (float) ($confidence['puissance_fiscale'] ?? 0),
            'matricule' => (float) ($confidence['matricule'] ?? 0),
            'vin'       => (float) ($confidence['vin'] ?? 0),
        ];

        // On ne renvoie que les champs réellement trouvés ; on signale les peu fiables.
        $out = [];
        $low = [];
        foreach ($fields as $name => $value) {
            if ($value === null || $value === '') {
                continue;
            }
            $out[$name] = $value;
            if (($conf[$name] ?? 0) < $threshold) {
                $low[] = $name;
            }
        }

        return [
            'success'        => true,
            'fields'         => $out,
            'low_confidence' => $low,
            // Indique si marque/modèle ont été résolus sur un vrai enregistrement.
            'matched'        => [
                'mark'   => $markName !== null,
                'modele' => $modeleName !== null,
            ],
        ];
    }

    /** Fuzzy-match marque → Brand (nom canonique) ou null. */
    private function matchBrand(?string $raw, $conf): array
    {
        $raw = trim((string) $raw);
        if ($raw === '') {
            return [null, 0.0];
        }
        $needle = Str::lower($raw);

        $brand = Brand::whereRaw('LOWER(name) = ?', [$needle])->first()
            ?? Brand::whereRaw('LOWER(name) LIKE ?', [$needle . '%'])->first()
            ?? Brand::whereRaw('? LIKE CONCAT(LOWER(name), \'%\')', [$needle])->first();

        return $brand ? [$brand->name, (float) $conf] : [null, 0.0];
    }

    /** Fuzzy-match modèle (token de tête) → Modele de la marque, ou null. */
    private function matchModele(?string $markName, ?string $raw, $conf): array
    {
        $raw = trim((string) $raw);
        if ($markName === null || $raw === '') {
            return [null, 0.0];
        }
        $brand = Brand::whereRaw('LOWER(name) = ?', [Str::lower($markName)])->first();
        if (! $brand) {
            return [null, 0.0];
        }

        // La carte grise est verbeuse ("A4 TDI 2.0L EXCLUSIVE") → on matche sur le 1er token.
        $token = Str::lower(preg_split('/\s+/', $raw)[0] ?? '');
        if ($token === '') {
            return [null, 0.0];
        }

        $modele = Modele::where('brand_id', $brand->id)
            ->where(function ($q) use ($token) {
                $q->whereRaw('LOWER(name) = ?', [$token])
                  ->orWhereRaw('LOWER(name) LIKE ?', [$token . '%'])
                  ->orWhereRaw('? LIKE CONCAT(LOWER(name), \'%\')', [$token]);
            })
            ->orderByRaw('CHAR_LENGTH(name)')
            ->first();

        return $modele ? [$modele->name, (float) $conf] : [null, 0.0];
    }

    /** Année depuis l'entier annee ou l'année de la date de 1re circulation. */
    private function resolveYear($annee, $date): ?int
    {
        if (is_numeric($annee) && $annee >= 1950 && $annee <= (int) date('Y') + 1) {
            return (int) $annee;
        }
        if (is_string($date) && preg_match('/(\d{4})/', $date, $m)) {
            $y = (int) $m[1];
            if ($y >= 1950 && $y <= (int) date('Y') + 1) {
                return $y;
            }
        }

        return null;
    }

    /** Aligne le carburant sur les options du formulaire. */
    private function normalizeCarburant(?string $raw): ?string
    {
        $raw = Str::lower(trim((string) $raw));
        if ($raw === '') {
            return null;
        }

        return match (true) {
            str_contains($raw, 'diesel') || str_contains($raw, 'gasoil') || str_contains($raw, 'gazole') => 'Diesel',
            str_contains($raw, 'essence') || str_contains($raw, 'petrol') || str_contains($raw, 'gasoline') => 'Essence',
            str_contains($raw, 'electri') => 'Electrique',
            str_contains($raw, 'hybrid') => 'Hybride',
            default => null,
        };
    }
}
