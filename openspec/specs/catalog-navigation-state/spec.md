# Catalog Navigation State Specification

## Purpose

Preserve the catalog's active category filter and scroll position across a round trip through the product detail page, using optional URL params appended client-side at click time. Also governs the detail page's visual realignment (trust badges, description box, features list) without breaking `.actions-area` behavior.

## Requirements

### Requirement: Detail link carries origin filter state

The system MUST append `from_categoria` (the currently active category, if any) and `from_id` (the clicked product's id) to a "VER DETALLES" link's href at click time, via a delegated click listener on `#lista-productos`.

#### Scenario: Click with active category filter

- GIVEN the catalog is filtered to category "Sillas" via `.filtro-categoria`
- WHEN the user clicks "VER DETALLES" on a product card with id `42`
- THEN the resulting navigation URL includes `?from_categoria=sillas&from_id=42`

#### Scenario: Click with no active filter

- GIVEN the catalog filter is "Todos" (no category selected)
- WHEN the user clicks "VER DETALLES" on a product card
- THEN the resulting URL MUST NOT include a `from_categoria` param, or MUST include it empty, and MUST still include `from_id`

### Requirement: Detail page restores filter and scroll on return

The system MUST re-apply the origin category filter and scroll the originating product card into view when the catalog page loads with `from_categoria` and/or `from_id` present in `location.search`.

#### Scenario: Return via breadcrumb

- GIVEN the detail page was opened with `?from_categoria=sillas&from_id=42`
- WHEN the user clicks the "CATÁLOGO" breadcrumb link
- THEN the breadcrumb link forwards `from_categoria` and `from_id` to `/productos`
- AND on load, `/productos` calls `aplicarFiltro('sillas')` and syncs `.filtro-activo-label`
- AND the card matching `data-producto-id="42"` is scrolled into view

#### Scenario: Return via dedicated back button

- GIVEN the detail page was opened with `?from_categoria=sillas&from_id=42`
- WHEN the user clicks the "Volver al catálogo" button
- THEN the button forwards the same params and the resulting `/productos` load behaves identically to the breadcrumb scenario

#### Scenario: Scroll only after filter application

- GIVEN `from_id=42` refers to a card belonging to category "sillas"
- WHEN the filter `sillas` is applied
- THEN the card is confirmed visible (not `display:none`) before `scrollIntoView` runs

### Requirement: Missing or invalid params fall back to normal behavior

The system MUST treat `from_categoria` and `from_id` as fully optional and MUST NOT error or force a filtered state when they are absent, unmatched, or stale.

#### Scenario: Direct or shared detail link, no params

- GIVEN a user opens `producto/detalle/42` directly with no query params
- WHEN they click the breadcrumb or back button
- THEN they are sent to `/productos` with no filter forced, showing all products, no JavaScript error

#### Scenario: Entry from favorites (no filter context)

- GIVEN a user opens a product detail page from `mis_favoritos.php`, which carries no `from_categoria`
- WHEN they click the breadcrumb or back button
- THEN the catalog loads unfiltered, identical to today's behavior

#### Scenario: Unknown or removed category slug

- GIVEN `from_categoria=descontinuada` does not match any `.filtro-categoria[data-categoria]` value
- WHEN the catalog page loads
- THEN the system silently falls back to the unfiltered "Todos" state without throwing

#### Scenario: Unknown product id

- GIVEN `from_id` does not match any rendered card's `data-producto-id`
- WHEN the scroll-into-view step runs
- THEN it is skipped silently, with no error and no partial scroll

### Requirement: Visual realignment preserves actions-area behavior

The system MUST restyle `trust-badges` (bi-* icons replacing emojis), `description-box` (kicker + `divider-artisan`, no dashed border), and `features-list` (same fixed content, restyled) without altering `.actions-area` markup, CSRF handling, or any of its 6 state combinations (session logged in/out × cart enabled/disabled × stock > 0/= 0).

#### Scenario: Visual changes do not touch actions-area

- GIVEN the detail page redesign is applied
- WHEN any of the 6 `.actions-area` state combinations renders
- THEN the rendered markup, CSRF token placement, and submit behavior are unchanged from before the redesign

#### Scenario: No emoji or dashed border remains

- GIVEN `detalle_producto.php` and `detalle_producto.css` after the change
- WHEN inspected
- THEN no emoji character exists in trust badges and no `dashed` border rule exists in the description box styles

### Requirement: Cache-busting on touched assets

The system MUST bump the `?v=` query parameter on every CSS or JS file modified by this change.

#### Scenario: Asset version bump

- GIVEN `productos.js`, `detalle_producto.css`, or `detalle_producto.js` is modified
- WHEN the corresponding `<link>` or `<script>` tag is inspected
- THEN its `?v=` value is higher than the pre-change value

## Key Learnings

1. The active category filter exists only client-side, so origin-state params must be appended at click time via a delegated listener, not emitted server-side by `product_card.php`.
2. Scroll-into-view must be sequenced strictly after filter application to avoid targeting a `display:none` node.
3. Falling back silently on missing/unmatched params is a hard requirement because `product_card.php` is shared with `mis_favoritos.php`, which never carries filter context.
4. `.actions-area` is explicitly excluded from requirements scope but is included as a regression-guard scenario since it is the change's highest-likelihood risk.
