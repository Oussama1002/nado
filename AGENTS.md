# AGENTS.md — NADO / autoenchere.ma

Guide for AI agents (and developers) working on this codebase. Read this before
making changes. Keep it updated when you learn something non-obvious.

---

## 1. What this is

**NADO** (domain: **https://autoenchere.ma**) is a Moroccan car marketplace built
with **Laravel 10 / PHP 8.1 / MySQL**. Bilingual **French + Arabic (RTL)**.

Main areas (public):
- **Achat / Annonces** — car listings (`BuyController`, `AnnoncesController`), detail page `annonces/detail.blade.php` (route `achat.detail`).
- **Vente** — sell a car; the "Déposez votre annonce" modal (`VenteController`, `vente/vente.blade.php`). Primary seller entry point.
- **Auto Enchère** (`nadoautoenchere`) — B2B auctions, gated to `type = flotte&enchere` clients.
- **Reprise** — trade-in (Arabic label: **استبدال**, not استرجاع).
- **Financement** — financing simulator + bot.
- **Demandes** — buyer "wanted" requests forum.
- **Blog / Articles**, **Nado Radio** (opt-in music), **AI carte-grise OCR** (pre-fill the vente form).

Admin area under `auth:admin` + `role:admin` (Spatie). Two client/admin auth guards:
`client` (site users) and `admin`.

---

## 2. Environment & how to run things

### Local (Windows + XAMPP)
- **PHP CLI**: `C:\xampp2\php\php.exe` — NOT on PATH. In PowerShell: `$env:Path = "C:\xampp2\php;" + $env:Path`. The Bash tool has no `php`.
- **Composer**: `php C:\composer\composer.phar …`. **`composer dump-autoload` HANGS** (slow `package:discover`) — avoid it. New classes in existing PSR-4 namespaces (e.g. `App\Services\…`) autoload WITHOUT a dump.
- **MySQL (local)**: `root` / no password, DB `autoench_base` (`C:\xampp2\mysql\bin\mysql.exe`). **But `.env` holds PRODUCTION DB creds.** To run DB commands locally: back up `.env`, swap `DB_USERNAME=root` / `DB_PASSWORD=` (empty), run, then **restore `.env`** and `php artisan config:clear`. (PowerShell `$env:DB_PASSWORD=""` unsets the var, so inline overrides don't work — edit `.env`.)
- **Migrations**: the local DB was imported from a SQL dump, so the `migrations` table is **out of sync** — a blanket `php artisan migrate` fails ("table already exists"). Run NEW migrations individually: `php artisan migrate --path=database/migrations/<file>.php --force`.
- **`php artisan route:list` is BROKEN** project-wide (an unimported controller reference). Check routes via `app('router')->has('name')` in tinker instead.
- **Lint before committing**: `php -l path/to/file.php`.

### Production (cPanel / LiteSpeed, user `autoench`, server `eude19`)
- Deploy = **`git pull`** in `/home2/autoench/nado` (remote = github.com/Oussama1002/nado, branch `main`). **Prod `.env` already has correct DB creds — never change them.**
- **CRITICAL: the served docroot is SEPARATE** → `/home2/autoench/public_html` is its OWN copy of `public/` (NOT a symlink to `nado/public`). Blade/PHP/routes/lang deploy fine via git pull, but any **new file under `nado/public/`** (images, compiled CSS/JS) must also be copied into `~/public_html/`. (We avoid this by keeping CSS/JS inline in Blade.)
- **No composer on the server.** PSR-4 autoload works; packages already in `vendor/` work.
- **Post-deploy** (run one line at a time; `php artisan` works, php is on PATH):
  - New migration: `php artisan migrate --path=database/migrations/<file>.php --force`
  - Seeder: `php artisan db:seed --class=<Seeder> --force`
  - `php artisan config:clear` / `view:clear` (views ARE cached in prod — clear after Blade changes).
- **LiteSpeed page cache**: the **homepage is page-cached**. After changing it (or toggling a flag that affects it), **purge the LiteSpeed cache** (cPanel → LiteSpeed Cache → Purge). Non-cached pages (financement, detail, etc.) update immediately.
- **Mail**: Gmail SMTP as `oussamaelhadi11@gmail.com`, `QUEUE_CONNECTION=sync`. Notices go to `contact@nado.ma`.
- **www vs non-www**: `APP_URL=https://www.autoenchere.ma` but users browse `autoenchere.ma` too. Treat both as the same host (see redirect/CSRF gotchas below).

---

## 3. Workflow conventions

- **After changes: commit AND push to `main`.** The user deploys. (Branch off main only if asked.)
- **Commit messages** end with the attribution line the session specifies (e.g. `Co-Authored-By: Claude …`). PR descriptions end with the Generated-with line.
- Keep code in the **style of the surrounding file** (this repo mixes conventions heavily — match the file you're in).
- Prefer **inline CSS/JS in Blade** over new `public/` assets (avoids the separate-docroot copy step).

---

## 4. Architecture & conventions you MUST know

### 4.1 Navbars are DUPLICATED
There is NO single navbar. Editing one does not affect the others. Copies:
- `resources/views/partials/nado-navbar.blade.php` — shared partial (used by `layouts.app`).
- `resources/views/front.blade.php` — homepage **inline** navbar.
- `resources/views/financement.blade.php` — inline.
- `resources/views/annonces/detail.blade.php` — inline, **`.nv-` prefixed** classes.
- `resources/views/nadoautoenchere/partials/navbar.blade.php` — Auto-Enchère, **Tailwind (Play CDN)**.
- `resources/views/partials/navbar.blade.php` — legacy (owl/Font Awesome, no top bar).

When adding a nav link/icon, update ALL relevant copies. The top-bar strip (mail/phone/socials/lang) and mobile responsiveness differ per copy.

### 4.2 Layouts
- `layouts.app` = standard public layout (includes the shared `nado-navbar`, loads Lucide + Font Awesome + Inter). Many pages. But **homepage, financement, detail, reprise, nadoautoenchere** are **standalone** full HTML files with their own `<head>` and inline navbars.
- Always ensure standalone pages have `<meta name="viewport" …>` (a missing one made the reprise page render tiny on mobile).

### 4.3 i18n / RTL
- Strings via `__('messages.key')`, files `lang/fr/messages.php` + `lang/ar/messages.php` (mirror every key). `lang/fr/validation.php` provides French validation messages (`fallback_locale=fr`, so AR falls back to FR for validation).
- `app()->getLocale()` is `fr`|`ar`. `<html dir="rtl">` for Arabic.
- **Locale detection**: `App\Http\Middleware\SetLocale` resolves `?lang=` → session → cookie → **browser `Accept-Language`** → default `fr`. The FR/AR switcher (`partials/lang-switcher.blade.php`, shows "FR"/"AR") sets session + a 1-year cookie.
- **RTL gotchas**: Owl Carousel needs `rtl: <bool>` or slides render off-screen (testimonials). `translateX` marquees break in RTL (brands ticker → forced `direction:ltr`). Hardcoded `left/right` positions need `html[dir="rtl"]` overrides (homepage hero is mirrored this way).
- Hardcoded French in JS bots (e.g. the financement "Assistant financement" bot in `detail.blade.php`) must branch on a `@json(app()->getLocale()==='ar')` flag.

### 4.4 Centralized settings (doc 02 §6.bis)
- `App\Models\Setting` (table `settings`), helpers **`settings($key, $default)`** and **`feature_on($key)`** (in `app/helpers.php`, cached with `rememberForever`, invalidated on write).
- Keys are **group-prefixed dotted** (e.g. `radio.enabled`, `ai.cartegrise_enabled`, `finance.enabled`) — NOT the bare names some old notes use.
- Seed via `database/seeders/SettingsSeeder.php` (idempotent, won't overwrite admin-changed values). Admin UI: `Admin\SettingsController` (groups in its `GROUPS` const) + `admin/settings/*` routes. Some modules manage their settings on their own page instead (Nado Radio).

### 4.5 Images / media
- Uploads → `storage/app/public/ads/` (and `/vente`, `/imgslider`, `/songs`, …), served via the **`public/storage` symlink** (prod has a `/fix-symlink` route). Reference with `asset('storage/…')`.
- `annonces.images` is cast to array but is a **twice-encoded JSON string** on older rows; the real photos are often in the **`media` relation** (`Annonces::media()`). Use the media fallback when `images` is empty (see `BuyController`/detail view patterns).
- `annonces.mark` and `annonces.modele` hold **brand/model IDs** (FK to `Brand` / `Modele` via `brand()` / `modeleRelation()`) on new rows, **names** on migrated rows. `annonces.city` is a **name** (not an ID).

### 4.6 Phone numbers
Stored like `+212 6…`. Use `<input type="tel">` (NOT `type="number"`, which refuses to display `+`/spaces). In Excel exports, phones/VINs/IDs must be written as **text** or Excel shows scientific notation.

---

## 5. Notable features & where they live

| Feature | Key files |
|---|---|
| Car detail + seller chat + finance bot | `annonces/detail.blade.php`, `BuyController` |
| Vente modal (sell) | `vente/vente.blade.php`, `VenteController@store` (forceCreate + `withRequiredAnnonceDefaults`) |
| AI carte-grise OCR pre-fill | `App\Services\CarteGriseExtractor` (**local Tesseract**, see below), `CarteGriseController` (route `cartegrise.extract`), UI in the vente modal (recto/verso slots) |
| Auto-Enchère access + approval | `EnsureClientCanAccessAutoEnchere` (needs `type=flotte&enchere` AND `ae_approved`), `NadoAutoEnchereController@upgrade`, admin `ClientController@approveEnchere`, `admin/clients/encheres` |
| Nado Radio | `Song` model, `Admin\SongController` (+ settings on that page), `RadioController` (`/nado-radio`), `partials/radio-mini-player.blade.php` injected site-wide by `InjectRadioMiniPlayer` middleware |
| Admin Excel export (all modules) | `App\Exports\GenericExport`, `Admin\ExportController` (route `admin.export({module})`, Laravel Excel). Resolves FK columns to names, booleans → Oui/Non, French headers, excludes password/tokens. |
| Settings / feature flags | `Setting`, `app/helpers.php`, `Admin\SettingsController`, `SettingsSeeder` |
| Client auth | `Client\Auth\LoginController` / `RegisterController` (email + phone signup; email verification + admin approval gates) |
| Admin emails | `RegisterController::notifyAdminNewClient`, `App\Services\EnchereNotifier::adminPending` → `contact@nado.ma` |

### AI carte-grise OCR specifics
- **No paid API.** Uses the **local `tesseract` binary** via `Symfony\Component\Process`, then parses the fixed Moroccan carte-grise template by French labels + the MRZ line (matricule + date only — **never the owner name/address**). VIN via 17-char regex.
- Config via `.env`: `TESSERACT_PATH` (prod has no system tesseract — install into the home dir, e.g. `~/tess/bin/tesseract` via micromamba), `TESSERACT_LANG` (`fra+eng`), `TESSDATA_PREFIX`. Read through `config/services.tesseract.*`.
- Privacy: vehicle fields only; uploaded image stays on the **private `local` disk** (`storage/app/cartegrise`) only if `ai.retain_image` is on, else processed in-memory and dropped. Never the public disk.

---

## 6. Gotchas / lessons learned

- **CSRF 419 on admin forms**: tokens can go stale (shared browser session with client login/logout which regenerates the token). Fix by fetching a fresh token from the existing `/csrf-token` endpoint before submitting (pattern used in the vente modal and `encheres` approve button). **Do NOT "fix" by switching a state-changing POST to GET** (removes CSRF protection).
- **Login redirect / www**: after login, honor the requested return URL (`redirect` field or `url.intended`) for ALL client types **before** the `flotte&enchere → categories` default. `isSafeRedirect` must treat `www.` and non-`www.` as the same host (APP_URL is www, users browse non-www), or returns are silently rejected. `showLoginForm` also captures the referer so any login returns to the previous page.
- **Mobile camera** (`getUserMedia`): use a real `<label for>` + visually-hidden input (not `display:none` + JS `.click()`) for reliable camera open on iOS; show friendly errors, not raw `err.message`.
- **Open Graph / share previews** (detail page): `og:image` must be the real main photo (compute from `images[0]` or first `media`), absolute URL; WhatsApp/Facebook cache the preview (use the FB Sharing Debugger to refresh).
- **Classifier guardrails**: avoid commits/changes that look like weakening security (POST→GET on mutations) or leaking secrets (hardcoded API keys in config) — use env vars and keep CSRF.
- **New migrations**: use the project's dated filename style; guard with `Schema::hasColumn` for idempotency.

---

## 7. Memory / recurring facts

Several of the above are also kept as point-in-time notes (local dev env, production deploy, navbars map, settings system, commit/push workflow, carte-grise OCR). Treat this file as the canonical, maintained version; verify any claim against the current code before relying on it.
