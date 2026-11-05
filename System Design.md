# Khmer Wedding E-Invitation — System Design

Nov 5, 2026 · @HIN CHEU

## Summary

Build it as one Laravel 13 app on a DigitalOcean droplet: couples sign in with Google, pick a theme, fill one structured wedding profile, then share a generic link plus a personal link per guest. Every invitation starts with a 2-day free preview; you switch it to live from the admin panel once they pay.

| Decision | Choice | Why |
| --- | --- | --- |
| Stack | Laravel 13; Blade + Tailwind + Alpine for invitation pages; Inertia + Vue 3 (plain JS) for the couple dashboard; Filament v5 for admin | Laravel and Vue only, no new language; public pages stay light and server-rendered |
| Content vs design | One theme-agnostic wedding profile; themes only render it | Add themes any time; couples switch theme without retyping |
| Themes | Code packages in the repo, each with a manifest, inheriting a shared base theme | Safe (no uploaded code), quick to add, common sections reused |
| Guest links | `/{slug}` generic, `/{slug}/{code}` personal | Short in Telegram, trackable, guests can't edit the name |
| Free preview | 2 days by default (admin setting), open to anyone with a link, "Preview" ribbon, links pause afterwards | Your rule, adjustable without a deploy |
| Payment | Phase 1: manual KHQR transfer, you approve. Phase 2: automatic Bakong KHQR check | Launch fast, automate when volume grows |
| Plans | Basic and Premium, each priced in USD and KHR | Your call; prices and limits editable in admin |
| Hosting | Docker Compose on a Singapore droplet, Spaces for files, Cloudflare in front | Your usual setup; closest region to Cambodia |

## Users and roles

Four roles; guests never log in, so every guest feature must work from the link alone.

| Role | Login | Can do |
| --- | --- | --- |
| Guest | None | Open a generic or personal link, view the invitation, RSVP, leave a wish, open the map, scan the gift KHQR |
| Customer (couple) | Google | Create invitations, pick or switch theme, edit content, manage guests, share links, see RSVPs, wishes and opens, request upgrade |
| Admin (you) | Google, email on an allowlist | Manage customers, invitations, themes, plans, payments and settings; activate, extend trial, suspend; preview any invitation |
| Collaborator (Phase 2) | Google | Co-edit one invitation after an email invite, e.g. spouse or parent |

One customer can own several invitations (say, engagement and wedding), so access is checked per invitation, not per account.

## Features

Your core flow (Google login, theme choice, 2-day preview, named links) is the MVP. Most additions target how Khmer weddings run: parents as hosts, several ceremonies, honorific titles, hundreds of guests and cash gifts.

| Feature | What it does | Phase |
| --- | --- | --- |
| Theme gallery | Live demo of each theme with sample data; switch theme any time without retyping | MVP |
| Couple and parents | Khmer and Latin names, photos, both sets of parents with titles; parents usually appear as the hosts | MVP |
| Ceremony program | Presets for ហែជំនូន, សូត្រមន្ត, កាត់សក់, សំពះផ្ទឹម, ចងដៃ and the reception, plus custom items, each with time and venue; presets editable in admin | MVP |
| Lunar date | Builds the Khmer lunar date line from the wedding date; couple can override it | MVP |
| Venues and map | Google Maps link and Directions button per venue; ceremony and reception can be in different places | MVP |
| Guest list | Title (លោក, លោកស្រី, កញ្ញា, ឯកឧត្តម, លោកជំទាវ, អ្នកឧកញ៉ា), name, suffix (និងភរិយា, និងក្រុមគ្រួសារ), side (bride or groom), group | MVP |
| Bulk add | Paste one name per line or import Excel/CSV; export the list | MVP |
| Two link types | Generic link for group chats and Facebook; personal link per guest | MVP |
| Share tools | Copy, phone share sheet (Telegram, Messenger), QR per guest for printed cards, a "sent" tick | MVP |
| Envelope intro | Opening screen with the guest's name; the tap starts the music, since browsers block sound until a tap | MVP |
| Link preview card | Telegram and Messenger show the couple photo and សូមគោរពអញ្ជើញ plus the guest's name | MVP |
| Gift section | Couple's KHQR codes or bank details for the cash gift (ចំណងដៃ); useful for guests abroad | MVP |
| Guest extras | Khmer/English toggle (English optional), countdown, add-to-calendar | MVP |
| Preview and upgrade | 2-day preview anyone can open, with ribbon; Basic or Premium upgrade; admin activation | MVP |
| Media | Gallery, YouTube pre-wedding video, music from a library or upload | MVP |
| RSVP | Attending plus head count, totals for table planning; on or off per invitation | Phase 2 |
| Wishes | Guest book with hide and approve | Phase 2 |
| Open tracking | Opened, not opened and last opened per guest | Phase 2 |
| Theme options | Colour palette, fonts, section order and visibility | Phase 2 |
| Collaborators | Invite a co-editor by Google email | Phase 2 |
| Automatic payment | Dynamic Bakong KHQR with a status check, auto-activation | Phase 2 |
| Couple alerts | Telegram message to the couple on each RSVP or wish | Phase 2 |
| Gift ledger | Digital ចំណងដៃ book at the reception table: scan the guest's QR, record the gift, see totals, export Excel | Phase 3 |
| Thank-you mode | After the wedding, the same links show a thank-you page with photos | Phase 3 |
| No-code skins | New looks from admin (backgrounds, colours) on existing layouts | Phase 3 |

