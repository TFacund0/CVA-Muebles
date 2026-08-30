# Exploration: Optimización de rendimiento y experiencia mobile (imágenes + tipografía)

## Current State

### Image pipeline — two parallel systems

1. **Cloudinary (current/future uploads)** — `app/Services/CloudinaryService.php::subir()` (lines 56–66) already calls `optimizarUrl()` on every upload, which inserts `q_auto,f_auto` into the `secure_url` (lines 76–83: `str_replace('/upload/', '/upload/q_auto,f_auto/', $url)`). **Any image the admin uploads today through `ProductoService.php` (lines 76, 108, 179) is already served compressed and format-negotiated (WebP/AVIF) — no code change needed for future uploads.** A retroactive CLI command `app/Commands/OptimizarImagenesCloudinary.php` (`php spark cva:optimizar-imagenes-cloudinary`) back-fills `q_auto,f_auto` onto product/gallery rows already stored with plain Cloudinary URLs.
2. **Static local assets (the problem area)** — `public/assets/img/content/hero/` and `public/assets/img/content/products/` are plain files served directly by the webserver, no controller in the path, no transformation applied.
3. **`app/Helpers/imagen_helper.php::imagen_url()`** decides at render time: absolute URL (`^https?://`) → returned as-is (Cloudinary); otherwise resolved as `assets/uploads/{subcarpeta}/{imagen}` (legacy local upload case).
4. Files named in the original request (`estante.jpg`, `silla.jpg`, `mesa.jpg`, `banco-carpintero.jpeg`) live in `public/assets/img/content/products/` but are **not referenced anywhere in `app/Views` or controllers** (only `mesa.jpg` appears as a literal string in a PHPUnit fixture, `tests/unit/CarritoServiceUnitTest.php:37`, not as a real file read). Appear orphaned — needs team confirmation before deleting.
5. Only 4 static images are actually wired into views: `hero/taller.jpg` (hero slide 1, above the fold), `hero/Muebles 22.jpeg` (hero slide 2 + specialties section), `hero/Muebles 69.jpeg` (hero slide 3 + specialties section), `products/Muebles 10.jpeg` (specialties section).

### `<img>` / `loading="lazy"` audit

- `app/Views/components/product_card.php:14` — already has `loading="lazy"` on every product-card image, uses `imagen_url()`, works for both Cloudinary and legacy paths. No change needed.
- `app/Views/front/home/section-carrusel.php` (hero, lines 21/38/51) — no `loading="lazy"`. Slide 1 (`taller.jpg`) is the LCP candidate and should stay eager (candidate for `fetchpriority="high"`); slides 2/3 stay in the DOM at load regardless of active slide (Bootstrap carousel) and are lazy-load candidates.
- `app/Views/front/home/section-catalogo.php` (lines 23, 35, 47 — "Nuestras Especialidades") — no `loading="lazy"`, all three below the hero, good lazy-load candidates.
- No other raw `<img>` tags reference `assets/img/content/*` in `app/Views` (grep confirmed).
- No manual Cloudinary URL construction found in `app/Views` (`res.cloudinary.com` / `q_auto` grep: zero matches) — all logic centralized in `CloudinaryService`.

### Typography — `display-1` to `display-4`, 19 files, 35 occurrences (grep-verified)

| File | Class(es) | Context |
|---|---|---|
| `front/home/section-carrusel.php` | `display-2` (×1), `display-3` (×2) | Hero slide headlines (H1/H2) |
| `front/home/section-catalogo.php` | `display-3` (×2) | Section headings |
| `front/pages/terminosYCondiciones.php` | `display-3` | Page H1 |
| `front/pages/informacionContacto.php` | `display-3`, `display-4` (×3) | Page H1 + section H2s |
| `front/pages/comercializacion.php` | `display-3`, `display-4` | Page H1 + section H2 |
| `front/pages/productos.php` | `display-4` | Catalog header H2 |
| `front/pages/beneficios.php` | `display-3` | Page H1 |
| `front/pages/galeria_clientes.php` | `display-2` | Page H1 |
| `front/pages/carrito.php` | `display-1` | Icon (`bi-cart-x`), not text |
| `front/pages/mis_favoritos.php` | `display-3` | Page H1 |
| `front/pages/quienesSomos.php` | `display-3`, `display-4` (×3) | Page H1 + section H2s |
| `back/products/alta_producto.php` | `display-3` | Icon |
| `back/products/crud_productos.php` | `display-1` (×3) | Icons (empty-state) |
| `back/users/crud_usuarios.php` | `display-4` (×3) | Icons (empty-state) |
| `back/sales/gestion_pedido_admin.php` | `display-4` | Icon |
| `back/messages/lista_consultas.php` | `display-4` (×2) | Icon |
| `back/sales/vistaCompras.php` | `display-1` | Icon |
| `back/sales/detalleVentas.php` | `display-4` (×2) | Icon |
| `back/sales/estadisticas.php` | `display-4` | Stat number (`fw-bold`), not a heading |

Two distinct problems inside one grep count: (a) real page **headings** (H1/H2, ~15 occurrences across front pages) that genuinely oversize on mobile, and (b) `display-*` used as an **icon-sizing utility** on Bootstrap Icons (~17 occurrences, mostly `back/` admin views) — not text, should not be shrunk the same way.

**Existing patch (`public/assets/css/pages/carrusel.css`)** already overrides `#heroCarousel .display-2`/`.display-3` at two breakpoints (`max-width: 991.98px` → 2.5rem/2.2rem, `max-width: 575.98px` → 2rem/1.8rem). Proves the breakpoint convention but doesn't touch the other 18 files.

