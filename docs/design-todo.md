# Design to-do (for Gemini)

Claude builds features with plain, working markup. This file lists the screens waiting for the design pass. Style them to `DESIGN.md` (Morodok Snaeha), tick each box when done, and commit.

## Rules for every item

- Change markup and classes only. Leave `<script setup>` blocks, `autosave.js` and every PHP file alone.
- Keep every `@click`, `@change`, `@submit`, `v-model`, `v-if`/`v-else`, `v-for` and `:key` binding. You may move one to a new element, but it must stay wired.
- Keep `accept="…"` on file inputs, `lang="km"` on Khmer text, and `role`/`aria-*` attributes.
- Phone first: touch targets at least 44px, nothing wider than the screen at 375px.
- Run `npm run build` before committing; it must pass.

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
