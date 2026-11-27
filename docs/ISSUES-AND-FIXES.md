# NADO (nado.ma) — Bugs, Risks & Architectural Pains

**Purpose:** hand-off document for a fixer agent. Every item below is a concrete
issue grounded in the current code (as of 2026-11-27). Fix in roughly the
order listed — P0 first. Each item has: **where**, **why it hurts**, **suggested fix**.

Domain is `nado.ma`. Laravel 10 / PHP 8.1 / MySQL. See `AGENTS.md` for workflow
conventions (commit + push `main`; local XAMPP; shared LiteSpeed prod).

---

## P0 — Bugs that are actively broken or data-unsafe

### 1. `php artisan route:list` is broken project-wide
- **Where**: somewhere in `routes/web.php` (770 lines) or an admin controller import — a referenced controller class can't be resolved at boot.
- **Impact**: can't audit routes; mistakes stay hidden; new devs can't onboard.
- **Fix**: run `php artisan route:list` locally, read the error, fix the bad `use` / typo. Grep every `use App\Http\Controllers\...` in `routes/*.php` and `app/Http/Controllers/Admin/*` for missing classes.

### 2. Duplicate file: `app/Http/Controllers/Admin/StoriesController.php.php`
- **Where**: `app/Http/Controllers/Admin/StoriesController.php.php` (double extension, 70 lines) sits next to the real `StoriesController.php` (339 lines).
- **Impact**: dead file; confusing; may conflict with IDE class indexing.
- **Fix**: diff the two, port anything unique into the real one, delete the `.php.php`.

### 3. Stray non-Blade files inside views
- **Where**: `resources/views/nadoautoenchere/itsystemss.online.conf`, `nginx.conf`, `php.ini`.
- **Impact**: shipped into views folder, potentially leaking server config hints; definitely not views.
- **Fix**: move to `docs/old/` or delete. Verify they contain no secrets before deleting from git history if sensitive.

### 4. `AGENTS.md` names the wrong domain
- **Where**: `AGENTS.md` §1 says `https://autoenchere.ma`. Real domain is `nado.ma`.
- **Impact**: onboarding + any AI agent reading AGENTS.md will use the wrong URL in redirects/CSRF/OG tags.
- **Fix**: replace `autoenchere.ma` → `nado.ma` everywhere in `AGENTS.md`. Also check `config/app.php`, `.env.example`, and any `APP_URL` references.

### 5. Legacy data in `annonces.images` is double-encoded JSON
- **Where**: `app/Models/Annonces.php` casts `images` to array; migrated rows store it as a JSON string wrapped inside another JSON string. Real photos may only be in the `media` relation.
- **Impact**: image accessors silently return empty; detail page falls back to the media relation inconsistently; `og:image` can be wrong → bad WhatsApp/Facebook previews.
- **Fix**:
  - Write a one-off migration/command that normalizes `annonces.images` for every row: decode until it's an array of strings, re-save.
  - Add a model accessor `getImagesAttribute` that handles both shapes until data is clean.
  - Verify `og:image` computation in `annonces/detail.blade.php`.

### 6. `annonces.mark` / `annonces.modele` hold IDs on new rows, NAMES on legacy rows
- **Where**: same model; `brand()` / `modeleRelation()` relations.
- **Impact**: filters, exports, search, detail page all have to branch on "is it a number?" — subtle bugs in brand/model pages.
- **Fix**: data migration to map every legacy name → ID (fuzzy-match against `brands` / `modeles`, log unresolved rows for manual review). Then make the columns FK-constrained.

### 7. `annonces.city` is a name (not an ID)
- **Where**: same table; `villes` table exists but isn't the FK source here.
- **Impact**: city filters depend on exact string match; typos split the inventory.
- **Fix**: normalize existing values (trim, title-case, fold accents), then add `city_id` FK and keep `city` as derived. Update forms + filters.

### 8. CSRF 419 regressions keep happening on admin forms
- **Where**: any admin form that opens after a `client` login/logout in the same browser; AGENTS.md §6 documents the pattern.
- **Impact**: admins see 419, re-login, lose unsaved work.
- **Fix**: the `/csrf-token` refetch pattern must be applied consistently. Audit every admin `<form method="POST">` in `resources/views/admin/**/*.blade.php` and either (a) call the refetch helper on submit, or (b) reload the CSRF meta tag on focus. Do NOT change any POST to GET as a shortcut.

