# Apply Progress — rediseno-mobile-ux

## Fase 1

- [x] Task 1.1 — `app/Views/partials/navbar.php`: agregadas clases `nav-cell-actions`, `nav-cell-brand`, `nav-cell-auth` a las tres celdas del navbar móvil; removido `d-none d-lg-block` del `<h1 class="titulo-logo">`. Sin otros cambios en el archivo (offcanvas y lógica PHP intactos).
- [x] Task 1.2 — `public/assets/css/layout/main-layout.css`: agregados al final los bloques `@media (max-width: 991.98px)` y `@media (max-width: 575.98px)` con el reordenamiento flex (`order`) de las tres celdas y ajustes de tamaño de logo/título/botones circulares.
- [ ] Task 1.3 — GATE HUMANO, no ejecutado (ver checklist abajo).

Task 1.1 y 1.2 quedan en el mismo commit pendiente (sin commitear todavía, tal como se pidió).

## Task 1.3 — Checklist de verificación manual (pendiente, gate humano)

Verificar en navegador, con DevTools responsive, en los siguientes anchos: 320px, 375px, 390px, 768px, 992px, 1440px.

Para cada ancho, repetir:
1. Sin sesión iniciada (`$isLogged = false`).
2. Con sesión iniciada (`$isLogged = true`).
3. Con sesión iniciada y `$cartCount > 0` (el badge del carrito puede desbordar el círculo de 40px en breakpoints móviles chicos — revisar que el badge no se corte ni tape el ícono).

Puntos a confirmar en cada combinación:
- En <992px: orden visual debe ser [botón hamburguesa + carrito] — [logo + título] — [vacío/auth], con el logo/título ocupando el espacio flexible central y sin desbordar (`text-overflow: ellipsis` en `.titulo-logo`).
- En ≥992px: layout desktop sin cambios (debe verse igual que antes de esta fase).
- El offcanvas lateral (menú hamburguesa) abre y cierra normalmente, sin regresiones.
- El badge del carrito (`.navbar-cart-badge`) es legible y no se corta contra los bordes del círculo de 40px/36px en 320-375px.
- No hay overlap ni salto de layout (CLS) al cargar la página en ningún ancho.
- El título "CVA Muebles" ya no tiene `d-none d-lg-block`: confirmar que ahora es visible en mobile y se trunca correctamente si no entra.
