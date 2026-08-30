# Proposal: Mobile performance and readability for static assets

## Intent

The public home page ships 4 uncompressed static images (220KB–627KB each, ~1.4MB total) with no `loading` hints, and Bootstrap `display-*` headings render oversized on 375–390px viewports. Admin-uploaded images are already handled (`CloudinaryService::subir()` applies `q_auto,f_auto`), so the remaining mobile cost is entirely in hand-authored static assets and CSS.

## Scope

### In Scope
- Re-encode the 4 in-use static images to WebP with original-format fallback via `<picture>`: `hero/taller.jpg`, `hero/Muebles 22.jpeg`, `hero/Muebles 69.jpeg`, `products/Muebles 10.jpeg`.
- Add `loading="lazy"` to images outside the initial viewport: hero slides 2–3 and the 3 "Especialidades" images. Slide 1 (`taller.jpg`) stays eager as the LCP element (optional `fetchpriority="high"`).
- Add one centralized `@media` block in `public/assets/css/base/global.css` scoping `display-1`–`display-4` reductions to heading elements only (`h1.display-*`, `h2.display-*`), reusing the existing `991.98px` / `575.98px` breakpoints.
- Reconcile the pre-existing `#heroCarousel .display-2/.display-3` overrides in `public/assets/css/pages/carrusel.css` with the new rule so the hero is not double-overridden.

### Out of Scope
- Deleting the 4 apparently orphaned images (`estante.jpg`, `silla.jpg`, `mesa.jpg`, `banco-carpintero.jpeg`) — requires explicit user confirmation.
- The ~17 `display-*` usages that size Bootstrap Icons in admin empty states — must remain visually unchanged.
- Any change to `CloudinaryService` or `cva:optimizar-imagenes-cloudinary` — already solved.
- Migrating static hero/catalog images into Cloudinary.
- Introducing `clamp()` utility classes across all 19 views.

## Capabilities

### New Capabilities
- `mobile-asset-delivery`: how public static images are encoded, served with fallback, and loading-prioritized.
- `responsive-typography`: how display-class headings scale at project breakpoints without affecting icon sizing.

### Modified Capabilities
- None (no existing specs in `openspec/specs/`).

## Approach

Exploration Approaches 1 + 3 + 4. One-off `cwebp` re-encode of exactly 4 files committed alongside originals; views switch to `<picture><source type="image/webp">` + `<img>` fallback preserving current `alt`/classes. Lazy attributes follow the existing `app/Views/components/product_card.php:14` pattern. Typography uses a single element-qualified media block in `global.css`, with `carrusel.css` reduced to whatever the hero still needs beyond the global rule (or removed if fully subsumed).

## Affected Areas

| Area | Impact | Description |
|------|--------|-------------|
| `public/assets/img/content/hero/*`, `products/Muebles 10.jpeg` | Modified/New | WebP siblings added, originals kept as fallback |
| `app/Views/front/home/section-carrusel.php` | Modified | `<picture>` + lazy on slides 2–3, eager slide 1 |
| `app/Views/front/home/section-catalogo.php` | Modified | `<picture>` + `loading="lazy"` on 3 images |
| `public/assets/css/base/global.css` | Modified | First `@media` block, heading-scoped display sizing |
| `public/assets/css/pages/carrusel.css` | Modified | Hero overrides reconciled/deduplicated |

## Risks

| Risk | Likelihood | Mitigation |
|------|------------|------------|
| Browser/crawler without WebP support | Low | `<picture>` with original JPEG/JPEG-2 `<img>` fallback; never remove originals |
| CSS specificity conflict with `carrusel.css` | Med | Same-breakpoint reconciliation in one pass; verify hero visually at both breakpoints |
| Global rule shrinking admin icon `display-*` | Med | Rule qualified by `h1`/`h2` element selectors only; verify admin empty states |
| LCP regression from lazy-loading slide 1 | Low | Slide 1 explicitly excluded from lazy pass |
| Visible quality loss after re-encode | Low | Target WebP q≈80; side-by-side visual check before commit |

## Rollback Plan

Pure asset/view/CSS change, no DB migration and no checkout-flow code. Revert the commit: WebP files are additive (originals untouched), `loading` attributes are additive, and the CSS change is a single removable `@media` block plus the `carrusel.css` restore. No data or config state to unwind.

## Dependencies

- A local WebP encoder (`cwebp` or equivalent) for the one-off re-encode.
- Explicit user confirmation before any follow-up change touching the 4 orphaned images.

## Decisions (user-confirmed)

- Success metric: combined KB weight of the 4 images, before/after. No Lighthouse baseline required for this change.
- WebP files: pre-compressed once and committed to the repo alongside the JPEG originals. No build-time generation step.
- `back/sales/estadisticas.php` `display-4` stat numbers: left untouched, treated as data display, not a heading.
- `carrusel.css` hero overrides: removed once the centralized `global.css` rule is confirmed sufficient for the hero at both breakpoints.

## Success Criteria

- [ ] Combined transfer weight of the 4 in-use images drops from ~1.4MB to ≤400KB (target ≥70% reduction), measured before/after.
- [ ] Every image outside the initial viewport carries `loading="lazy"`; `taller.jpg` remains eager.
- [ ] All 4 images render correctly in a WebP-less fallback path (fallback `<img>` reachable).
- [ ] Headings look proportioned at 375px and 390px viewport widths on home, `quienesSomos`, `informacionContacto`, `comercializacion`.
- [ ] Admin empty-state icons (`crud_productos`, `crud_usuarios`, `vistaCompras`) render at unchanged size.
- [ ] Hero headline sizes are governed by exactly one rule set; no duplicate/conflicting override remains.
- [ ] `vendor/bin/phpunit` stays green in CI.
