# Spec: rediseno-mobile-ux

Five new capabilities. No prior specs exist for these domains — all sections below are full (not delta) specs.

## Capability: mobile-navbar-layout

### Purpose
Navbar cell order and brand text visibility on mobile viewports (<992px).

### Requirements

#### Requirement: Mobile cell order
On viewports below 992px, the navbar MUST render logo+brand text as the leftmost group and cart+hamburger as the rightmost group (inverted from the current 3-cell centered-logo layout).

##### Scenario: Mobile layout order
- GIVEN a viewport width of 375px
- WHEN the navbar renders
- THEN the logo and brand text appear before the cart and hamburger icons in DOM/visual order

#### Requirement: Brand text visibility
The brand text "CVA Muebles" MUST be visible on mobile (no `d-none` class hiding it below the lg breakpoint).

##### Scenario: Text present at 375px
- GIVEN a viewport width of 375px
- WHEN the navbar renders
- THEN `.titulo-logo` has no `d-none` class applied and is visually rendered

#### Requirement: Truncation without overflow
The brand text MUST truncate with ellipsis when it does not fit at 320px width, and MUST NOT overflow or overlap the cart/hamburger icons.

##### Scenario: Truncation at 320px
- GIVEN a viewport width of 320px
- WHEN the brand text exceeds available flex space
- THEN the text is clipped with `text-overflow: ellipsis` and `overflow: hidden`
- AND no navbar element visually overlaps the cart or hamburger icon bounding boxes

#### Requirement: Offcanvas unaffected
The offcanvas side menu triggered by the hamburger MUST remain unchanged.

##### Scenario: Offcanvas opens as before
- GIVEN the mobile navbar
- WHEN the hamburger icon is activated
- THEN the existing offcanvas menu opens with unmodified markup and behavior

## Capability: global-button-responsiveness

### Requirements

#### Requirement: Two-tier mobile scaling
`.btn-artisan`, `.btn-vivid`, `.btn-premium-action`, and `.btn-gold` in `global.css` MUST each receive reduced padding and font-size at `@media (max-width: 991.98px)` and further reduced values at `@media (max-width: 575.98px)`.

##### Scenario: 991.98px rule present
- GIVEN `global.css`
- WHEN inspected for each of the 4 button selectors
- THEN an `@media (max-width: 991.98px)` block exists with reduced `padding` and `font-size` versus the base rule

##### Scenario: 575.98px stricter than 991.98px
- GIVEN the two mobile breakpoints for the same selector
- WHEN comparing `padding` and `font-size` values
- THEN the 575.98px values are strictly smaller than the 991.98px values

#### Requirement: Predictable specificity vs. page overrides
The new mobile rules MUST NOT be silently overridden by existing page-level overrides in `contacto.css:156` and `comercializacion.css:202`.

##### Scenario: Page override coexists
- GIVEN `contacto.css` has an override for one of the 4 buttons (possibly with `!important`)
- WHEN the new mobile breakpoint rule applies at the same viewport
- THEN the effective computed style is deterministic (documented winner), not accidental

## Capability: product-card-small-viewport

### Requirements

#### Requirement: 575.98px tier added
`productos.css` MUST add `@media (max-width: 575.98px)` rules for the same selectors already covered at 991.98px (card image, `.card-body`, title, price, buttons).

##### Scenario: New breakpoint block exists
- GIVEN `productos.css`
- WHEN inspected
- THEN an `@media (max-width: 575.98px)` block exists for image, card-body padding, title font-size, price font-size, and button padding/font-size

##### Scenario: Values strictly smaller
- GIVEN the same selector's value at 991.98px and 575.98px
- WHEN compared
- THEN the 575.98px value is strictly less than the 991.98px value for image size, card-body padding, title, price, and button font-size

## Capability: mobile-icon-scaling

### Requirements

#### Requirement: Badge/icon mobile reduction
`.badge-logistica`, `.icon-badge-gold` (both 50x50px fixed) MUST have reduced dimensions under a mobile `@media` rule.

##### Scenario: Badge reduced below 992px
- GIVEN `.badge-logistica` or `.icon-badge-gold`
- WHEN viewport is below 992px
- THEN width/height are less than 50px via an `@media` rule

#### Requirement: Empty-state icon reduction without deletion
`.empty-gallery-icon` and `.gallery-empty-icon` (both font-size 5rem) MUST both receive the same reduced mobile font-size rule; neither class may be removed.

##### Scenario: Both classes covered
- GIVEN `galeria.css`
- WHEN inspected for a mobile `@media` block
- THEN both `.empty-gallery-icon` and `.gallery-empty-icon` have a reduced `font-size` rule and neither selector is deleted from the base ruleset

## Capability: gallery-image-aspect

### Requirements

#### Requirement: Fixed reduced height on 1-column mobile
`.gallery-item img` (height: 320px fixed) MUST use a smaller fixed height (~220px) in the 1-column mobile layout, not `aspect-ratio`.

##### Scenario: Mobile height reduced
- GIVEN the 1-column mobile gallery layout
- WHEN `.gallery-item img` is inspected
- THEN `height` is a fixed pixel value approximately 220px (no `aspect-ratio` property used)

##### Scenario: Desktop unchanged
- GIVEN a viewport ≥992px with the 3-column gallery layout
- WHEN `.gallery-item img` is inspected
- THEN `height: 320px` remains unmodified

## Cross-cutting Requirement: No desktop regression

##### Scenario: Base rules unmodified
- GIVEN any of the 6 affected CSS/PHP files
- WHEN diffed against their pre-change state
- THEN no rule outside an `@media (max-width: ...)` block or the navbar's mobile-only markup path is modified
