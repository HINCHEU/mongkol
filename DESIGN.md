---
name: Morodok Snaeha
name-km: មរតកស្នេហា
slug: base
version: 1.0.0
description: Base theme of Khmer Wedding E-Invitation. Royal Khmer ceremony meets editorial stationery; Khmer-first and phone-first.
colors:
  # Brand palette: the source of truth
  crimson-900: '#58111A'
  crimson-700: '#7B1122'
  crimson-500: '#A83540'
  crimson-100: '#FFDAD9'
  gold-800: '#775A19'
  gold-700: '#8C6D23'
  gold-600: '#A88B4A'
  gold-500: '#C5A059'
  gold-400: '#D4AF37'
  gold-100: '#F6EEDB'
  lotus-300: '#E8B4B8'
  lotus-100: '#F8E6E7'
  ivory-50: '#FDFBF7'
  ivory-100: '#FAF6EE'
  ivory-200: '#F1EDE6'
  ink-900: '#1A1412'
  ink-700: '#574141'
  ink-500: '#8B7171'
  # Material 3 roles mapped onto the brand palette (kept for design tools)
  background: '#FAF6EE'
  on-background: '#1A1412'
  surface: '#FAF6EE'
  surface-dim: '#E8E1D4'
  surface-bright: '#FDFBF7'
  surface-container-lowest: '#FFFFFF'
  surface-container-low: '#FDFBF7'
  surface-container: '#F5F0E6'
  surface-container-high: '#F1EDE6'
  surface-container-highest: '#E9E3D8'
  surface-variant: '#F1EDE6'
  on-surface: '#1A1412'
  on-surface-variant: '#574141'
  inverse-surface: '#31302B'
  inverse-on-surface: '#F4F0E8'
  outline: '#A88B4A'
  outline-variant: '#E9DBC0'
  surface-tint: '#7B1122'
  primary: '#7B1122'
  on-primary: '#FDFBF7'
  primary-container: '#FFDAD9'
  on-primary-container: '#58111A'
  inverse-primary: '#FFB3B4'
  secondary: '#775A19'
  on-secondary: '#FFFFFF'
  secondary-container: '#F6EEDB'
  on-secondary-container: '#5D4201'
  tertiary: '#6B3A40'
  on-tertiary: '#FFFFFF'
  tertiary-container: '#E8B4B8'
  on-tertiary-container: '#3E1E22'
  error: '#BA1A1A'
  on-error: '#FFFFFF'
  error-container: '#FFDAD6'
  on-error-container: '#93000A'
  success: '#2F6B3A'
  on-success: '#FFFFFF'
  primary-fixed: '#FFDAD9'
  primary-fixed-dim: '#FFB3B4'
  on-primary-fixed: '#40000A'
  on-primary-fixed-variant: '#881C2A'
  secondary-fixed: '#FFDEA5'
  secondary-fixed-dim: '#E9C176'
  on-secondary-fixed: '#261900'
  on-secondary-fixed-variant: '#5D4201'
  tertiary-fixed: '#FFDADC'
  tertiary-fixed-dim: '#EEB9BD'
  on-tertiary-fixed: '#301216'
  on-tertiary-fixed-variant: '#623C40'
palettes:
  crimson: { primary: '#7B1122', primary-hover: '#58111A', primary-soft: '#FFDAD9' }
  jade: { primary: '#1F5B4A', primary-hover: '#143D32', primary-soft: '#D7EBE3' }
  sapphire: { primary: '#23305E', primary-hover: '#161F40', primary-soft: '#DDE3F5' }
