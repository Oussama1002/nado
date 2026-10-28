<?php

namespace App\Http\Controllers;

use App\Models\Annonces;
use App\Models\Avistext;
use App\Models\Avisvideos;
use App\Models\Publication;
use App\Models\NadoAutoEnchere;
use Illuminate\Support\Str;
use App\Models\Modele;
use App\Services\Valuation\ValuationBaseResolver;
use App\Services\Valuation\ValuationRules;
use App\Services\Valuation\VehicleTrims;
use App\Services\Valuation\VehicleValuationService;
use App\Models\Brand;
use App\Models\Imgslider;
use App\Models\Article;
use App\Models\Story;

use App\Models\Videosads;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class FrontController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $articles = Article::where('is_active', 1)
    ->orderBy('created_at', 'desc')
    ->limit(3)
    ->get();

        $leftBanner = Publication::where('placement', 'left-banner')->where('status', true)->first();
        $rightBanner = Publication::where('placement', 'right-banner')->where('status', true)->first();
        $videoAds = Videosads::latest()->take(6)->get(); // 👈 Add this line
        // Stories come from the database, not from scanning storage/app/public/stories.
        // NO expiry filter: stories stay visible until an admin deletes them. Filtering
        // on expires_at made the section silently empty itself, twice.
        $stories = Story::query()
            ->orderByDesc('id')
            ->limit(6)
            ->get();

        // 6 derniers vehicules mis en ligne (toutes categories) - affiches 3 par 3
        // dans un slider sur la page d'accueil.
        $latestAds = Annonces::with(['brand', 'modeleRelation'])
            ->where('active', 1)
            ->where('status', 'disponible')
            ->orderByDesc('id')
            ->limit(6)
            ->get();

        $centerBanner = Publication::where('placement', 'center-banner')->where('status', true)->first();
        $imgslider = Cache::remember('Imgslider', 86400, function () {
            return Imgslider::where('active', 1)->limit(6)->get();
        });
$autoEncheres = Cache::remember('autoEncheres', 86400, function () {
        return NadoAutoEnchere::with(['brand', 'modeleRelation'])
            ->where('active', 1)
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();
    });
        $vdAds = Cache::remember('vdAds', 86400, function () {
            return Videosads::orderBy('id', 'desc')->limit(6)->get();
        });

        $avistext = Cache::remember('avistext', 86400, function () {
            return Avistext::orderBy('id', 'desc')->limit(6)->get();
        });
        $avisvideos = Cache::remember('avisvideos', 86400, function () {
            return Avisvideos::orderBy('id', 'desc')->limit(6)->get();
        });
   $brands = Cache::remember('brands', 86400, function () {
    return Brand::all()->filter(function ($brand) {
        return \App\Models\Annonces::where('mark', $brand->name)->exists();
    });
});


    $similar_adsp = Cache::remember('similar_adsp', 86400, function () {
    return Annonces::where('active', 1)
        ->where('status', 'disponible')
        ->where('particulie', 1) // And particulier
        ->where('certifie', 0) // Only NOT certified
        ->orderBy('id', 'desc')
        ->limit(9)
        ->get();
});

$similar_ads_pro = Cache::remember('similar_ads_pro', 86400, function () {
    return Annonces::where('active', 1)
        ->where('status', 'disponible')
        ->where('pro', 1)
        ->orderBy('id', 'desc')
        ->limit(9)
        ->get();
});

$similar_ads = Cache::remember('similar_ads', 86400, function () {
    return Annonces::where('active', 1)
        ->where('status', 'disponible')
        ->where('certifie', 1) // Certified
        ->orderBy('id', 'desc')
        ->limit(9)
        ->get();
});


        $villes = Cache::remember('villes', 86400, function () {
            return \App\Models\Ville::orderBy('name')->get();
        });

        return view('front', ['imgslider' => $imgslider,    'articles'      => $articles,
        'autoEncheres' => $autoEncheres, 'villes' => $villes,
 'vdAds' => $vdAds, 'avistext' => $avistext, 'avisvideos' => $avisvideos, 'brands' => $brands,
            'similar_ads' => $similar_ads, 'latestAds' => $latestAds, 'videoAds' =>$videoAds, 'stories' => $stories, 'leftBanner' => $leftBanner, 'rightBanner' => $rightBanner, 'centerBanner' => $centerBanner,'similar_adsp' => $similar_adsp, 'similar_ads_pro' => $similar_ads_pro, 'formatDate' => function ($date) {
                return $this->formatDateLabel($date);
            }]);
    }
    
