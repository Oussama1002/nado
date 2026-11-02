<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Symfony\Component\Process\Exception\RuntimeException;
use Symfony\Component\Process\Process;

/**
 * Extraction des champs VÉHICULE d'une carte grise marocaine — 100 % local,
 * sans API ni service externe : OCR via Tesseract (binaire sur le serveur)
 * puis analyse par étiquettes/MRZ du gabarit standard.
 *
 * RÈGLE VIE PRIVÉE : tout reste sur le serveur. On n'extrait QUE des champs
 * véhicule. Le nom du propriétaire, l'adresse, l'immatriculation antérieure et
 * le nom présent dans la MRZ ne sont jamais capturés (de la MRZ on ne lit que
 * le matricule + la date).
 */
class CarteGriseExtractor
{
    /** Clés véhicule renvoyées (aucune donnée personnelle). */
    public const FIELDS = [
        'marque', 'modele', 'carburant', 'annee', 'date_premiere_circulation',
        'puissance_fiscale', 'matricule', 'vin', 'nombre_places', 'genre',
    ];

    /**
     * @param  UploadedFile[]  $files  1 ou 2 images (recto / verso)
     * @return array{ok:bool, data?:array, confidence?:array, error?:string}
     */
    public function extract(array $files): array
    {
        $texts = [];
        foreach ($files as $file) {
            $text = $this->ocr($file);
            if ($text === null) {
                return ['ok' => false, 'error' => 'ocr_unavailable']; // binaire Tesseract introuvable
            }
            if (trim($text) !== '') {
                $texts[] = $text;
            }
        }

        $full = trim(implode("\n", $texts));
        if ($full === '') {
            return ['ok' => false, 'error' => 'parse'];
        }

        return $this->parse($full);
    }

    /**
     * OCR d'une image via Tesseract local. Retourne le texte, '' si rien,
     * ou null si le binaire Tesseract est introuvable.
     */
    private function ocr(UploadedFile $file): ?string
    {
        // Tesseract ne lit pas les PDF nativement — on se concentre sur les images.
        if ($file->getMimeType() === 'application/pdf') {
            return '';
        }

        $prepared = $this->preprocess($file);
        if ($prepared === null) {
            return '';
        }

        $bin      = (string) config('services.tesseract.path', 'tesseract');
        $lang     = (string) config('services.tesseract.lang', 'fra+eng');
        $tessdata = config('services.tesseract.tessdata'); // dossier des .traineddata (optionnel)

        $text = $this->runTesseract($bin, $prepared, $lang, $tessdata);
        if ($text === null && $lang !== 'eng') {
            // Pack de langue 'fra' peut-être absent → on retente en anglais (texte latin).
            $text = $this->runTesseract($bin, $prepared, 'eng', $tessdata);
        }

        @unlink($prepared);

        return $text; // null = binaire introuvable
    }

    /** Lance Tesseract ; null si le binaire est introuvable, '' si échec d'exécution. */
    private function runTesseract(string $bin, string $imagePath, string $lang, ?string $tessdata): ?string
    {
        $cmd = [$bin, $imagePath, 'stdout', '-l', $lang, '--psm', '6'];
        if (! empty($tessdata)) {
            $cmd[] = '--tessdata-dir';
            $cmd[] = $tessdata;
        }

        try {
            $process = new Process($cmd);
            $process->setTimeout(60);
            $process->run();
        } catch (RuntimeException $e) {
            Log::error('CarteGriseExtractor: tesseract introuvable', ['bin' => $bin, 'error' => $e->getMessage()]);

            return null;
        }

        if (! $process->isSuccessful()) {
            Log::warning('CarteGriseExtractor: tesseract a échoué', [
                'exit' => $process->getExitCode(),
                'err'  => $process->getErrorOutput(),
            ]);

            return '';
        }

        return $process->getOutput();
    }

    /** Pré-traitement : agrandir + niveaux de gris + contraste → PNG temporaire. */
    private function preprocess(UploadedFile $file): ?string
    {
        try {
            $manager = new ImageManager(new Driver);
            $img = $manager->read($file->getRealPath());
            if ($img->width() < 1700) {
                $img->scale(width: 1700); // les photos basse résolution lisent mal
            }
            $img->greyscale();
            $img->contrast(8);

            $tmp = tempnam(sys_get_temp_dir(), 'cg_') . '.png';
            $img->toPng()->save($tmp);

            return $tmp;
        } catch (\Throwable $e) {
            Log::warning('CarteGriseExtractor: image illisible', ['error' => $e->getMessage()]);

            return null;
        }
    }

    // ───────────────────────── Analyse du texte OCR ─────────────────────────

    private function parse(string $text): array
    {
        // On enlève l'arabe (étiquettes de droite) pour isoler les valeurs françaises.
        $clean = $this->stripArabic($text);
        $lines = array_values(array_filter(array_map('trim', preg_split('/\r?\n/', $clean))));

        $data = [
            'marque'                    => $this->afterLabel($lines, ['marque']),
            'modele'                    => $this->afterLabel($lines, ['modele', 'modèle']),
            'carburant'                 => $this->carburant($clean, $lines),
            'puissance_fiscale'         => $this->intAfterLabel($lines, ['puissance fiscale', 'fiscale']),
            'nombre_places'             => $this->intAfterLabel($lines, ['nombre de places', 'places']),
            'genre'                     => $this->afterLabel($lines, ['genre']),
            'vin'                       => $this->vin($clean),
            'matricule'                 => null,
            'date_premiere_circulation' => null,
            'annee'                     => null,
        ];

        // Matricule + date : d'abord la MRZ (fiable), sinon le texte imprimé.
        [$mrzMatricule, $mrzDate] = $this->fromMrz($text);
        $data['matricule'] = $this->matricule($lines) ?? $mrzMatricule;

        $date = $this->premiereCirculation($lines) ?? $mrzDate;
        if ($date) {
            $data['date_premiere_circulation'] = $date;
            $data['annee'] = (int) substr($date, 0, 4);
        }

        // Confiance heuristique : trouvé via MRZ/VIN = très fiable, via étiquette = fiable.
        $conf = [];
        foreach (self::FIELDS as $key) {
            $conf[$key] = ($data[$key] === null || $data[$key] === '') ? 0.0 : 0.85;
        }
        if ($data['vin'] !== null) {
            $conf['vin'] = 0.95;
        }
        if ($mrzMatricule !== null && $data['matricule'] === $mrzMatricule) {
            $conf['matricule'] = 0.95;
        }

        return ['ok' => true, 'data' => $data, 'confidence' => $conf];
    }