typography:
  # Latin
  display-hero:
    fontFamily: Playfair Display
    fontSize: 56px
    fontWeight: '600'
    lineHeight: 68px
    letterSpacing: -0.02em
  display-hero-mobile:
    fontFamily: Playfair Display
    fontSize: 38px
    fontWeight: '600'
    lineHeight: 48px
    letterSpacing: -0.01em
  headline-lg:
    fontFamily: Playfair Display
    fontSize: 40px
    fontWeight: '500'
    lineHeight: 50px
    letterSpacing: -0.01em
  headline-lg-mobile:
    fontFamily: Playfair Display
    fontSize: 30px
    fontWeight: '500'
    lineHeight: 40px
    letterSpacing: 0em
  headline-md:
    fontFamily: Playfair Display
    fontSize: 28px
    fontWeight: '500'
    lineHeight: 36px
    letterSpacing: 0em
  headline-sm:
    fontFamily: Playfair Display
    fontSize: 22px
    fontWeight: '600'
    lineHeight: 30px
    letterSpacing: 0.01em
  subheading:
    fontFamily: Playfair Display
    fontSize: 18px
    fontWeight: '400'
    lineHeight: 26px
    letterSpacing: 0.05em
  body-lg:
    fontFamily: Plus Jakarta Sans
    fontSize: 18px
    fontWeight: '400'
    lineHeight: 30px
    letterSpacing: 0em
  body-md:
    fontFamily: Plus Jakarta Sans
    fontSize: 16px
    fontWeight: '400'
    lineHeight: 26px
    letterSpacing: 0em
  body-sm:
    fontFamily: Plus Jakarta Sans
    fontSize: 13px
    fontWeight: '400'
    lineHeight: 20px
    letterSpacing: 0.01em
  label-lg:
    fontFamily: Plus Jakarta Sans
    fontSize: 14px
    fontWeight: '600'
    lineHeight: 20px
    letterSpacing: 0.08em
  label-sm:
    fontFamily: Plus Jakarta Sans
    fontSize: 11px
    fontWeight: '600'
    lineHeight: 16px
    letterSpacing: 0.12em
  numeral:
    fontFamily: Playfair Display
    fontSize: 36px
    fontWeight: '600'
    lineHeight: 44px
    letterSpacing: 0em
  # Khmer: letterSpacing is always 0; never uppercase or italic
  display-hero-km:
    fontFamily: Moul
    fontSize: 40px
    fontWeight: '400'
    lineHeight: 60px
    letterSpacing: 0em
  display-hero-km-mobile:
    fontFamily: Moul
    fontSize: 30px
    fontWeight: '400'
    lineHeight: 46px
    letterSpacing: 0em
  headline-lg-km:
    fontFamily: Moul
    fontSize: 30px
    fontWeight: '400'
    lineHeight: 46px
    letterSpacing: 0em
  headline-lg-km-mobile:
    fontFamily: Moul
    fontSize: 24px
    fontWeight: '400'
    lineHeight: 38px
    letterSpacing: 0em
  headline-md-km:
    fontFamily: Moul
    fontSize: 22px
    fontWeight: '400'
    lineHeight: 36px
    letterSpacing: 0em
  headline-sm-km:
    fontFamily: Kantumruy Pro
    fontSize: 20px
    fontWeight: '600'
    lineHeight: 34px
    letterSpacing: 0em
  subheading-km:
    fontFamily: Kantumruy Pro
    fontSize: 17px
    fontWeight: '400'
    lineHeight: 30px
    letterSpacing: 0em
  body-lg-km:
    fontFamily: Kantumruy Pro
    fontSize: 18px
    fontWeight: '400'
    lineHeight: 34px
    letterSpacing: 0em
  body-md-km:
    fontFamily: Kantumruy Pro
    fontSize: 16px
    fontWeight: '400'
    lineHeight: 29px
    letterSpacing: 0em
  body-sm-km:
    fontFamily: Kantumruy Pro
    fontSize: 14px
    fontWeight: '400'
    lineHeight: 25px
    letterSpacing: 0em
  label-lg-km:
    fontFamily: Kantumruy Pro
    fontSize: 14px
    fontWeight: '600'
    lineHeight: 24px
    letterSpacing: 0em
  label-sm-km:
    fontFamily: Kantumruy Pro
    fontSize: 12px
    fontWeight: '600'
    lineHeight: 20px
    letterSpacing: 0em
  numeral-km:
    fontFamily: Kantumruy Pro
    fontSize: 32px
    fontWeight: '600'
    lineHeight: 48px
    letterSpacing: 0em
  guest-name-km:
    fontFamily: Moul
    fontSize: 26px
    fontWeight: '400'
    lineHeight: 42px
    letterSpacing: 0em
rounded:
  sm: 0.125rem
  DEFAULT: 0.25rem
  md: 0.375rem
  lg: 0.5rem
  xl: 0.75rem
  full: 9999px
  control: 0.25rem
  card: 0.5rem
spacing:
  gutter: 1.5rem
  gutter-mobile: 1rem
  margin: 3rem
  margin-mobile: 1.25rem
  space-xs: 0.25rem
  space-sm: 0.5rem
  space-md: 1rem
  space-lg: 1.75rem
  space-xl: 3rem
  space-2xl: 5rem
  canvas-max: 30rem
  folio-max: 35rem
  site-max: 71.25rem
shadows:
  hairline: '0 0 0 1px rgba(197, 160, 89, 0.35)'
  float: '0 12px 32px -4px rgba(88, 17, 26, 0.08), 0 4px 12px -2px rgba(140, 109, 35, 0.06)'
  deboss: 'inset 0 1px 2px rgba(26, 20, 18, 0.04)'
  focus-ring: '0 0 0 3px rgba(212, 175, 55, 0.35)'
gradients:
  gold-leaf: 'linear-gradient(135deg, #D4AF37 0%, #C5A059 45%, #8C6D23 100%)'
motion:
  duration-fast: 150ms
  duration-base: 300ms
  duration-slow: 600ms
  duration-ceremonial: 1200ms
  ease-standard: 'cubic-bezier(0.2, 0, 0, 1)'
  ease-ceremonial: 'cubic-bezier(0.65, 0, 0.35, 1)'
breakpoints:
  sm: 480px
  md: 768px
  lg: 1024px
---

# Morodok Snaeha · មរតកស្នេហា

Base theme design system for **Khmer Wedding E-Invitation** (`resources/themes/base`). Every other theme inherits these tokens, components and section partials, and overrides only what it restyles. The YAML front matter is the source of truth; §14 mirrors it as Tailwind 4 CSS.

## 1. Principles

1. **Khmer first.** Khmer is the main voice and English the quieter second line. Check every rule against Khmer text first.
2. **Phone first.** Guests open the link inside Telegram or Messenger on a phone. Design at 360–430 px; larger screens only frame the same column.
3. **A ceremony, not a website.** Opening the page should feel like opening a royal invitation: one envelope, one reveal, then sections that unfold like a folio.
4. **Readable for grandparents.** 16 px minimum body text, strong contrast, 48 px tap targets, no small light-gold text.
5. **Light and fast.** Ornaments are SVG, motion is CSS, and the first screen loads on 4G in under 2.5 s.

Mood: solemn reverence, joyful celebration, aristocratic warmth. Materials: crimson silk (សំពត់ចងក្បិន, *sampot chang kben*), antique gold leaf, ivory rag paper and Angkorian carving (ក្បាច់, *kbach*). The style mixes tactile luxury (embossing, gold hairlines, parchment) with editorial restraint (generous space, strict hierarchy, sharp type contrast).

