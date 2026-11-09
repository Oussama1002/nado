# Argus NADO — « La Côte Auto du Maroc »

**Feature spec + implementation instructions for the developing agent.**
Status: proposed / not started. Owner: NADO (autoenchere.ma).
Read `AGENTS.md` at the repo root first — this document assumes its conventions and gotchas.

---

## 0. TL;DR for the agent

Build a **public used-car price index** ("la côte") generated automatically from the
site's own `annonces` data, published as **programmatic-SEO pages** (one per
brand / model / year / city). The goal is **organic search traffic** that funnels
back into the existing Achat (listings) and Vente (sell) funnels.

The build has four parts, in order:

1. **Aggregation engine** — a scheduled command that rolls `annonces` up into a
   pre-computed `price_stats` table.
2. **Public pages** — 3 levels of standalone Blade pages (hub → brand → model),
   bilingual FR/AR, reading only from `price_stats` (never crunching live).
3. **SEO plumbing** — XML sitemap(s), JSON-LD structured data, meta/OG tags,
   internal cross-links to/from detail pages.
4. **Integration touchpoints** — a "vs la côte" badge on the car detail page and a
   standalone `/estimation` entry that reuses the same stats.

**Do NOT start coding before Phase 0 (data audit).** The whole feature's viability
depends on data density. See §2.

---

## 1. Why this exists (context, don't skip)

