# Apply Progress: Fix Superposición Navbar Mobile — Panel Admin

## Status: Implementado, pendiente QA en dispositivo real (Task 3)

## Resumen de cambios aplicados

1. `public/assets/css/admin/admin-panel.css` — `.btn-admin-toggle`: `z-index: 10001` → `z-index: 1`.
2. `app/Views/layout/admin_layout.php` — bump `admin-panel.css?v=31.0` → `?v=32.0`.

## Verificación realizada

- Sin entorno PHP+MySQL disponible en este sandbox (no `vendor/`, no `.env`) — se reconstruyó el fragmento real de layout (`admin_layout.php` topbar + `admin_sidebar.php`) como HTML estático, sirviendo los mismos `public/assets/css/admin/admin-panel.css` y `public/assets/vendor/bootstrap/*` del repo, sin modificar su contenido salvo el fix bajo prueba.
- Capturado con Chromium headless (Playwright) a 375×700, estado cerrado y estado abierto (`.sidebar-visible`), antes y después del fix.
- **Antes**: el botón hamburguesa del topbar (☰) se renderizaba flotando sobre el header del sidebar ("CVA ADMIN"), confirmando el defecto reportado.
- **Después**: el botón queda correctamente tapado por el overlay/panel; el header del sidebar se ve limpio.
- Estado cerrado (<576px): sin cambios visuales — no había ningún título junto al botón en ese estado (el breadcrumb está oculto por `d-none d-sm-block`), y no se detectó ningún otro defecto ahí.
- No se ejecutó `vendor/bin/phpunit` (no disponible en el sandbox) — el cambio es CSS puro, no toca ninguna clase/controlador PHP, así que no hay superficie de test unitario/de integración afectada.

## Pendiente (Task 3, no delegable)

QA manual en un dispositivo real: confirmar que no hay superposición al abrir el menú, que tocar "CVA ADMIN" navega al dashboard, y que el botón hamburguesa en estado cerrado se ve/funciona igual que antes.

## Key Learnings

1. La reconstrucción estática con Playwright (mismo HTML/CSS reales del repo, solo sustituyendo el webfont de íconos bloqueado por la política de red del sandbox) permitió confirmar visualmente la causa raíz y el fix sin necesitar el stack PHP+MySQL completo — técnica reusable para futuros fixes CSS/layout puramente presentacionales del panel admin.
2. z-index arbitrariamente altos ("por encima de todo") son un antipatrón: rompen el stacking del propio componente que se supone deben ceder ante otro estado (acá, el sidebar mobile abierto). Vale la pena revisar si hay otros z-index similares en el proyecto antes de la próxima iteración (reducción de íconos por pestaña).