## 2. Color

### Palette

| Token | Hex | Role | Contrast on ivory-100 |
| --- | --- | --- | --- |
| crimson-700 | `#7B1122` | Primary actions, envelope, focal accents | 10.0:1 |
| crimson-900 | `#58111A` | Hover and pressed states, trial ribbon | 12.9:1 |
| crimson-500 | `#A83540` | Text links | 6.0:1 |
| crimson-100 | `#FFDAD9` | Soft primary fill | Background only |
| gold-800 | `#775A19` | Small gold text (times, labels) | 6.0:1 |
| gold-700 | `#8C6D23` | Icons, gold text 18 px and up, focus border | 4.5:1 |
| gold-600 | `#A88B4A` | Field borders and other UI boundaries | 3.0:1 (3.3:1 on white) |
| gold-500 | `#C5A059` | Hairlines, dividers, ornaments | 2.3:1, never text |
| gold-400 | `#D4AF37` | Gold-leaf highlight; gold text on crimson | 5.1:1 on crimson-700 |
| gold-100 | `#F6EEDB` | Gold tint fills | Background only |
| lotus-300 | `#E8B4B8` | Tag and highlight fills | Background only |
| lotus-100 | `#F8E6E7` | Selected-state fill | Background only |
| ivory-50 | `#FDFBF7` | Cards; text on primary | Surface |
| ivory-100 | `#FAF6EE` | Page background | Surface |
| ivory-200 | `#F1EDE6` | Sunken areas, disabled fills | Surface |
| ink-900 | `#1A1412` | Text | 16.9:1 |
| ink-700 | `#574141` | Secondary text | 8.7:1 |
| ink-500 | `#8B7171` | Disabled text only | 4.1:1 |
| error | `#BA1A1A` | Errors | 6.0:1 |
| success | `#2F6B3A` | Confirmations | 5.9:1 |

- Gold-500 is decoration only. Gold text is gold-800 (small) or gold-700 (18 px and up). Gold boundaries people must see, such as field borders, are gold-600.
- One crimson moment per screen: a primary button, the seal or a heading accent. The envelope is the only full-crimson surface.
- Lotus fills tags and selected states; it never carries text on its own colour.
- Names and dates never sit on top of photos. The cover places them below the photo.
- The gold-leaf gradient (`linear-gradient(135deg, #D4AF37 0%, #C5A059 45%, #8C6D23 100%)`) is only for the monogram seal ring, ornament strokes and a 1 px bevel line on primary buttons.
- Light only: set `color-scheme: light` in CSS and as a meta tag, so in-app browsers don't auto-darken the page.

### Palette option

The manifest's `palette` option swaps only the primary trio; gold, ivory and ink stay fixed. Basic invitations use crimson; switching palette is a Premium theme option (Phase 2).

| Palette | primary | primary-hover | primary-soft | Ivory-50 text on primary |
| --- | --- | --- | --- | --- |
| crimson (default) | `#7B1122` | `#58111A` | `#FFDAD9` | 10.4:1 |
| jade | `#1F5B4A` | `#143D32` | `#D7EBE3` | 7.7:1 |
| sapphire | `#23305E` | `#161F40` | `#DDE3F5` | 12.3:1 |

## 3. Typography

### Families

| Role | Latin | Khmer | Used for |
| --- | --- | --- | --- |
| Display | Playfair Display 600 | Moul 400 | Couple names, hero, guest name on the envelope |
| Headline | Playfair Display 500 | Moul 400 | Section titles, 22 px and up |
| Title | Playfair Display 600 | Kantumruy Pro 600 | Card and ceremony titles |
| Body and UI | Plus Jakarta Sans 400, 600 | Kantumruy Pro 400, 600 | Paragraphs, labels, buttons, forms |

Each stack lists the Latin face first, then the Khmer face. Latin letters and digits render in Playfair or Jakarta, and Khmer glyphs fall through to Moul or Kantumruy Pro, so one `font-family` serves bilingual lines.

### Scale

Sizes are font-size/line-height in px; exact tokens are in the YAML.

| Token | Latin, mobile → desktop | Khmer, mobile → desktop |
| --- | --- | --- |
| display-hero | 38/48 → 56/68 | Moul 30/46 → 40/60 |
| headline-lg | 30/40 → 40/50 | Moul 24/38 → 30/46 |
| headline-md | 28/36 | Moul 22/36 |
| headline-sm | 22/30 | Kantumruy 600, 20/34 |
| subheading | 18/26, tracked 0.05em | Kantumruy 17/30 |
| body-lg | 18/30 | 18/34 |
| body-md | 16/26 | 16/29 |
| body-sm | 13/20 | 14/25 |
| label-lg | 14/20, uppercase, 0.08em | 14/24, no tracking |
| label-sm | 11/16, uppercase, 0.12em | 12/20, no tracking |
| numeral | Playfair 600, 36/44, tabular | Kantumruy 600, 32/48 |
| guest-name | (uses display-hero-mobile) | Moul 26/42 |

### Khmer typesetting rules

