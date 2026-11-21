# WhatsApp integration — setup

NADO uses **UltraMsg** as an unofficial WhatsApp Web gateway (it was already
wired up for OTPs). Three entry points use it now:

1. **Detail page "Discuter avec le vendeur"** — opens a direct
   `wa.me` chat to the seller with a prefilled message. No server call,
   no credentials needed. Always on.
2. **Email mirror** — every outgoing mail is also posted to the admin
   WhatsApp group. Gated by `whatsapp.enabled` + `whatsapp.mirror_emails`.
3. **Demandes mirror** — every new `demande` is posted to the admin
   WhatsApp group, in parallel to the existing Telegram post. Gated by
   `whatsapp.enabled` + `whatsapp.mirror_demandes`.

## 1. Create an UltraMsg instance

1. Sign up at <https://ultramsg.com/>.
2. Create an instance (Developer plan is enough for our volume).
3. In the instance dashboard: **Scan the QR code** with the WhatsApp
   account that NADO should speak from (ideally a dedicated business
   line, not a personal one — if the account gets banned, personal
   chats go with it).
4. Note the **Instance ID** (`instance12345`) and the **Token**.

## 2. Create / find the admin group id

UltraMsg sends to a group using its id, which looks like
`120363XXXXXXXXXXXX@g.us`. Two ways to get it:

- **Easiest:** send any message *to* that group from a phone, then open
  UltraMsg dashboard → *Messages* → the incoming log shows the group id
  under `from`.
- Or call the API endpoint
  `GET https://api.ultramsg.com/<instance>/groups?token=<token>`.

## 3. `.env`

```ini
WHATSAPP_PROVIDER=ultramsg
ULTRAMSG_INSTANCE_ID=instance12345
ULTRAMSG_TOKEN=xxxxxxxxxxxxxxxx

# Group id (xxxxxxxxxxxx@g.us). Leave empty to send only to admin_numbers.
WHATSAPP_ADMIN_GROUP=120363XXXXXXXXXXXX@g.us

# Optional comma-separated DMs (useful before the group is set up).
WHATSAPP_ADMIN_NUMBERS=+2126XXXXXXXX,+2126YYYYYYYY
```

Then `php artisan config:clear` on production.

## 4. Enable the feature flags

The seeder created three settings under the `whatsapp` group — toggle
them in **Admin → Settings → WhatsApp**:

- `whatsapp.enabled` — master kill-switch (default **off**; flip to **on**
  once UltraMsg is paid and the QR is scanned).
- `whatsapp.mirror_emails` — default **on**.
- `whatsapp.mirror_demandes` — default **on**.

## 5. Smoke test

```bash
php artisan tinker
>>> app(App\Services\WhatsAppService::class)->broadcastToAdmins("✅ Test depuis NADO");
```

Expected: `1` (or more) returned, and the message appears in the group
within a few seconds.

## Gotchas / limits

- UltraMsg is **unofficial**: if WhatsApp's anti-spam flags the number
  it can get banned. Use a dedicated business line and keep message
  volume reasonable.
- `Mail::sending` mirrors **every** outgoing mail. If a specific flow
  must *not* be mirrored, flip `whatsapp.mirror_emails` off, or add a
  per-call exclusion in `AppServiceProvider::boot()`.
- Message bodies are truncated to 1200 characters; the full email still
  goes to the inbox.
- CallmeBot is still configured as a fallback **for OTPs only** — it
  cannot post to groups.
