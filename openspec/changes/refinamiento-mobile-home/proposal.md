# Proposal: Refinamiento Mobile del Home

## Intent

Post-QA on a real phone surfaced 5 confirmed mobile defects on the home page: an oversized hero, inconsistent lateral padding between sections, oversized Swiper arrows overlapping product photos, sticky `:hover` on touch, and the Maps iframe glued to the footer. All are CSS-only presentation defects that make a finished redesign feel unpolished on the primary traffic device.

## Scope

### In Scope
1. **Hero size** — reduce `#heroCarousel .carousel-inner` `min-height` at `575.98px` (500px/400px today) toward 340–360px; decide whether hero `display-2/3` needs a hero-scoped size instead of the global rule.
2. **Section padding consistency** — align `.section-ubicacion` inner padding with the Bootstrap gutter used by the other 3 home sections, keeping the map full-bleed.
3. **Swiper arrows** — add a mobile `@media` reducing 50px → ~36–40px and reposition so they no longer overlap the product image edge.
4. **Touch hover guard** — wrap `.product-card-vivid:hover` (and, for consistency, `.catalogo-card-premium:hover`) in `@media (hover: hover) and (pointer: fine)`.
5. **Map/footer separation** — restore vertical bottom spacing on `.section-ubicacion`, defeated today by Bootstrap `.py-0 !important`.

### Out of Scope
- **Navbar overlap — already fixed outside this change, corrected retroactively here.** Exploration point 1 first claimed the hamburger sat on the left while the panel opened right; that specific claim was wrong (button already on the right via `order: 2`). But the user then reported a *different*, real symptom on an actual device: the centered brand title overlapped the icon zone. Root cause confirmed by math: `position: absolute; left: 50%` with `max-width: 48-55%` ignored the actual width of the icons cell — at 375px with the cart badge visible, the title's box could reach ~277px while the icons zone started at ~271px (worse at 320px, ~10px overlap). Since this was a live regression on production, it was fixed directly (commit `c00e6d6`, already pushed to `main`) rather than routed through this proposal: replaced `position: absolute` centering with `grid-template-columns: auto 1fr auto` on `.container-fluid`, which allocates space based on actual sibling width and makes overlap structurally impossible. No further navbar work remains in scope here.
- Product filters, any PHP logic, `CloudinaryService`.
- Typography refactor beyond the hero decision in item 1.

## Capabilities

### New Capabilities
None.

### Modified Capabilities
None — presentational CSS only, no spec-level behavior change.

## Approach

CSS-first, minimum-blast-radius edits at the existing project breakpoints (`991.98px`, `575.98px`). Keep typography centralized in `global.css` — do not duplicate `display-*` rules into `carrusel.css`. Prefer removing the Bootstrap utility (`py-0`) from markup and owning the padding in project CSS over stacking a competing `!important`.

## Affected Areas

| Area | Impact | Description |
|------|--------|-------------|
| `public/assets/css/pages/carrusel.css` | Modified | Hero `min-height` / `vh` at `575.98px` |
| `public/assets/css/base/global.css` | Modified (conditional) | Only if hero display sizes are scoped |
| `public/assets/css/pages/catalogo.css` | Modified | Swiper arrows, hover guards, `.section-ubicacion` padding |
| `app/Views/front/home/section-catalogo.php` | Modified | Line 133: likely removal of `py-0` |

## Success Criteria

- [ ] **Hero**: at ≤575.98px the hero occupies clearly less than the full initial viewport; caption text does not wrap awkwardly or overflow `.glass-caption`.
- [ ] **Padding**: the text column of `.section-ubicacion` visually matches the left/right inset of the other 3 sections at ≤991.98px, while the map still touches both viewport edges.
- [ ] **Arrows**: at ≤575.98px arrows measure ~36–40px and no part overlaps the product image bounding box; both arrows remain tappable (≥36px hit area).
- [ ] **Hover**: on a touch device, tapping a product card produces no persistent `scale(1.05)` state; on desktop mouse the hover effect is unchanged.
- [ ] **Footer**: measurable bottom gap between the iframe and the footer `border-top` at ≤991.98px (target ≥40px), with no added top gap.
- [ ] Navbar rendering is byte-for-byte unchanged.

## Risks

| Risk | Likelihood | Mitigation |
|------|------------|------------|
| **(5)** Fighting Bootstrap `.py-0 !important`: an `!important` counter-rule creates a fragile specificity war | High | Prefer removing `py-0` from `section-catalogo.php:133` and setting `padding: 0 0 Xpx` in `catalogo.css`; verify the top spacing did not regress |
| **(5)** Removing `py-0` re-enables the base `padding: 100px 0` top spacing | Med | Explicitly set `padding-top: 0` in project CSS at the same time |
| **(4)** First-ever use of `@media (hover: hover)` in the project — no existing pattern, risk of inconsistent adoption | Med | Apply to both card selectors in one pass; document the pattern so future hover rules follow it |
| **(4)** Hybrid touch+mouse laptops lose hover if the guard is too strict | Low | Use `hover: hover` **and** `pointer: fine`, which hybrids satisfy with a mouse attached |
| **(1)** Scoping hero typography could desync from other `display-2/3` uses site-wide | Med | Decide explicitly; if scoped, keep the change under `#heroCarousel` only |
| **(3)** Repositioning Swiper arrows fights CDN library defaults | Med | Override only `width`/`height`/`top`/offset inside the mobile media query; visual check on 375px and 414px widths |
| **(2)** Changing `.section-ubicacion` padding may break the map's full-bleed intent | Low | Pad the text column only; leave `container-fluid`/`row g-0` intact |

## Rollback Plan

All changes are confined to 3 CSS files plus one line of markup. `git revert` of the change commit restores previous rendering with no data, schema, or cache implications. Individual items are independently revertible since each lives in a distinct selector block.

## Dependencies

- Confirmation from the user that the navbar observation was browser cache (blocks nothing; only closes the erroneous finding).
- Real-device verification at ≤575.98px for items 1, 3, 4.

## Decisions (user-confirmed)

- Hero typography: scoped to `#heroCarousel` only, not the centralized `global.css` rule. Other pages using `display-2/3` are unaffected.
- Touch hover guard: applied to both `.product-card-vivid` and `.catalogo-card-premium` in the same pass.
- Swiper arrows: shrink and reposition (not hide) at mobile widths, keeping tap-to-navigate available.

## Key Learnings

1. The exploration's navbar finding was wrong; the hamburger button already renders on the right side.
2. Bootstrap's `py-0` utility uses `!important` and silently overrides the project's section padding.
3. The project has never used `@media (hover: hover)`, so the touch guard is a new convention.
4. Swiper arrows are fixed at 50px with no responsive media query anywhere in the project.
5. Three of four home sections use `.container`, and only `.section-ubicacion` breaks that gutter pattern.
