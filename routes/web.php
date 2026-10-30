<?php

use App\Http\Controllers\Client\Auth\RegisterController;
use App\Http\Controllers\Client\Auth\LoginController as ClientAuthController;
use App\Http\Controllers\Client\Auth\LogoutController;
use App\Http\Controllers\Client\ClientDashboardController;
use App\Http\Controllers\Client\ClientAnnonceController;
use App\Http\Controllers\Client\EquipeController;
use App\Http\Controllers\Admin\Auth\AuthenticatedSessionController;


use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\DemandeController;
use App\Http\Controllers\Admin\DemandeAdminController;
use App\Http\Controllers\Admin\StoriesController;
use App\Http\Controllers\Admin\SocialPostController;

use App\Http\Controllers\AccessoiresController;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    PlanController,
    SellController,
    FavoriteController,
    ContactController,
    ServicesController,
    FinancementController,
    RepriseController,
    VenteController,
    BuyController,
    NadoAutoEnchereController,
    ComparisonController,
    Admin\AnnoncesController,
    Admin\PublicationController,
    Admin\AdminNadoAutoEnchereController,

    Admin\EntretienController,

    Admin\AvisTextController,
    Admin\FinancementSettingsController,
    Admin\AvisVdController,
    Admin\BrandsController,
    Admin\ColorController,
    Admin\ClientController,

    Admin\FinanceController,
    Admin\ImgSlideController,
    Admin\ModelsController,
    Admin\OffreController,
    Admin\RepriseVenteController,
    Admin\UserController,
    Admin\VenteAdminController,
    Admin\VideoAdsController,
    Admin\VilleController,
    Admin\AutoEnchereController,
    Admin\NadoAutoEnchereBidController,
    Admin\SettingsController
};
use Illuminate\Support\Facades\Auth;

// At the bottom of web.php or where routes are defined

// Public Routes

// Language switch (FR / AR). Stores the choice in session + a 1-year cookie,
// then returns to the page the visitor came from. See App\Http\Middleware\SetLocale.
Route::get('/locale/{locale}', function (string $locale) {
    if (in_array($locale, \App\Http\Middleware\SetLocale::SUPPORTED, true)) {
        session(['locale' => $locale]);
        cookie()->queue(cookie('locale', $locale, 60 * 24 * 365));
    }

    return redirect()->back(fallback: '/');
})->name('locale.switch');

Route::get('/admin/annonces/approval', [AnnoncesController::class, 'approval'])->name('admin.annonces.approval');
Route::get('/admin/annonces/showAnnonce/{id}', [AnnoncesController::class, 'showAnnonce'])->name('admin.annonces.showAnnonce');
Route::get('/', [FrontController::class, 'index']);
Route::get('/articles/{slug}', [FrontController::class, 'articleShow'])
    ->name('articles.show');
Route::get('/articles', [FrontController::class, 'articlesList'])
    ->name('articles.index');

// L'estimation est reservee aux clients connectes.
Route::middleware(['auth:client'])->group(function () {
    Route::get('/estimate-value', [App\Http\Controllers\FrontController::class, 'estimateView'])
        ->name('estimate.page');
    Route::post('/estimate-value', [App\Http\Controllers\FrontController::class, 'estimate'])
        ->name('estimate.value');
});

// Client Auth Routes
Route::get('/client/login', [ClientAuthController::class, 'showLoginForm'])->name('client.login');
Route::post('/client/login', [ClientAuthController::class, 'login'])->name('client.login.submit');

// nadoautoenchere login (end any existing client session first)
// (moved below near nadoautoenchere routes)

Route::get('/client/register', [RegisterController::class, 'showRegistrationForm'])->name('client.register');
Route::post('/client/register', [RegisterController::class, 'register'])->name('client.register.submit');
Route::post('/client/register/phone', [RegisterController::class, 'registerWithPhone'])->name('client.register.phone');

// Email verification — same model as password reset: signed link emailed to the client.
Route::get('/client/verify/pending', [RegisterController::class, 'showVerifyPending'])
    ->name('client.verify.pending');
Route::get('/client/verify/email/{id}', [RegisterController::class, 'verifyEmail'])
    ->middleware('signed')
    ->name('client.verify.email');
Route::post('/client/verify/resend', [RegisterController::class, 'resendVerification'])
    ->middleware('throttle:6,1')
    ->name('client.verify.resend');