### 9. `isSafeRedirect` must treat `www.` and bare domain as the same host
- **Where**: `app/Http/Controllers/Client/Auth/LoginController.php` (per AGENTS.md).
- **Impact**: visitors on `nado.ma` get silently redirected to `/categories` after login instead of their intended URL because the host check rejects `www.nado.ma` as a different origin.
- **Fix**: normalize both sides (strip leading `www.`) before comparing in `isSafeRedirect()`. Add a unit test.

### 10. Mobile camera open in vente modal
- **Where**: `resources/views/vente/vente.blade.php` (and anywhere else that opens `<input type="file" accept="image/*" capture>`).
- **Impact**: iOS Safari refuses to open the camera when the input is `display:none` and JS-clicked.
- **Fix** (already stated as a lesson in AGENTS.md): use a real `<label for>` + visually-hidden input (`sr-only` / clip-path), not `display:none`. Audit every file picker on the site.

---

## P1 — High-risk bugs & architectural pains

### 11. Six duplicated navbars
- **Where**:
  - `resources/views/partials/nado-navbar.blade.php` (shared, used by `layouts.app`)
  - `resources/views/front.blade.php` (inline)
  - `resources/views/financement.blade.php` (inline)
  - `resources/views/annonces/detail.blade.php` (inline, `.nv-` prefixed classes)
  - `resources/views/nadoautoenchere/partials/navbar.blade.php` (Tailwind Play CDN)
  - `resources/views/partials/navbar.blade.php` (legacy, owl/FontAwesome)
- **Impact**: every nav/menu/link change must be made in up to 6 files; drift between them.
- **Fix**: consolidate. Pick the richest copy, parameterize it (`$variant` = `default|detail|auction|legacy`), extract page-specific styles via `@push('nav-css')`. Delete the legacy copy. Update all `@include`s.

### 12. `front.blade.php` is 6,503 lines
- **Impact**: unmaintainable; mixes HTML + CSS + JS + inline bots.
- **Fix**: break into section partials — `home/hero`, `home/brands-ticker`, `home/featured`, `home/stories`, `home/radio`, `home/finance-cta`, etc. Move inline `<style>` into either Vite pipeline or a single `public/css/home.css` (copied to prod docroot). Same approach for `detail.blade.php` (4,883 lines), `financement.blade.php` (2,859), `vente.blade.php` (2,068).

### 13. `annonces/detail.blade.php` — JS bot hardcoded French
- **Where**: the "Assistant financement" bot inside `annonces/detail.blade.php`.
- **Impact**: Arabic visitors see French strings only in this bot.
- **Fix**: pass `@json(app()->getLocale()==='ar')` into a JS flag, and either hardcode AR translations in a small `botMessages` object or read from `__('messages.*')` keys.

### 14. Separate docroot on prod — `public/` is NOT symlinked
- **Where**: `/home2/autoench/public_html/` is its own copy of `/home2/autoench/nado/public/`.
- **Impact**: any new file dropped in `public/` is 404 on prod unless manually copied. Compiled Vite assets, uploaded images in `storage/*`, favicons, OG images.
- **Fix** (long-term): on-server cron that `rsync`s `nado/public/` → `public_html/` on every deploy. Or a `/fix-symlink` route that already exists for `public/storage` — extend to the whole folder. Document the convention explicitly.

### 15. Local `migrations` table out of sync
- **Where**: local MySQL (imported from dump).
- **Impact**: `php artisan migrate` fails with "table already exists"; devs skip migrations locally and forget them on prod.
- **Fix**: write a one-shot script that marks every pre-existing migration as "run" in the `migrations` table (based on a known baseline date). Document in AGENTS.md § how to rehydrate a fresh dev DB.

### 16. `composer dump-autoload` hangs locally
- **Where**: XAMPP + Windows path; `package:discover` is slow/broken.
- **Impact**: can't add a package or regenerate classmaps; workarounds (PSR-4-only) can hide bugs.
- **Fix**: investigate the root cause — probably a broken service provider referenced in `config/app.php`. Try `composer dump-autoload --no-scripts` to isolate, then narrow down.

### 17. LiteSpeed page cache on homepage
- **Where**: prod LiteSpeed config caches `/`.
- **Impact**: feature-flag toggles don't take effect on the home page until a manual purge.
- **Fix**: add a cache-tag / `X-LiteSpeed-Purge` header in `FrontController@index` when any `global.*` or home-visible setting changes. Or hook `Setting::saved()` to send a purge request to LiteSpeed.

