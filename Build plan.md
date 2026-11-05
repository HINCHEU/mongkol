# Build plan

Ten milestones take the MVP from an empty repo to the first paying couple, about 8 weeks for one full-time developer. Each one ends with something on staging you can click through.

## Timeline

&#91;embedded content: MVP build timeline · 10 milestones, 41 working days\]

The editor takes longest because of autosave, uploads and the live preview. Working part-time stretches every bar but keeps the order.

## Code layout

Keep business rules in small Action and Service classes, so controllers, Filament and queued jobs all share them.

```
app/
  Actions/               CreateInvitation, ApprovePayment, ImportGuests, GenerateGuestCode
  Enums/                 InvitationState, PlanTier, GuestSide, MediaKind
  Http/Controllers/
    Auth/                GoogleController
    Dashboard/           InvitationController, EditorController, GuestController,
                         ShareController, UpgradeController
    Public/              InvitationPageController, OpenBeaconController
  Http/Requests/         one Form Request per editor step
  Models/                Invitation, InvitationPerson, Event, EventPreset, Venue, Guest,
                         Media, GiftAccount, Plan, Payment, Theme, Setting
  Policies/              InvitationPolicy
  Services/
    Themes/              ThemeRegistry, ThemeRenderer, InvitationViewModel
    LunarDate.php        wraps chhankitek, always Asia/Phnom_Penh
    PlanLimits.php       guest cap, gallery size, Premium themes; trial = Premium
    Payments/            ManualKhqr (later BakongGateway)
    Telegram.php         alerts to you
  Jobs/                  ProcessMedia, SendTelegramAlert
  Filament/Resources/    Invitations, Payments, Users, Themes, Plans, EventPresets
  Console/Commands/      ThemesSync, MakeTheme
resources/
  js/dashboard/          Inertia pages and Vue components, plain JS
  themes/                base/, angkor-gold/, ...
  views/public/          landing, paused and ended pages
lang/km, lang/en         dashboard and system strings
routes/web.php           invitation routes registered last
docker/                  Dockerfile, nginx.conf, php.ini, supervisord.conf
tests/                   Pest: Feature/, Unit/
```

- Controllers stay thin: Form Request validates, an Action does the work, Inertia or a redirect answers.
- Dashboard queries start from `$request->user()->invitations()` or pass `InvitationPolicy`.
- Invitation pages only read `InvitationViewModel`; they never import dashboard code.

## M0–M2: foundation

### M0 · Setup

- [ ] `laravel new` on Laravel 13 with PHP 8.4 and no starter kit. Laravel's Vue starter kits are TypeScript, so add Inertia and Vue by hand in plain JS.
- [ ] Docker Compose for local work: nginx, app (PHP-FPM with pdo\_mysql, redis, intl, exif, gd, zip, bcmath, opcache), worker and scheduler under supervisord, redis, mysql, mailpit.
- [ ] Multi-stage Dockerfile: Composer install, then npm build, then a slim runtime image.
- [ ] Vite and Tailwind 4 with two entries to start: `dashboard.js` and the base theme.
- [ ] Pest and Pint, plus a first test that `/up` returns 200.
- [ ] GitHub Actions: Pint, tests, build and push the image, deploy to staging.
- [ ] Staging droplet in Singapore, Cloudflare DNS, a Spaces bucket, `.env` only on the server.

Done when: a push to `main` deploys to staging and `/up` is green.

### M1 · Google login and roles

- [ ] Socialite Google at `/auth/google` and its callback; match on `google_id`, then email; save name and avatar.
- [ ] Google Cloud OAuth client with staging and production callback URLs.
- [ ] `role` column plus an `ADMIN_EMAILS` allowlist; Filament's `canAccessPanel()` checks both.
- [ ] Filament v5 at `/admin` with a Users resource.
- [ ] Inertia layout for `/app` with a km/en switch and logout.

Done when: your account opens `/admin`, and any other Google account lands on `/app` and gets 403 on `/admin`.

### M2 · Data model

- [ ] Migrations in dependency order: plans, themes, invitations, invitation\_people, event\_presets, venues, events, media, gift\_accounts, guests, payments, settings, activity\_log. RSVP and wish tables wait for Phase 2.
- [ ] Models, relations and factories; `Invitation::state()` derived from the four timestamps, with scopes `trial()`, `paused()`, `live()` and `ended()`.
- [ ] `InvitationPolicy`, used by every dashboard route.
- [ ] Seeders: Khmer ceremony presets, Basic and Premium plans (30-day live period), settings (`trial_days` 2, `trial_guest_cap` 10).
- [ ] `LunarDate` service on chhankitek, always in `Asia/Phnom_Penh`.
- [ ] Pest tests: every state transition, the policy refusing other users, the lunar date for a fixed day.

Done when: `migrate:fresh --seed` builds a demo invitation and all state tests pass.

## M3–M5: the product

### M3 · Theme engine and base theme