// Client password reset
Route::get('/client/forgot-password', [\App\Http\Controllers\Client\Auth\ClientPasswordResetController::class, 'showLinkRequestForm'])->name('client.password.request');
Route::post('/client/forgot-password', [\App\Http\Controllers\Client\Auth\ClientPasswordResetController::class, 'sendResetLinkEmail'])->name('client.password.email');
Route::get('/client/reset-password/{token}', [\App\Http\Controllers\Client\Auth\ClientPasswordResetController::class, 'showResetForm'])->name('client.password.reset');
Route::post('/client/reset-password', [\App\Http\Controllers\Client\Auth\ClientPasswordResetController::class, 'reset'])->name('client.password.update');

// WhatsApp OTP verification
Route::get('/client/verify-whatsapp', [\App\Http\Controllers\Client\Auth\VerifyWhatsAppController::class, 'show'])->name('client.verify.whatsapp');
Route::post('/client/verify-whatsapp', [\App\Http\Controllers\Client\Auth\VerifyWhatsAppController::class, 'verify'])->name('client.verify.whatsapp.submit');
Route::post('/client/verify-whatsapp/resend', [\App\Http\Controllers\Client\Auth\VerifyWhatsAppController::class, 'resend'])->name('client.verify.whatsapp.resend');
// Inside auth:client group
Route::middleware(['auth:client'])->prefix('client')->name('client.')->group(function () {
    Route::get('/dashboard', [ClientDashboardController::class, 'index'])->name('dashboard');
    Route::resource('annonces', ClientAnnonceController::class); // <-- this should exist
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites');
    Route::resource('equipe', \App\Http\Controllers\Client\EquipeController::class);
    Route::resource('nadoautoencheresbid', \App\Http\Controllers\Client\ClientNadoAutoEncheresBidController::class);

    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/', [FrontController::class, 'index'])->name('home');
    // Bids routes
    Route::get('/nadoautoencheres/bids', [ClientNadoAutoEncheresBidController::class, 'index'])
        ->name('nadoautoencheresbid.index');
    // Recherche entreprises (AJAX)
    Route::get('search-companies', [\App\Http\Controllers\NadoAutoEnchereController::class, 'searchCompanies'])
        ->name('companies.search');

    Route::get('/export', [ClientNadoAutoEncheresBidController::class, 'export'])
        ->name('nadoautoencheresbid.export');

    Route::delete('/nadoautoencheres/bids/{id}', [ClientNadoAutoEncheresBidController::class, 'destroy'])
        ->name('nadoautoencheresbid.destroy');

    // Discussion routes
    Route::post('discussions/start', [\App\Http\Controllers\Client\DiscussionController::class, 'startOrGet'])->name('discussions.start');
    Route::get('discussions', [\App\Http\Controllers\Client\DiscussionController::class, 'index'])->name('discussions.index');
    Route::get('discussions/{discussion}', [\App\Http\Controllers\Client\DiscussionController::class, 'show'])->name('discussions.show');
    Route::post('discussions/{discussion}/send', [\App\Http\Controllers\Client\DiscussionController::class, 'sendMessage'])->name('discussions.send');
    Route::get('discussions/{discussion}/messages', [\App\Http\Controllers\Client\DiscussionController::class, 'messages'])->name('discussions.messages');
});

// Route::prefix('admin')->group(function () {
//     Route::get('nadoautoenchere/create', [NadoAutoEnchereController::class, 'create'])->name('admin.nadoautoenchere.create');
//     Route::post('nadoautoenchere', [NadoAutoEnchereController::class, 'store'])->name('admin.nadoautoenchere.store');
//     Route::get('nadoautoenchere/{id}/edit', [NadoAutoEnchereController::class, 'edit'])->name('admin.nadoautoenchere.edit');
//     Route::put('nadoautoenchere/{id}', [NadoAutoEnchereController::class, 'update'])->name('admin.nadoautoenchere.update');
//     Route::delete('nadoautoenchere/{id}', [NadoAutoEnchereController::class, 'destroy'])->name('admin.nadoautoenchere.destroy');

//    });
// Public route (no auth)
Route::get('/nadoautoenchere/prelogin', [NadoAutoEnchereController::class, 'prelogin'])
    ->name('nadoautoenchere.prelogin');