1. No letter-spacing, uppercase or italics on Khmer. §14 enforces this with `:lang(km)`.
2. Mark the language of every text node with `lang="km"` or `lang="en"`, and use `<span lang>` inside mixed lines.
3. Line height at least 1.7 for Khmer body text and 1.4 for Moul. Never put Khmer in fixed-height boxes or behind `overflow: hidden`; stacked vowels and subscript consonants get clipped.
4. Moul only at 20 px and above; never for paragraphs or buttons.
5. Khmer text uses Khmer numerals (០–៩) for dates, times and counts; English text uses Latin numerals.
6. Never justify Khmer. Let the browser's dictionary break lines, and keep `overflow-wrap: anywhere` as a last-resort guard in narrow cards.
7. Form fields use 16 px text or larger, or iOS Safari zooms the page on focus.

### Loading

Four families from Google Fonts with `display=swap`. Google serves only the Latin or Khmer subsets a page uses; keep fonts under 300 KB. Self-host later if you want to preload them.

```html
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="color-scheme" content="light">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:wght@400;600&family=Moul&family=Playfair+Display:ital,wght@0,500;0,600;1,400&family=Plus+Jakarta+Sans:wght@400;600&display=swap">
```

## 4. Layout and spacing

- **Canvas:** one column, `max-width: 30rem` (480 px), 20 px side margins. From 768 px the canvas becomes a folio card (`max-width: 35rem`) centred on the textured ivory page, with kbach corners. From 1024 px, faint kbach panels at 6% opacity fill the sides.
- **Site grid:** the 12-column, 1140 px grid is for the marketing site and theme gallery only, never the invitation.
- **Rhythm:** 80 px (`space-2xl`) between sections with a lotus divider in the middle; 28 px (`space-lg`) inside a section; 24 px card padding (20 px under 400 px).
- **Viewport:** full-screen layers use `min-height: 100dvh` with a `100vh` fallback; fixed bars add `env(safe-area-inset-bottom)`.
- **Order:** envelope → cover → invitation → date → program → venues → gallery → gift → rsvp → wishes → footer. A section with no content is skipped, never shown empty.
- **Breakpoints:** 480, 768 and 1024 px.

## 5. Depth, borders and texture

Depth comes from stationery layering, not heavy shadows.

- **Page:** ivory-100, with an optional paper-grain WebP tile (15 KB or less) at 4% opacity.
- **Cards:** ivory-50 with a 1 px hairline `rgb(197 160 89 / 0.35)`. Ceremony cards add a second gold line inset 6 px, like printed stationery.
- **Framing density is hierarchy:** section cards get one hairline; modals and the gift envelope get a double-ruled gold border and kbach corners.
- **Shadows:** `float` for floating buttons, sheets and seals; `deboss` inside fields and steppers. One shadow per element; flat sections get none.
- **No blur:** skip `backdrop-filter`, which can stutter in low-end Android in-app browsers. Use solid ivory at 96% opacity instead.

## 6. Shape

| Token | Radius | Used for |
| --- | --- | --- |
| control | 4 px | Buttons, fields, chips |
| card | 8 px | Cards, sheets and modals; sheets round only their top corners |
| full | 9999 px | Seals only: monogram, music, video play, RSVP submit |
| arch | Semicircular top | Cover photo mask, echoing Angkorian doorways |

Avoid sharp 90° corners, which feel cold, and pills, which feel casual.

## 7. Ornaments and icons

| Asset | Spec | Where |
| --- | --- | --- |
| Lotus divider | SVG 120×24, 1.25 px stroke, `currentColor` (gold-500) | Between sections; a 16 px version under card titles |
| Kbach corner | SVG 48×48, mirrored four ways, gold-500 | Folio, modals, gift envelope |
| Monogram seal | SVG circle 88 px: primary fill, gold-leaf ring, initials in Playfair italic | Envelope, cover, footer, RSVP success |
| Arch mask | SVG `clipPath`, 4:5 | Cover photo |
| Envelope | CSS shapes plus an SVG flap; primary with a gold edge | Intro |
| Paper grain | WebP tile, 15 KB or less | Page background |

- Icons are line style: 1.5 px stroke, 20 or 24 px, gold-700 or ink-700. Lucide works well.
- At most two ornament types in one viewport, and never under text.
- Ornaments use `currentColor`, so palettes and child themes recolour them.
- Draw ornaments yourself or license them. Kbach motifs are traditional, but a specific illustrator's drawing is not free to copy.

## 8. Motion

| Token | Value |
| --- | --- |
| fast | 150 ms |
| base | 300 ms |
| slow | 600 ms |
| ceremonial | 1200 ms |
| ease-standard | `cubic-bezier(0.2, 0, 0, 1)` |
| ease-ceremonial | `cubic-bezier(0.65, 0, 0.35, 1)` |

| Moment | Motion |
| --- | --- |
| Envelope opens | One ceremonial sequence of 1200 ms on tap: seal presses (0–150 ms, scale 0.96), seal fades (150–450 ms), flap opens (300–900 ms, ceremonial ease), card rises 24 px while the page fades in (600–1200 ms). Music starts and the open beacon fires on the same tap. |
| Section reveal | Fade in and rise 12 px over 400 ms, once per section, when 15% of it is visible |
| Buttons | Colour and shadow change over 150 ms; pressed scale 0.98 |
| Music seal | Ring turns once every 12 s while music plays |
| Countdown | No animation; tabular numerals so nothing shifts |
| Petals (option, off by default) | At most 12 CSS-animated petals, paused when the tab is hidden |

Animate only `transform` and `opacity`. With `prefers-reduced-motion: reduce`, the envelope opens with a 200 ms fade, and nothing rises, turns or falls.

## 9. Components

### 9.1 Buttons