### 18. Inconsistent code style across files
- **Where**: whole `app/` and `resources/views/`.
- **Impact**: AGENTS.md literally says "match the file you're in" — because styles differ by author/era.
- **Fix**: run `./vendor/bin/pint` on everything in a dedicated commit (so diffs are easy to review). Add it to a CI step. Keep the "match the file" rule only for Blade until the codebase stabilizes.

### 19. Minimal test suite
- **Where**: `tests/Unit/ExampleTest.php`, `tests/Feature/ExampleTest.php`, `ProfileTest.php`, `Auth/` stubs. That's it.
- **Impact**: no safety net for the two most sensitive services (`AuctionService`, `TokenService`), and no feature tests at all for critical flows.
- **Fix** (minimum viable):
  - `tests/Feature/AuctionServiceTest` — concurrent proxy bids, anti-sniping, min-increment, ineligible client, closed auction.
  - `tests/Feature/TokenServiceTest` — spend blocks when balance too low, idempotent signup bonus, ledger ↔ `tokens_balance` consistency under concurrent charges.
  - `tests/Feature/VenteStoreTest` — happy path, carte-grise prefill, image upload.
  - `tests/Feature/ClientLoginRedirectTest` — `isSafeRedirect` across www/non-www, intended URL preserved, `flotte&enchere` default kicks in only without `?redirect`.