Route::get('/nadoautoenchere/register', [NadoAutoEnchereController::class, 'register'])
    ->name('nadoautoenchere.register');

Route::get('/nadoautoenchere/login', function () {
    // ensure clean session when switching to enchere login
    if (Auth::guard('client')->check()) {
        Auth::guard('client')->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
    }
    // Clear any stored redirect (e.g. from vente modal → client login)
    // so the enchere login always goes to prelogin after auth
    request()->session()->forget('url.intended');
    return view('nadoautoenchere.login');
})->name('nadoautoenchere.login');

// Alias submit route for the enchere login page
Route::post('/nadoautoenchere/login', [ClientAuthController::class, 'login'])
    ->name('nadoautoenchere.login.submit');

Route::get('/nadoautoenchere/vendre', function () {
    $brandsData = \App\Models\Brand::with('modeles')->orderBy('name')->get()->map(function ($b) {
        return [
            'id'      => $b->id,
            'name'    => $b->name,
            'modeles' => $b->modeles->map(function ($m) {
                return ['id' => $m->id, 'name' => $m->name];
            })->values(),
        ];
    })->values();
    return view('nadoautoenchere.vendre', ['brandsJson' => json_encode($brandsData)]);
})->name('nadoautoenchere.vendre');

// ✅ Use controller with middleware
Route::middleware(['flotte.enchere'])->group(function () {
    Route::get('/nadoautoenchere/categories', [NadoAutoEnchereController::class, 'categories'])->name('nadoautoenchere.categories');
    Route::get('/client/filter-categories', [App\Http\Controllers\NadoAutoEnchereController::class, 'filterCategories'])
        ->name('nadoautoenchere.filterCategories');

    Route::get('/nadoautoenchere', [NadoAutoEnchereController::class, 'index'])->name('nadoautoenchere.index');
    Route::get('/nadoautoenchere/{id}', [NadoAutoEnchereController::class, 'show'])->name('nadoautoenchere.show');
    Route::post('/nadoautoenchere/{id}/proposer-prix', [NadoAutoEnchereController::class, 'proposerPrix'])
        ->name('nadoautoenchere.proposerPrix');
    // Add a prefix to the PDF route
    Route::get('/nadoautoenchere/pdf/{pdf}', [NadoAutoEnchereController::class, 'showPdf'])
        ->name('nadoautoenchere.pdf');
});

Route::get('/', [FrontController::class, 'index'])->name('index');
Route::resource('/vendre', SellController::class);
Route::resource('/contactez-nous', ContactController::class);
Route::get('/plans', [PlanController::class, 'index'])->name('plans.index');
// Show index.blade.php (with the two boxes and button)
// Page d’intro (juste le bouton)
Route::get('/reprise', [App\Http\Controllers\RepriseController::class, 'index'])->name('reprise.index');
Route::get('/reprise/{id}', [App\Http\Controllers\RepriseController::class, 'destroy'])->name('reprise.destroy');

Route::get('/reprise/store', [App\Http\Controllers\RepriseController::class, 'store'])->name('reprise.reprise');

Route::get('/get-models', [App\Http\Controllers\RepriseController::class, 'getModels'])->name('get.models');

// Public
Route::get('/nadoautoenchere', [NadoAutoEnchereController::class, 'publicIndex'])->name('nadoautoenchere.index');
// Route::get('/nadoautoenchere/{id}', [NadoAutoEnchereController::class, 'show'])->name('nadoautoenchere.show');

// Page du formulaire (avec les données)
//  Route::get('/reprise/formulaire', [App\Http\Controllers\RepriseController::class, 'form'])->name('reprise.form'); 
Route::get('/reprises/formulaire', [App\Http\Controllers\RepriseController::class, 'form'])->name('reprise.form');




Route::get('/vente', [VenteController::class, 'index'])->name('vente.index');
Route::get('/vente/formulaire', [VenteController::class, 'form'])->name('vente.form');
Route::get('/vente/particulier', [VenteController::class, 'particulier'])->name('vente.particulier');
Route::get('/vente/pro', [VenteController::class, 'pro'])->name('vente.pro');
Route::post('/vente/pro/store', [VenteController::class, 'storePro'])->name('vente.pro.store');
Route::get('/get-models', [App\Http\Controllers\VenteController::class, 'getModels'])->name('get.models');
Route::post('/vente/store', [VenteController::class, 'store'])->name('vente.store')->middleware('auth:client');