- [ ] `ThemeRegistry` reads `resources/themes/*/theme.json`, registers `theme-{slug}` view namespaces and resolves `extends`.
- [ ] `themes:sync` and `make:theme` commands.
- [ ] `InvitationViewModel`: one cached object per invitation in Redis, cleared on every save; guest context added per request.
- [ ] `ThemeRenderer` loops the manifest's sections with a `view()->first()` fallback to base.
- [ ] Base theme built to design.md (Morodok Snaeha): layout and nine sections, Playfair Display and Plus Jakarta Sans for Latin, Moul and Kantumruy Pro for Khmer, envelope intro with the guest's name, music toggle, countdown, add-to-calendar (.ics and Google link), km/en toggle.
- [ ] One Vite entry per theme.
- [ ] `/themes` gallery and `/themes/{slug}` demo from `demo.json`.

Done when: the demo couple renders on a phone with envelope, lunar date and map, and a test theme that overrides only `cover` works.

### M4 · Couple editor

- [ ] Inertia pages: invitation list, plus a create wizard (theme, names, date) that sets `trial_ends_at`.
- [ ] Editor steps: couple and parents, date and program (presets, drag to reorder), venues, photos and music, gift, wording.
- [ ] Autosave per field group: debounced PUT, Form Request validation, a "Saved" indicator.
- [ ] Preview iframe in a phone frame, reloaded after each save.
- [ ] Uploads to Spaces; the `ProcessMedia` job makes WebP sizes and strips EXIF. Music as MP3 with a size cap; KHQR images.
- [ ] Theme switcher that keeps all content.

Done when: someone other than you builds a full invitation on a phone without help.

### M5 · Guests and links

- [ ] Guest CRUD: title dropdown, suffix, side, group; 6-character codes from the safe alphabet.
- [ ] Bulk paste (one name per line) and Excel/CSV import with a preview step; Excel export.
- [ ] Public routes `/{slug}` and `/{slug}/{code}`: registered last, reserved slugs blocked, lookups cached.
- [ ] OG tags per link; `noindex` meta tag and `X-Robots-Tag` header.
- [ ] Share page: copy, `navigator.share`, Telegram share URL, QR SVG, Sent tick, editable message.
- [ ] Open beacon on the envelope tap, rate-limited.

Done when: 300 pasted names produce 300 links, and three of them shared in Telegram show the right name in the preview card and on the page.

## M6–M9: money, admin, launch

### M6 · Plans, trial and payments

- [ ] `PlanLimits` service: guest cap, gallery size, own music, Premium themes, custom slug; the trial uses Premium limits.
- [ ] Trial ribbon and trial guest cap; neutral Paused and Ended pages.
- [ ] Upgrade page: plan and currency (USD or KHR), then your KHQR image, the amount and a `KW-{id}` reference, then a proof upload to a private disk.
- [ ] Basic checkout lists Premium-only items in use; Basic to Premium charges the difference.
- [ ] A pending payment sends you a Telegram alert with plan, amount and an admin link.
- [ ] Approve sets `paid_at`, `plan_id` and `live_until` (wedding date plus the plan's days), clears the cache and emails the couple; Reject asks for a reason.
- [ ] Scheduler sends a "preview ends soon" email before `trial_ends_at`.

Done when: a test couple pays on staging, you approve from your phone, and the ribbon is gone on the next page load.

### M7 · Admin panel

- [ ] Filament resources: Invitations (state filters; Extend trial, Set live-until, Suspend, Preview), Payments (proof through a signed URL, Approve, Reject), Users, Themes, Plans (USD and KHR prices, limits, live days), Event presets.
- [ ] Settings page: trial days, trial guest cap, payment instructions, KHQR images, Telegram chat id.
- [ ] Dashboard widgets: new trials, pending payments, live invitations, revenue this month in USD and KHR.
- [ ] Activity log on every admin action.

Done when: you can run a week of sales from `/admin` without SSH.

### M8 · Themes and polish

- [ ] Two more themes, one of them Premium, built only by overriding sections.
- [ ] Test in the Telegram and Messenger in-app browsers on iPhone and Android, plus Safari and Chrome.
- [ ] Check Khmer line wrapping, font loading and image weight on a slow connection.
- [ ] Dashboard copy in Khmer and English (`lang/km`, `lang/en`).

Done when: all three themes look right in Telegram's in-app browser on a mid-range Android phone.

### M9 · Launch

- [ ] CSP and security headers, rate limits on guest routes, upload checks reviewed.
- [ ] Nightly database dump to Spaces with a tested restore; droplet snapshots; uptime monitor; failed-job alerts to Telegram.
- [ ] Production droplet, Cloudflare Full (strict), Spaces CDN, Google OAuth consent screen set to production.
- [ ] Privacy policy, terms and refund pages in Khmer and English.
- [ ] Pilot with two or three real couples before announcing.

Done when: the first real couple pays and their guests open the links.

## After launch

Ship Phase 2 in this order, one piece at a time, and size each once real couples are using the MVP.

1. RSVP and wishes, with a responses page and Excel export.
2. Open tracking per guest on the share page.
3. Bakong automatic payment: dynamic KHQR, an md5 polling job, auto-approve; manual stays as the fallback.
4. Theme options for Premium: palette, fonts, section order.
5. Couple alerts on Telegram through a "Connect Telegram" deep link.
6. Collaborators, so a spouse or parent can co-edit.