- Morocco has **no trusted used-car price reference** (France has *l'Argus*). NADO
  already owns the raw data — every `annonces` row is a real market data point.
- **Programmatic SEO**: one template × thousands of DB-driven combinations =
  thousands of pages, each targeting a real high-volume query
  (`"prix dacia logan 2018 maroc"`, `"côte golf 7 occasion"`, …).
- The payoff compounds: more listings → more/denser pages → more traffic →
  more listings. It is a long-horizon asset (weeks-to-months to rank), not a quick spike.

**Success metric:** indexed côte pages ranking and sending organic sessions that
click through to Achat listings. Secondary: sell-side leads from `/estimation`.

---

## 2. Phase 0 — Data audit (DO THIS FIRST, gate the whole build)

Before writing any schema, measure the real data. The feature only works where
there are enough comparable listings to produce a *credible* price.

Run (locally — see `AGENTS.md` §2 for the `.env` prod-cred swap, or ask the user to
run on prod via tinker). Remember: **do not blanket-`php artisan migrate`**.

Measure, per the active-listing definition in §3.1:
- Total active listings.
- Distinct (brand, model) pairs, and how many listings each has.
- Distribution of listings per (brand, model, year) and per (brand, model, year, city).
- How many rows store `mark`/`modele` as **IDs** vs **names** (see gotcha §3.2).
- `prix` sanity: count of 0 / null / absurd values (outliers to exclude).

**Decision rule (pick the finest level with enough density):**
- A published price cell needs **≥ 5 listings** (tune after the audit). Below that,
  roll up (see §4.3). Never publish a price computed from 1–2 listings.
- If city-level cells are almost all thin → **launch national-level first**
  (`/cote-auto/{brand}/{model}` with a per-year table, no city breakdown), and add
  city pages later once volume grows.

Report the numbers back to the user and confirm the launch level before Phase 1.

---

## 3. Data model & domain facts (verified against the code)

### 3.1 What counts as an "active" listing
Match the existing public queries (`FrontController`, `BuyController`):
```php
Annonces::where('active', 1)->where('status', 'disponible')
```
Use exactly this. Do not invent a new "active" definition.

### 3.2 ⚠️ CRITICAL gotcha: `mark` / `modele` are IDs **or** names
`annonces.mark` and `annonces.modele` hold **brand/model IDs** (FK to `Brand` /
`Modele`) on newer rows, but **names** on older migrated rows. The current
`BuyController@index` even filters `mark` as a *name* (`UPPER(mark) = ?`).

The aggregation MUST normalize both into a canonical **brand_id + model_id**:
- If the value is numeric and exists in `brands` / `models` → treat as ID.
- Else treat as a name and resolve case-insensitively against `brands.name` /
  `models.name`; cache a name→id map. Log and skip rows that resolve to neither.

Aggregate on the resolved IDs so new and migrated rows merge into the same cell.
Store the canonical brand/model **name + slug** (`Str::slug($brand->name)`) in
`price_stats` for URL building and display.

### 3.3 Relevant `annonces` columns
| Column | Meaning | Notes |
|---|---|---|
| `prix` | asking price (dh) | exclude 0/null/outliers (§4.2) |
| `mark` | brand (ID or name) | normalize, §3.2 |
| `modele` | model (ID or name) | normalize, §3.2 |
| `year` | model year | bucket/validate (reject absurd years) |
| `km` | mileage | optional secondary dimension (km bands) |
| `city` | city **name** (not an ID) | normalize casing/spelling; map variants |
| `carburant` | fuel | optional facet |
| `active`, `status` | visibility | §3.1 |

Models: `App\Models\Brand` (`name`, `modeles()`), `App\Models\Modele`
(table **`models`**, `name`, `brand_id`), `App\Models\Annonces`
(`brand()` → `mark`→`id`, `modeleRelation()` → `modele`→`id`).

### 3.4 New table: `price_stats`
One row per published cell, at whatever granularity the audit allows. Suggested
migration (use the project's dated filename style; guard with
`Schema::hasColumn`/`hasTable` for idempotency; run with
`php artisan migrate --path=…` per `AGENTS.md`):

```
price_stats
  id
  brand_id            (FK, indexed)
  brand_name          (denormalized, for display)
  brand_slug          (indexed)
  model_id            (FK nullable, indexed)
  model_name
  model_slug          (indexed)
  year                (nullable → national/all-year rollups)
  city                (nullable → national rollup; store name + a slug)
  city_slug           (nullable, indexed)
  sample_count        (int)   -- how many listings backed this number
  price_median        (int)
  price_p25           (int)
  price_p75           (int)
  price_min           (int)
  price_max           (int)
  km_median           (int nullable)
  trend_pct           (decimal nullable)  -- vs previous snapshot, §4.4
  level               (enum/string: 'model' | 'model_year' | 'model_year_city')
  computed_at         (timestamp)
  UNIQUE (brand_slug, model_slug, year, city_slug, level)
  INDEX (brand_slug, model_slug)
```
Keep it a **rebuildable cache**: the command truncates/upserts it. It is not a
source of truth, so it is safe to drop and regenerate.

---

## 4. Phase 1 — Aggregation engine

### 4.1 The command
`App\Console\Commands\BuildPriceStats` (`php artisan nado:build-price-stats`).
New class in an existing PSR-4 namespace → **no `composer dump-autoload`** needed
(and it hangs — see `AGENTS.md`). Register it on a schedule in
`app/Console/Kernel.php` (nightly, e.g. `->dailyAt('04:00')`).

Flow:
1. Load the brand/model name→id maps (§3.2).
2. Stream active listings (chunk; the table can be large) and group into
   canonical cells.
3. Within each cell, clean then compute stats (§4.2).
4. Compute rollups so no publishable page is thin (§4.3).
5. Compute trend vs the previous run (§4.4).
6. Upsert into `price_stats`, stamp `computed_at`, remove cells that fell below
   threshold this run.
7. Invalidate any page cache for côte pages (and purge LiteSpeed on prod — §6.3).

### 4.2 Price cleaning (do this, or the numbers lie)
Per cell, before computing:
- Drop `prix` ≤ a floor (e.g. < 5 000 dh) and null/0.
- Drop obvious outliers: compute p25/p75, then exclude values outside
  `[p25 − 1.5·IQR, p75 + 1.5·IQR]` (IQR filter). Recompute median on the cleaned set.
- Validate `year` (e.g. 1990 … current+1); drop the rest.
- Require `sample_count ≥ threshold` (from §2) to publish the cell.

Publish the **median** as the headline price and **p25–p75** as "fourchette
habituelle". Never publish mean (skews on luxury outliers).

### 4.3 Rollup fallback (prevents empty/embarrassing pages)
For each (brand, model), build the finest cells that pass the threshold, and fall
back upward when a cell is too thin:
```
model + year + city   (level = model_year_city)   ← if dense enough
        ↓ fallback
model + year           (level = model_year)
        ↓ fallback
model (all years)      (level = model)
```
A model-level page always shows the per-year table using whatever levels passed;
a year with too few listings shows "pas assez de données" for that row rather than
a fake number. A city page only exists when its cells pass.

### 4.4 Trend
Keep the previous run's median per cell (either a `price_stats_history` table or a
`previous_median` column snapshotted before upsert). `trend_pct = (new − old)/old`.
Display as ↑/↓ X% "sur les 90 derniers jours" (word it by snapshot cadence).
Suppress trend when the prior sample was thin.

