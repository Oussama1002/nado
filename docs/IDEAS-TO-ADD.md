# NADO — Creative Feature Ideas

**Purpose:** a pool of product + engineering ideas to grow nado.ma beyond "another car classifieds site" into a defensible, data-rich Moroccan auto platform. Each idea names **what**, **why it fits the market**, **how it plugs into what we already have**, and a **build size** (S/M/L).

Grouped by audience. Pick cherries — not everything should ship.

---

## A. For buyers — make Nado the smartest place to shop

### A1. Argus Nado — public price index per model/year/km  · L
**What**: SEO-indexed pages at `/argus/{marque}/{modele}` showing median price curve by year × km × ville, "recommandé", "bonne affaire", "trop cher" bands, and the live listings matching each band.
**Why**: Morocco has no trusted public price reference. Google traffic is huge for "prix Dacia Logan occasion Maroc".
**Uses**: `App\Services\Valuation\*` (already pure functions), `price_history` table (already migrated 2026_11_12), `annonces`.
**Moat**: the more listings + sales we close, the better the index — flywheel.

### A2. "Should I buy this?" score — 0–100 badge on every detail page · M
**What**: color-coded score + 3-line explanation ("prix 8% sous l'argus, km normaux pour l'âge, vendeur noté 4.6/5"). Buyers are drowning in listings; a score cuts the noise.
**Uses**: Valuation engine (argus delta) + `SellerReview` (seller rep) + `price_history` (days on market) + simple rule engine.
**Build note**: one `ListingScorer` service, display component on `annonces/detail.blade.php`.

### A3. Historique du véhicule — one-click VIN/matricule timeline · M
**What**: on the detail page, "Voir l'historique de ce véhicule" shows: previous listings of the same VIN/matricule, price drops, days-on-market, if it was ever re-listed.
**Why**: builds trust, exposes flippers, surfaces shady re-list patterns.
**Uses**: `annonces.vin` / `annonces.matricule` + `price_history` + soft-deleted trail.

### A4. Alertes WhatsApp sur recherches sauvées · S
**What**: push new matches from `saved_searches` straight to WhatsApp (not email). Moroccans read WhatsApp, not email.
**Uses**: `saved_searches` (migrated 2026_11_12), `WhatsAppService`, a daily cron.
**Charge**: 1 token / alert if `tokens.enabled`.

### A5. Alerte "prix tombé" · S
**What**: if a listing I've favorited drops ≥ 5 %, WhatsApp me. Pair with "20 personnes regardent cette voiture en ce moment" presence badge.
**Uses**: `favorites`, `price_history`, `WhatsAppService`.

### A6. Assistant négociation — auto-rédige le premier message · M
**What**: button "Contacter le vendeur": pre-fills a polite WhatsApp opener in FR or AR, tuned to the argus delta ("Bonjour, votre annonce m'intéresse. D'après l'argus moyen, j'aimerais proposer X MAD. Est-ce négociable ?").
**Uses**: existing WhatsApp integration, valuation engine, locale.

### A7. Voitures autour de moi · S
**What**: geolocation-based listing page (`navigator.geolocation` + haversine on `villes` lat/lng).
**Uses**: `annonces.city` → `villes` (needs ville lat/lng columns — small migration).

### A8. Comparateur de financement multi-banques · M
**What**: enter price + apport → table showing monthly across 36/48/60/72 months at each partner bank's current rate.
**Uses**: `FinancementSetting`, extend to a `financement_partners` table. Bots in the detail page already compute monthly.
**Monetization**: finance leads sold to banks (see Lead Marketplace below).

### A9. Pré-approbation crédit en 1 clic · L
**What**: "Je suis pré-approuvé" badge next to serious buyers. Mini-formulaire (revenu, CDI/CDD, apport), matched to partner rules, instant Green/Orange/Red light. Sellers see the badge → prioritize these buyers.
**Uses**: `financements` table, `Client` fields, partners.

### A10. Mode "comparer jusqu'à 3 voitures" · S
**What**: `ComparisonController` already exists — flesh out: side-by-side tech specs, argus delta, score A2, options grid, seller reputation.

### A11. Nado Fiche Technique — fiches modèles enrichies · M
**What**: page per model with specs, options par trim, consommation réelle (crowdsourced), options les plus demandées, prix moyens par année. SEO monster.
**Uses**: `ModelGuide` + `VehicleTrims` (already in Valuation) + aggregates from `annonces`.