Benchmark: [ChungDoi](https://partner.chungdoi.com/en/wedding-website) already bundles RSVP, venue maps, an album, a guest book, music and a gift QR in every design. It offers a 3-day trial and a one-time payment per invitation, close to your model.

## Invitation lifecycle, trial and payment

Every invitation starts in Trial; only an approved payment makes it Live, and dates move it everywhere else. Links never change between states.

&#91;embedded content: invitation states · 5 states, 7 transitions\]

State is computed from `trial_ends_at`, `paid_at`, `live_until` and `suspended_at`, not stored, so a late cron job never leaves a link in the wrong state. Query scopes such as `Invitation::live()` drive the admin filters; the scheduler only sends reminders.

| Rule | Default | Changed in |
| --- | --- | --- |
| Trial length | 2 days from creation | Admin settings; per invitation via Extend trial |
| Personal links during trial | 10 | Admin settings |
| Live period | Until 30 days after the wedding | Admin panel, per plan or per invitation |
| Paused page | Neutral notice, no names, dates or photos | Fixed |
| Editing and owner preview | Always open to the owner; watermark until paid | Fixed |

**Payment, phase 1 (manual).** Each upgrade gets a reference such as KW-1042 that the couple types in the transfer note, so you can match it in your bank app. You approve or reject in Filament; approval sets `paid_at` and `live_until` and emails the couple.

**Payment, phase 2 (automatic):**

- **Bakong KHQR:** generate a dynamic KHQR for the exact amount (it expires after 5 minutes by default), then poll `check_transaction_by_md5` from a queued job until paid. [konthaina/khqr-php](https://packagist.org/packages/konthaina/khqr-php) does both, but v1.3.0 allows `illuminate/support` only up to ^12; on Laravel 13, fork it or call the API directly. Needs a Bakong Open API token.
- **ABA PayWay:** hosted checkout taking ABA KHQR, cards, WeChat Pay and Alipay, plus a check-transaction API. Sandbox keys come by sign-up; production keys go through ABA's merchant team ([PayWay developer docs](https://developer.payway.com.kh)).

## Plans: Basic and Premium

Two tiers, each priced in USD and KHR from the admin panel; the couple picks a currency at checkout and pays the matching KHQR.

| Feature | Basic | Premium |
| --- | --- | --- |
| Themes | Standard themes | All themes, including Premium ones |
| Personal guest links | Up to 300 | Unlimited |
| Core invitation: couple, parents, program, lunar date, map, gift KHQR, countdown | Yes | Yes |
| Photo gallery | Up to 6 photos | Up to 30 photos |
| Background music | Built-in library | Library or own upload |
| Pre-wedding video (YouTube) | No | Yes |
| Custom link slug | No | Yes |
| Footer credit | Shown | Hidden |
| RSVP and wishes (Phase 2) | Yes | Yes |
| Theme options, open tracking, couple alerts (Phase 2) | No | Yes |
| Live period | 30 days after the wedding | 30 days after the wedding |

- The limits are starting suggestions. They live in `plans.max_guests` and `plans.features`, so you change them in admin without a deploy.
- The trial runs with Premium limits so couples see everything. Picking Basic at checkout lists the Premium-only items in use (a Premium theme, extra photos) to swap first.
- Basic to Premium later: the couple pays the difference and you approve it like any payment.
- Keep two KHQR images in settings, one for your USD account and one for KHR.

## System architecture

A modular monolith: one Laravel 13 codebase with three front doors (invitation pages, couple dashboard, admin), one database and one queue. For a solo developer that means one deploy, one set of logs and one auth system.

&#91;embedded content: system components · one droplet, five external services\]

Photos and music load from the Spaces CDN, not through the droplet, which keeps it light when a couple sends hundreds of links at once.

| Layer | Choice | Notes |
| --- | --- | --- |
| Framework | Laravel 13, PHP 8.3+ | Laravel 12 bug fixes ended Aug 13, 2026; security fixes end Feb 24, 2027 ([Laravel News](https://laravel-news.com/laravel-13-released)) |
| Invitation pages | Blade, Tailwind CSS 4, Alpine.js | Server-rendered for link previews; little JavaScript |
| Couple dashboard | Inertia + Vue 3, plain JavaScript | Rich editor, live preview, drag-to-reorder, no separate API |
| Admin | Filament v5 | v4 features plus Livewire 4; needs Tailwind 4 ([Filament v5 notes](https://dev.to/hafiz619/filament-v5-released-whats-new-what-changed-and-should-you-upgrade-2h8l)) |
| Login | Laravel Socialite, Google | Admin by role plus email allowlist |
| Lunar date | [asorasoft/chhankitek](https://packagist.org/packages/asorasoft/chhankitek) | v2.2.2 needs only PHP 8.2+ and Carbon, so it runs on Laravel 13; [chanthorn/carbon-kh](https://packagist.org/packages/chanthorn/carbon-kh) is a fallback |
| Queue and cache | Redis; Horizon optional | Image jobs, emails, alerts |
| Images | Intervention Image | WebP variants, EXIF stripped |
| QR codes | chillerlan/php-qrcode | SVG per guest |
| Excel | spatie/simple-excel | Guest import and export |
| Payments (Phase 2) | Bakong Open API or ABA PayWay | See the lifecycle section |
| Tests | Pest | State rules, link resolution, policies |

Check each package's Laravel 13 support on Packagist before adding it; khqr-php is one that isn't there yet.

## Theme system

A theme is a folder of Blade views and assets that renders the shared wedding profile. Adding one means a folder, a manifest and one sync command; no database or editor changes.

```
resources/themes/
  base/                      # every section, plain styling, fallback for all themes
    theme.json
    demo.json                # sample couple for the theme gallery
    views/layout.blade.php
    views/sections/          # cover, invitation, date, program, venues,
                             # gallery, gift, rsvp, wishes
    assets/app.css, app.js
  angkor-gold/
    theme.json               # "extends": "base"
    thumbnail.webp
    views/layout.blade.php
    views/sections/cover.blade.php   # only the sections this theme restyles
    assets/app.css, app.js
```

```json
{
  "slug": "angkor-gold",
  "name": "Angkor Gold",
  "version": "1.0.0",
  "extends": "base",
  "tier": "premium",
  "intro": "envelope",
  "sections": ["cover", "invitation", "date", "program", "venues", "gallery", "gift", "rsvp", "wishes"],
  "fonts": { "heading": "Moul", "body": "Battambang" },
  "options": {
    "palette": { "type": "select", "choices": ["gold", "rose", "emerald"], "default": "gold" },
    "show_countdown": { "type": "bool", "default": true }
  }
}
```

Rules:

- Each section resolves theme-first, then base: `view()->first(['theme-angkor-gold::sections.cover', 'theme-base::sections.cover'])`.
- Every theme gets the same `InvitationViewModel` (wedding, events, venues, guest, lunar date, `isPreview`); themes never query the database.
- Section order and visibility come from the manifest, overridable per invitation in `theme_settings` (Phase 2).
- One Vite entry per theme, so an invitation loads only its own CSS and JS.
- Never break a live theme: small fixes bump `version`; a redesign gets a new slug.
- Themes live in Git, not admin uploads. Blade runs PHP, so uploaded themes would be a code-execution hole; admin only toggles active, tier, order and thumbnail.

Adding a theme:

1. Run your own `php artisan make:theme royal-lotus` command, which copies a starter that extends `base`.
2. Override only the sections you restyle; add assets, `demo.json` and a thumbnail.
3. Run `php artisan themes:sync` to read manifests into the `themes` table (inactive by default).
4. In admin, set tier and order, check the demo, then activate.

## Invitation links and sharing

Put a short random code in each personal link, not the guest's name. A Khmer name in a URL often shares as a long `%E1%9E%…` string, anyone can edit it, and it can't be tracked.

| Link | Pattern | Example | Greets |
| --- | --- | --- | --- |
| Generic | `/{slug}` | `/sokha-dara` | សូមគោរពអញ្ជើញ ភ្ញៀវកិត្តិយស (honoured guest) |
| Personal | `/{slug}/{code}` | `/sokha-dara/k7Px2Q` | Title, name and suffix, e.g. លោក ចាន់ ថា និងភរិយា |
| QR | `/{slug}/{code}/qr.svg` |  | The personal link, for printed cards |
| Owner preview | `/app/invitations/{id}/preview` |  | Draft data with watermark, in any state |

Rules:

- Slug: Latin letters from the couple's romanised names, plus a number if taken (`sokha-dara-2`). Block reserved words (`app`, `admin`, `api`, `auth`, `themes`, `login`).
- Code: 6 random characters from an alphabet without look-alikes (no 0, O, 1, I, l), about 34 billion combinations, unique per invitation, with rate limiting on lookups.
- Links never change: payment, theme switches and edits keep the same URLs, so couples can send early.
- An RSVP through the generic link asks for a name and creates a guest record.
- Render `<head>` on the server: `og:title` = សូមគោរពអញ្ជើញ + title + name, `og:description` = couple and date, `og:image` = cover photo at 1200×630. Link-preview bots don't run JavaScript, which is why invitation pages are Blade, not a SPA.
- Keep Khmer text out of generated images. PHP's GD library doesn't shape complex scripts, so subscript consonants break; put names in `og:title`, which the apps render correctly.
- Telegram caches previews, so require a cover photo before the share step unlocks.
- Count an "open" from a beacon fired when the guest taps the envelope, not from page load. Preview bots fetch the page as soon as a link is pasted and would mark every guest as opened.
- Send `noindex, nofollow` (meta tag and `X-Robots-Tag` header); weddings shouldn't appear in Google.
- Dashboard sharing: `navigator.share` opens the phone's share sheet; fall back to copy and `https://t.me/share/url?url=…&text=…`, with the message text editable per invitation.

## Data model

`invitations` is the hub: every couple-owned table carries `invitation_id`, so one `InvitationPolicy` check guards everything under it.

| Table | Key fields | Notes |
| --- | --- | --- |
| users | name, email, google\_id, avatar\_url, role (customer, admin), locale, last\_login\_at | Created on first Google login |
| invitations | user\_id, slug, theme\_id, theme\_settings (json), content (json), plan\_id, wedding\_date, trial\_ends\_at, paid\_at, live\_until, suspended\_at | `content` holds wording blocks (opening, closing, dress code) in km and en; state is derived from the timestamps |
| invitation\_people | invitation\_id, role (groom, bride, groom\_father, groom\_mother, bride\_father, bride\_mother), title, name\_km, name\_en, is\_late, photo\_media\_id | One row per person, so themes loop over them |
| event\_presets | key, title\_km, title\_en, default\_sort, is\_active | The ceremony list, editable in admin |
| events | invitation\_id, preset\_key, title\_km, title\_en, starts\_at, ends\_at, venue\_id, note, sort | `preset_key` null for custom items |
| venues | invitation\_id, name, address, maps\_url, lat, lng | `maps_url` alone is enough for MVP |
| guests | invitation\_id, code, title, name, suffix, side, group, phone, sent\_at, first\_opened\_at, last\_opened\_at, open\_count | Unique index on (invitation\_id, code) |
| rsvps | invitation\_id, guest\_id, name, attending, party\_size, message | `guest_id` set even for generic-link replies |
| wishes | invitation\_id, guest\_id, name, message, is\_hidden | Moderated by the couple |
| media | invitation\_id, kind (cover, person, gallery, khqr, music), path, variants (json), sort | Files on Spaces; variants are resized WebP |
| gift\_accounts | invitation\_id, owner (groom, bride, couple), bank, account\_name, account\_number, khqr\_media\_id | Feeds the gift section |
| themes | slug, name, version, tier, is\_active, sort, thumbnail\_path, manifest (json) | Synced from theme folders |
| plans | name, price\_usd\_cents, price\_khr, max\_guests, features (json), live\_days\_after\_wedding, is\_active | Feature limits read from `features` |
| payments | invitation\_id, user\_id, plan\_id, amount\_minor, currency, method (manual\_khqr, bakong, payway), reference, proof\_media\_id, status, reviewed\_by, reviewed\_at, note | Approval sets `paid_at` and `live_until` |
| settings | key, value (json) | trial\_days, trial\_guest\_cap, payment instructions, KHQR images (USD and KHR), Telegram chat id |
| activity\_log | actor\_id, action, subject, changes (json) | Every admin status change |

Later tables: `collaborators` (invitation\_id, user\_id, role) in Phase 2; `gift_entries` (invitation\_id, guest\_id, amount\_minor, currency, method, recorded\_by, recorded\_at) in Phase 3.

- Store money as integers in minor units (US cents, whole riel), never floats.
- Store times in UTC; render and compute the lunar date in `Asia/Phnom_Penh`.
- Soft-delete invitations, so an accidental delete can be restored by you.

## Key flows and routes

Five flows cover the MVP; payment rules are in the lifecycle section above.

1. **Create:** Google login → pick a theme → enter couple names and wedding date → invitation created with `trial_ends_at = now + trial_days` → editor opens.
2. **Edit:** steps Couple and parents → Date and program → Venues → Photos and music → Gift → Guests → Share. Each change autosaves; a phone-sized iframe beside the form shows the live preview.
3. **Guests and share:** add, paste or import guests → codes generated → share page lists every guest with Copy, Share, QR and a Sent tick, generic link on top.
4. **Guest opens:** request to `/{slug}/{code}` → invitation and guest resolved from cache → state check (live, trial with ribbon, or paused page) → theme renders → tap on the envelope logs the open and starts music → RSVP, wish, map, gift.
5. **Pay:** Upgrade → plan and currency → your KHQR plus a reference such as KW-1042 → couple pays and uploads the screenshot → Telegram alert to you → you approve in admin → invitation goes live, couple gets an email.

| Area | Method | Route | Purpose |
| --- | --- | --- | --- |
| Public | GET | `/`, `/themes`, `/themes/{slug}` | Landing page, theme gallery, demo with sample data |
| Auth | GET | `/auth/google`, `/auth/google/callback` | Socialite redirect and callback |
| Dashboard | GET, POST | `/app`, `/app/invitations` | List and create invitations |
| Dashboard | GET, PUT | `/app/invitations/{id}/edit/{step}` | Editor steps with autosave |
| Dashboard | GET | `/app/invitations/{id}/preview` | Owner preview inside the editor iframe |
| Dashboard | GET, POST | `/app/invitations/{id}/guests`, `/guests/import` | Guest list and bulk add |
| Dashboard | GET | `/app/invitations/{id}/share` | Links, QR codes, Sent ticks |
| Dashboard | GET | `/app/invitations/{id}/responses` | RSVPs, wishes, opens (Phase 2) |
| Dashboard | GET, POST | `/app/invitations/{id}/upgrade` | Plans and payment proof |
| Admin | any | `/admin/*` | Filament panel |
| Guest | POST | `/{slug}/rsvp`, `/{slug}/wishes`, `/{slug}/opened` | Guest actions, rate-limited, code in the body |
| Guest | GET | `/{slug}`, `/{slug}/{code}`, `/{slug}/{code}/qr.svg` | Invitation pages; register these last so they don't swallow other routes |

## Deployment and operations

One Docker Compose stack on a 2 vCPU / 4 GB Singapore droplet is enough to launch. Files live on Spaces from day one, so the app stays stateless and can scale out later.

| Service | Runs | Notes |
| --- | --- | --- |
| nginx | Reverse proxy, built assets | Cloudflare in front, origin certificate, Full (strict) TLS |
| app | PHP 8.4 FPM (Laravel 13 needs 8.3+) | Multi-stage Dockerfile: Composer and Vite build, slim runtime, OPcache on |
| worker | Same image, supervisord running `queue:work` | Image resizing, emails, Telegram alerts |
| scheduler | Same image, `schedule:work` | Trial reminders, cleanup, nightly backup |
| redis | Cache, queues, sessions | Sessions in Redis keep the app stateless |
| db | MySQL 8 or PostgreSQL | Move to a managed database when you need point-in-time recovery |

- **Files:** uploads go to DigitalOcean Spaces with its CDN. A queued job makes WebP variants (480, 960, 1600 px) and strips EXIF, including GPS location.
- **Caching:** cache each invitation's view model in Redis and clear it on save. Skip edge-caching invitation HTML in the MVP; it avoids stale "paused" pages after payment.
- **Cloudflare:** DNS, static asset caching, rate-limit rules on guest POST routes, Turnstile on the RSVP and wish forms.
- **Backups:** nightly database dump to Spaces, kept 14 days, plus a weekly droplet snapshot. Restore once on staging before launch.
- **Alerts:** your Telegram bot posts new payment requests, failed jobs, error spikes and disk above 80%; an uptime monitor pings Laravel's `/up` route.
- **Deploys:** GitHub Actions runs tests, builds and pushes the image, then SSHes in for `docker compose pull && docker compose up -d` and `php artisan migrate --force`.
- **Scale path:** managed database first, then a second app droplet behind a load balancer; nothing else changes.

## Security and privacy

Each guest list holds names, and sometimes phone numbers, of hundreds of people, so treat every invitation as private data owned by the couple.

- **Login:** Google only, so no passwords are stored. Admin needs `role = admin` and an email on a config allowlist; turn on 2-step verification for that Google account.
- **Authorization:** every dashboard route goes through `InvitationPolicy`; never trust an invitation or guest ID from the request on its own.
- **Guest endpoints:** rate-limit per IP and per invitation, add Turnstile and a honeypot to forms, cap wish length, store wishes as plain text.
- **Uploads:** check type and size, re-encode every image (drops hidden payloads), strip EXIF, keep originals private and serve only the variants.
- **Themes:** couple text is always echoed escaped (`{{ }}`, never `{!! !!}`); a CSP header allows only your CDN, Google Fonts and YouTube embeds.
- **Payments:** proof screenshots are private, shown to admin through signed URLs; every approval is logged. For automatic payment, mark paid only after the Bakong or PayWay server confirms amount and reference, never from the browser.
- **Retention:** couples can export guests, RSVPs and wishes to Excel and delete the invitation. A setting auto-deletes guest phones and media some months after `live_until` (e.g. 6).
- **Secrets:** `.env` stays out of the image; payment keys live only on the server; renew the Bakong API token before it expires (the API has a renew endpoint).
- **Before launch:** a privacy policy and terms page covering what you store, for how long, and refunds.

## Roadmap

Four phases, each closed by a gate; the Build plan tab breaks the MVP into milestones.

&#91;embedded content: roadmap · 4 phases, 3 gates\]

Phase 2 starts only after a real couple has paid and shared, and their feedback sets its order. To move a feature between phases, change its Phase dropdown in the Features table.

Milestones, task checklists and effort estimates: Build plan

## Decisions

Settled on 5 Nov 2026; the sections above already reflect them.

| Decision | Answer |
| --- | --- |
| Trial | Anyone with a link can view during the 2-day preview; "Preview" ribbon on; personal-link cap of 10, set in admin |
| Price model | Basic and Premium tiers, priced in USD and KHR |
| Live period | 30 days after the wedding, editable in the admin panel |
| Payment | Manual KHQR transfer plus screenshot, approved by you; Bakong automation later |
| Sign-ups | Couples only |

## Sources

- [Laravel 13 Released](https://laravel-news.com/laravel-13-released), Laravel News: release, PHP requirement, support dates
- [Filament v5 Released](https://dev.to/hafiz619/filament-v5-released-whats-new-what-changed-and-should-you-upgrade-2h8l), DEV Community: changes and requirements
- [asorasoft/chhankitek](https://packagist.org/packages/asorasoft/chhankitek), Packagist: Khmer lunar date
- [chanthorn/carbon-kh](https://packagist.org/packages/chanthorn/carbon-kh), Packagist: lunar date fallback
- [konthaina/khqr-php](https://packagist.org/packages/konthaina/khqr-php), Packagist: KHQR generation and Bakong transaction check
- [ABA PayWay Developer Suite](https://developer.payway.com.kh): APIs, sandbox and production access
- [ChungDoi wedding website](https://partner.chungdoi.com/en/wedding-website): comparable product, features and pricing model
