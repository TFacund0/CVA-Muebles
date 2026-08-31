# Tasks: Fix Superposición Navbar Mobile — Panel Admin

Backend: manual (sin CLI de OpenSpec/Engram disponible en esta sesión — se siguió la misma convención de archivos que los changes previos del repo). Delivery strategy: single-shot (cambio de 1 línea + 1 bump de versión). Presupuesto: trivial (<10 líneas de diff).

## Task 1 — Fix de z-index del botón hamburguesa [x]

- Archivo: `public/assets/css/admin/admin-panel.css` (`.btn-admin-toggle`, línea ~416).
- Cambio: `z-index: 10001;` (con comentario `/* Muy por encima de todo */`) → `z-index: 1;`.
- Criterio de hecho: en repro estático a 375px con `.sidebar-visible` activo, el botón hamburguesa del topbar no se renderiza por encima del header del sidebar (verificado con captura antes/después vía Playwright).

## Task 2 — Cache-busting [x]

- Archivo: `app/Views/layout/admin_layout.php`.
- Cambio: `admin-panel.css?v=31.0` → `?v=32.0`.
- Criterio de hecho: el número de versión coincide entre el commit y el `<link>`.

## Task 3 — QA manual en dispositivo real [ ] PENDIENTE (usuario)

- No reproducible con certeza total fuera de un dispositivo real (aunque el repro estático con Chromium headless ya reconstruyó el HTML/CSS real y confirmó el fix visualmente).
- Verificaciones:
  1. Abrir el menú lateral en un celular real, confirmar que no hay superposición entre el hamburguesa y "CVA ADMIN".
  2. Confirmar que tocar el ícono/texto "CVA ADMIN" navega al dashboard.
  3. Confirmar que el botón hamburguesa en estado cerrado se ve y funciona igual que antes.
- Criterio de hecho: las 3 verificaciones confirmadas por el usuario en un dispositivo real.

## Review Workload Forecast

- **Alcance total**: 2 líneas de diff (1 CSS + 1 bump de versión), 1 commit.
- **Riesgo**: bajo — cambio aislado de una sola propiedad, sin otros elementos compitiendo por ese z-index en el mismo contexto (confirmado por grep).
- **Delivery strategy = single-shot**: diff trivial, no amerita tramos.

## Key Learnings

1. Este change no tiene `spec.md` ni `design.md` — el defecto es puramente de `z-index`, sin decisiones de arquitectura ni cambio de capability, así que `proposal.md` + `tasks.md` alcanzan (mismo criterio que `refinamiento-mobile-home`, que también prescindió de `spec.md` para fixes CSS presentacionales).
2. El pedido de que el título navegue al inicio ya estaba resuelto en el código — quedó documentado en `exploration.md`/`proposal.md` en vez de convertirse en una tarea de código.
