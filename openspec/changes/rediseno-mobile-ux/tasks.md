# Tasks: rediseno-mobile-ux

Delivery strategy: ask-on-risk. Review budget: 400 lines.

Ordering note: Phase 1 must run first and is atomic (markup + CSS in one commit). Phases 2-5 are mutually independent — each may be implemented, committed, and reviewed in any order or in parallel, but each is its own separate, revertible commit. Recommended sequence 1 → 2 → 3 → 4 → 5 only to keep review scope small per commit.

---

## Phase 1 — Navbar (sequential, atomic, blocking for nothing else)

### Task 1.1 — Add semantic cell classes + remove `d-none d-lg-block` in navbar.php
- File: `app/Views/partials/navbar.php`
- Add `nav-cell-actions` to cell 1 (~line 28, today `d-lg-none d-flex align-items-center gap-2 navbar-flex-cell`)
- Add `nav-cell-brand` to cell 2 (~line 46)
- Add `nav-cell-auth` to cell 3 (~line 66)
- Remove `d-none d-lg-block` from `<h1 class="titulo-logo">` (~line 49), leaving only `titulo-logo`
- Satisfies: spec `mobile-navbar-layout` — Requirement "Mobile cell order", Requirement "Brand text visibility"
- Done when: the 4 attribute edits are present in the diff and no other markup line changed.
- MUST be committed together with Task 1.2 (same commit) — never split markup and CSS.

### Task 1.2 — Add mobile navbar `@media` block to main-layout.css
- File: `public/assets/css/layout/main-layout.css`
- Append the two `@media` blocks (991.98px, 575.98px) exactly as specified in design.md Decision 1: `.nav-cell-brand` order 1 flex 1 1 auto min-width 0, `.nav-cell-actions` order 2 flex 0 0 auto, `.nav-cell-auth` order 3 flex 0 0 auto, `.navbar-brand` flex/min-width/overflow chain, `.logo-img-nav` flex 0 0 auto width 40px (36px at 575.98px), `.titulo-logo` block/ellipsis/font-size 1.15rem (1rem at 575.98px), `.boton-icon-circle` 42px (40px at 575.98px)
- Satisfies: spec `mobile-navbar-layout` — Requirement "Truncation without overflow"
- Done when: both `@media` blocks exist verbatim as in design.md, appended at the end of the file, and no rule outside these blocks is modified.
- MUST be committed together with Task 1.1 (same commit).

### Task 1.3 — QA gate: manual visual validation of navbar (HUMAN GATE, NOT auto-completable)
- Not a code task. Requires a human to open the running app and visually inspect.
- Validate at 320 / 375 / 390 / 768 / 992 / 1440px.
- Validate logged in and logged out.
- Validate with `$cartCount > 0` (cart badge may overflow the 40px icon circle — this is the documented open risk in design.md).
- If the badge overflows: add `padding-right` to `.nav-cell-actions`. Do NOT shrink the icon further.
- Satisfies: spec `mobile-navbar-layout` — Requirement "Truncation without overflow" (overlap check), Requirement "Offcanvas unaffected" (confirm hamburger still opens the existing offcanvas unmodified)
- Done when: a human confirms, at every listed width/state, no overlap between brand text and cart/hamburger icons, and reports the outcome (pass, or the badge-overflow fix was applied and re-verified).
- Blocks: nothing downstream (phases 2-5 do not depend on phase 1), but Phase 1 itself cannot be marked complete without this gate.

---

## Phase 2 — Global button responsiveness (independent, parallelizable)

### Task 2.1 — Add two-tier mobile `@media` rules to global.css
- File: `public/assets/css/base/global.css`
- Append at the end: `@media (max-width: 991.98px)` and `@media (max-width: 575.98px)` blocks for `.btn-artisan`, `.btn-vivid`, `.btn-premium-action`, `.btn-gold`, exactly as specified in design.md Decision 2
- `.btn-vivid` and `.btn-gold` MUST keep `!important` on `padding` (their base declaration already uses it — omitting `!important` here means the rule silently loses)
- `.btn-artisan` and `.btn-premium-action` MUST NOT use `!important` (their base has none)
- Satisfies: spec `global-button-responsiveness` — Requirement "Two-tier mobile scaling", Requirement "Predictable specificity vs. page overrides"
- Done when: both blocks exist for all 4 selectors, 575.98px values are strictly smaller than 991.98px values for padding and font-size, and `!important` usage matches the base rule per selector.

### Task 2.2 — QA: verify page-level overrides still resolve deterministically
- Visually check `contacto.css` (line ~156, `.btn-vivid-premium` ID-scoped override) and `comercializacion.css` (line ~202, `.btn-vivid` ID-scoped override) at mobile widths.
- Expected: no regression. `comercializacion.css:202`'s padding was already dead code (no `!important` against an `!important` base) and will now visibly take the new mobile padding — this is expected, not a regression.
- Satisfies: spec `global-button-responsiveness` — Requirement "Predictable specificity vs. page overrides"
- Done when: a human confirms buttons on contacto and comercialización pages render correctly at 375px/991px and the comercializacion.css:202 behavior change is accepted as intentional.