---

## 5. Phase 2 — Public pages

### 5.1 Routes (add to `routes/web.php`, dotted names)
```php
Route::get('/cote-auto', [CoteAutoController::class, 'index'])->name('cote.index');
Route::get('/cote-auto/{brand}', [CoteAutoController::class, 'brand'])->name('cote.brand');
Route::get('/cote-auto/{brand}/{model}', [CoteAutoController::class, 'model'])->name('cote.model');
// Optional city level, only if the audit supports it:
Route::get('/cote-auto/{brand}/{model}/{city}', [CoteAutoController::class, 'city'])->name('cote.city');
```
`{brand}`/`{model}`/`{city}` are **slugs**, resolved against `price_stats`
(`brand_slug`, …). 404 (real 404, so Google doesn't index empties) when no stats row
matches. Note `route:list` is broken project-wide — verify names via
`app('router')->has('cote.model')` in tinker (`AGENTS.md`).

### 5.2 Controller
`App\Http\Controllers\CoteAutoController`. Reads **only** `price_stats` (fast).
- `index` — list brands that have published stats (with a representative avg).
- `brand` — all models for the brand + headline median each; link to model pages.
- `model` — the money page: headline median + fourchette, per-year table, per-city
  table (if present), trend, `sample_count` ("basé sur N annonces"), and a CTA
  button to the matching Achat listing query.
- Cache each page (`Cache::remember`, keyed by slug+locale) for the day; the build
  command busts it.

### 5.3 Views (standalone pages — match the project)
Standalone full HTML pages like financement/detail (own `<head>`, inline navbar,
**inline CSS/JS**, no new `public/` assets — avoids the separate-docroot copy step,
`AGENTS.md` §2 & §6).
- **Always include `<meta name="viewport" …>`** (a missing one has broken mobile here before).
- Bilingual: all strings via `__('messages.*')`, mirror every key in
  `lang/fr/messages.php` **and** `lang/ar/messages.php`. Honor `<html dir="rtl">`
  for Arabic; any inline chart/marquee must handle RTL (see `AGENTS.md` §4.3).
- **Navbar is duplicated** — reuse the closest existing inline navbar (financement's
  is a good base) and add a "Côte Auto" link; if you add that nav link, add it to
  all relevant navbar copies (`AGENTS.md` §4.1).
- **Charts inline as SVG** (price-by-year curve). No chart library asset in
  `public/`; build the SVG in Blade/JS inline.

### 5.4 Page content checklist (so pages are not thin content)
Each model page must carry real, unique value or Google ignores it:
- Headline median + fourchette (p25–p75) in dh.
- Per-year price table (+ inline SVG trend curve).
- Per-city table where available.
- `sample_count` transparency line ("Prix calculé sur N annonces NADO").
- Trend indicator.
- CTA: "Voir les N annonces de {model}" → Achat filtered query.
- A short auto-generated but readable FR/AR paragraph (template with real numbers,
  not keyword stuffing).
- Internal links: to the brand page, to 3–4 sibling models.

---

## 6. Phase 3 — SEO plumbing (this is what actually earns the traffic)

### 6.1 Sitemap
Generate an XML sitemap (or a sitemap index + child sitemaps if > 50k URLs)
covering every published côte URL, both locales. A command
`php artisan nado:build-sitemap` run after the stats build, writing to a route
(preferred, to dodge the separate-docroot copy) e.g. `/sitemap-cote.xml`, or a
file that is also copied to `~/public_html` on deploy. Reference it from
`robots.txt` and the main sitemap index. Include `<lastmod>` from `computed_at`.

### 6.2 Structured data + meta
- JSON-LD on model pages: `Product` / `AggregateOffer`
  (`lowPrice`=p25, `highPrice`=p75, `priceCurrency`="MAD", `offerCount`=sample_count).
- Unique `<title>` and `<meta name="description">` per page, built from real
  numbers ("Prix d'une {model} d'occasion au Maroc : à partir de … dh").
- `hreflang` FR/AR alternates. Canonical URLs. `og:` tags (reuse a real model
  image if available via the `media` relation; `AGENTS.md` §4.5).

### 6.3 Caching / deploy notes
- **Homepage and some pages are LiteSpeed page-cached** on prod. Côte pages are new,
  but if you link them from the homepage, **purge LiteSpeed after deploy**
  (`AGENTS.md` §2). After Blade changes on prod run `php artisan view:clear`.
- Views are cached in prod — clear after deploy.

---

## 7. Phase 4 — Integration touchpoints (conversion, not just traffic)

1. **Detail page "vs la côte" badge** (`annonces/detail.blade.php`, inline `.nv-`
   world): look up the matching `price_stats` cell for the listing's brand/model/
   year and show "Ce prix est X% sous/au-dessus de la côte NADO" + link to the côte
   page. Pure read from `price_stats`. Guard behind a feature flag.
2. **`/estimation` standalone page**: "Estimez votre voiture en 30s" — select
   brand/model/year(/city), read the stats, show the fourchette, then CTA into the
   Vente modal. Reuses the same engine; strong sell-side lead capture + SEO landing.
3. Reciprocal link: côte model page → the Achat filtered listing; Achat/detail →
   côte page.

---

## 8. Settings / feature flags (use the existing system)

Use the centralized `Setting` system (`settings()`, `feature_on()`,
`SettingsSeeder`, `Admin\SettingsController` — `AGENTS.md` §4.4). Add a `cote.`
group, keys **group-prefixed dotted**:
- `cote.enabled` — master switch for the whole côte section + routes.
- `cote.city_pages_enabled` — toggle the city level independently.
- `cote.min_sample` — the density threshold (so the user can tune without a deploy).
- `cote.detail_badge_enabled` — the "vs la côte" badge on detail pages.
- `cote.estimation_enabled` — the `/estimation` page.
Seed idempotently in `SettingsSeeder` (won't overwrite admin-changed values).

---

## 9. Build order & acceptance criteria

| # | Deliverable | Done when |
|---|---|---|
| 0 | Data audit (§2) | Numbers reported; launch level (national vs city) agreed with user |
| 1 | `price_stats` migration + `nado:build-price-stats` + schedule | Command populates stats; IDs/names normalized; outliers excluded; rollups fill thin cells; `php artisan tinker` shows sane medians |
| 2 | `cote.*` settings + seeder | Flags present, toggle the section |
| 3 | Routes + `CoteAutoController` + 3 Blade pages (FR/AR, mobile, inline) | Pages render FR + AR + RTL, mobile OK, read only from `price_stats`, CTA links into Achat, no thin pages |
| 4 | Sitemap + JSON-LD + meta/hreflang/canonical | Sitemap lists all URLs; rich-result test passes on a model page |
| 5 | Detail-page "vs la côte" badge | Badge shows correct delta, flag-gated |
| 6 | `/estimation` page | Returns a fourchette, CTA into Vente modal |

**Global acceptance:** `php -l` clean on every PHP file; no new file under
`public/` (keep CSS/JS inline); migrations run via `--path`; FR/AR key parity;
feature fully off when `cote.enabled` is false.

---

## 10. Guardrails (things that will cause a bad commit here)

- ❌ Don't publish a price from < `cote.min_sample` listings. Roll up instead.
- ❌ Don't compute stats live in the controller — read `price_stats`.
- ❌ Don't add assets under `public/` — inline CSS/JS/SVG (separate docroot, §6.3).
- ❌ Don't `composer dump-autoload` (hangs) or blanket `php artisan migrate` (fails).
- ❌ Don't forget AR keys / RTL / viewport meta on the standalone pages.
- ❌ Don't weaken security to "fix" things (no POST→GET on mutations, no hardcoded
  secrets) — classifier guardrails, `AGENTS.md` §6.
- ❌ Don't trust `mark`/`modele` as always-IDs — normalize (§3.2).
- ✅ Commit **and push to `main`** after changes; then give the user the prod deploy
  commands (migrate `--path`, seeder `--force`, `view:clear`, LiteSpeed purge).

---

## 11. Open questions for the user (resolve during Phase 0)

1. Launch granularity: national model/year pages first, or straight to city-level?
   (Depends on audit density.)
2. `cote.min_sample` starting value (default suggestion: 5).
3. Should `/estimation` replace or complement any estimation already in the Vente
   flow?
4. Trend window / rebuild cadence (nightly build, 90-day trend?).
