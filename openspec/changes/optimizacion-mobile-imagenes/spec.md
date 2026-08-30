# Spec Delta: optimizacion-mobile-imagenes

## ADDED Requirements

### Requirement: mobile-asset-delivery

The system SHALL serve the 4 in-use static images (`hero/taller.jpg`, `hero/Muebles 22.jpeg`, `hero/Muebles 69.jpeg`, `products/Muebles 10.jpeg`) as pre-compressed WebP siblings with the original format as fallback, and SHALL apply `loading`/`fetchpriority` hints so only the hero's first slide is eager.

#### Scenario: Combined weight of the 4 images drops below 400KB
- **GIVEN** the 4 in-use static images under `public/assets/img/content/`
- **WHEN** their file sizes on disk are summed (WebP files actually referenced by the views, not the legacy JPEG/JPEG-2 originals)
- **THEN** the combined size is ≤400KB, down from the ~1.4MB baseline

#### Scenario: Each in-use image has a committed WebP sibling
- **GIVEN** each of `hero/taller.jpg`, `hero/Muebles 22.jpeg`, `hero/Muebles 69.jpeg`, `products/Muebles 10.jpeg`
- **WHEN** the repository is inspected
- **THEN** a `.webp` file with the same base name exists alongside the original in the same directory
- **AND** the original JPEG/JPEG-2 file still exists unmodified (never deleted)
- **AND** no build step generates the `.webp` file (it is a committed static asset, not a build artifact)

#### Scenario: Each image is served via `<picture>` with WebP source and original fallback
- **GIVEN** the rendered markup for `section-carrusel.php` (hero slides) and `section-catalogo.php` (Especialidades)
- **WHEN** the DOM for each of the 4 images is inspected
- **THEN** each image is wrapped in a `<picture>` element
- **AND** contains a `<source type="image/webp" srcset="...webp">` element
- **AND** contains a fallback `<img src="...jpg">` (or `.jpeg`) pointing at the original, unmodified file
- **AND** the fallback `<img>` retains its original `alt` text and CSS classes

#### Scenario: Hero slide 1 remains eager and is not lazy-loaded
- **GIVEN** the hero carousel markup in `section-carrusel.php`
- **WHEN** the `<img>` for slide 1 (`taller.jpg`) is inspected
- **THEN** it does NOT carry `loading="lazy"`
- **AND** it MAY carry `fetchpriority="high"`

#### Scenario: Hero slides 2–3 and Especialidades images are lazy-loaded
- **GIVEN** the hero carousel slides 2 and 3, and the 3 "Especialidades" images in `section-catalogo.php`
- **WHEN** each corresponding `<img>` is inspected
- **THEN** each carries `loading="lazy"`

#### Scenario: Fallback path renders correctly without WebP support
- **GIVEN** a browser or crawler that does not support WebP (no `<picture>`/`<source>` support, or explicit override)
- **WHEN** any of the 4 `<picture>` elements is rendered
- **THEN** the browser falls back to the `<img src>` original JPEG/JPEG-2, which loads successfully

### Requirement: responsive-typography

The system SHALL scale Bootstrap `display-1`–`display-4` heading font sizes down at established project breakpoints (991.98px, 575.98px) via a single centralized rule in `public/assets/css/base/global.css`, without altering the visual size of Bootstrap Icons or admin statistic numbers that reuse the same `display-*` classes.

#### Scenario: A single centralized media rule governs heading display classes
- **GIVEN** `public/assets/css/base/global.css`
- **WHEN** the stylesheet is inspected
- **THEN** exactly one set of `@media` rules (at breakpoints `991.98px` and `575.98px`) targets `display-1`–`display-4` sizing
- **AND** each selector in that rule is qualified by a heading element (e.g. `h1.display-3`, `h2.display-2`), not a bare `.display-*` class selector

#### Scenario: display-3 heading is smaller than Bootstrap default at 375px viewport
- **GIVEN** a page containing `<h1 class="display-3">` (e.g. home, `quienesSomos`, `informacionContacto`, `comercializacion`)
- **WHEN** the viewport width is 375px (and separately 390px)
- **THEN** the computed `font-size` of that heading is smaller than Bootstrap's unmodified `display-3` default (5rem / 80px)

#### Scenario: Bootstrap Icon using display-1 in admin is visually unaffected
- **GIVEN** an admin empty-state icon such as `<i class="bi bi-inbox display-1">` in `crud_productos`, `crud_usuarios`, or `vistaCompras`
- **WHEN** the page is rendered at any viewport width, including 375px and 575.98px/991.98px breakpoints
- **THEN** the icon's computed `font-size` is unchanged from Bootstrap's default `display-1` value (no reduction is applied)
- **AND** this holds because the centralized rule's selectors are element-qualified (`h1`/`h2`) and do not match `<i>` elements

#### Scenario: Admin statistics numbers using display-4 remain untouched
- **GIVEN** `back/sales/estadisticas.php`, which uses `display-4` on numeric stat values
- **WHEN** the page is rendered at any viewport width
- **THEN** the computed `font-size` of those numbers is unchanged from Bootstrap's default `display-4` value

#### Scenario: Hero headline sizing is governed by exactly one rule set
- **GIVEN** `#heroCarousel` headline elements using `display-2`/`display-3` and the pre-existing overrides in `public/assets/css/pages/carrusel.css`
- **WHEN** the centralized `global.css` rule is confirmed to size the hero correctly at both `991.98px` and `575.98px`
- **THEN** the duplicate `#heroCarousel .display-2`/`.display-3` overrides are removed from `carrusel.css`
- **AND** no two stylesheet rules apply conflicting `font-size` values to the same hero heading at the same breakpoint
