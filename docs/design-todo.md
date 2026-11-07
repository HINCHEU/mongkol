# Design to-do (for Gemini)

Claude builds features with plain, working markup. This file lists the screens waiting for the design pass. Style them to `DESIGN.md` (Morodok Snaeha), tick each box when done, and commit.

## Rules for every item

- Change markup and classes only. Leave `<script setup>` blocks, `autosave.js` and every PHP file alone, except `lang/*/dashboard.php` (copy) and the Blade views and assets inside `resources/themes/`.
- Keep every `@click`, `@change`, `@submit`, `v-model`, `v-if`/`v-else`, `v-for` and `:key` binding. You may move one to a new element, but it must stay wired.
- Keep `accept="…"` on file inputs, `lang="km"` on Khmer data (names, inputs), and `role`/`aria-*` attributes.
- Dashboard text is translated: write `{{ $t('group.key') }}`, never literal text. Add new keys to both `lang/en/dashboard.php` and `lang/km/dashboard.php` (a test fails if one is missing). Show one language at a time; the ខ្មែរ/EN switch in the header picks it.
- Phone first: touch targets at least 44px, nothing wider than the screen at 375px.
- Run `npm run build` before committing; it must pass.

## Password sign-in

- [ ] **Username and password on the login page**: the plain block under the Google button in `resources/js/dashboard/Pages/Auth/Login.vue`. The "or" line, the two fields, the error line (`role="alert"`), Sign in, and "No Google account? Message us on Telegram" (shown only when `telegram` is set). Google stays the main way in; the password form is for couples you made an account for.
  - Keep `autocomplete="username"` / `"current-password"` and `autocapitalize="none"`, so phones offer saved passwords and don't capitalise the username.

## Phase 2: theme options (Premium)

- [x] **Colours, fonts and sections in the Theme step**: the plain block under the theme cards in `resources/js/dashboard/Components/Steps/Theme.vue`. The colour and font choices (radio chips, one per choice the theme offers), the countdown checkbox, and the section list with Show and ↑/↓. Basic plans see one line with an Upgrade link instead.
  - The options come from each theme's `theme.json`, so draw them from the `v-for`, not one by one: a new option must show up without markup changes.
  - A colour chip could show a swatch of its palette; the font chips could show "មង្គលការ" in that font. The fonts load only on the invitation page, so a swatch or sample needs the font added to the dashboard too.
- [x] **Font pairings on the invitation**: `:root[data-font="modern"]` and `:root[data-font="traditional"]` in `resources/themes/base/assets/app.css`, and their Google Fonts families in `$extraFonts` at the top of `resources/themes/base/views/layout.blade.php`. Check the Khmer headings (Koulen, Moulpali) and body (Battambang) on a phone; swap a family if it reads badly, in both places.
- [x] **Palettes for Angkor Gold and Romdoul Silk**: they offer only their own colours for now (`"choices": ["default"]` in their `theme.json`). To add some, define `:root[data-palette="…"]` in that theme's `app.css` (it must outrank the theme's `:root`) and list the names in its `choices`, plus a label in `theme.choices.palette` in both `lang/*/dashboard.php`.

## Phase 2: Bakong automatic payment

- [x] **Bakong QR on the Upgrade page**: the plain block marked "Bakong" in `resources/js/dashboard/Pages/Invitations/Upgrade.vue` (shown when `pending.method === 'bakong'`). The QR (`pending.khqr`, an image), "scan to pay exactly…", the countdown (`role="timer"`), the expired state with "Make a new QR", and the "pay by transfer instead" link. The page polls by itself; once paid it turns live with no tap.
  - Keep the QR large and on a white background with its quiet zone: bank apps must scan it from another phone's screen.
  - The manual reference / screenshot blocks below it are unchanged and still used for manual payments.

## Phase 2: open tracking

- [x] **Opened filter and last opened on the Share page**: `resources/js/dashboard/Pages/Invitations/Share.vue`. The All / Opened / Not opened / Sent, not opened chips and the order select (guest list / recently opened) by the search box, the "opened" counter next to "sent", and "last 3 hours ago" on each guest (mobile card and the Opens column). Plain for now; "Sent, not opened" is the list the couple follows up on.

## Phase 2: RSVP and wishes

