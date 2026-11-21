# WhatsApp integration — setup (Meta Cloud API)

NADO talks to WhatsApp through the **official Meta Cloud API**
(`graph.facebook.com`). Same Meta Business account as Facebook /
Instagram Ads, so the business profile is shared across channels.

Three entry points use it:

1. **Detail page "Discuter avec le vendeur"** — opens a direct
   `wa.me` chat from the buyer to the seller with a prefilled message.
   This is a client-side link, no API call, no credentials required.
   Always on.
2. **Email mirror** — every outgoing mail is also pushed as a WhatsApp
   message to each admin phone in `WHATSAPP_ADMIN_NUMBERS`. Gated by
   `whatsapp.enabled` + `whatsapp.mirror_emails`.
3. **Demandes mirror** — each new `demande` is pushed to each admin
   phone, in parallel to the existing Telegram group post (which
   stays as-is — Meta Cloud API cannot post to WhatsApp groups).
   Gated by `whatsapp.enabled` + `whatsapp.mirror_demandes`.

## ⚠️ Limits you need to know

- **No groups.** Meta Cloud API delivers 1-to-1 only. "WhatsApp group
  broadcast" is not an option through the official API — that's why
  admin notifications fan out to a list of phone numbers. If you want
  a group experience, keep using Telegram for that.
- **24h customer-service window.** Business-initiated messages
  (everything we send server-side) only go through as plain text if
  the recipient messaged your business number in the last 24 hours.
  Outside that window, you **must** use a pre-approved **template**.
  Two templates cover all of NADO's needs — see §3 below.

## 1. Meta Business setup (one-time)

1. <https://business.facebook.com> → confirm you have a verified
   Business Account (same one you'll use for Ads).
2. In **Business Settings → Accounts → WhatsApp Accounts**, create a
   WhatsApp Business Account (WABA) if none exists.
3. Add the phone number NADO will send from (dedicated business line
   recommended; the number cannot be in use on regular WhatsApp or
   WhatsApp Business app — Meta claims it).
4. In **App Dashboard** (<https://developers.facebook.com/apps>), create
   an app of type "Business" if you don't have one, add the WhatsApp
   product to it.
5. Copy the **Phone Number ID** (in *WhatsApp → API Setup* under
   "From"). It's a numeric id, not the phone number itself.
6. Generate a **System User permanent access token** (Business
   Settings → System Users → Add → assign the WhatsApp app with
   `whatsapp_business_messaging` + `whatsapp_business_management`
   scopes → *Generate New Token*). Temporary tokens expire in 24h and
   are only good for smoke tests.

## 2. `.env`

```ini
WHATSAPP_PHONE_NUMBER_ID=1234567890123456
WHATSAPP_ACCESS_TOKEN=EAAG...                 # system user token
WHATSAPP_API_VERSION=v21.0                    # optional; current default

# Comma-separated admin phones (international format; spaces/+ ok).
WHATSAPP_ADMIN_NUMBERS=+2126XXXXXXXX,+2126YYYYYYYY

# Approved template names (see §3). Leave empty to send plain text,
# which only works inside an open 24h session.
WHATSAPP_ADMIN_TEMPLATE=nado_admin_notify
WHATSAPP_ADMIN_TEMPLATE_LANG=fr
WHATSAPP_OTP_TEMPLATE=nado_otp
WHATSAPP_OTP_TEMPLATE_LANG=fr
```

Then on prod: `php artisan config:clear`.

## 3. Approve the two templates

In **WhatsApp Manager → Message templates → Create template**:

### `nado_admin_notify` (category: **Utility**)
- Language: French (fr)
- Body:
  ```
  🔔 NADO : {{1}}
  ```
- Example variable (for Meta's review): any sample notification text.

### `nado_otp` (category: **Authentication**)
- Language: French (fr)
- Body:
  ```
  Votre code de vérification NADO est {{1}}. Il expire dans 10 minutes.
  ```
- Mark "Add security recommendation" if offered.
- Example variable: `123456`.

Approval usually lands in a few minutes (Utility / Authentication are
fast lanes). Once approved, flip the feature flags on.

## 4. Enable the feature flags

**Admin → Settings → WhatsApp**:

- `whatsapp.enabled` — master kill-switch (seeded **off**).
- `whatsapp.mirror_emails` — default **on**.
- `whatsapp.mirror_demandes` — default **on**.

## 5. Smoke test

```bash
php artisan tinker
>>> app(App\Services\WhatsAppService::class)->broadcastToAdmins("✅ Test depuis NADO");
```

Expected: returns the count of admin numbers that got through. If 0,
check `storage/logs/laravel.log` — the Meta API response body is
logged on any non-2xx.

## Gotchas

- **Body parameters** in approved templates cannot contain newlines,
  tabs, or 4+ consecutive spaces. The service collapses these for you,
  so an email with multi-line text still goes through as a
  single-paragraph WhatsApp message. The full email still lands in the
  recipient's inbox unchanged.
- The admin template truncates implicitly if the message is longer
  than ~1024 chars (Meta's variable limit). The mail-mirror listener
  already caps at 1200 chars; adjust in `AppServiceProvider::boot()`
  if you need shorter.
- **OTP fallback** (`VerifyWhatsAppController`): if WhatsApp send
  fails, the user sees a flash message with the OTP so they can still
  log in. No change — works the same as before.
- **Ads integration**: because the WhatsApp number lives under the
  same Business Account as Ads, you can run *Click-to-WhatsApp* ads
  from day one. Those messages enter the 24h window automatically.
