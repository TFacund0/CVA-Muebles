# Exploration: Panel Admin — Superposición Título/Hamburguesa en Mobile

## Contexto

El usuario reporta que en el panel de administrador (`admin_layout.php` + `admin_sidebar.php`), en mobile, el título "CVA Muebles" (percibido así por el usuario; el texto real es "CVA ADMIN") y el botón de hamburguesa se ven superpuestos / desprolijos al abrir el menú. Es el primer paso de una serie de mejoras mobile al admin (después se reducirá el tamaño de íconos pestaña por pestaña).

Nota: no hay entorno de ejecución PHP+MySQL disponible en este sandbox (sin `vendor/`, sin `.env`, sin DB). Se investigó leyendo el código fuente y se validó visualmente reconstruyendo el fragmento de layout real (mismo HTML, mismo `admin-panel.css`, mismo Bootstrap vendorizado) en un archivo estático servido localmente y capturado con Chromium headless (Playwright) a 375px de ancho — la única sustitución fue el webfont de Bootstrap Icons (CDN externo, bloqueado por política de red del sandbox), reemplazado por glifos Unicode de ancho equivalente (~1em) para no alterar las mediciones de layout.

## Estructura real

- `app/Views/layout/admin_layout.php` — topbar (`.admin-topbar`) con el botón `#sidebarToggle` (`.btn-admin-toggle`, ícono `bi-list`), sin ningún título junto a él (el breadcrumb "DASHBOARD" está oculto por debajo de `576px` vía `d-none d-sm-block`).
- `app/Views/partials/admin_sidebar.php:2-12` — `.sidebar-header` del panel lateral (`.admin-sidebar`), con `<a class="sidebar-logo">` (ícono martillo + texto **"CVA ADMIN"**, ya envuelto en un link a `/admin-dashboard`) y el botón de cierre `#sidebarClose` (`bi-x-lg`).

## Hallazgo: causa raíz confirmada (no es un problema de espaciado/padding)

`public/assets/css/admin/admin-panel.css`:
- Línea 35: `.admin-sidebar { z-index: 1000; }` (base, desktop).
- Línea 393: `.sidebar-overlay { z-index: 999; }` (el fondo oscuro semi-transparente detrás del panel).
- Línea 416 (antes del fix): `.btn-admin-toggle { z-index: 10001; /* Muy por encima de todo */ }`.
- Línea 430 (dentro de `@media (max-width: 991.98px)`): `.sidebar-visible .admin-sidebar { z-index: 10000; }` — el panel sube a 10000 solo en mobile, para quedar por encima del overlay (999).

El botón hamburguesa del topbar (`.btn-admin-toggle`, `z-index: 10001`) queda **por encima de absolutamente todo**, incluyendo el overlay (999) y el propio panel lateral abierto (10000). Como el `<header class="admin-topbar">` no tiene `position: fixed` pero sí ocupa la esquina superior izquierda del viewport en el flujo normal, sus coordenadas en pantalla coinciden exactamente con las del `.sidebar-header` del panel (que también vive en la esquina superior izquierda, position fixed). Resultado: al abrir el menú, el botón hamburguesa del topbar (que debería quedar tapado/oculto detrás del panel) se sigue renderizando encima del logo "CVA ADMIN" y del botón de cierre, produciendo la superposición visual que describe el usuario.

**Captura antes del fix** (`open_375.png`, reconstrucción fiel del layout real): el ícono ☰ del topbar aparece flotando sobre el header del panel, a la izquierda del texto "CVA ADMIN" — exactamente el defecto reportado.

**Captura después del fix** (bajar `.btn-admin-toggle` a `z-index: 1`, muy por debajo de `.sidebar-overlay` (999) y `.admin-sidebar` (10000/1000)): el botón queda correctamente tapado por el overlay/panel al abrir el menú; el header del panel se ve limpio (martillo + "CVA ADMIN" + botón X, sin superposición).

## Sobre "que el título direccione al inicio"

Ya está implementado: `admin_sidebar.php:4` — `<a href="<?= base_url('/admin-dashboard') ?>" class="sidebar-logo">` envuelve tanto el ícono como el texto "CVA ADMIN". Tocar el título (ícono o texto) ya navega a `/admin-dashboard`, que es la página de inicio del panel admin. No se requiere cambio de código para este punto; se deja documentado como verificado, no como tarea.

## Estado cerrado (closed state, <576px)

Capturado también el estado cerrado del menú a 375px: solo aparece el botón hamburguesa (arriba-izquierda) y el avatar de usuario (arriba-derecha); el breadcrumb "DASHBOARD" está oculto (`d-none d-sm-block`) así que no hay ningún título visible junto al botón en este estado — no se detectó ningún otro defecto de superposición en el estado cerrado.

## Fuera de alcance de este change

- Achicar íconos/tamaños del resto del panel (sidebar nav items, tablas, botones de acción por pestaña) — pedido explícitamente por el usuario como una iteración **posterior**, pestaña por pestaña.
- Cualquier lógica PHP/backend — el defecto es puramente CSS (un solo `z-index`).

## Key Learnings

1. El texto real del logo del sidebar admin es "CVA ADMIN", no "CVA Muebles" — el usuario lo nombró de oído/memoria: no hay dos textos distintos, es el mismo elemento.
2. La causa no era falta de espacio (padding/font-size) sino un `z-index` fuera de escala (`10001`) que ignoraba por completo el stacking del propio sidebar mobile (`10000`) y su overlay (`999`) — comentado en el código como "Muy por encima de todo", probablemente pensado solo para el estado cerrado sin considerar el estado abierto.
3. El botón de logo del sidebar ya es un link a `/admin-dashboard` desde antes; el pedido de "que el título direccione al inicio" ya estaba resuelto en el código existente.
4. El sandbox no tiene acceso de red a `cdn.jsdelivr.net` (Bootstrap Icons vendorizado solo vía CDN, no local) — la reconstrucción visual usó glifos Unicode de ancho equivalente para no afectar las mediciones; en producción el ícono real carga sin problema y no cambia el diagnóstico (es un problema de `z-index`, no de ancho de glifo).