| Variant | Spec |
| --- | --- |
| Primary | Primary fill, on-primary text, 1 px gold-500 border, 1 px gold-leaf bevel line on the top inner edge, control radius, min-height 48 px, padding 12 × 24 px. Hover: primary-hover plus the `float` shadow. Pressed: scale 0.98. Disabled: ivory-200 fill, ink-500 text, no border. Loading: a small seal spinner, label width kept. |
| Secondary | Transparent, double hairline in gold-600 (1 px border plus an inset line 3 px in), ink text. Hover: gold-100 fill. |
| On crimson | For the envelope: transparent, 1 px gold-400 border, gold-400 text. Hover: ivory fill at 8%. |
| Text link | crimson-500, underlined on hover and focus. |
| Seal | 56 px circle, primary fill, 2 px gold-leaf ring, 24 px icon in on-primary. For music, video play and RSVP submit. |

Labels use label-lg (Latin, uppercase, tracked) or label-lg-km (Khmer, no tracking). Every button shows focus as a 2 px gold-700 outline with a 2 px offset. Primary actions inside sections are full width on phones.

### 9.2 Stationery card

ivory-50 fill, hairline border, card radius, 24 px padding (20 px under 400 px). The title sits centred in the title style with a 16 px lotus divider under it. Ceremony cards add the inner frame.

### 9.3 Form fields (RSVP and wishes, Phase 2)

- Label above the field in label-lg or label-lg-km, ink colour; never a placeholder on its own.
- Field: white, 1 px gold-600 border, control radius, 48 px tall, 16 px text, `deboss` shadow.
- Focus: gold-700 border plus the `focus-ring` shadow.
- Error: error-coloured border and a body-sm message with an icon under the field; the typed value stays.
- Helper text: body-sm in ink-700.

### 9.4 Attendance choice (Phase 2)

Two full-width option cards: "ចូលរួម · Attending" and "មិនអាចចូលរួម · Can't attend". Selected: 2 px primary border, lotus-100 fill, gold lotus check. It behaves as a radio group with arrow-key support.

### 9.5 Guest-count stepper (Phase 2)

[−] count [+], with 48 px square buttons in control radius and the count in the numeral style (Khmer numerals in Khmer UI). Minimum 1.

### 9.6 Chips

label-sm on lotus-100 or gold-100, 1 px gold-500 border, control radius, 28 px tall. For event tags, dress-code notes and "បន្ទាប់ · Next".

### 9.7 Ceremony timeline

- A 1 px gold-500 line 20 px from the left edge, with a 16 px lotus node per event.
- Each item: time in label-lg gold-800 (Khmer: "ម៉ោង ៧:០០ ព្រឹក"), title in the title style, a venue chip, and an optional note in body-sm ink-700.
- Multi-day weddings group items under day headers ("ថ្ងៃទី១ · Day 1" plus the date).
- On the wedding day, the next upcoming event gets a primary node and a "បន្ទាប់ · Next" chip, worked out from the guest's clock.
- One column at every width.

### 9.8 Date block and countdown

- Gregorian date in headline-md: "ថ្ងៃសៅរ៍ ទី១២ ខែធ្នូ ឆ្នាំ២០២៦" or "Saturday, 12 December 2026".
- The lunar line under it in body-sm ink-700, exactly as the LunarDate service returns it.
- Countdown: four mini cards (ថ្ងៃ, ម៉ោង, នាទី, វិនាទី / Days, Hours, Minutes, Seconds) in the numeral style.
- Once the first event starts, the countdown hides and the couple's thank-you line shows.
- "បញ្ចូលក្នុងប្រតិទិន · Add to calendar" opens a small sheet: a Google Calendar link, or an .ics file for Apple and Outlook.

### 9.9 Venue card

Venue name in the title style, address in body-md ink-700, chips for the events held there, then the primary button "បើកផែនទី · Open map", which opens the venue's `maps_url` in a new tab. An optional map preview loads the Google Maps iframe only after a tap, inside a fixed 16:10 box so nothing jumps.

### 9.10 Gallery and video

- Grid: 2 columns on phones, 3 from 768 px; 8 px gaps; 4:5 and 1:1 tiles with `aspect-ratio` set.
- `srcset` at 480, 960 and 1600 px WebP; `loading="lazy"` on everything except the cover.
- Lightbox: full screen on ink-900 at 96%, swipe between photos, a 48 px close button, the counter in label-sm.
- Video: a thumbnail with a play seal; the `youtube-nocookie.com` iframe loads only after the tap.

### 9.11 Chong dai gift card (ចំណងដៃ)

This replaces the draft's "Ang Pao": ចំណងដៃ is the Khmer wedding gift.

- Envelope frame: primary pocket with a gold edge, double-ruled gold border, kbach corners.
- Tabs when there is more than one account: Groom · Bride · Couple (from `gift_accounts.owner`).
- The KHQR image sits on pure white with a 16 px quiet zone, at least 220 px wide. Never tint, crop or recolour it; show the bank's KHQR artwork as uploaded.
- Below it: bank, account name, and account number with a Copy button (toast "បានចម្លង · Copied"), plus the currency (USD, or KHR ៛).
- "រក្សាទុក QR · Save QR" downloads the image, with a hint for in-app browsers: long-press the QR to save it.

### 9.12 RSVP sheet (Phase 2)

A bottom sheet on phones (top corners 8 px, drag handle); a centred modal from 768 px with a double-ruled gold frame. It holds the attendance choice, the stepper if attending, an optional message and the seal submit button. Success shows the monogram seal and "សូមអរគុណ · Thank you".

