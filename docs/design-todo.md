# Design to-do (for Gemini)

Claude builds features with plain, working markup. This file lists the screens waiting for the design pass. Style them to `DESIGN.md` (Morodok Snaeha), tick each box when done, and commit.

## Rules for every item

- Change markup and classes only. Leave `<script setup>` blocks, `autosave.js` and every PHP file alone, except `lang/*/dashboard.php` (copy) and the Blade views and assets inside `resources/themes/`.
- Keep every `@click`, `@change`, `@submit`, `v-model`, `v-if`/`v-else`, `v-for` and `:key` binding. You may move one to a new element, but it must stay wired.
- Keep `accept="…"` on file inputs, `lang="km"` on Khmer data (names, inputs), and `role`/`aria-*` attributes.
- Dashboard text is translated: write `{{ $t('group.key') }}`, never literal text. Add new keys to both `lang/en/dashboard.php` and `lang/km/dashboard.php` (a test fails if one is missing). Show one language at a time; the ខ្មែរ/EN switch in the header picks it.
- Phone first: touch targets at least 44px, nothing wider than the screen at 375px.
- Run `npm run build` before committing; it must pass.

## M9: legal pages

- [ ] **Privacy, terms and refunds**: `resources/views/public/legal.blade.php` (`/privacy`, `/terms`, `/refunds`, `?lang=en` for English). One plain template for all three; text comes from `lang/{km,en}/legal.php`. Make it calm and easy to read on a phone (long Khmer paragraphs: generous line height). Keep the ខ្មែរ/English links and the footer links between the pages.
- [ ] **The small links that point to them**: the sign-in page's "By continuing, you agree…" line (`Pages/Auth/Login.vue`), the refund line under the pay button (`Pages/Invitations/Upgrade.vue`), the privacy link in the invitation footer (`resources/themes/base/views/layout.blade.php`) and the legal row in the landing footer (`resources/views/welcome.blade.php`).

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
