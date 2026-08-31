# Design: Refinamiento Mobile del Home

## Technical Approach

Presentation-only change at the two existing project breakpoints (`991.98px`, `575.98px`). No PHP logic, no JS, no Swiper initialization change. Two guiding rules, both already established by the proposal: (a) never stack a competing `!important` against a Bootstrap utility — remove the utility from markup and own the value in project CSS; (b) keep every selector under its existing page scope (`#heroCarousel`, `#home-page`).

Note the real selector prefixes in `catalogo.css` are `#home-page .…` (specificity 1-0-1), not the bare class names. All snippets below use the exact prefixed form.

## Architecture Decisions

| # | Decision | Alternatives rejected | Rationale |
|---|---|---|---|
| 1 | Hero typography gets a `#heroCarousel`-scoped rule (ID+element+class, 1-0-2) instead of editing `global.css` | Edit the centralized rule; add `!important` | Wins over `h1.display-2` (0-1-1) by specificity alone; the rest of the site keeps the centralized sizes |
| 2 | Align `.section-ubicacion` text inset to the Bootstrap gutter by removing the `p-5` utility and owning padding in CSS | Override `p-5` with `!important`; change `container-fluid` to `container` | `p-5` is `!important`; changing the container would kill the map full-bleed |
| 3 | Shrink + relocate Swiper arrows via CSS only, using Swiper's own positioning vars/props | Hide arrows (`display:none`); JS re-mount with different navigation nodes | Keeps tap-to-navigate (proposal success criterion); no CDN library patching |
| 4 | Touch guard = `@media (hover: hover) and (pointer: fine)`, applied to both card selectors in one pass | `:hover` + `@media (pointer: coarse)` unset; `.no-touch` body class via JS | Establishes one project-wide convention; hybrid laptops with a mouse still satisfy both conditions |
| 5 | Remove `py-0` from markup, set `padding: 0 0 X` in `catalogo.css` | `padding-bottom: X !important` in project CSS | Avoids a specificity war with a Bootstrap `!important` utility |

## Verified facts (measured, not assumed)

- `--bs-gutter-x: 1.5rem` → `.container` lateral padding `0.75rem` = **12px**; row `-12px` / col `+12px` cancel, so the effective content inset of the other 3 home sections at mobile is **12px**.
- The `.section-ubicacion` text column is **not** at 20px: `catalogo.css:399-401` gives `padding: 40px 20px`, and the inner wrapper at `section-catalogo.php:137` carries `p-5` (`3rem !important`). Effective lateral inset today = **20 + 48 = 68px**. That is the real source of the inconsistency the QA saw.

## Change specification

### 1. Hero — `public/assets/css/pages/carrusel.css`

Inside the existing `@media (max-width: 991.98px)` block, replacing the "no duplicar" comment at lines 207-208:

```css
    /* Hero-scoped display sizes: ID+element+class (1-0-2) beats the
       centralized h1.display-2 (0-1-1) in global.css without !important.
       Intentional divergence: only the hero shrinks; the rest of the
       site keeps the global scale. */
    #heroCarousel h1.display-2, #heroCarousel h2.display-2 { font-size: 2.1rem; }
    #heroCarousel h1.display-3, #heroCarousel h2.display-3 { font-size: 1.9rem; }
```

Inside `@media (max-width: 575.98px)`, replace line 227 and add the typography:

```css
    #heroCarousel .carousel-inner { height: 55vh; min-height: 350px; }
    #heroCarousel h1.display-2, #heroCarousel h2.display-2 { font-size: 1.75rem; }
    #heroCarousel h1.display-3, #heroCarousel h2.display-3 { font-size: 1.55rem; }
```