// Fresh CSRF token for long-lived forms (vente modal) — avoids 419 when the
// session was regenerated in another tab (login/logout) or has expired
Route::get('/csrf-token', function () {
    return response()->json(['token' => csrf_token()])
        ->header('Cache-Control', 'no-store');
})->name('csrf.token');

Route::get('/a-propos', function () {
    $stories = \App\Models\Story::query()
        ->where('expires_at', '>', now())
        ->orderByDesc('id')
        ->limit(12)
        ->get();

    return view('about', compact('stories'));
})->name('about.index');
Route::get('/accessoires', [AccessoiresController::class, 'index'])->name('accessoires.index');
Route::get('/annonces/{id}', [AnnoncesController::class, 'show'])->name('annonces.show');

Route::resource('/services', ServicesController::class);
Route::resource('/financement', FinancementController::class);
Route::resource('/reprise', RepriseController::class);
Route::resource('/nadoautoenchere', NadoAutoEnchereController::class);

Route::resource('/vente', VenteController::class);

Route::resource('/comparison', ComparisonController::class);
Route::resource('annonce', SellController::class)->middleware('auth');

Route::get('/achat/voiture/{marque}_{modele}_{id}.html', [BuyController::class, 'show'])->name('achat.detail');
//Route::get('/achat/incrementviews/{id}', [AchatController::class, 'incrementViews'])->name('achat.incrementviews');

Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');

Route::post('/annonces/{id}/proposer-prix', [AnnoncesController::class, 'proposerPrix'])->name('annonces.proposerPrix')->middleware('auth:client');;
Route::post('/nadoautoenchere/{id}/proposer-prix', [NadoAutoEnchereController::class, 'proposerPrix'])->name('nadoautoenchere.proposerPrix');


Route::middleware(['auth'])->group(function () {
    Route::get('/client/profile', [ProfileController::class, 'index'])->name('client.profile');
});
// User Profile
Route::middleware(['auth'])->group(function () {
    Route::get('/profil', [ProfileController::class, 'index'])->name('profile');
});
Route::middleware('auth:client')->group(function () {
    Route::post('/annonce/{id}/favorite', [FavoriteController::class, 'toggle'])->name('favorite.toggle');
});

Route::middleware('auth')->get('/annonce', function () {
    return redirect()->route('annonces.index');
})->name('dashboard');
Route::get('/admin/autoenchere/nav', function () {
    return view('admin.layouts.autoenchere_navigation');
})->name('admin.autoenchere.nav');

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/clients/approval', [ClientApprovalController::class, 'index'])->name('admin.clients.approval');
    Route::patch('/admin/clients/{id}/update-type', [ClientController::class, 'updateType'])
        ->name('admin.clients.updateType');

    Route::post('/admin/clients/approve/{id}', [ClientApprovalController::class, 'approve'])->name('admin.clients.approve');

    Route::get('/admin/clients/{id}', [ClientController::class, 'show'])->name('admin.clients.show');
    Route::delete('/admin/clients/{id}', [ClientController::class, 'destroy'])->name('admin.clients.destroy');
});



Route::get('clients', [ClientController::class, 'index'])->name('clients.index');

