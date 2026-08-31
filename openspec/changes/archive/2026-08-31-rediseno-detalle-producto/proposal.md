# Proposal: Rediseño de la Página de Detalle de Producto

## Intent

Two confirmed problems on `producto/detalle/{id}`:

1. **Lost catalog context.** The catalog category filter is 100% client-side (`productos.js` toggles `display` on `#lista-productos > div`). Entering a detail page and returning via the breadcrumb (`base_url('productos')`, always clean) resets the filter to "Todos" and the scroll to top. Users browsing one category must re-filter and re-scroll after every product they inspect.
2. **Visual language divergence.** The detail page uses raw emojis (🚚🛡️🌿) in trust badges, a `dashed`-border description box used nowhere else, and hardcoded feature bullets. The rest of the site (e.g. `informacionContacto.php`) uses a gold uppercase kicker + `font-lora` title + `.divider-artisan` + `.icon-wrapper` with Bootstrap Icons (`bi-*`).

## Scope

### In Scope — Return-to-catalog state

- **Round-trip contract**: `?from_categoria=<slug>&from_id=<id>` on the detail URL. Both params optional; absence = today's behavior.
- **Origin (`productos.js`)**: extract the filter body from the click handler into a reusable `aplicarFiltro(categoria)`; add a delegated click listener on `#lista-productos` "VER DETALLES" links that appends the currently active category + the card's product id to the href at click time. **Decision correction:** PHP cannot emit `from_categoria` in `product_card.php`, because the active category only exists in the browser — `product_card.php` is shared with `mis_favoritos.php` and would need a filter value the server never has. The param is therefore appended client-side; `product_card.php` only needs a stable `data-producto-id` hook if one is missing.
- **Return (`productos.js` on load)**: read `from_categoria` from `location.search`, match it to the `.filtro-categoria` button (`data-categoria`, case-insensitive), call `aplicarFiltro`, sync `.filtro-activo-label`; then `scrollIntoView` the card matching `from_id`. Anchor by product id, not pixel offset.
- **Return (`detalle_producto.php`)**: breadcrumb "CATÁLOGO" link forwards the same params when present; plus a dedicated `← Volver al catálogo` action so the affordance is explicit on mobile.
- Unknown/removed category or id → silently fall back to unfiltered catalog, no JS error.

### In Scope — Visual realignment

- **`trust-badges`**: emojis → `.icon-wrapper` + `bi-truck` / `bi-shield-check` / `bi-tree`; section gets the kicker + `divider-artisan` treatment.
- **`description-box`**: drop the one-off `dashed` border; adopt the site's card/kicker+`font-lora` heading pattern.
- **`features-list`**: restyle to the site's icon+label rhythm. Content stays hardcoded (see open decision).
- CSS lands in `detalle_producto.css`; bump `?v=5.2 → 5.3` and add the new JS/bump `detalle-producto.js` if touched.

### Out of Scope

- Cart logic, `csrf_field()` placement, `.actions-area` markup/behavior.
- `ProductoController`, models, queries, `imagen_url()`.
- Making the catalog filter server-side or paginated.
- Re-tuning font sizes / `.main-artisan-card` shadow (already done).
- Persisting scroll across full page reloads beyond the `from_id` anchor.

## Capabilities

### New Capabilities

- `catalog-navigation-state`: preserving and restoring the catalog's active category filter and scroll anchor across a detail-page round trip.

### Modified Capabilities

None.

## Approach

Client-side only, additive, fully backward compatible. The filter mechanism stays as-is; we make it *addressable* (readable from the URL) instead of replacing it. Visual work is CSS + markup swaps inside three isolated sections, reusing existing global classes rather than inventing new ones.

## Affected Areas

| Area | Impact | Description |
|------|--------|-------------|
| `public/assets/js/pages/productos.js` | Modified | Extract `aplicarFiltro`, append params on click, restore on load |
| `app/Views/components/product_card.php` | Modified (minimal) | `data-producto-id` hook only if absent |
| `app/Views/front/pages/detalle_producto.php` | Modified | Breadcrumb param forwarding, back button, trust-badges/description-box/features-list markup |
| `public/assets/css/pages/detalle_producto.css` | Modified | New section styling; `?v=` bump |
| `app/Views/front/pages/productos.php` | Read-only | Verify `data-categorias` values match `data-categoria` buttons |

## Risks

| Risk | Likelihood | Mitigation |
|------|------------|------------|
| One of the 6 `.actions-area` state combinations (session × cart-enabled × stock) breaks | Med | `.actions-area` is explicitly out of scope; verify all 6 render paths before merge |
| Stale CSS/JS cache hides the redesign on real devices | High | Mandatory `?v=` bump on every touched asset (known project failure mode) |
| Entering the detail page without params (Home, favorites, direct link) regresses | Med | Params strictly optional; guard every read with a null check |
| Category slug mismatch between `data-categorias` and `data-categoria` | Med | Normalize with the same `toLowerCase()` comparison already used |
| `from_id` card hidden by the reapplied filter → `scrollIntoView` on a `display:none` node | Low | Scroll only after the filter is applied, and only if the node is visible |
| `product_card.php` change leaks into `mis_favoritos.php` | Low | Add a data attribute only; the listener is scoped to `#lista-productos` |

## Rollback Plan

Single-commit `git revert`. No DB, schema, session, or route changes. The URL params are cosmetic: reverting leaves any bookmarked `?from_categoria=` link working as a plain catalog/detail URL. If only the visual half is wrong, the two halves live in disjoint files (`productos.js` vs. the detail view/CSS) and can be reverted independently — remembering to re-bump `?v=` after any revert.

## Dependencies

- Bootstrap Icons already loaded globally (used by `product_card.php`, confirmed).
- Real-device verification with a warm cache to prove the `?v=` bump took effect.

## Success Criteria

- [ ] Filtering by a category, opening a product, and pressing "Volver al catálogo" restores that same category filter.
- [ ] After returning, the previously opened product's card is in view without manual scrolling.
- [ ] `/productos` with no params behaves exactly as today.
- [ ] No emoji remains in `detalle_producto.php`; trust badges use `bi-*` inside `.icon-wrapper`.
- [ ] No `dashed` border remains in `detalle_producto.css`.
- [ ] All 6 `.actions-area` combinations render and submit unchanged, CSRF intact.
- [ ] Every touched asset's `?v=` was bumped.

## Proposal question round

One product decision is unresolved and should be confirmed before `sdd-spec`:

1. **`features-list` content** — the two feature bullets are hardcoded in the view and identical for every product. Options: (a) leave hardcoded, restyle only (assumed default, smallest slice); (b) make them dynamic from product data, which requires touching `ProductoController`/the model and contradicts the current out-of-scope boundary. **Assumed: (a).**
2. **Back affordance** — replace the breadcrumb "CATÁLOGO" link with the dedicated back button, or keep both? **Assumed: keep both**, breadcrumb for orientation, button for the action.
3. **Deep-link shareability** — should `?from_categoria` also be reflected on `/productos` itself (so a filtered catalog is shareable), or stay purely a return mechanism? **Assumed: return mechanism only**, no history rewriting on filter click.
