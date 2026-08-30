# Design: Mobile performance and readability for static assets

## Technical Approach

Three independent, additive edits: (1) a single element-qualified `@media` block in `public/assets/css/base/global.css`, (2) pre-committed WebP siblings consumed through `<picture>` in the two home partials, (3) `loading`/`fetchpriority` hints following `app/Views/components/product_card.php:14`. No build step, no PHP/service code, no DB. `carrusel.css` hero overrides are deleted only after the global rule is visually confirmed.

## Architecture Decisions

### Decision: Element-qualified selectors, not `.display-*` alone

**Choice**: `h1.display-1 … h2.display-4` (8 selectors per breakpoint).
**Alternatives**: bare `.display-*` (breaks ~17 `<i class="bi bi-* display-*">` admin icons); `:not(.bi)` (fragile, still hits `back/sales/estadisticas.php` `display-4` inside a non-heading); per-view `clamp()` utilities (out of scope, 19 files).
**Rationale**: the icons and the stats number are never `h1`/`h2`, so element qualification is a structural guarantee rather than an exclusion list. Specificity (0,1,1) beats Bootstrap's (0,1,0) without `!important`.

```css
/* global.css — first @media block in the file, appended at EOF */
@media (max-width: 991.98px) {
  h1.display-1, h2.display-1 { font-size: 3rem; }
  h1.display-2, h2.display-2 { font-size: 2.5rem; }
  h1.display-3, h2.display-3 { font-size: 2.2rem; }
  h1.display-4, h2.display-4 { font-size: 2rem; }
}
@media (max-width: 575.98px) {
  h1.display-1, h2.display-1 { font-size: 2.4rem; }
  h1.display-2, h2.display-2 { font-size: 2rem; }
  h1.display-3, h2.display-3 { font-size: 1.8rem; }
  h1.display-4, h2.display-4 { font-size: 1.6rem; }
}
```

`display-2`/`display-3` values are copied verbatim from `carrusel.css:207-208,221-222`, so the hero is pixel-identical after the patch is removed. `display-1`/`display-4` extrapolate the same ratio.

### Decision: `src` on `<source>`, no `srcset` descriptors, no spaces in WebP filenames

**Choice**: `<source type="image/webp" srcset="<single url>">` with WebP files renamed to hyphenated lowercase (`muebles-22.webp`, `muebles-69.webp`, `muebles-10.webp`, `taller.webp`). Originals keep their current names untouched.
**Alternatives**: keeping `Muebles 22.webp`; multi-density `srcset` with `1x/2x`.
**Rationale**: `srcset` parses whitespace as the URL/descriptor separator, so a filename containing a literal space silently breaks the candidate. Renaming is safer and more readable than `%20` escaping. Only one source resolution exists, so density descriptors would be fabricated.

### Decision: `<picture>` must be made a block box in the hero

**Choice**: add `#heroCarousel .carousel-item > picture { display: block; width: 100%; height: 100%; }` to `carrusel.css` in the same pass that removes the display overrides.
**Rationale**: `<picture>` defaults to `display: inline`; the hero `<img>` carries `h-100 w-100 object-fit-cover`, and `height:100%` would resolve against an unsized inline parent, collapsing the slide. Catalog images (`card-img-artisan`) are width-driven and need no wrapper rule.

### Decision: WebP generation via Python + Pillow

**Choice**: one-off local script using Pillow (`img.save(dst, "WEBP", quality=80, method=6)`), run from Git Bash/PowerShell, not committed as a build task.
**Alternatives**: `cwebp` (not guaranteed installed on this Windows box, extra binary dependency); ImageMagick (same); Docker build step (rejected in the proposal).
**Rationale**: Pillow wheels bundle libwebp, and Pillow was already used in this project to generate favicons, so the toolchain is proven here. Output is verified by byte-size delta and side-by-side visual check before commit.

## Data Flow

    Pillow script ──→ *.webp committed ──→ <picture><source webp>
                                                    │
                          browser supports webp? ───┼── yes → webp bytes
                                                    └── no  → <img src=jpg>

    global.css @media ──→ h1/h2.display-* only ──→ 15 heading instances
                       ╳ bi icons, ╳ stats display-4

## File Changes

| File | Action | Description |
|------|--------|-------------|
| `public/assets/img/content/hero/taller.webp` | Create | LCP hero, eager |
| `public/assets/img/content/hero/muebles-22.webp` | Create | Slide 2 + catalog card 1 |
| `public/assets/img/content/hero/muebles-69.webp` | Create | Slide 3 + catalog card 2 |
| `public/assets/img/content/products/muebles-10.webp` | Create | Catalog card 3 |
| `public/assets/css/base/global.css` | Modify | Append two `@media` blocks |
| `public/assets/css/pages/carrusel.css` | Modify | Remove 4 `#heroCarousel .display-*` lines; add `> picture` block rule |
| `app/Views/front/home/section-carrusel.php` | Modify | 3 `<picture>`; slide 1 `fetchpriority="high"`, slides 2–3 `loading="lazy"` |
| `app/Views/front/home/section-catalogo.php` | Modify | 3 `<picture>` + `loading="lazy"` |

## Interfaces / Contracts

```php
<picture>
  <source type="image/webp" srcset="<?= base_url('assets/img/content/hero/muebles-22.webp') ?>">
  <img src="<?= base_url('assets/img/content/hero/Muebles 22.jpeg') ?>"
       class="d-block w-100 h-100 object-fit-cover zoom-animation"
       alt="Calidad" loading="lazy">
</picture>
```

Contract: `alt`, `class` and `src` stay on `<img>` unchanged; only `<picture>`/`<source>` and the loading attribute are added.

## Testing Strategy

| Layer | What to Test | Approach |
|-------|-------------|----------|
| Unit | None applicable | No PHP logic changes |
| Integration | `vendor/bin/phpunit` stays green | CI |
| Manual/E2E | Hero at 991.98/575.98px; admin empty-state icons; stats `display-4`; WebP off (DevTools disable image formats) | Browser check at 375/390px |
| Metric | Combined KB of the 4 images before/after (~1.4MB → ≤400KB) | `ls -l` / DevTools Network |

## Threat Matrix

N/A — no routing, shell, subprocess, VCS/PR automation, executable-file classification, or process-integration boundary. The Pillow script is a one-off local authoring tool, not shipped or invoked by the app.

## Migration / Rollout

No migration. Order of application, lowest risk first:

1. Append the `global.css` `@media` blocks (isolated, revertible, affects no images).
2. Visually verify hero + the 4 public pages at both breakpoints and admin icons.
3. Only then delete the `#heroCarousel .display-2/.display-3` overrides and re-verify — deleting before verifying would leave the hero unstyled if step 1 is wrong.
4. Generate and commit the 4 WebP files.
5. Swap the 6 `<img>` to `<picture>` and add `loading`/`fetchpriority` + the `> picture` block rule together, since the markup change depends on that CSS.

CSS-first isolates typography regressions from image regressions; each phase is a separate revertible commit.

## Open Questions (resolved, user-confirmed)

- [x] `fetchpriority="high"` on slide 1: confirmed, add it.
- [x] WebP rename convention (`muebles-22.webp`, hyphenated, lowercase): confirmed. Original JPEG/JPEG-2 filenames stay untouched.