- [x] **RSVP section** on the invitation: `resources/themes/base/views/sections/rsvp.blade.php`. The two big choices (coming / can't come), the head count (shown only when coming), a note, Send, then the thank-you state with "Change my reply". The generic link also asks for a name.
- [x] **Wishes section** (guest book): `resources/themes/base/views/sections/wishes.blade.php`. The form, then the list of wishes (newest first), and the empty, thank-you and "waiting for approval" states.
  - Keep the hidden `website` input exactly as it is: it's the honeypot that catches spam bots. Don't make it visible or remove it.
  - Wishes are shown with `x-text`, never `x-html` (guests' text must not become markup).
  - New themes can override these two sections like any other.
- [x] **Responses page** for the couple: `resources/js/dashboard/Pages/Invitations/Responses.vue` (`/app/invitations/{id}/responses`). The three switches, the four totals (people coming is the one they plan tables with), the replies list and the guest book with Hide / Show / Delete.
- [x] **Responses button in the editor nav** (`Edit.vue`), next to Share.

## M9: legal pages

- [x] **Privacy, terms and refunds**: `resources/views/public/legal.blade.php` (`/privacy`, `/terms`, `/refunds`, `?lang=en` for English). One plain template for all three; text comes from `lang/{km,en}/legal.php`. Make it calm and easy to read on a phone (long Khmer paragraphs: generous line height). Keep the ខ្មែរ/English links and the footer links between the pages.
- [x] **The small links that point to them**: the sign-in page's "By continuing, you agree…" line (`Pages/Auth/Login.vue`), the refund line under the pay button (`Pages/Invitations/Upgrade.vue`), the privacy link in the invitation footer (`resources/themes/base/views/layout.blade.php`) and the legal row in the landing footer (`resources/views/welcome.blade.php`).

## M8: two more themes

Build two new invitation themes. Each is a folder that **extends base and overrides only the sections it restyles**; everything else (data, envelope, language toggle, music, beacon) comes from base.

- [x] **Theme 1 (Standard tier)**: a distinct look, e.g. a lighter, modern layout.
- [x] **Theme 2 (Premium tier)**: the showpiece; set `"tier": "premium"` in its `theme.json`.

How:
1. `php artisan make:theme <slug>` (lower-case, e.g. `angkor-gold`) creates `resources/themes/<slug>/` with `theme.json`, `assets/app.css`, `assets/app.js`.
2. Copy into `views/` only the files you change from `resources/themes/base/views/` (a section, the layout, a component). Don't copy what you don't change.
3. Add a `demo.json` only if the demo needs different sample content; otherwise base's is used.
4. `php artisan themes:sync`, then open `/themes/<slug>` to see the demo. New themes start hidden; switch them on in `/admin/themes` once they look right.
5. Use only the data in `$vm` (see `app/Services/Themes/InvitationViewModel.php`); a theme never queries the database.

Must hold in every theme:
- A layout override keeps `@include('partials.og')` in `<head>` and `envelope(@js($beaconUrl))` on the envelope (see "Don't remove").
- Both languages: every text has a `data-l="km"` and a `data-l="en"` version, or uses `<x-base::t>`.
- Test on a mid-range Android in Telegram's in-app browser: Khmer wraps cleanly (no broken subscripts), fonts load, photos aren't heavy.

## M6: plans and payment

- [x] **Upgrade page**: `resources/js/dashboard/Pages/Invitations/Upgrade.vue` (`/app/invitations/{id}/upgrade`)
  - Three states on one page: choose a plan (Basic/Premium radio cards with price, features and the amber "Not shown on Basic: …" warning) and currency; then the pay panel (amount, big `KW-…` reference, the KHQR image, instructions, screenshot upload, "Proof received ✓", cancel); or "You're on the top plan".
  - The red "Payment KW-… was not approved: reason" alert must stay prominent.
  - The reference is what couples type in their bank's transfer note: make it large and easy to read, maybe tap-to-copy.
- [x] **Upgrade button in the editor nav**: the plain "Upgrade / Plan" link after Share in `resources/js/dashboard/Pages/Invitations/Edit.vue`. During the trial it should invite the click (it's how the app earns money); once live it can be quiet.
- [x] **Unavailable page, again**: `resources/views/public/unavailable.blade.php` now shows a neutral "ធៀបការ" heading instead of the couple's names. By rule, a paused or ended page shows **no names, dates or photos**, and the view no longer receives them. Keep it that way.

The admin Payments list (`/admin/payments`) is Filament: no design work.

## M5: guests and sharing

- [x] **Guests page**: `resources/js/dashboard/Pages/Guests/Index.vue` (`/app/invitations/{id}/guests`)
  - Add-a-guest form, "Add many" (paste box, Excel/CSV picker, preview table with Import/Cancel), and the guest list (search, inline edit, Sent tick, Delete).
  - The list can hold 300+ guests: on a phone, make the table rows into stacked cards or similar.
- [x] **Share page**: `resources/js/dashboard/Pages/Invitations/Share.vue` (`/app/invitations/{id}/share`)
  - "Add a cover photo first" notice, message box, generic link card, personal links list (Copy · Share… · Telegram · QR · opens · Sent).
  - Copy shows "Copied ✓" for 1.5 s: keep that feedback visible.
- [x] **Guests / Share buttons in the editor nav**: the two plain links at the end of the step tabs in `resources/js/dashboard/Pages/Invitations/Edit.vue`. Make them match the tabs, but set apart (they are pages, not autosaved steps).
- [x] **Unavailable invitation page**: `resources/views/public/unavailable.blade.php`
  - What guests see when the trial is over and unpaid ("isn't available"), or after the wedding ("has passed"). Royal, calm, short; neutral (no names, see M6).
  - Keep the `noindex` meta tag.

## M4: uploads (already briefed)

- [x] **Photos & music step**: `resources/js/dashboard/Components/Steps/Photos.vue`
- [x] **KHQR block in the Gift step**: the section near the bottom of each account card in `resources/js/dashboard/Components/Steps/Gift.vue`

## Don't remove

- `@include('partials.og')` in the `<head>` of `resources/themes/base/views/layout.blade.php` (and of any new theme layout). It draws the Telegram/Messenger preview card with the guest's name.
- `envelope(@js($beaconUrl))` on the envelope: the tap that counts a guest's open.