### 9.13 Wish card (Phase 2)

A small stationery card: guest name in headline-sm, message in body-md, relative date in body-sm ink-700. One column on phones, two from 768 px. Hidden wishes never render.

### 9.14 Music seal

Fixed bottom right, above the dock and the safe area: a 56 px seal shown once the envelope opens. It swaps play and pause icons, and its ring turns slowly while playing. Labels: "ចាក់ភ្លេង · Play music" and "ផ្អាក · Pause".

### 9.15 Quick dock

A fixed bottom bar across the canvas width: ivory-50 at 96% with a top hairline, holding four 48 px targets with icon and label-sm: កម្មវិធី Program, ផែនទី Map, ចំណងដៃ Gift, ពរជ័យ Wishes (or RSVP once it ships). It hides while scrolling down and returns on scroll up; each target scrolls to its section.

### 9.16 Language toggle

A "ខ្មែរ | EN" pill at the top right, 36 px tall with a 44 px tap area. It starts in the invitation's default language and remembers the guest's choice.

### 9.17 Trial ribbon

A 32 px bar fixed at the top: crimson-900 fill, ivory label-sm text "PREVIEW · មើលជាមុន". Not dismissible; it pushes content down instead of covering it.

### 9.18 Toast

Bottom centre, above the dock: ink-900 fill, ivory body-sm text, control radius, 2.5 s, `role="status"`.

### 9.19 State pages

Paused, ended and not-found pages: ivory page, lotus divider, one short neutral line. No names, dates or photos.

## 10. Section blueprints