### 20. Logging gaps
- **Where**: many services swallow errors with `catch (\Throwable $e) {}` (e.g., Client::booted token grant).
- **Impact**: silent failures in prod; no breadcrumbs.
- **Fix**: audit every empty/narrow catch. At minimum, `Log::warning` with context (user id, module, exception message). Consider Sentry/Flare (there's already `spatie/laravel-ignition`).

---

## P2 — Correctness / medium-risk

### 21. Phone number storage inconsistency
- **Where**: `clients.phone`, `annonces.phone`, Excel exports.
- **Impact**: numbers stored in mixed formats ("+212 6…", "06…", "+2126…"). Excel exports can still turn them into scientific notation if the exporter changes.
- **Fix**: normalize on save via a mutator (`setPhoneAttribute`) → always E.164. Add a migration/command that backfills existing rows.

### 22. Twice-encoded JSON pattern used in other places?
- **Where**: `annonces.images` is the known case — grep for other columns cast to array that might have been imported.
- **Fix**: inspect every `protected $casts = [... => 'array']` column; sample a few rows; if any are double-encoded, add a `getXAttribute` accessor + normalize command.

### 23. OTP hashing migration only partially rolled out
- **Where**: `2026_11_21_000001_widen_otp_code_for_hash.php` widens the column; verify every OTP sender actually hashes now.
- **Fix**: grep for OTP generation (`otp_code`, `otp =`), confirm every write stores a hash (bcrypt/sha256). Audit `Client\Auth\VerifyWhatsAppController` and `WhatsAppService`.

### 24. Feature flags without a clear kill-switch pattern
- **Where**: `feature_on('finance.enabled')`, `whatsapp.enabled`, `radio.enabled`, etc. — checked ad-hoc inside controllers/views.
- **Impact**: flipping a flag off doesn't always 404 cleanly; links remain in nav, forms still accept submits.
- **Fix**: use the existing `EnsureFeatureEnabled` middleware consistently on every entry route of a flagged feature. Blade: build an `@featureOn('key')` directive to hide nav links. Add a test that a disabled feature actually 404s.

### 25. Setting cache invalidation
- **Where**: `settings($key)` uses `rememberForever`, invalidated on save of `Setting` row.
- **Risk**: if any code bypasses the model (`DB::table('settings')->update(...)`), the cache goes stale.
- **Fix**: grep for raw `settings` table writes; convert to `Setting` model saves. Optional: add an admin route `/admin/settings/clear-cache` + a `Setting::saved` observer (which is probably already there — verify).

### 26. Admin role/permission fallback is permissive
- **Where**: `admin_can($module, $ability)` returns `true` for any `admin` role and treats "no granular perms" as "inherited full access".
- **Risk**: adding a new granular permission to a module silently drops access for users who had the old monolithic `access.<module>` perm.
- **Fix**: document the fallback in `app/helpers.php`. When adding granular perms, add a migration that explicitly grants matching granular perms to existing holders of `access.<module>`.

### 27. Static WhatsApp templates vs admin-editable templates
- **Where**: `WhatsAppService::sendOtp` reads `settings('whatsapp.meta_template_name')` with a hardcoded `otp_verification` fallback.
- **Risk**: if Meta disables the template and admin didn't update the setting, OTPs fail silently.
- **Fix**: on template-not-found Meta error, log loudly + alert admin via `AdminNotifications`.

### 28. No rate limiting on carte-grise OCR beyond `throttle:20,60`
- **Where**: `Route::post('/vente/carte-grise/extract')` is `throttle:20,60`.
- **Risk**: one abusive client can DOS Tesseract (CPU-heavy) on shared hosting.
- **Fix**: lower to `throttle:5,60` per-user AND add a global throttle via middleware (`throttle:60,1`). Consider a simple queue.

### 29. Signed URL coverage
- **Where**: `Route::get('/client/verify/email/{id}')->middleware('signed')` — good.
- **Risk**: are download / unsubscribe / password-reset links also signed?
- **Fix**: audit `resources/views/mail/**` for any URL that includes an identifier; make sure they go through signed routes or tokens.

### 30. Mail from a personal Gmail
- **Where**: `MAIL_FROM_ADDRESS=oussamaelhadi11@gmail.com`.
- **Impact**: spam filters, brand damage. Gmail SMTP app-password can be revoked at any time.
- **Fix**: switch to `no-reply@nado.ma` via a transactional provider (Postmark, SES, Mailgun). Keep the Gmail fallback only for dev.

### 31. `QUEUE_CONNECTION=sync`
- **Where**: `.env` prod.
- **Impact**: every email / WhatsApp send runs inline in the HTTP request → slow POSTs, dropped sends under load.
- **Fix**: switch to `database` queue, add a cron `php artisan queue:work --tries=3 --sleep=5 --timeout=60`. Cron is already feasible on cPanel.

---

## P3 — Hygiene, DX, documentation

### 32. `routes/web.php` is 770 lines
- **Fix**: split into `routes/public.php`, `routes/auction.php`, `routes/admin.php`, loaded from `RouteServiceProvider`.

### 33. `routes/client.php` has duplicate / unreachable routes
- **Where**: the `guest:client` group redefines `home` and includes `nadoautoencheres` resource inside the guest group — that resource is then re-registered outside.
- **Fix**: read `routes/client.php` carefully; move `nadoautoencheres` and `equipe` out of the `guest:client` group (they're not guest actions).

### 34. Mixed Tailwind CDN + legacy CSS
- **Where**: `nadoautoenchere/*` uses Tailwind Play CDN (slow, console warning in prod).
- **Fix**: either compile a dedicated Tailwind build in Vite for the auction module, or swap to a static utility set. Don't ship the CDN in production.

### 35. `old_version/`, `nado.zip` (22 MB), `*.sql` dumps checked in
- **Where**: repo root.
- **Impact**: bloats clone size (~23 MB wasted per clone), slows CI.
- **Fix**: remove from git history (`git filter-repo`), add to `.gitignore`. Move to an S3 bucket if they're still needed.

### 36. `leads_voitures_maroc.csv` in repo root
- **Risk**: may contain PII (names, phones).
- **Fix**: verify contents; if PII, remove from history and store in a private bucket.

### 37. `indexmtn.php` at repo root
- **Risk**: unknown file. Looks like a maintenance page but is loose.
- **Fix**: inspect, move to `public/` if intentional, or delete.

### 38. `docs/` has only 2 files, `specifications/` only 4 screenshots
- **Fix**: add architecture.md (module map), deploy.md, env.md. Mirror the AGENTS.md content into developer-facing docs.

### 39. No `.env.example` audit
- **Fix**: check `.env.example` exists and covers all env vars actually used (`TESSERACT_PATH`, `WHATSAPP_*`, `APP_URL`, mail, DB, token/finance flags). Missing vars → surprise defaults in prod.

### 40. Models without `$fillable` discipline
- **Where**: `Annonces::$fillable` has dozens of columns. Easy to miss a new one.
- **Fix**: audit `VenteController::store` + `ClientAnnonceController::store` + `withRequiredAnnonceDefaults` concern to ensure every new column added via migration is also in `$fillable`.

### 41. SoftDeletes — restore UX?
- **Where**: `Annonces` uses SoftDeletes; admin can "trash" ads.
- **Fix**: check admin has a visible "trashed listings" view + restore action. If not, add it.

### 42. Admin Excel export (`GenericExport`) phone/VIN safety
- **Where**: `app/Exports/GenericExport.php`.
- **Fix**: verify every phone / VIN / ID column is written as `'@text'` (leading apostrophe or `setCellValueExplicit(... DataType::TYPE_STRING)`). Sample an export to confirm.

### 43. Open redirects via `?next=` or `redirect=` params
- **Fix**: `isSafeRedirect` must be used everywhere `redirect()->to($userInput)` could happen. Grep for `request('redirect')` / `url.intended`.

### 44. XSS in Blade
- **Fix**: grep for `{!! ... !!}` across all views; justify each one (wysiwyg output etc.) and make sure it's sanitized with a HtmlPurifier or `strip_tags` where appropriate.

### 45. SQL injection via `DB::raw` / `whereRaw`
- **Fix**: grep for `whereRaw`, `orderByRaw`, `DB::select(...` with user input — verify every one uses bindings. Likely offenders: `AnnoncesController` filters, admin search.

### 46. N+1 queries
- **Fix**: homepage + listing pages likely have `annonces` + `media` + `brand` + `modele` + `city` relations loaded per-row. Use `with([...])` eager-loading. Install `barryvdh/laravel-debugbar` locally to audit.

### 47. Image compression on upload
- **Fix**: verify `Intervention\Image` is used on all upload paths (vente, auction, carte-grise). Enforce max dimensions (e.g., 1920 px long edge) + JPEG quality 82. Otherwise storage grows unbounded.

### 48. `storage/app/public/` symlink
- **Where**: prod has a `/fix-symlink` route.
- **Risk**: route itself is public → anyone can trigger a filesystem op.
- **Fix**: lock `/fix-symlink` behind admin auth or a one-time token.

### 49. OpenGraph / share previews on detail page
- **Fix** (per AGENTS.md lesson): `og:image` must be absolute URL to the real main photo; test with Facebook Sharing Debugger after any detail-page change. Build a small "social preview" test harness.

### 50. Accessibility audit
- **Fix**: run axe/Lighthouse on home, detail, vente modal. Likely issues: missing `alt`, low contrast, unlabeled buttons, Arabic `dir` only on `<html>` but not nested LTR tokens.

### 51. SEO: `sitemap` + `robots.txt` + canonical
- **Where**: `SitemapController` exists (71 lines).
- **Fix**: verify it covers annonces, articles, brand pages. Add canonical tags on paginated listing pages; add `hreflang` for FR ↔ AR.

### 52. README.md is skeletal
- **Fix**: rewrite README with a 2-minute project overview, how to run locally, how to deploy, where AGENTS.md sits.

---

## P4 — "Nice to haves" / future work

### 53. Real-time auctions
- Current: JSON polling. Works, but doesn't scale and burns phone battery on the detail page.
- Future: Laravel Reverb (or Pusher) + Echo. `AuctionService` already returns everything a broadcast event would need — add an event dispatch right after `placeBid` returns.

### 54. WhatsApp Business is tied to a single phone number
- **Risk**: Meta disables the number → OTP + inbox outage.
- **Future**: multi-number support already partially in schema (`whatsapp_numbers` table). Finish the routing logic.

### 55. Token purchases
- Verify every payment gateway callback is signed/verified and idempotent (webhook replay safety). Grep for `TokenPurchase` writes.

### 56. Valuation / Argus engine
- Currently pure-function with admin-editable coefficients. Future: feed `price_history` back to self-tune coefficients; expose the engine as an API for partners.

### 57. Monitoring
- No APM, no error aggregator beyond Laravel's log files. Add Sentry or Flare; set up log rotation on prod.

### 58. Backups
- `.ftpquota` in repo root suggests shared hosting. Document the backup strategy for MySQL + `storage/app/public/`. Automate.

---

## How a fixer agent should proceed

1. Read `AGENTS.md` first (workflow + prod gotchas).
2. Pick P0 items 1–4 (cheap, high-value). One commit per item, commit style matches existing history, ends with the attribution lines the session reminder specifies.
3. Items 5–10 are data-touching — write a migration + verify on a copy of prod data first.
4. Only after P0 is clean, touch P1. Refactoring 6 navbars (#11) and splitting blade files (#12) must land in reviewable chunks.
5. For every fix, add at least one test (even a smoke test). Growing `tests/Feature/` is a goal in itself (#19).
6. Deploy per AGENTS.md §2 (git pull → artisan migrate per-file --path → config:clear + view:clear → LiteSpeed purge if home affected).

---

*Generated 2026-11-27. Keep this file updated as items are resolved —
delete fixed entries or move them to a `## Done` section at the bottom.*