public function articlesList()
{
    $articles = Article::where('is_active', 1)
        ->orderBy('created_at', 'desc')
        ->paginate(10); // or get() if you want all

    return view('articles.index', compact('articles'));
}

public function articleShow(string $slug)
{
    $article = Article::where('slug', $slug)
        ->where('is_active', 1)
        ->firstOrFail();

    $formatDate = function ($date) {
        return $this->formatDateLabel($date);
    };

    return view('articles.article-show', compact('article', 'formatDate'));
}

public function estimateView()
{
    return view('estimate-value', $this->estimateFormData());
}

/**
 * Estimation instantanée: validates the form, then delegates to the valuation engine
 * (App\Services\Valuation). All coefficients live in the valuation_rules table.
 */
public function estimate(Request $r)
{
    $data = $r->validate([
        'brand'                => 'required|string|max:100',
        'model'                => 'required|string|max:100',
        'version'              => 'nullable|string|max:100',
        'version_other'        => 'nullable|string|max:100',
        // The year is derived from the 1st registration date, which is now the only
        // field asked for (the duplicate 'Année' select was removed from the form).
        'first_registration'   => 'required|date|after_or_equal:1990-01-01|before_or_equal:today',
        'km'                   => 'required|numeric|min:0|max:1500000',
        'fuel'                 => 'required|string',
        'power_cv'             => 'nullable|integer|min:1|max:200',
        'gearbox'              => 'nullable|in:Manuelle,Automatique',
        'origin'               => 'nullable|in:' . implode(',', array_keys(VehicleValuationService::ORIGINS)),
        'condition_mechanical' => 'required|in:' . implode(',', array_keys(VehicleValuationService::CONDITIONS)),
        'condition_body'       => 'required|in:' . implode(',', array_keys(VehicleValuationService::CONDITIONS)),
        'condition_interior'   => 'required|in:' . implode(',', array_keys(VehicleValuationService::CONDITIONS)),
        'maintenance'          => 'required|in:' . implode(',', array_keys(VehicleValuationService::MAINTENANCE)),
        'owners'               => 'nullable|in:' . implode(',', array_keys(VehicleValuationService::OWNERS)),
        'accident'             => 'required|in:' . implode(',', array_keys(VehicleValuationService::ACCIDENTS)),
        'options'              => 'nullable|array',
        'options.*'            => 'in:' . implode(',', array_keys(VehicleValuationService::OPTIONS)),
        'reference_value'      => 'nullable|numeric|min:0|max:50000000',
    ]);

    // "Autre (préciser)" in the version list → use the typed text
    if (($data['version'] ?? null) === '__other') {
        $data['version'] = trim((string) ($data['version_other'] ?? '')) ?: null;
    }
    unset($data['version_other']);

    $data['brand'] = trim($data['brand']);
    $data['model'] = trim($data['model']);
    $data['fuel']  = $this->normalizeFuel($data['fuel']);
    $data['km']    = (int) $data['km'];
    $data['options'] = $data['options'] ?? [];

    // Age is counted from the 1st registration date.
    $firstRegistration = new \DateTimeImmutable($data['first_registration']);
    $today = new \DateTimeImmutable('today');
    if ($firstRegistration > $today) {
        $firstRegistration = $today;
    }
    $data['year'] = (int) $firstRegistration->format('Y');

    $rules    = ValuationRules::load();
    $engine   = new VehicleValuationService($rules['values']);
    $resolver = new ValuationBaseResolver($engine);
    $base     = $resolver->resolve(
        $data['brand'], $data['model'], (int) $data['year'], $data['reference_value'] ?? null
    );

    $valuation = $engine->calculate(
        array_merge($data, [
            'first_registration' => $firstRegistration,
            'brand_tier'         => $resolver->brandTier($data['brand']),
        ]),
        $base,
        $base['comparables'],
        $today
    );
    $valuation['first_registration_estimated'] = false; // always supplied now
    $valuation['comparables_median'] = $base['comparables_median'];
    $valuation['rules_source'] = $rules['source'];
    $valuation['performed_by'] = 'Système NADO (calcul automatique)';

    // Keep a record so the team can review what clients estimated (admin > Estimations).
    try {
        \App\Models\Estimation::create([
            'client_id'          => \Illuminate\Support\Facades\Auth::guard('client')->id(),
            'brand'              => $data['brand'] ?? null,
            'model'              => $data['model'] ?? null,
            'version'            => $data['version'] ?? null,
            'year'               => $data['year'] ?? null,
            'first_registration' => $firstRegistration->format('Y-m-d'),
            'km'                 => $data['km'] ?? null,
            'fuel'               => $data['fuel'] ?? null,
            'gearbox'            => $data['gearbox'] ?? null,
            'power_cv'           => $data['power_cv'] ?? null,
            'origin'             => $data['origin'] ?? null,
            'estimated_value'    => $valuation['final_value'] ?? null,
            'range_min'          => $valuation['range']['min'] ?? null,
            'range_max'          => $valuation['range']['max'] ?? null,
            'confidence'         => $valuation['confidence']['label'] ?? null,
            'payload'            => $data,
        ]);
    } catch (\Throwable $e) {
        // Never block the visitor's estimate because the record could not be stored.
        \Illuminate\Support\Facades\Log::error('Estimation not saved: ' . $e->getMessage());
    }

    return view('estimate-value', array_merge($this->estimateFormData(), [
        'valuation' => $valuation,
        'input'     => $data,
    ]));
}