// Admin Routes
// 🟢 Admin Auth Routes
Route::prefix('admin')->name('admin.')->middleware('guest:admin')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
});
Route::middleware(['auth:admin', 'role:admin'])->group(function () {
    // Estimations soumises par les clients
    Route::get('/admin/estimations', [App\Http\Controllers\Admin\EstimationController::class, 'index'])
        ->name('admin.estimations.index');
    Route::get('/admin/estimations/{id}', [App\Http\Controllers\Admin\EstimationController::class, 'show'])
        ->name('admin.estimations.show');
    Route::delete('/admin/estimations/{id}', [App\Http\Controllers\Admin\EstimationController::class, 'destroy'])
        ->name('admin.estimations.destroy');

    Route::get('/admin/users', [App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
    Route::get('/ventes', [VenteController::class, 'index'])->name('ventes.index');
    Route::get('/reprises', [RepriseVenteController::class, 'index'])->name('reprises.index');
    Route::get('/finances', [FinanceController::class, 'index'])->name('finances.index');
    Route::get('/FinancementSettings/edit', [FinancementSettingsController::class, 'edit'])->name('FinancementSettings.edit');
    Route::put('/FinancementSettings', [FinancementSettingsController::class, 'update'])->name('FinancementSettings.update');
    Route::get('/FinancementSettings', [FinancementSettingsController::class, 'index'])->name('FinancementSettings.index');
    Route::delete('/reprises/{id}', [RepriseVenteController::class, 'destroy'])->name('reprises.destroy');

    Route::get('/offres', [OffreController::class, 'index'])->name('offres.index');
    Route::get('/colors', [ColorController::class, 'index'])->name('colors.index');
    Route::get('/villes', [VilleController::class, 'index'])->name('villes.index');
    Route::get('/brands', [BrandsController::class, 'index'])->name('brands.index');
    Route::get('/annonces', [AnnoncesController::class, 'index'])->name('annonces.index');
    Route::get('/models', [ModelsController::class, 'index'])->name('models.index');
    Route::get('/autoenchere', [AutoEnchereController::class, 'index'])->name('autoenchere.index');
    Route::get('/admin/nadoautoencherebid/export', [NadoAutoEnchereBidController::class, 'export'])
        ->name('admin.nadoautoencherebid.export');


    Route::get('clients', [ClientController::class, 'index'])->name('clients.index');
    Route::get('clients/approval', [ClientController::class, 'approval'])->name('clients.approval');
    Route::post('clients/{id}/approve', [ClientController::class, 'approve'])->name('clients.approve');
    Route::delete('clients/{id}', [ClientController::class, 'destroy'])->name('clients.destroy');
    Route::get('entretiens', [EntretienController::class, 'index'])->name('entretiens.index');
    Route::get('entretiens/create/{annonce}', [EntretienController::class, 'create'])->name('entretiens.create');

    Route::post('entretiens/create/{annonce}', [\App\Http\Controllers\Admin\EntretienController::class, 'create'])->name('entretiens.create');
});
Route::post('/admin/annonces/toggle-homepage', [App\Http\Controllers\Admin\AnnoncesController::class, 'toggleHomepage'])
    ->name('admin.annonces.toggleHomepage');
// ⬇️ place this before the resource
Route::get('/achat/models-by-brand/{brandId}', [BuyController::class, 'getModelsByBrand'])
    ->name('achat.models.by.brand');

// then the resource route
Route::resource('/achat', BuyController::class);

// 🟢 Admin Protected Routes
Route::prefix('admin')->name('admin.')->middleware('auth:admin')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    // Blog articles management
    Route::patch('articles/{id}/toggle', [\App\Http\Controllers\Admin\ArticleController::class, 'toggleActive'])
        ->name('articles.toggle');
    // In-editor image upload (TinyMCE drag & drop / file picker)
    Route::post('articles/upload-image', [\App\Http\Controllers\Admin\ArticleController::class, 'uploadImage'])
        ->name('articles.uploadImage');
    Route::resource('articles', \App\Http\Controllers\Admin\ArticleController::class)->except(['show']);
    // Archive (soft-deleted annonces) — must stay above Route::resource('annonces')
    Route::get('annonces/archive', [AnnoncesController::class, 'archive'])->name('annonces.archive');
    Route::patch('annonces/{id}/restore', [AnnoncesController::class, 'restore'])->name('annonces.restore');
    Route::delete('annonces/{id}/force-delete', [AnnoncesController::class, 'forceDelete'])->name('annonces.forceDelete');
    Route::resource('annonces', AnnoncesController::class);
    Route::resource('ville', VilleController::class); // <-- this line
    Route::resource('entretiens', EntretienController::class); // <-- this line
    Route::get('/FinancementSettings', [FinancementSettingsController::class, 'index'])->name('FinancementSettings.index');
    Route::put('/FinancementSettings', [FinancementSettingsController::class, 'update'])->name('FinancementSettings.update');
    // Doit précéder la resource : résolution AJAX d'un lien Google Maps (admin form)
    Route::post('nadoautoenchere/resolve-maps', [\App\Http\Controllers\Admin\AdminNadoAutoEnchereController::class, 'resolveMaps'])
        ->name('nadoautoenchere.resolveMaps');
    Route::resource('nadoautoenchere', \App\Http\Controllers\Admin\AdminNadoAutoEnchereController::class);

    Route::resource('videoads', VideoAdsController::class); // <-- this line
    Route::resource('imgslider', ImgSlideController::class); // <-- this line
    Route::resource('models', ModelsController::class); // <-- this line
    Route::resource('avistext', AvisTextController::class); // <-- this line
    Route::resource('avis-vd', AvisVdController::class); // legacy slug
    // Backwards-compatible routes used by views (admin.avisvideos.*)
    Route::resource('avisvideos', AvisVdController::class)->names('avisvideos');
    Route::resource('offres', OffreController::class); // <-- this line
    Route::get('admin/autoenchere/{id}', [AutoEnchereController::class, 'show'])->name('admin.autoenchere.show');
    Route::get('publications/', [PublicationController::class, 'index'])->name('publications.index');
    Route::get('publications/create', [PublicationController::class, 'create'])->name('publications.create');
    Route::post('publications/', [PublicationController::class, 'store'])->name('publications.store');
    Route::get('publications/{publication}/edit', [PublicationController::class, 'edit'])->name('publications.edit');

    Route::get('colors/edit', [ColorController::class, 'edit'])->name('colors.edit');
    Route::put('publications/{publication}', [PublicationController::class, 'update'])->name('publications.update');
    Route::delete('publications/{publication}', [PublicationController::class, 'destroy'])->name('publications.destroy');
    Route::post('publications/reorder', [PublicationController::class, 'reorder'])->name('publications.reorder');
    Route::resource('autoenchere', AutoEnchereController::class);
    Route::resource('nadoautoencherebid', NadoAutoEnchereBidController::class);

    Route::get('clients', [ClientController::class, 'index'])->name('clients.index');
    // Clients Auto Enchère (type flotte&enchere) — liste dédiée côté enchère.
    Route::get('enchere-clients', [ClientController::class, 'encheresClients'])->name('nadoautoenchere.clients');
    Route::get('clients/create', [ClientController::class, 'create'])->name('clients.create');
    Route::get('clients/store', [ClientController::class, 'store'])->name('clients.store');
    Route::resource('clients', \App\Http\Controllers\Admin\ClientController::class)->except(['edit', 'update']);

    Route::get('entretiens/create/{annonce}', [EntretienController::class, 'create'])->name('entretiens.create');
    Route::get('entretiens', [\App\Http\Controllers\Admin\EntretienController::class, 'index'])->name('entretiens.index');
    Route::get('/clients/{id}', [ClientController::class, 'show'])->name('clients.show');
    Route::get('annonces/approval', [AnnoncesController::class, 'approval'])->name('annonces.approval');
    Route::patch('annonces/{id}/approve', [AnnoncesController::class, 'approve'])->name('annonces.approve');
    Route::get('clients/approval', [ClientController::class, 'approval'])->name('clients.approval');
    Route::post('clients/{id}/approve', [ClientController::class, 'approve'])->name('clients.approve');
    Route::delete('clients/{id}', [ClientController::class, 'destroy'])->name('clients.destroy');

    Route::get('/clients/{id}/verify', [ClientController::class, 'verify'])
        ->name('clients.verify');


    Route::patch('/clients/{client}/verify', [ClientController::class, 'toggleVerification'])
        ->name('admin.clients.toggleVerification');




    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    // all your admin resources go here:
    Route::post('/annonces/{id}/verify', [AnnoncesController::class, 'verify'])->name('annonces.verify');
    Route::resource('annonces', AnnoncesController::class);
    Route::resource('avis-text', AvisTextController::class);
    Route::resource('avis-vd', AvisVdController::class);
    Route::resource('brands', BrandsController::class);
    Route::resource('stories', StoriesController::class);
    Route::resource('social-posts', SocialPostController::class)->except(['show']);
    Route::resource('colors', ColorController::class);
    Route::resource('finances', FinanceController::class);
    // Route::resource('FinancementSettings', FinancementSettingsController::class);

    Route::resource('img-slides', ImgSlideController::class);
    Route::resource('models', ModelsController::class);
    Route::resource('offres', OffreController::class);
    Route::resource('reprises', RepriseVenteController::class);
    Route::resource('users', UserController::class);
    Route::resource('ventes', VenteAdminController::class);
    Route::resource('video-ads', VideoAdsController::class);
    Route::resource('villes', VilleController::class);
    Route::get('/admin/users', [UserController::class, 'index'])->name('users.index');

    Route::get('/clients/{id}/verify', [ClientController::class, 'verify'])
        ->name('clients.verify');
    Route::get('client-approvals', [ClientApprovalController::class, 'index'])->name('client.approvals');
    Route::post('client-approve/{id}', [ClientApprovalController::class, 'approve'])->name('client.approve');

    // Discussions
    Route::get('discussions', [\App\Http\Controllers\Admin\DiscussionController::class, 'index'])->name('discussions.index');
    Route::get('discussions/{discussion}', [\App\Http\Controllers\Admin\DiscussionController::class, 'show'])->name('discussions.show');
    Route::post('discussions/{discussion}/send', [\App\Http\Controllers\Admin\DiscussionController::class, 'sendMessage'])->name('discussions.send');
    Route::get('discussions/{discussion}/messages', [\App\Http\Controllers\Admin\DiscussionController::class, 'messages'])->name('discussions.messages');
    Route::patch('discussions/{discussion}/close', [\App\Http\Controllers\Admin\DiscussionController::class, 'close'])->name('discussions.close');
    Route::patch('discussions/{discussion}/reopen', [\App\Http\Controllers\Admin\DiscussionController::class, 'reopen'])->name('discussions.reopen');
});
/*
|--------------------------------------------------------------------------
| Configuration marketplace (doc 02 — §6.bis)
| Panneau admin : prix, taux, durées, feature-flags. Zéro valeur en dur.
|--------------------------------------------------------------------------
*/
Route::prefix('admin/settings')->name('admin.settings.')
    ->middleware(['auth:admin', 'role:admin'])
    ->group(function () {
        Route::get('/', [SettingsController::class, 'index'])->name('index');
        Route::post('/toggle/{key}', [SettingsController::class, 'toggle'])->name('toggle');
        Route::get('/{group}', [SettingsController::class, 'group'])->name('group');
        Route::post('/{group}', [SettingsController::class, 'update'])->name('update');
    });

