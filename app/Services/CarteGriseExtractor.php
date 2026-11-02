<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

/**
 * Extraction des champs VÉHICULE d'une carte grise marocaine via un modèle
 * de vision Claude (Anthropic Messages API, appel HTTP direct — pas de SDK).
 *
 * RÈGLE VIE PRIVÉE : on n'extrait JAMAIS le nom du propriétaire, l'adresse,
 * l'immatriculation antérieure ni la ligne MRZ. Le prompt l'interdit
 * explicitement et le schéma de sortie ne contient aucun champ PII.
 */
class CarteGriseExtractor
{
    private const ENDPOINT = 'https://api.anthropic.com/v1/messages';

    /** Clés véhicule renvoyées (aucune donnée personnelle). */
    public const FIELDS = [
        'marque', 'modele', 'carburant', 'annee', 'date_premiere_circulation',
        'puissance_fiscale', 'matricule', 'vin', 'nombre_places', 'genre',
    ];

    /**
     * @param  UploadedFile[]  $files  1 ou 2 images (recto / verso) ou PDF
     * @return array{ok:bool, data?:array, confidence?:array, error?:string}
     */
    public function extract(array $files): array
    {
        $apiKey = config('services.anthropic.key');
        if (empty($apiKey)) {
            Log::error('CarteGriseExtractor: ANTHROPIC_API_KEY manquante');

            return ['ok' => false, 'error' => 'config'];
        }

        $blocks = [];
        foreach ($files as $file) {
            $block = $this->toContentBlock($file);
            if ($block) {
                $blocks[] = $block;
            }
        }
        if (empty($blocks)) {
            return ['ok' => false, 'error' => 'no_image'];
        }

        $blocks[] = ['type' => 'text', 'text' => $this->userPrompt()];

        try {
            $response = Http::withHeaders([
                'x-api-key'         => $apiKey,
                'anthropic-version' => config('services.anthropic.version', '2023-06-01'),
                'content-type'      => 'application/json',
            ])->timeout(60)->post(self::ENDPOINT, [
                'model'      => settings('ai.model', 'claude-haiku-5-5'),
                'max_tokens' => 1024,
                'system'     => $this->systemPrompt(),
                'messages'   => [[
                    'role'    => 'user',
                    'content' => $blocks,
                ]],
            ]);
        } catch (\Throwable $e) {
            Log::error('CarteGriseExtractor: appel API échoué', ['error' => $e->getMessage()]);

            return ['ok' => false, 'error' => 'request'];
        }

        if (! $response->successful()) {
            Log::error('CarteGriseExtractor: réponse API non-OK', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            return ['ok' => false, 'error' => 'api'];
        }

        $text = $this->firstText($response->json());
        $parsed = $this->parseJson($text);
        if ($parsed === null) {
            Log::warning('CarteGriseExtractor: JSON illisible', ['text' => $text]);

            return ['ok' => false, 'error' => 'parse'];
        }

        return $this->normalize($parsed);
    }

    /** Construit le bloc de contenu (image compressée ou PDF) pour l'API. */
    private function toContentBlock(UploadedFile $file): ?array
    {
        $mime = $file->getMimeType();

        if ($mime === 'application/pdf') {
            return [
                'type'   => 'document',
                'source' => [
                    'type'       => 'base64',
                    'media_type' => 'application/pdf',
                    'data'       => base64_encode(file_get_contents($file->getRealPath())),
                ],
            ];
        }

        // Image : on redimensionne + ré-encode en JPEG pour contrôler le coût en tokens.
        try {
            $manager = new ImageManager(new Driver);
            $img = $manager->read($file->getRealPath());
            $img->scaleDown(width: 1500);
            $data = (string) $img->toJpeg(quality: 75);

            return [
                'type'   => 'image',
                'source' => [
                    'type'       => 'base64',
                    'media_type' => 'image/jpeg',
                    'data'       => base64_encode($data),
                ],
            ];
        } catch (\Throwable $e) {
            Log::warning('CarteGriseExtractor: image illisible', ['error' => $e->getMessage()]);

            return null;
        }
    }

    private function systemPrompt(): string
    {
        return <<<'TXT'
You extract VEHICLE data from a Moroccan vehicle registration card ("Certificat d'Immatriculation" / "شهادة التسجيل"). The document is bilingual (French + Arabic); read the French values.

PRIVACY — ABSOLUTE RULES:
- NEVER extract or output the owner's name ("Propriétaire"), the owner's address ("Adresse"), the previous registration ("Immatriculation antérieure"), or the machine-readable zone (the long MRZ line like "D1VMA...<<<"). Ignore them completely.
- Output ONLY the vehicle fields defined below.

Return ONLY a single JSON object, no prose, no markdown fences, with EXACTLY these keys (use null when a value is absent or unreadable):
{
  "marque": string|null,                      // back: "Marque", e.g. "AUDI"
  "modele": string|null,                      // back: "Modèle", e.g. "A4 TDI 2.0L EXCLUSIVE"
  "carburant": string|null,                   // back: "Type carburant", e.g. "Diesel"
  "annee": integer|null,                       // YEAR from "Première mise en circulation"
  "date_premiere_circulation": string|null,    // "YYYY-MM-DD" from "Première mise en circulation"
  "puissance_fiscale": integer|null,           // back: "Puissance fiscale"
  "matricule": string|null,                    // front: "Numéro d'immatriculation"
  "vin": string|null,                          // back: "N° du chassis"
  "nombre_places": integer|null,               // back: "Nombre de places"
  "genre": string|null,                        // back: "Genre", e.g. "CONDUITE INTERIEURE"
  "confidence": {                              // per-field confidence 0..1
    "marque": number, "modele": number, "carburant": number, "annee": number,
    "date_premiere_circulation": number, "puissance_fiscale": number,
    "matricule": number, "vin": number, "nombre_places": number, "genre": number
  }
}
Do not output owner name, address, previous registration, or MRZ under any key.
TXT;
    }

    private function userPrompt(): string
    {
        return "Extract the vehicle fields from this Moroccan carte grise. Respond with the JSON object only.";
    }

    /** Récupère le premier bloc texte de la réponse Messages API. */
    private function firstText(?array $json): string
    {
        foreach ($json['content'] ?? [] as $block) {
            if (($block['type'] ?? null) === 'text' && isset($block['text'])) {
                return $block['text'];
            }
        }

        return '';
    }

    /** Tolère d'éventuelles fences ```json ... ``` autour du JSON. */
    private function parseJson(string $text): ?array
    {
        $text = trim($text);
        if ($text === '') {
            return null;
        }
        // Isole le premier objet { ... }
        $start = strpos($text, '{');
        $end   = strrpos($text, '}');
        if ($start === false || $end === false || $end <= $start) {
            return null;
        }
        $json = substr($text, $start, $end - $start + 1);
        $data = json_decode($json, true);

        return is_array($data) ? $data : null;
    }

    /** Sépare les valeurs des confidences, en ne gardant QUE les champs véhicule. */
    private function normalize(array $parsed): array
    {
        $confidence = is_array($parsed['confidence'] ?? null) ? $parsed['confidence'] : [];
        $data = [];
        foreach (self::FIELDS as $key) {
            $data[$key] = $parsed[$key] ?? null;
        }

        // Normalisation légère des types numériques.
        foreach (['annee', 'puissance_fiscale', 'nombre_places'] as $int) {
            $data[$int] = is_numeric($data[$int]) ? (int) $data[$int] : null;
        }

        $conf = [];
        foreach (self::FIELDS as $key) {
            $conf[$key] = isset($confidence[$key]) && is_numeric($confidence[$key])
                ? max(0, min(1, (float) $confidence[$key]))
                : 0.0;
        }

        return ['ok' => true, 'data' => $data, 'confidence' => $conf];
    }
}