/** Choice lists shared by the form and the result. */
private function estimateFormData(): array
{
    $brands = Brand::orderBy('name')->get();

    return [
        'brands'           => $brands,
        'trimsByBrand'     => VehicleTrims::forBrands($brands->pluck('name')),
        'fuelChoices'      => VehicleValuationService::FUELS,
        'originChoices'    => VehicleValuationService::ORIGINS,
        'conditionChoices' => VehicleValuationService::CONDITIONS,
        'maintenanceChoices' => VehicleValuationService::MAINTENANCE,
        'ownerChoices'     => VehicleValuationService::OWNERS,
        'accidentChoices'  => VehicleValuationService::ACCIDENTS,
        'optionChoices'    => VehicleValuationService::OPTIONS,
    ];
}

/**
 * Normalize a fuel string to one of: essence, diesel, hybride, electrique, gpl.
 * Strips accents and lowercases (PHP's strtolower can't handle 'É').
 */
private function normalizeFuel($fuel): string
{
    $fuel = strtolower(trim((string) $fuel));
    $fuel = strtr($fuel, [
        'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e',
        'É' => 'e', 'È' => 'e', 'Ê' => 'e', 'Ë' => 'e',
        'à' => 'a', 'á' => 'a',
    ]);
    return in_array($fuel, ['essence', 'diesel', 'hybride', 'electrique', 'gpl'], true)
        ? $fuel
        : 'essence';
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public static function formatDateLabel($date)
    {

        $datediff = time() - strtotime($date);
        $tm = 60;
        $res = round($datediff / $tm);
        $tt = 'minutes';

        if ($res == 0) {
            $res = 1;
        }

        if ($res > 50) {
            $res = round($datediff / ($tm * 60));
            $tt = 'heures';
            if ($res > 23) {
                $res = round($datediff / ($tm * 60 * 24));
                $tt = 'jours';
                if ($res > 30) {
                    $res = round($datediff / ($tm * 60 * 24 * 30));
                    $tt = 'mois';
                    if ($res >= 12) {
                        $res = round($datediff / ($tm * 60 * 24 * 30 * 12));
                        $tt = 'ans';
                    }
                }
            }
        }

        return 'Il y a '.$res.' '.$tt;

    }
}