| Section | Data | Layout |
| --- | --- | --- |
| Envelope | Guest title, name and suffix (`guests`), or the honoured-guest greeting; couple initials | Full screen in primary; seal in the centre; "សូមគោរពអញ្ជើញ" in label-lg gold-400; guest name in guest-name-km, ivory; "បើកសំបុត្រអញ្ជើញ · Open invitation" as the on-crimson button |
| Cover | Groom and bride names in km and en (`invitation_people`), cover photo (`media` kind cover), wedding date | Monogram seal; arch-masked photo with kbach corners; Khmer names in display-hero-km with Latin names under them; "&" in Playfair italic gold-700; date in label-lg |
| Invitation | Opening wording (`content.opening`), both sets of parents, the couple, closing wording | Section title; parents side by side from 480 px (groom's side first), stacked below that; title before each name; late parents in ink-700 with the family's chosen marker; couple names in the title style |
| Date | `wedding_date`, lunar line, countdown | Centred date block, countdown, calendar button |
| Program | `events` with their venues, grouped by day | Ceremony timeline |
| Venues | `venues` and the events at each | One venue card each; a single card when everything is in one place |
| Gallery | `media` kind gallery, video URL | Gallery grid, lightbox, video facade |
| Gift | `gift_accounts` and their KHQR `media` | Chong dai gift card |
| RSVP (Phase 2) | RSVP settings | Card with a primary button that opens the RSVP sheet |
| Wishes (Phase 2) | Approved `wishes` | Wish cards and a "ផ្ញើពរជ័យ · Send a wish" button |
| Footer (layout) | Closing line, initials, plan | Small seal, thank-you line, "Made with …" credit on Basic only |

Overlays on every invitation page: music seal, quick dock, language toggle, and the trial ribbon during the trial.

| Section title | Khmer | English |
| --- | --- | --- |
| Invitation | សំបុត្រអញ្ជើញ | Invitation |
| Date | កាលបរិច្ឆេទ | Date |
| Program | កម្មវិធីមង្គលការ | Program |
| Venues | ទីតាំង | Location |
| Gallery | កម្រងរូបភាព | Gallery |
| Gift | ចំណងដៃ | Wedding gift |
| RSVP | បញ្ជាក់ការចូលរួម | RSVP |
| Wishes | ពរជ័យ | Wishes |

## 11. Copy and formats

| Item | Khmer | English |
| --- | --- | --- |
| Guest line | `{title} {name} {suffix}`, e.g. លោក ចាន់ ថា និងភរិយា | Mr. and Mrs. Chan Tha |
| Generic greeting | ភ្ញៀវកិត្តិយស | Dear honoured guest |
| Date | ថ្ងៃសៅរ៍ ទី១២ ខែធ្នូ ឆ្នាំ២០២៦ | Saturday, 12 December 2026 |
| Time | ម៉ោង ៧:០០ ព្រឹក (ព្រឹក, រសៀល, ល្ងាច, យប់) | 7:00 AM |
| Lunar line | LunarDate output as returned, e.g. ថ្ងៃច័ន្ទ ៤ រោច ខែបឋមាសាឍ ឆ្នាំឆ្លូវ ត្រីស័ក ពុទ្ធសករាជ ២៥៦៥ | Shown in Khmer only |
| Money | ៛ for KHR | USD |

| Key | Khmer | English |
| --- | --- | --- |
| open_invitation | បើកសំបុត្រអញ្ជើញ | Open invitation |
| open_map | បើកផែនទី | Open map |
| add_to_calendar | បញ្ចូលក្នុងប្រតិទិន | Add to calendar |
| copy, copied | ចម្លង, បានចម្លង | Copy, Copied |
| save_qr | រក្សាទុក QR | Save QR |
| attending | ចូលរួម | Attending |
| not_attending | មិនអាចចូលរួម | Can't attend |
| send_wish | ផ្ញើពរជ័យ | Send a wish |
| play_music, pause | ចាក់ភ្លេង, ផ្អាក | Play music, Pause |
| next | បន្ទាប់ | Next |
| preview | មើលជាមុន | Preview |
| thank_you | សូមអរគុណ | Thank you |
| countdown | រាប់ថយក្រោយ | Countdown |

- Names never truncate; they wrap to a second line.
- English guest lines follow English conventions, so each title and suffix option stores its English form.
- Keep every string in `lang/km/invitation.php` and `lang/en/invitation.php`, and have a native speaker review them before launch.

## 12. Accessibility and performance

| Pair | Ratio | Allowed use |
| --- | --- | --- |
| ink-900 on ivory-100 | 16.9:1 | All text |
| ink-700 on ivory-100 | 8.7:1 | Secondary text |
| ivory-50 on crimson-700 | 10.4:1 | Labels on primary, envelope text |
| crimson-500 on ivory-100 | 6.0:1 | Text links |
| gold-800 on ivory-100 | 6.0:1 | Small gold text |
| gold-700 on ivory-100 | 4.5:1 | Icons, gold text 18 px and up, focus |
| gold-400 on crimson-700 | 5.1:1 | Gold text on the envelope |
| ink-900 on lotus-300 | 10.1:1 | Text on tags |
| gold-600 on white | 3.3:1 | Field borders (UI boundaries need 3:1) |
| gold-500 on ivory-100 | 2.3:1 | Decoration only |
| ink-500 on ivory-100 | 4.1:1 | Disabled text only |
| error on ivory-100 | 6.0:1 | Error text |

- Tap targets of at least 44 px; buttons are 48 px tall.
- Visible focus on every control; the dock and seals work from the keyboard.
- Audio never plays before the guest's tap, and the pause control is always visible.
- `prefers-reduced-motion` is honoured everywhere.
- Cover photo alt text is the couple's names; gallery alt text comes from captions.
- Sizes are in rem, so phone font settings scale the page.

| Budget (first view, compressed, gallery excluded) | Target |
| --- | --- |
| HTML and CSS | 60 KB or less |
| JavaScript (Alpine and theme) | 50 KB or less |
| Fonts | 300 KB or less |
| Cover image | 200 KB or less, WebP |
| Largest Contentful Paint, 4G mid-range Android | 2.5 s or less |
| Cumulative Layout Shift | Under 0.1 |

## 13. Theming contract

Child themes may override `--inv-*` variables, ornament partials and section partials. They must keep every pair in §12 at or above its ratio, keep the envelope contract (tap, open, music, open beacon), and keep the Khmer rules, reduced-motion handling and the palette option.

| Variable | Default | Meaning |
| --- | --- | --- |
| `--inv-bg` | ivory-100 | Page |
| `--inv-surface` | ivory-50 | Cards |
| `--inv-sunken` | ivory-200 | Sunken areas |
| `--inv-field` | white | Form fields, KHQR backing |
| `--inv-ink` | ink-900 | Text |
| `--inv-ink-muted` | ink-700 | Secondary text |
| `--inv-primary` | crimson-700 | Primary actions, envelope |
| `--inv-primary-hover` | crimson-900 | Hover and pressed |
| `--inv-primary-soft` | crimson-100 | Soft primary fill |
| `--inv-on-primary` | ivory-50 | Text on primary |
| `--inv-hairline` | gold-500 at 35% | Card borders |
| `--inv-accent` | gold-500 | Ornaments, dividers |
| `--inv-accent-ui` | gold-600 | Field borders |
| `--inv-accent-strong` | gold-700 | Icons, focus |
| `--inv-accent-text` | gold-800 | Small gold text |
| `--inv-highlight` | lotus-100 | Selected fills |

Base manifest, `resources/themes/base/theme.json`:

```json
{
  "slug": "base",
  "name": "Morodok Snaeha",
  "version": "1.0.0",
  "tier": "standard",
  "intro": "envelope",
  "sections": ["cover", "invitation", "date", "program", "venues", "gallery", "gift", "rsvp", "wishes"],
  "fonts": {
    "display": ["Playfair Display", "Moul"],
    "body": ["Plus Jakarta Sans", "Kantumruy Pro"]
  },
  "options": {
    "palette": { "type": "select", "choices": ["crimson", "jade", "sapphire"], "default": "crimson" },
    "show_countdown": { "type": "bool", "default": true },
    "petals": { "type": "bool", "default": false }
  }
}
```

Blade components live in `resources/themes/base/views/components` and register with `Blade::anonymousComponentPath(resource_path('themes/base/views/components'), 'base')`, so sections call `<x-base::card>` or `<x-base::seal-button>`. A child theme registers its own prefix and falls back to base.

## 14. Tailwind 4 tokens

`resources/themes/base/assets/app.css`:

```css
@import "tailwindcss";
@source "../views";

@theme {
  --color-crimson-900: #58111a;
  --color-crimson-700: #7b1122;
  --color-crimson-500: #a83540;
  --color-crimson-100: #ffdad9;
  --color-gold-800: #775a19;
  --color-gold-700: #8c6d23;
  --color-gold-600: #a88b4a;
  --color-gold-500: #c5a059;
  --color-gold-400: #d4af37;
  --color-gold-100: #f6eedb;
  --color-lotus-300: #e8b4b8;
  --color-lotus-100: #f8e6e7;
  --color-ivory-50: #fdfbf7;
  --color-ivory-100: #faf6ee;
  --color-ivory-200: #f1ede6;
  --color-ink-900: #1a1412;
  --color-ink-700: #574141;
  --color-ink-500: #8b7171;
  --color-error: #ba1a1a;
  --color-success: #2f6b3a;

  --font-display: "Playfair Display", "Moul", serif;
  --font-title: "Playfair Display", "Kantumruy Pro", serif;
  --font-body: "Plus Jakarta Sans", "Kantumruy Pro", system-ui, sans-serif;

  --radius-control: 0.25rem;
  --radius-card: 0.5rem;

  --shadow-float: 0 12px 32px -4px rgb(88 17 26 / 0.08), 0 4px 12px -2px rgb(140 109 35 / 0.06);
  --shadow-deboss: inset 0 1px 2px rgb(26 20 18 / 0.04);
  --shadow-focus: 0 0 0 3px rgb(212 175 55 / 0.35);

  --ease-standard: cubic-bezier(0.2, 0, 0, 1);
  --ease-ceremonial: cubic-bezier(0.65, 0, 0.35, 1);
}

/* Semantic roles: components use these; palettes and child themes override them */
:root {
  color-scheme: light;
  --inv-bg: var(--color-ivory-100);
  --inv-surface: var(--color-ivory-50);
  --inv-sunken: var(--color-ivory-200);
  --inv-field: #ffffff;
  --inv-ink: var(--color-ink-900);
  --inv-ink-muted: var(--color-ink-700);
  --inv-primary: var(--color-crimson-700);
  --inv-primary-hover: var(--color-crimson-900);
  --inv-primary-soft: var(--color-crimson-100);
  --inv-on-primary: var(--color-ivory-50);
  --inv-hairline: rgb(197 160 89 / 0.35);
  --inv-accent: var(--color-gold-500);
  --inv-accent-ui: var(--color-gold-600);
  --inv-accent-strong: var(--color-gold-700);
  --inv-accent-text: var(--color-gold-800);
  --inv-highlight: var(--color-lotus-100);
}

[data-palette="jade"] {
  --inv-primary: #1f5b4a;
  --inv-primary-hover: #143d32;
  --inv-primary-soft: #d7ebe3;
}

[data-palette="sapphire"] {
  --inv-primary: #23305e;
  --inv-primary-hover: #161f40;
  --inv-primary-soft: #dde3f5;
}

/* Semantic utilities: bg-canvas, bg-surface, text-ink, border-accent-ui, ... */
@theme inline {
  --color-canvas: var(--inv-bg);
  --color-surface: var(--inv-surface);
  --color-sunken: var(--inv-sunken);
  --color-field: var(--inv-field);
  --color-ink: var(--inv-ink);
  --color-ink-muted: var(--inv-ink-muted);
  --color-primary: var(--inv-primary);
  --color-primary-hover: var(--inv-primary-hover);
  --color-primary-soft: var(--inv-primary-soft);
  --color-on-primary: var(--inv-on-primary);
  --color-hairline: var(--inv-hairline);
  --color-accent: var(--inv-accent);
  --color-accent-ui: var(--inv-accent-ui);
  --color-accent-strong: var(--inv-accent-strong);
  --color-accent-text: var(--inv-accent-text);
  --color-highlight: var(--inv-highlight);
}

@layer base {
  html {
    background: var(--inv-bg);
    color: var(--inv-ink);
    font-family: var(--font-body);
  }

  :lang(km) {
    letter-spacing: 0 !important;
    text-transform: none !important;
    font-style: normal !important;
  }

  :lang(km) :is(p, li, dd, blockquote) {
    line-height: 1.8;
  }

  input, select, textarea {
    font-size: max(16px, 1rem);
  }
}

@media (prefers-reduced-motion: reduce) {
  *, *::before, *::after {
    animation-duration: 1ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 1ms !important;
    scroll-behavior: auto !important;
  }
}
```

## 15. Do and don't

Do:

- Write Khmer first and give it room: taller lines, no tracking.
- Keep one crimson moment per screen and let the ivory breathe.
- Test in the Telegram and Messenger in-app browsers on a mid-range Android phone.
- Reserve space for every image, so nothing jumps while the page loads.

Don't:

- Set text in gold-500, or small text in any gold lighter than gold-800.
- Track, uppercase or italicise Khmer.
- Use Moul for paragraphs or buttons.
- Play audio before the guest taps.
- Recolour, crop or tint a KHQR code.
- Use blur effects or autoplaying video.
- Add meal or dietary choices; they aren't part of a Khmer wedding RSVP.

## 16. Changes from the source draft

- Colour tokens now match the described palette. The draft's YAML held a generated tonal set whose primary, secondary and tertiary didn't match the crimson, gold and lotus in its own text.
- Added Khmer type (Moul for display, Kantumruy Pro for body and UI) with Khmer typesetting rules; body text rises from 15 to 16 px.
- Added an accessible gold scale. Gold-500 failed contrast for text and field borders, so it is now decoration only.
- Corrected cultural terms: the ceremonies are ហែជំនូន (*hae chomnoun*), កាត់សក់ (*kat sak*) and សំពះផ្ទឹម (*sampeah phtem*); background music is ភ្លេងការ (*phleng kar*, wedding music) rather than Pinpeat; the gift is ចំណងដៃ (*chong dai*) rather than Ang Pao; meal and dietary choices are gone.
- The invitation is a phone-first single column; the 12-column grid now serves only the marketing site.
- Added palette options, motion, ornaments, section blueprints, copy formats, the theming contract, Tailwind 4 tokens and a performance budget.