    private function stripArabic(string $s): string
    {
        return preg_replace('/[\x{0600}-\x{06FF}\x{0750}-\x{077F}\x{FB50}-\x{FDFF}\x{FE70}-\x{FEFF}]+/u', ' ', $s);
    }

    /** Valeur située après une étiquette (sur la même ligne). */
    private function afterLabel(array $lines, array $labels): ?string
    {
        foreach ($lines as $line) {
            $low = $this->deaccent(mb_strtolower($line));
            foreach ($labels as $label) {
                $l = $this->deaccent(mb_strtolower($label));
                $pos = strpos($low, $l);
                if ($pos !== false) {
                    $value = trim(substr($line, $pos + strlen($label)));
                    $value = trim($value, " :.-\t•|");
                    if ($value !== '' && mb_strlen($value) <= 60) {
                        return $value;
                    }
                }
            }
        }

        return null;
    }

    private function intAfterLabel(array $lines, array $labels): ?int
    {
        $raw = $this->afterLabel($lines, $labels);
        if ($raw !== null && preg_match('/(\d{1,3})/', $raw, $m)) {
            return (int) $m[1];
        }

        return null;
    }

    /** Carburant : étiquette puis repli par mots-clés. */
    private function carburant(string $text, array $lines): ?string
    {
        $raw = $this->afterLabel($lines, ['type carburant', 'carburant', 'energie', 'énergie']);
        $hay = mb_strtolower(($raw ?? '') . ' ' . $text);

        return match (true) {
            str_contains($hay, 'diesel') || str_contains($hay, 'gasoil') || str_contains($hay, 'gazole') => 'Diesel',
            str_contains($hay, 'essence') || str_contains($hay, 'petrol') => 'Essence',
            str_contains($hay, 'electri') => 'Electrique',
            str_contains($hay, 'hybrid') => 'Hybride',
            default => null,
        };
    }

    /** N° de châssis : un VIN est 17 caractères alphanum sans I, O, Q. */
    private function vin(string $text): ?string
    {
        if (preg_match('/\b([A-HJ-NPR-Z0-9]{17})\b/', strtoupper($text), $m)) {
            return $m[1];
        }

        return null;
    }

    /** Matricule imprimé : NNNNN-L-NN (lettre latine après OCR de l'arabe). */
    private function matricule(array $lines): ?string
    {
        foreach ($lines as $line) {
            $low = $this->deaccent(mb_strtolower($line));
            if (str_contains($low, 'immatriculation') && ! str_contains($low, 'anterieure')) {
                if (preg_match('/(\d{2,6})\s*[-ـ]?\s*([A-Za-z]{1,3})\s*[-ـ]?\s*(\d{1,3})/', $line, $m)) {
                    return strtoupper($m[1] . '-' . $m[2] . '-' . $m[3]);
                }
            }
        }

        return null;
    }

    /** Date de 1re mise en circulation (dd/mm/yyyy) → YYYY-MM-DD. */
    private function premiereCirculation(array $lines): ?string
    {
        foreach ($lines as $line) {
            $low = $this->deaccent(mb_strtolower($line));
            if (str_contains($low, 'circulation') && preg_match('#(\d{2})/(\d{2})/(\d{4})#', $line, $m)) {
                return "{$m[3]}-{$m[2]}-{$m[1]}";
            }
        }

        return null;
    }

    /**
     * MRZ (3e ligne) : on ne lit QUE le matricule et la date (DDMMYY),
     * jamais le nom qui suit. Ex. D1VMA27314B72<221119ELHADI<<<3.
     *
     * @return array{0:?string,1:?string}  [matricule, date YYYY-MM-DD]
     */
    private function fromMrz(string $text): array
    {
        $compact = str_replace(' ', '', strtoupper($text));
        if (preg_match('/(\d{2,6})([A-Z]{1,3})(\d{1,3})<(\d{6})/', $compact, $m)) {
            $matricule = "{$m[1]}-{$m[2]}-{$m[3]}";
            $dd = substr($m[4], 0, 2);
            $mm = substr($m[4], 2, 2);
            $yy = (int) substr($m[4], 4, 2);
            $year = $yy <= ((int) date('y') + 1) ? 2000 + $yy : 1900 + $yy;
            $date = checkdate((int) $mm, (int) $dd, $year) ? sprintf('%04d-%s-%s', $year, $mm, $dd) : null;

            return [$matricule, $date];
        }

        return [null, null];
    }

    private function deaccent(string $s): string
    {
        return strtr($s, [
            'à' => 'a', 'â' => 'a', 'ä' => 'a', 'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e',
            'î' => 'i', 'ï' => 'i', 'ô' => 'o', 'ö' => 'o', 'ù' => 'u', 'û' => 'u', 'ü' => 'u', 'ç' => 'c',
        ]);
    }
}