### A12. Chat IA "trouve-moi la voiture" · L
**What**: conversational search bot. "Famille 4 enfants, budget 150k, essence, boîte auto" → returns a shortlist. Uses the existing filters as tools.
**Caveat**: needs a real LLM key — budget + guardrails first.

### A13. Mode "night drive" / dark mode · S
**What**: respecting `prefers-color-scheme`. Tiny UX win.

### A14. "Signaler cette annonce" + file d'attente admin modération · S
**What**: button on detail page, admin triage queue. Protects trust.
**Uses**: new `reports` table, admin view.

---

## B. For sellers — make listing dead-simple and selling fast

### B1. "Combien vaut ma voiture en 60 secondes" — carte-grise photo + 3 questions · M
**What**: photo of carte grise (already OCR'd) + état, km, ville → argus estimate + "voici 5 annonces similaires vendues la semaine dernière" + CTA "publier maintenant".
**Why**: biggest conversion lever. Today's vente form is long; a 3-tap estimate pulls many more sellers in.
**Uses**: `CarteGriseExtractor` + `VehicleValuationService` + listings query. All exist, just not glued.

### B2. Smart pricing — le prix qui vend vite · M
**What**: on the vente form, show "À 95k tu vendras en ~8 jours. À 90k, en ~3 jours." Slider shows the speed/price trade-off.
**Uses**: `price_history` to compute days-on-market at each price band for similar cars.

### B3. Photo coach IA · M
**What**: after upload, AI critique: "Photo 3 trop sombre, reprends-la en lumière naturelle. Ajoute une photo du moteur et du compteur." Guides to the proven "best-sellers' photo recipe".
**Uses**: a small Python/PHP image analysis service (brightness, blur, background clutter) — no need for GPT-Vision.

### B4. Boost intelligent — propositions au bon moment · S
**What**: inbox-style suggestions: "ton annonce a 12 vues sur 3 jours — boost + 500 MAD = 3× vues garanties". Pay in tokens.
**Uses**: `annonces.views_count` (needs column) + `TokenService` + `Pack`.

### B5. Carnet d'entretien numérique · M
**What**: seller uploads service receipts; Nado OCRs them (Tesseract déjà en place) → timeline affichée sur la fiche: "vidange 60k, courroie 85k, …". Énorme boost de confiance.
**Uses**: `Entretien` (déjà existant), OCR, `annonces.carnet_entretien` (déjà migré 2026_11_18).

### B6. Vidéo 360 à partir de 8 photos · M
**What**: upload 8 photos, auto-stitched into a swipeable 360 viewer (JS lib). No need for a real 360 camera.

### B7. Pre-launch waitlist · S
**What**: "Je vends dans 2 semaines" — buyers can follow; when the ad goes live, they get first dibs via WhatsApp. Creates buzz + fast sales.
**Uses**: new `listing_waitlist` table + WhatsAppService.

### B8. Inbox négociation unifiée · M
**What**: one place for every WhatsApp + chat + "proposer un prix" offer, classed by AI: serious / lowball / scam / tire-kicker. Stop losing good buyers in WhatsApp noise.
**Uses**: `discussions`, `whatsapp_conversations`, add a lightweight classifier (rules first; LLM later).

### B9. Détecteur de scam acheteur · S
**What**: patterns like "je paie par Western Union", "mon fils viendra chercher", "transport international" → auto-flag. Red banner in the chat.

### B10. Mise en avant "Pro vérifié" · S
**What**: KYB flow for dealers (CIN/RC upload, admin approval). Badge on every listing. Charges Pro plan.
**Uses**: `ClientType = flotte&enchere` + `ae_approved` déjà présents.

### B11. Reprise + Vente combinées · S
**What**: dans le formulaire de vente, case "je souhaite reprendre un autre véhicule" → ouvre le flow reprise après la vente.

---

## C. Auto-Enchère / B2B — monetize the bid flywheel

### C1. Enchères flash pro — 30 minutes chrono · M
**What**: short pro-only auctions every Tuesday 20h, 10–20 lots. Drives habit, FOMO, concentrated bidding.
**Uses**: `NadoAutoEnchere` already time-boxed; add a "flash" flag + landing page.

### C2. Caution Wallet pour enchérisseurs · M
**What**: pré-dépôt remboursable qui débloque l'accès aux enchères. Supprime les gagnants fantômes.
**Uses**: `TokenService` + `clients.caution_reminder_sent_at` (déjà migré).

### C3. Reverse auction — l'acheteur poste, les vendeurs enchérissent à la baisse · L
**What**: buyer posts "I want a Dacia Sandero 2020". Dealers compete by lowering prices.
**Uses**: `Demande` (already exists as a "wanted" board), add a bid flow re-using `AuctionService`.

### C4. Groupes d'achat — pros se cotisent sur un lot flotte · L
**What**: 3 dealers pool capital to buy a 10-car flotte, split by lot later.

### C5. Live auction avec vidéo · L
**What**: real-time video stream during the auction (phone on a dealer's lot). Already have Stories infra — extend.

### C6. Pro dashboard — sell-through, hammer price, rang · S
**What**: monthly digest for pros: nb lots proposés, taux de vente, prix moyen, classement. Push via WhatsApp + email.

### C7. Convoyage inter-villes · M
**What**: après adjudication, "livrer à Agadir" option. Partenariat transporteur (ou flotte interne).
**Monetization**: commission 5–10 %.

### C8. Anti-sniping déjà implémenté — ajouter "notification 60s avant clôture" · S
**What**: push WhatsApp 60s avant la fin aux 5 derniers enchérisseurs.
**Uses**: déjà dans `AuctionService` — juste l'event.

---

## D. Services & revenue streams — beyond the listing fee

### D1. Nado Check — inspection pré-achat payante · M
**What**: 300–500 MAD, un expert partenaire inspecte la voiture, PDF détaillé + score. Déclenchable depuis la fiche.
**Uses**: `DemandeExpert` déjà présent, `LegalCheck` montre que le pattern existe.

### D2. Garantie Nado — garantie mécanique après-vente · L
**What**: 1 000–3 000 MAD/an selon véhicule, pris sur des véhicules passant Nado Check. Nado prend une commission partenaire assureur.

### D3. Assurance en 1 clic · M
**What**: devis assurance en utilisant la valeur argus du véhicule. Lead vendu aux courtiers.

### D4. Legal check turbo — NARSA express · S
**What**: `LegalCheck` existe déjà. Marketer plus fort: badge "situation légale OK" sur chaque annonce + rapport téléchargeable.

### D5. Assistant dédouanement · M
**What**: pour imports, chatbot qui calcule les droits + liste les documents. Lead vers des transitaires partenaires.

### D6. Transfert carte grise — form filler digital · S
**What**: upload carte grise acheteur + vendeur + CIN → PDF pré-rempli à signer. Petit forfait + partenariat guichet.

### D7. Reprise instantanée "Nado rachète ta voiture" · L
**What**: une offre ferme sous 24h, Nado rachète directement et revend sur sa propre inventaire. Pur revenu B2C — gros changement de modèle.

### D8. Vente express 48h garantie · M
**What**: "si on ne vend pas en 48h, on te rend 500 MAD en tokens". Marketing fort, filtre les prix réalistes.

### D9. Lead Marketplace — vendre des leads qualifiés · M
**What**: `Demande` existe déjà. Les pros paient en tokens pour voir le contact d'une demande qui matche leur stock. Déjà la moitié de l'infra en place.
**Uses**: `DemandeMatcher` (service), `TokenService`.

### D10. Priority inbox (pro upsell) · S
**What**: les Pro Premium voient leurs messages en haut + "typing indicator" live + réponses sauvées.

---

## E. Content & community — the moat Google can't copy

### E1. Nado Garage — Q&A mécanique avec experts vérifiés · M
**What**: "Ma Logan fait un bruit à 60 km/h, c'est quoi ?" → experts certifiés répondent. Meilleur pour SEO que mille blog posts.
**Uses**: `DemandeExpert` déjà présent, étend aux questions publiques.

### E2. Avis propriétaire — "j'ai eu cette voiture 3 ans" · S
**What**: `OwnerReview` existe. Pousser en forme de fiche modèle. Un vrai avis vaut 10 articles.

### E3. Nado Academy — école acheteur · M
**What**: 10 modules vidéo courts: "Comment vérifier un accident", "Négocier à Casablanca", "Lire une carte grise". Les diplômés portent un badge "acheteur éduqué".
**Uses**: `Article`, `Episode` déjà présents.

### E4. Nado TV — stories + vidéos courtes · M
**What**: `Story` déjà présent + `Episode`. Pousser en TikTok-like sur la home. Vendeurs qui tournent la voiture en 15s → énorme engagement.

### E5. Classement mensuel des concessionnaires · S
**What**: ranking public (par vol, par note, par temps de réponse). Les pros se battent pour le top 10 → améliore le service.

### E6. Leaderboard acheteurs "deals de la semaine" · S
**What**: "10 Nadoistes ont économisé 50 000 MAD cette semaine en payant sous l'argus". Preuve sociale.

### E7. Podcast Nado — "L'auto au Maroc en 15 min" · M
**What**: `nado-radio` déjà en place. Ajouter un segment podcast hebdo marché auto. Génère du SEO audio + confiance.

### E8. Local communities — groupes par ville · M
**What**: un sub-site par ville avec meetups, bons plans, mécaniciens recommandés. Nado Casablanca, Nado Agadir, etc.

---

## F. AI & data — low-cost smart features

### F1. VIN decoder auto-fill · S
**What**: 17-car VIN (déjà extrait par carte-grise OCR) → pré-remplit marque/modèle/année/options via une base publique (NHTSA ou local). 30 secondes de saisie en moins.

### F2. Duplicate detection — même VIN/matricule reposté · S
**What**: sur upload, warn "ce véhicule a déjà été annoncé il y a 2 mois par un autre vendeur". Protège contre les arnaques "titre lavé".

### F3. Price prediction live · M
**What**: "cette annonce se vendra probablement en ~11 jours au prix actuel". Confidence interval. Visible au vendeur ET à l'acheteur.
**Uses**: statistical regression sur `price_history` et `annonces` fermées. Pas besoin de ML-lourd — un bon modèle linéaire suffit.

### F4. Fraud scoring listings · M
**What**: score 0–100 à la création d'une annonce. Facteurs: prix vs argus, âge du compte, photos uniques ou volées (hash perceptuel), téléphone déjà vu.
**Uses**: nouvelle table `listing_risk_scores` + file de modération admin.

### F5. Image recognition auto-tag · M
**What**: dès l'upload, détecte: couleur (confirme vs saisie), jantes alu oui/non, toit ouvrant visible, dégât visible. Enrichit la fiche sans effort.

### F6. Chatbot vente "comme en concession" · L
**What**: sur la fiche voiture, chatbot formé sur les questions récurrentes + specs du modèle. Répond quand le vendeur dort.
**Uses**: besoin d'un LLM — utiliser le modèle Claude via API avec `anthropic-ai/claude-opus` (voir skill `claude-api`). Budget mensuel + cache.

### F7. Sentiment on `SellerReview` + alertes · S
**What**: avis récents → NLP simple (keywords négatifs + note < 3) → alerte admin pour vendeurs qui dégradent.

---

## G. Trust, safety, UX

### G1. Rendez-vous sécurisé — carte des points de rencontre publics · S
**What**: liste de parkings de gare, commissariats-amis, stations lavage (partenariats). Les deux parties se retrouvent là. Cas d'usage énorme au Maroc.

### G2. Escrow light — Nado tient le dépôt 48h · L
**What**: acheteur verse 10 % de caution à Nado (via Stripe/CMI). Si pas de "problème signalé", l'argent part au vendeur. Protège les deux. Grosse responsabilité juridique — à étudier.

### G3. Vérification téléphone et CIN obligatoire pour annoncer · S
**What**: déjà en partie via OTP WhatsApp. Ajouter CIN upload optionnel = badge "vérifié CIN" (admin check).

### G4. Blocage visuel du matricule + VIN publics · S
**What**: auto-floutage du matricule sur les photos (Intervention + détection OCR), VIN jamais exposé publiquement. Protège contre le clonage d'identité véhicule.

### G5. Mode "safe browse" — masquer les annonces louches · S
**What**: toggle pour masquer les annonces sous un seuil de score A2 ou de score fraud F4.

### G6. Open redirect + CSRF audit déjà dans `ISSUES-AND-FIXES.md` · —

---

## H. Growth & viral

### H1. Parrainage — invite un vendeur, gagne 20 tokens · S
**What**: chaque signup via lien parrain = tokens pour les deux. Utilise la ledger déjà présente.

### H2. "Partage ton annonce, gagne un boost" · S
**What**: bouton partage WhatsApp/Facebook → auto-boost 24h si N clics trackés.

### H3. "Je viens d'acheter" — stories positives · S
**What**: badge "Acheté sur Nado" + photo → post dans Stories + share social. Preuve sociale massive.

### H4. Nado Wallet — cashback en tokens sur frais · S
**What**: 2% cashback token sur chaque Pack/Boost acheté. Augmente la rétention.

### H5. Nado pour pros — API publique + feed XML · M
**What**: permet aux gros concessionnaires de synchroniser leur stock automatiquement. Multiplie l'inventaire.

### H6. Code promo pour les premiers 1000 utilisateurs d'une nouvelle ville · S
**What**: lancement progressif par ville — "Nado à Marrakech: les 500 premières annonces gratuites".

---

## I. Ops & admin — scale the back office

### I1. Admin heatmap · S
**What**: dashboard : marques/modèles en hausse cette semaine, villes qui bougent, temps moyen de vente. Guide le marketing.

### I2. Dealer health score + churn alerts · M
**What**: dealer qui baisse d'activité → alerte équipe commerciale + WhatsApp automatique "tout va bien ?".

### I3. A/B framework pour la home · M
**What**: framework léger (table `experiments` + cookie-sticky) pour tester CTAs. La home a 6500 lignes de Blade — ça doit tourner proprement.

### I4. Market briefing hebdomadaire · S
**What**: email tous les lundis aux pros avec: top 10 modèles recherchés cette semaine, variations de prix, vos lots les plus vus.

### I5. Nado Score pour tous — pros ET particuliers · S
**What**: score public de confiance (réponse rapide, ventes closes, avis positifs). Devient la monnaie sociale du site.

### I6. Backups automatisés + monitoring · S
**What**: cron S3 pour MySQL + `storage/app/public/`. Sentry pour les erreurs.
Déjà listé dans ISSUES-AND-FIXES — mais à prioriser avant tout nouveau feature.

---

## J. Monétisation extra — token sinks créatifs

### J1. "Faire remonter une annonce" — 5 tokens · S
**What**: comme "bump" sur les forums. Ponctuel.

### J2. Masquer son annonce des autres pros — 50 tokens · S
**What**: particuliers qui ne veulent pas être spamés par 10 dealers.

### J3. Afficher le prix "ferme" / "négociable" · S
**What**: pas de tokens — mais affiche clairement. Les annonces "ferme" convertissent mieux.

### J4. Statistiques détaillées d'une annonce — 10 tokens · S
**What**: vues par ville, par heure, courbe sur 7 jours. Les sellers passionnés paient.

### J5. Nado Pro Plus — abonnement mensuel · M
**What**: inbox prioritaire + stats avancées + badge or + 2 boosts/mois. 300 MAD/mois.
**Uses**: `Plan`, `Pack` déjà présents.

---

## K. Mobile — PWA first, app later

### K1. Full PWA · M
**What**: install banner, offline saved searches, push notifications. Zéro App Store.
**Uses**: `public/manifest.json` + service worker.

### K2. Capture-first flow · S
**What**: sur mobile, "Vendre" ouvre direct la caméra. Photo → carte grise OCR → formulaire déjà rempli.

### K3. Push notifications natives · S
**What**: après install PWA, bid outbids / nouveau match / price drop.

### K4. Mode voiture — recherche vocale FR + AR · M
**What**: "Trouve-moi une Clio sous 100k à Rabat". `SpeechRecognition` API. Utile pour les conducteurs qui cherchent.

---

## L. Nice-to-have UX polish

- **L1**: Dark mode cohérent (actuellement partiel).
- **L2**: Skeleton loaders partout — perçu 2× plus rapide.
- **L3**: Comparateur favoris en flottant (barre inférieure mobile).
- **L4**: "Partager cette annonce par SMS" — la moitié des visiteurs envoient le lien à leur conjoint.
- **L5**: Mode print pour imprimer la fiche (certains imprime pour montrer au père).
- **L6**: Keyboard shortcuts admin (`g a` → annonces, `g c` → clients) — productivité modération.

---

## How to use this document

1. **Score each idea** on 2 axes: *impact* (buyer/seller value + revenue) × *build size*.
2. **Pick 3 per quarter**: 1 S (quick win), 1 M (differentiator), 1 L (moat).
3. Everything above fits the existing stack — no rewrites required.
4. Highest-leverage picks (opinion):
   - **A1** Argus Nado (SEO moat, powers everything else)
   - **B1** Combien vaut ma voiture en 60s (conversion lever)
   - **D1** Nado Check (recurring revenue, trust)
   - **F3** Price prediction (differentiator, low build cost — uses data we already have)
   - **A4** Alertes WhatsApp (retention, uses infra déjà en place)
5. Bundle complementary ideas: A1 + A2 + A3 + F3 are one "intelligence layer" release.

---

*Generated 2026-11-27. This is a brainstorm, not a roadmap — pick, prune, prioritize.*