`global.css` is **not** modified (the proposal's "Modified (conditional)" resolves to *not modified*).

### 2. Section padding consistency

**Markup** — `app/Views/front/home/section-catalogo.php:137`:

```diff
-                <div class="p-5 p-xl-5 w-100 mx-auto ubicacion-content">
+                <div class="w-100 mx-auto ubicacion-content">
```

`p-xl-5` is dropped too because it was a redundant duplicate of `p-5` at `≥1200px`.

**CSS** — `public/assets/css/pages/catalogo.css`, new base rule next to the `.section-ubicacion` block (~line 301), reproducing the previous desktop value exactly:

```css
/* Padding propio (antes venía de la utilidad Bootstrap .p-5, que es
   !important y no se podía ajustar por breakpoint). */
#home-page .section-ubicacion .ubicacion-content {
    padding: 3rem;
}
```

**CSS** — inside the existing `@media (max-width: 991.98px)` "Ubicación Responsivo" block, replacing lines 399-401:

```css
    #home-page .section-ubicacion .col-lg-5 {
        padding: 40px 0;
    }
    /* 12px = calc(--bs-gutter-x * .5), el mismo inset lateral efectivo
       que usan las otras 3 secciones del home vía .container. */
    #home-page .section-ubicacion .ubicacion-content {
        padding: 0 12px;
    }
```

The `container-fluid p-0` / `row g-0` full-bleed of the iframe column is untouched.

### 3. Swiper arrows — `catalogo.css`

New `@media (max-width: 575.98px)` block appended to section 3 of the file. Swiper's CDN CSS centers the buttons with `top: 50%` + a negative `margin-top`; overriding `top/bottom/margin-top` moves them into the `4rem` bottom padding already reserved by `.swiper-destacados`, i.e. fully outside `.product-img-container` and outside the card:

```css
@media (max-width: 575.98px) {
    #home-page .swiper-button-next,
    #home-page .swiper-button-prev {
        width: 38px !important;
        height: 38px !important;
        /* Sacarlas de encima de la foto: bajarlas al carril que ya
           reserva el padding-bottom: 4rem de .swiper-destacados. */
        top: auto !important;
        bottom: 0.35rem;
        margin-top: 0 !important;
        transform: none;
    }
    #home-page .swiper-button-prev { left: 12px !important; right: auto !important; }
    #home-page .swiper-button-next { right: 12px !important; left: auto !important; }
    #home-page .swiper-button-next:after,
    #home-page .swiper-button-prev:after { font-size: 0.95rem !important; }
}
```

38px ≥ the 36px tappable minimum. `!important` is required here only because the library's own rules carry equal-or-higher weight; this is the documented exception already used at `catalogo.css:198-208`.

### 4. Touch hover guard — convention

**Project convention (new, first use):** any `:hover` rule whose effect is a *persistent visual state* (`transform`, `box-shadow`, background swap on a card) must live inside `@media (hover: hover) and (pointer: fine)`. Both conditions are required: `hover: hover` alone still matches some hybrid/stylus devices, and `pointer: fine` alone matches a trackpad-less pen. Hybrid laptops with a mouse attached satisfy both and keep the effect.

Wrap the existing blocks at `catalogo.css:85-88` and `catalogo.css:136-140` — and, for coherence, their dependent `:hover` descendants:

```css
/* CONVENCIÓN DEL PROYECTO: todo :hover que deje un estado visual
   persistente va dentro de este guard. En touch, :hover se queda
   "pegado" tras el tap hasta que se toca otro elemento. */
@media (hover: hover) and (pointer: fine) {
    #home-page .catalogo-card-premium:hover {
        transform: translateY(-15px);
        box-shadow: 0 40px 80px rgba(62, 39, 35, 0.15);
    }
    #home-page .catalogo-card-premium:hover .card-img-artisan { transform: scale(1.1); }
    #home-page .catalogo-card-premium:hover .card-overlay-modern {
        background: linear-gradient(to top, var(--cva-brown) 0%, rgba(62, 39, 35, 0.6) 100%);
    }
    #home-page .catalogo-card-premium:hover .category-line { width: 100%; }

    #home-page .product-card-vivid:hover {
        transform: scale(1.05) translateY(-10px);
        box-shadow: 0 30px 60px rgba(62, 39, 35, 0.15);
        z-index: 10;
    }
    #home-page .product-card-vivid:hover .product-img-container img { transform: scale(1.1); }
}
```

The original ungrouped rules at lines 85-88, 97-99, 113-119, 136-140 and 157-159 are removed and replaced by this single guarded block, keeping their source order.

### 5. Map / footer separation

**Markup** — `app/Views/front/home/section-catalogo.php:133`:

```diff
-<section class="section-ubicacion py-0">
+<section class="section-ubicacion">
```

**CSS** — `catalogo.css:296-301`, replace `padding: 100px 0;`:

```css
#home-page .section-ubicacion {
    background-color: #faf7f2;
    /* padding-top: 0 lo aportaba .py-0 (Bootstrap, !important). Se quitó
       la utilidad del HTML y se declara acá para no pelear especificidad.
       El padding-bottom separa el iframe del footer, que no tiene margin-top. */
    padding: 0 0 100px;
    width: 100%;
    border-top: 1px solid rgba(0,0,0,0.03);
}
```

Plus, in the `@media (max-width: 991.98px)` "Ubicación Responsivo" block:

```css
    #home-page .section-ubicacion { padding-bottom: 60px; }
```

60px clears the ≥40px success criterion; top spacing stays at 0, exactly as `py-0` produced it.

## File Changes

| File | Action | Description |
|------|--------|-------------|
| `public/assets/css/pages/carrusel.css` | Modify | Item 1: `min-height`/`vh` + hero-scoped `display-2/3` |
| `public/assets/css/pages/catalogo.css` | Modify | Items 2, 3, 4, 5 |
| `app/Views/front/home/section-catalogo.php` | Modify | Line 133 (`py-0`), line 137 (`p-5 p-xl-5`) |
| `public/assets/css/base/global.css` | Untouched | Decision 1 keeps typography centralized for the rest of the site |

## Data Flow

    section-catalogo.php  ──(classes)──→  bootstrap.css (utilities, !important)
             │                                     │
             └──(#home-page scope)──→ catalogo.css ─┘  ← project CSS wins by
                                                        removing the utility,
                                                        not by out-!important-ing it

## Ordering and dependencies

The 5 items are **independent in effect** — no rule of one is a precondition of another — but two of them touch the same PHP file, so apply in this order to keep a single markup pass:

1. **Item 5** (remove `py-0` + `padding: 0 0 100px`) — must be atomic: removing the class without the CSS reintroduces a 100px top gap.
2. **Item 2** (remove `p-5 p-xl-5` + `.ubicacion-content` base 3rem + mobile 12px) — must also be atomic: removing the class without the base rule collapses desktop padding to 0.
3. **Item 1** (hero, `carrusel.css` only).
4. **Item 3** (Swiper arrows, additive block).
5. **Item 4** (hover guard, pure refactor of existing blocks; last so a visual diff isolates it).

Items 1, 3 and 4 are individually revertible. Items 2 and 5 are revertible as CSS+markup pairs.

## Testing Strategy

| Layer | What to Test | Approach |
|-------|-------------|----------|
| Visual / manual | All 5 items at 320px, 375px, 414px, 768px | Real device + DevTools device toolbar |
| Touch behavior | Item 4: tap a card, tap elsewhere, confirm no stuck `scale(1.05)` | Real phone (emulation does not reproduce sticky hover reliably) |
| Regression | Desktop ≥1200px byte-equivalent rendering; navbar untouched | Side-by-side screenshot vs `main` |
| Measurement | Item 2 lateral inset == 12px; item 5 bottom gap ≥ 40px | DevTools box model on `.ubicacion-content` and `.section-ubicacion` |

## Threat Matrix

N/A — no routing, shell, subprocess, VCS/PR automation, executable-file classification, or process-integration boundary. CSS and one view file only.

## Migration / Rollout

No migration required. Hard-refresh / cache-bust of the three static assets is the only deployment consideration.

## Open Questions

- [ ] Item 3: confirm on a real device that `bottom: 0.35rem` does not collide with `.swiper-pagination mt-4`; if it does, move the pagination up or inset the arrows to `left/right: 4px`.
- [ ] Item 1: `55vh` on very short landscape phones may still crop the caption — verify at 375×667 landscape.

## Key Learnings

1. The `.section-ubicacion` text column is inset 68px, not 20px, because an inner `p-5` utility adds 48px on top of the 20px column padding.
2. Bootstrap's effective mobile content inset is 12px, since `--bs-gutter-x: 1.5rem` halves to 0.75rem of container padding.
3. Hero typography can beat the centralized `global.css` rule using `#heroCarousel h1.display-2` without any `!important`.
4. Swiper arrows can be relocated out of the image area purely with `top: auto` plus `bottom`, reusing the 4rem padding already reserved.
5. Removing a Bootstrap utility from markup is cheaper than fighting its `!important`, and it applies to both `py-0` and `p-5` here.