/*
|--------------------------------------------------------------------------
| Demandes (saved search → public forum)
|--------------------------------------------------------------------------
*/
Route::prefix('demandes')->name('demandes.')->group(function () {
    Route::get('/',                   [DemandeController::class, 'index'])->name('index');
    Route::get('/publier',            [DemandeController::class, 'create'])->name('create');
    Route::post('/',                  [DemandeController::class, 'store'])->name('store');
    Route::get('/confirmation/{id}',  [DemandeController::class, 'confirmation'])->name('confirmation');
    Route::get('/{demande}',          [DemandeController::class, 'show'])->name('show');
    Route::post('/{demande}/propose', [DemandeController::class, 'propose'])
         ->middleware('throttle:10,60')->name('propose');
    Route::post('/{demande}/replies', [DemandeController::class, 'storeReply'])
         ->middleware(['auth:client', 'throttle:10,60'])->name('replies.store');
    Route::post('/{demande}/report',  [DemandeController::class, 'report'])
         ->middleware('throttle:5,60')->name('report');
});

Route::prefix('admin/demandes')->name('admin.demandes.')
    ->middleware(['auth:admin', 'role:admin'])
    ->group(function () {
        Route::get('/',                   [DemandeAdminController::class, 'index'])->name('index');
        Route::get('/pending',            [DemandeAdminController::class, 'pending'])->name('pending');
        Route::post('/{demande}/approve', [DemandeAdminController::class, 'approve'])->name('approve');
        Route::post('/{demande}/reject',  [DemandeAdminController::class, 'reject'])->name('reject');
        Route::delete('/{demande}',       [DemandeAdminController::class, 'destroy'])->name('destroy');
    });

Route::get('/fix-symlink', function () {
    // 1. The source of the images
    $target = storage_path('app/public');

    // 2. The destination (Your public_html folder)
    $link = base_path('public_html/storage');

    // Check if a wrong folder already exists there
    if (file_exists($link)) {
        return "Error: A 'storage' folder already exists in public_html. <br>Please Go to FTP > public_html and DELETE the 'storage' folder, then refresh this page.";
    }

    // Create the link
    try {
        symlink($target, $link);
        return "✅ Success! Symlink created inside public_html.";
    } catch (\Exception $e) {
        return "Error: " . $e->getMessage();
    }
});
