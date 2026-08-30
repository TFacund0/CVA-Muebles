# Apply Progress: optimizacion-mobile-imagenes

## Grupo 1 — Centralized responsive typography — DONE

- [x] 1.1 Added `@media (max-width: 991.98px)` block to `public/assets/css/base/global.css` (EOF, after `.badge-status`). Selectors qualified by `h1`/`h2`. Values: display-1=3rem, display-2=2.5rem, display-3=2.2rem, display-4=2rem.
- [x] 1.2 Added `@media (max-width: 575.98px)` block, same file/pattern. Values: display-1=2.4rem, display-2=2rem, display-3=1.8rem, display-4=1.6rem.
- Verified display-2/display-3 values match `carrusel.css:207-208` (2.5rem/2.2rem) and `carrusel.css:221-222` (2rem/1.8rem) exactly before writing — no value drift.
- `vendor/bin/phpunit` run afterward: 20 errors, all `DatabaseException: Unable to connect to the database` (no local MySQL in this environment). No PHP files were touched by this change; these failures are pre-existing/environmental, not regressions from Group 1.

## Grupo 2 — QA manual — BLOCKED (esperando confirmación humana)

- [ ] 2.1 Requires a human to verify in-browser at 375px, 390px, and breakpoints 575.98px/991.98px:
  1. Hero (`#heroCarousel`) and the 4 public pages (home, `quienesSomos`, `informacionContacto`, `comercializacion`): `display-3`/`display-2` headings render smaller than Bootstrap's 5rem default and consistent with the current hero look.
  2. Empty-state Bootstrap icons in admin views `crud_productos`, `crud_usuarios`, `vistaCompras` (e.g. `<i class="bi bi-inbox display-1">`) are UNCHANGED in size vs. Bootstrap default (these use bare `.display-*` on `<i>`, not `h1`/`h2`, so the new CSS should not touch them — needs visual confirmation).
  3. `back/sales/estadisticas.php` `display-4` stat numbers are UNCHANGED in size.
- Without explicit human confirmation of these 6 points, implementation must NOT proceed to Grupo 3 (delete duplicated rules in `carrusel.css`), Grupo 4 (WebP generation), or Grupo 5 (`<picture>` swap in views).

## Grupos 3, 4, 5 — NOT STARTED

Blocked behind Grupo 2 confirmation.

## Key Learnings

1. Carrusel.css display-2/display-3 values already matched the prompt's proposed values exactly.
2. No PHP files were modified, so PHPUnit failures are environmental DB connectivity, not regressions.
3. Selectors were qualified by h1/h2 tags to avoid affecting bare .display-1 icon classes.
4. Group 2 is a mandatory human QA gate before any deletion in carrusel.css occurs.
5. Review budget of 400 lines is far from at risk; group 1 alone added roughly 18 lines.