---

## Phase 3 — Product card small viewport (independent, parallelizable)

### Task 3.1 — Add 575.98px tier to productos.css
- File: `public/assets/css/pages/productos.css` (or wherever productos.css lives in the repo)
- Append a new `@media (max-width: 575.98px)` block after the existing 991.98px block (lines ~274-347), reusing the `#productos` prefix, exactly as specified in design.md Decision 3 (header, img-wrapper, card-body, card-title, p.small, precio-tag, filtro-categoria, buttons, card-footer)
- Satisfies: spec `product-card-small-viewport` — Requirement "575.98px tier added"
- Done when: the block exists with all listed selectors, and every value (image height, card-body padding, title font-size, price font-size, button padding/font-size) is strictly smaller than its 991.98px counterpart — verifiable by direct comparison.

---

## Phase 4 — Icon/badge mobile scaling (independent, parallelizable)

### Task 4.1 — Add badge-logistica rule inside existing comercializacion.css @media block
- File: `public/assets/css/pages/comercializacion.css`
- Insert `#container-comercializacion-wrapper .badge-logistica { width: 40px; height: 40px; font-size: 0.9rem; }` inside the already-existing `@media (max-width: 991.98px)` block (do not create a new block)
- Satisfies: spec `mobile-icon-scaling` — Requirement "Badge/icon mobile reduction"
- Done when: the rule is present inside the existing block and width/height are below 50px.

### Task 4.2 — Add icon-badge-gold and empty-gallery icon rules inside existing galeria.css @media block
- File: `public/assets/css/pages/galeria.css`
- Insert into the already-existing `@media (max-width: 991.98px)` block: `#galeria-page .icon-badge-gold { width: 40px; height: 40px; }` and `#galeria-page .empty-gallery-icon, #galeria-page .gallery-empty-icon { font-size: 3.5rem; }`
- Do NOT delete either icon class from the base ruleset.
- Satisfies: spec `mobile-icon-scaling` — Requirement "Badge/icon mobile reduction", Requirement "Empty-state icon reduction without deletion"
- Done when: both rules exist inside the existing block, both classes remain defined in the base (non-media) ruleset, and font-size is below 5rem under the media query.

---

## Phase 5 — Gallery image height, two-step (independent, parallelizable)

### Task 5.1 — Add stepped image height rules to galeria.css
- File: `public/assets/css/pages/galeria.css`
- Inside the existing `@media (max-width: 991.98px)` block (2-column layout): `#galeria-page .gallery-item img { height: 240px; }`
- Inside the existing `@media (max-width: 575.98px)` block (1-column layout): `#galeria-page .gallery-item img { height: 220px; }`
- Do NOT use `aspect-ratio`; do NOT modify the base `height: 320px` rule (desktop ≥992px stays unchanged).
- Satisfies: spec `gallery-image-aspect` — Requirement "Fixed reduced height on 1-column mobile" (both scenarios: mobile height reduced, desktop unchanged)
- Done when: both rules exist in their respective existing blocks, no `aspect-ratio` property is introduced, and the base 320px rule outside any `@media` is untouched.

---

## Cross-cutting

### Task X.1 — Final diff review: no desktop regression
- Diff all 6 touched files (`navbar.php`, `main-layout.css`, `global.css`, `productos.css`, `comercializacion.css`, `galeria.css`) against pre-change state.
- Satisfies: spec Cross-cutting Requirement "No desktop regression"
- Done when: every changed line is either inside an `@media (max-width: ...)` block, or is one of the 4 documented navbar.php markup edits from Task 1.1. No other line is touched.

---

## Review Workload Forecast

Estimated new/changed lines by phase (CSS additions + 4 markup attribute edits):

| Phase | File(s) | Est. lines |
|---|---|---|
| 1 | navbar.php (4 attr edits) + main-layout.css (2 @media blocks) | ~55 |
| 2 | global.css (2 @media blocks, 4 selectors each) | ~20 |
| 3 | productos.css (1 @media block, 10 selectors) | ~15 |
| 4 | comercializacion.css (1 line) + galeria.css (2 lines) | ~5 |
| 5 | galeria.css (2 lines) | ~5 |
| **Total** | | **~100** |

Risk of exceeding the 400-line review budget: low. Total estimated changed lines (~100) sit well under budget even if reviewed as a single combined diff. No PR split is required on line-count grounds alone.

Recommendation: split into 5 separate PRs anyway — not because of budget pressure, but because Phase 1 carries the only markup change and the only mandatory human QA gate (Task 1.3), while Phases 2-5 are independent, disjoint-file, low-risk CSS-only additions that benefit from isolated, fast, individually revertible review. This keeps each PR's blast radius obvious and matches the "atomic commit per phase" rule already established in design.md.

## Key Learnings

1. Phase 1 markup and CSS must ship in one commit to avoid a broken intermediate state.
2. Phases 2 through 5 have zero file overlap and no shared state.
3. The navbar QA task is a human gate that no automation can complete.
4. Total estimated diff size stays far below the 400-line review budget.
5. Comercializacion.css padding change in phase 2 is an accepted behavior fix, not a regression.