**`public/assets/css/base/global.css`** defines only `:root` custom properties and base resets — **zero `@media` queries** — so a new responsive-typography rule would be the first `@media` block here.

**Bootstrap** vendored locally at `public/assets/vendor/bootstrap/`, v5.3.3 confirmed. Safe override path: later-loaded project CSS with equal/higher specificity (current pattern), not editing the vendor file.

**Project-wide mobile breakpoint convention**: `max-width: 991.98px` (Bootstrap `lg`) and `max-width: 575.98px` (Bootstrap `sm`) used consistently across ~19 CSS files — matches Bootstrap 5.3's defaults and what `carrusel.css` already uses.

## Affected Areas

- `app/Views/front/home/section-carrusel.php` — hero `<img>` tags, lazy-load candidates for slides 2–3 only
- `app/Views/front/home/section-catalogo.php` — 3 below-the-fold `<img>` tags missing `loading="lazy"`, plus 2 `display-3` headings
- `app/Services/CloudinaryService.php` — already optimal for future uploads; no code change needed
- `app/Commands/OptimizarImagenesCloudinary.php` — retroactive fixer already exists; irrelevant to local static files
- `public/assets/img/content/hero/taller.jpg`, `Muebles 22.jpeg`, `Muebles 69.jpeg`, `public/assets/img/content/products/Muebles 10.jpeg` — the only 4 static images reachable from views; real optimization targets
- `public/assets/img/content/products/estante.jpg`, `silla.jpg`, `mesa.jpg`, `banco-carpintero.jpeg` — appear unused; candidates for deletion (out of scope until confirmed)
- `public/assets/css/base/global.css` — best place for a centralized, breakpoint-consistent `display-*` heading override
- `public/assets/css/pages/carrusel.css` — existing partial patch; should be superseded/merged, not duplicated
- 19 view files listed above — targets for the centralized override (headings only) or left untouched (icon-only usages)

## Approaches

1. **Re-encode the 4 in-use static images (WebP + JPEG fallback)** — Pros: no runtime dependency, biggest win for the exact files measured (600KB+ → ~50-150KB typical). Cons: manual/one-off, needs repeating if a file is swapped later. Effort: Low.
2. **Migrate the 4 in-use static images to Cloudinary, reuse `optimizarUrl()`** — Pros: reuses proven pipeline, single source of truth. Cons: hero/catalog images are hardcoded `base_url()` paths, not DB-driven — needs a small config/constant layer. Effort: Medium.
3. **Lazy-loading pass** — add `loading="lazy"` to hero slides 2–3 and the 3 "Especialidades" images; keep `taller.jpg` eager (`fetchpriority="high"` optional). Pros: zero risk, purely additive, matches existing `product_card.php` pattern. Cons: defers bytes, doesn't reduce them — pair with 1 or 2. Effort: Low.
4. **Typography: scope-limited centralized override in `global.css`** — `@media (max-width: 991.98px)` / `@media (max-width: 575.98px)` targeting `h1.display-*, h2.display-*` (heading contexts only), reusing existing breakpoints; reconcile with `carrusel.css`. Pros: single source of truth, doesn't touch icon-only usages, no Bootstrap file edits. Cons: needs correct ordering/specificity vs. `carrusel.css`. Effort: Low–Medium.
5. **Typography: custom utility classes (`.h-hero`, `.h-section`) with `clamp()`, swapped into all 19 views** — Pros: most maintainable long-term, no hard breakpoint jumps. Cons: touches 19 files, diverges from the established two-breakpoint convention. Effort: Medium–High.

## Recommendation

Pair **Approach 1** (re-encode the 4 in-use static images) + **Approach 3** (lazy-load non-first-visible images) for the image front — smallest, lowest-risk change targeting exactly the files already measured, no scope creep into a Cloudinary migration. Confirm with the team whether the 4 orphaned files are truly unused before deleting (out of scope for this change if unconfirmed).

For typography, **Approach 4** (centralized override in `global.css`, scoped to heading tags, reusing `991.98px`/`575.98px`), then reconcile with `carrusel.css` so the hero isn't double-overridden.

**No code change is needed for future admin-uploaded images** — `CloudinaryService::subir()` already applies `q_auto,f_auto` automatically. Call this out explicitly in the proposal as "already solved" so it isn't re-implemented.

## Risks

- Deleting the 4 orphaned static images without confirmation could break something outside the grepped scope (only `app/Views` + `app/Controllers` were checked).
- `carrusel.css` already overrides `display-2`/`display-3` under `#heroCarousel`; a new `global.css` rule must be ordered/scoped correctly to avoid conflicting with it.
- Re-encoding to WebP without a fallback could affect browsers/crawlers without WebP support — CI4 has no format-negotiation middleware, so a `<picture>` element may be needed.
- No performance budget or Lighthouse/PageSpeed baseline exists in the repo — recommend capturing a before/after baseline in the proposal.

## Key Learnings

1. Cloudinary uploads already receive automatic `q_auto,f_auto` optimization via `CloudinaryService::optimizarUrl()`.
2. A retroactive CLI command `cva:optimizar-imagenes-cloudinary` already back-fills optimization onto existing database-stored Cloudinary URLs.
3. Only four static images under `public/assets/img/content/` are actually referenced by any view; the rest appear to be orphaned legacy files.
4. The project consistently uses Bootstrap 5.3.3's `991.98px` and `575.98px` breakpoints across nearly twenty CSS files.
5. `global.css` currently defines only CSS custom properties and contains zero `@media` rules, leaving room for a first centralized responsive block.
