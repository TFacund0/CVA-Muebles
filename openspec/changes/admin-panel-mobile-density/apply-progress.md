# Apply Progress: Densidad Mobile del Panel Admin

## Fase 0 — Fundación compartida: COMPLETADA

### Cambios aplicados

1. **`.dashboard-icon-main` centralizado** (`admin-panel.css`, dentro de `@media max-width: 991.98px`): 60px→50px, `font-size` 2rem→1.5rem. Antes solo vivía en `admin-products.css` (`@media max-width: 767.98px`), así que solo Productos/Categorías lo recibían. Ahora aplica parejo en Dashboard, Ventas y Usuarios también. Se quitó la regla duplicada de `admin-products.css` (queda un comentario explicando dónde vive ahora).
2. **Avatar/imagen de fila compartido** (`admin-panel.css`, motor de tarjetas): 60px→50px, `font-size` 1.2rem→1rem, radio de imagen de producto 12px→10px. Afecta a la vez a Usuarios, Ventas (Activos y Solicitudes), Productos y Categorías, porque las 5 pantallas reusan la misma regla.
3. **`.btn-action-premium` en mobile**: nuevo override 40px→36px (antes no tenía ningún ajuste mobile propio).
4. **Bug real encontrado durante la verificación (no estaba en el plan original)**: el selector de respaldo `div.d-flex.justify-content-center` (pensado solo para forzar el contenedor de las pestañas segmentadas a 100% de ancho en mobile) no estaba acotado a las pestañas — como `.avatar-premium` también usa las utilidades Bootstrap `d-flex` + `justify-content-center` para centrar sus iniciales, ese mismo selector lo capturaba y lo estiraba a `width: 100% !important`, deformando el círculo del avatar en una **elipse** en mobile. Afecta a **Usuarios, Ventas (ambas pestañas) y Categorías** — 3 de las 5 pantallas pedidas. Se confirmó que los 4 usos reales de pestañas del proyecto ya son hijos directos de `.custom-segmented-tabs`, así que el otro selector del mismo bloque (`.d-flex:has(> .custom-segmented-tabs)`) alcanza solo — se quitó el selector genérico.
5. Bump de cache-busting: `admin-panel.css` v32→v33, `admin-products.css` v3→v4 (y sus 4 `<link>`).

### Verificación realizada

Mismo método que el fix de navbar: reconstrucción estática del HTML/CSS real (fragmentos fieles de encabezado de página + fila de tabla con avatar + botones de acción, copiados del markup real de `crud_usuarios.php`), capturada con Chromium headless a 375px, antes/después. Te mando las capturas.

- **Antes**: ícono de encabezado 60px, avatar de usuario **deformado en elipse** (bug pre-existente, no introducido por este cambio — confirmado idéntico en la versión "antes" con el código sin tocar), botones de acción 40px.
- **Después**: ícono de encabezado 50px, avatar circular correcto (50px), botones de acción 36px.
- Verificado por separado que las pestañas segmentadas (Productos/Ventas/Usuarios/Consultas) siguen ocupando 100% de ancho en mobile tras quitar el selector de respaldo — sin regresión.
- No se ejecutó `vendor/bin/phpunit` (no disponible en el sandbox) — cambio CSS puro, sin lógica PHP tocada.

### Pendiente

Fases 1-4 (Dashboard, Ventas, Productos/Catálogo, Usuarios) y Fase 5 (QA en dispositivo real) — ver `tasks.md`.

## Key Learnings

1. El bug de la elipse en el avatar es un ejemplo más del mismo patrón que el fix de navbar: una regla CSS "genérica" (acá, un selector de utilidades Bootstrap sin acotar) que en la práctica solo debía aplicar a un componente específico (las pestañas) termina afectando a cualquier otro elemento que comparta esas mismas clases utilitarias por casualidad. Vale la pena, en las próximas fases, revisar si hay otros selectores igual de genéricos (`div.d-flex.algo`, `.algo:has(> .otra-cosa)` sin acotar) en `admin-panel.css`.
2. La verificación visual con el repro estático no solo confirma el cambio planeado — encontró un bug real que no estaba en el relevamiento original. Vale la pena mantener esta técnica (antes/después con el HTML/CSS real) para las fases siguientes en vez de solo revisar el diff de CSS a ojo.

## Fase 1 — Dashboard: COMPLETADA

### Cambios aplicados

1. **KPIs de producción** (`admin-sales.css`, `@media max-width:767.98px`): `kpi-value` 2rem→1.75rem, `kpi-icon-container` 50px→44px (font 1.4rem→1.2rem), `kpi-body` padding `1.5rem 1rem`→`1.25rem 0.75rem`.
2. **Tarjeta "Rendimiento Histórico"**: nuevo override mobile para `.production-display-compact` (padding 1.25rem) y su `.display-4` interno (2.75rem).
3. **Bug real encontrado al verificar (no estaba en el plan)**: `.bg-cva-brown` en `global.css` no tenía `!important`. Por orden de carga (`global.css` antes que `admin-panel.css` en `admin_layout.php`), `.admin-card-v2 { background: white; }` le ganaba en cascada a cualquier elemento con ambas clases — la tarjeta de Rendimiento Histórico se veía **blanca en vez de marrón**, y su párrafo descriptivo (`text-white` heredado) quedaba **invisible** (blanco sobre blanco). Este bug afectaba **desktop y mobile por igual** — no es específico de la iniciativa mobile, pero se descubrió y corrigió acá porque es la misma tarjeta que se estaba auditando. Se agregó `!important`, igual que ya tenía `.text-gold` al lado.
4. **Hallazgo menor relacionado**: el panel interior con el número "142" usaba `bg-opacity-5`, una clase que Bootstrap no define (los pasos reales son 10/25/50/75/100) — quedaba sólido blanco en vez de un panel translúcido. Cambiado a `bg-opacity-10` en `estadisticas.php`.
5. Bump de cache-busting: `admin-sales.css` v33→v34 (4 vistas), `global.css` v3.0→v3.1 (admin) y v4.1→v4.2 (sitio público, mismo archivo compartido).

### Verificación realizada

Reconstrucción estática fiel del contenido completo de `estadisticas.php` (encabezado, 4 KPIs, acciones rápidas, tarjeta de rendimiento) con el CSS real del repo, capturada a 375px de ancho (`fullPage`), antes/después. Además, medición directa vía `getComputedStyle` (no solo visual) del `background-color` de la tarjeta y del panel interior, para confirmar los valores RGB reales antes y después del fix — no solo cómo "se ve", sino el valor computado real.

- **Antes**: tarjeta de rendimiento con fondo blanco (`rgb(255,255,255)`), párrafo descriptivo invisible, panel del número sólido blanco.
- **Después**: fondo marrón correcto (`rgb(45,27,25)` = `#2D1B19`), párrafo legible, panel del número translúcido coherente con el borde `border-opacity-10` que ya tenía al lado.
- No se ejecutó `vendor/bin/phpunit` (no disponible en el sandbox) — cambios CSS + un ajuste de clases Bootstrap en markup, sin lógica PHP tocada.

### Pendiente

Fases 2-4 (Ventas, Productos/Catálogo, Usuarios) y Fase 5 (QA en dispositivo real).

## Key Learnings (Fase 1)

1. Un bug de contraste/legibilidad puede pasar desapercibido durante mucho tiempo si el resto de la tarjeta (ícono, número grande) sigue siendo visualmente coherente — solo una línea de texto secundario quedaba invisible, no toda la tarjeta.
2. Medir con `getComputedStyle` (no solo mirar la captura) fue clave para confirmar con certeza el color de fondo real antes de escribir el fix — evita "creer que ya está bien" por una captura ambigua.
3. Vale la pena, en las próximas fases, revisar si aparecen más combinaciones `bg-*` + `bg-opacity-*` con un paso inválido (Bootstrap solo admite 10/25/50/75/100) — es un error fácil de cometer al escribir Tailwind-style opacity de memoria.

## Fase 2 — Ventas (listado + detalle): COMPLETADA

### Cambios aplicados

1. **`detalleVentas.php` (listado)**: revisado con repro fiel de `order-row` y `solicitud-row` (avatar, badge de ID, botones de prioridad, botones de acción/gestión) — ya quedaba bien después de la Fase 0, sin cambios adicionales. Los 4 KPIs del listado usan un componente distinto y ya compacto por diseño (ícono oculto bajo 576px), no se tocaron.
2. **Bug real encontrado al verificar (no estaba en el plan)**: en `gestion_pedido_admin.php`, el bloque "Pedido Pendiente de Aprobación" tenía dos botones lado a lado con `px-5 py-3 rounded-pill` y `justify-content-center` sin wrap. En 375px, el texto largo ("ACEPTAR Y EMPEZAR OBRA") se partía en 4 líneas dentro de un pill angosto — el botón se deformaba en un óvalo/casi-círculo y el texto del segundo botón se desbordaba fuera del borde visible. Se creó una clase dedicada `.approval-actions` (contenedor) + `.btn-approval` (botones) en `admin-sales.css`: en `≤575.98px` se apilan a ancho completo con padding reducido; en desktop mantienen el layout side-by-side original (verificado a 900px, sin regresión).
3. **Otro hallazgo menor en la misma página**: el ícono decorativo de billetera (`bi-wallet2`) en la tarjeta "Estado Financiero" usaba la clase `opacity-05`, que Bootstrap no define (los pasos reales son 0/25/50/75/100) — quedaba a opacidad 1 (sólido) en vez del watermark tenue que el diseño pedía (un ícono grande de 8rem semi-invisible detrás de los números). Se movió `opacity: 0.05` directo a `.wallet-icon-bg` (la clase propia del ícono, ya scoped a un solo uso) y se quitó la clase muerta del markup.

### Verificación realizada

Repros estáticos fieles (mismo HTML/CSS real): fila `order-row`/`solicitud-row` de la tabla de pedidos, y el bloque de aprobación pendiente en dos anchos (375px mobile y 900px desktop). Medición directa de `opacity` computada del ícono de billetera antes/después.

- **Bloque de aprobación, antes (375px)**: botones deformados en óvalos, texto "RECHAZAR PEDIDO" desbordando el borde visible del botón outline.
- **Bloque de aprobación, después (375px)**: botones apilados, ancho completo, texto legible dentro del borde.
- **Bloque de aprobación, después (900px)**: idéntico al diseño original (side-by-side), sin regresión de desktop.
- **Ícono billetera**: opacidad computada antes = `1`, después = `0.05` (confirmado con `getComputedStyle`).
- No se ejecutó `vendor/bin/phpunit` (no disponible en el sandbox) — cambios CSS + clases de markup, sin lógica PHP tocada (los botones de aprobación mantienen los mismos `<form>`/inputs, solo cambiaron las clases CSS del `<button>` y del `<div>` contenedor).

### Pendiente

Fases 3-4 (Productos/Catálogo, Usuarios) y Fase 5 (QA en dispositivo real).

## Key Learnings (Fase 2)

1. Segundo caso en dos fases seguidas de una clase de opacidad Bootstrap inválida (`bg-opacity-5` en Fase 1, `opacity-05` acá) — parece un patrón de error recurrente en el proyecto (asumir que Bootstrap admite cualquier múltiplo de 5, cuando solo admite 0/25/50/75/100). Vale la pena, antes de cerrar Fase 5, un grep rápido de `opacity-0[0-9]` y `bg-opacity-0[0-9]` en todo `app/Views` para descartar más casos.
2. `justify-content-center` sin `flex-wrap` en un contenedor con texto largo y botones de ancho fijo es una combinación de riesgo — no colapsa ni hace scroll, directamente deforma el botón (el pill intenta mantener su proporción con `border-radius:50rem` mientras el texto lo empuja en altura). Vale la pena recordar este patrón para las fases siguientes.

## Fase 3 — Catálogo (Productos + Categorías): COMPLETADA (sin cambios de código)

### Verificación realizada

Repros fieles a 375px de: fila de producto (`product-row`, imagen + título + 3 botones de gestión apilados), barra de filtros de `crud_productos.php`, y fila de categoría (`category-row`, avatar + 3 botones ícono, mismo patrón que `order-row`/`user-row` ya verificado en fases anteriores).

- **Imagen de producto**: confirmado que la Fase 0 ya resolvió el tamaño (50×50px real, medido con un `<img>` real en el repro — un primer intento con un `<div>` de reemplazo dio una lectura falsa de "sigue en 80px" porque el selector CSS de la Fase 0 apunta específicamente a la etiqueta `img`, no a cualquier hijo del contenedor; corregido el repro y confirmado el tamaño correcto).
- **Filtros**: buscador + categoría + reset se ven prolijos y usables a 375px tal como están.
- **Botones de acción**: tanto el patrón "texto + ícono apilado" (Productos) como "solo ícono en fila" (Categorías) se ven bien a 36px de alto (ajuste de Fase 0).
- No se aplicó ningún cambio de código en esta fase — la verificación no encontró defectos nuevos, y no se forzó un rediseño (ej. "fila compacta de íconos" en vez de apilado) sin un problema concreto que lo justifique.
- No se ejecutó `vendor/bin/phpunit` (no aplica, no hubo cambios de código en esta fase).

### Pendiente

Fase 4 (Usuarios) y Fase 5 (QA en dispositivo real).

## Key Learnings (Fase 3)

1. No todas las fases necesitan cambios de código — verificar y confirmar "ya está bien" es un resultado válido y vale la pena documentarlo con la misma rigurosidad que un fix, para que quede registro de que sí se revisó.
2. Un repro con un `<div>` de relleno en vez de un `<img>` real puede dar falsos positivos cuando el CSS real usa un selector de etiqueta (`... img { ... }`) en vez de una clase — al armar repros para las próximas fases, usar siempre la etiqueta HTML real del elemento que se está midiendo, no un sustituto "equivalente".

## Fase 4 — Usuarios: COMPLETADA

### Cambios aplicados

1. **Bug real encontrado al verificar (no estaba en el plan)**: la celda `ACCESO` de `user-row` (usuario + email) no depende del `::before` genérico como única etiqueta — tiene 2 `<div class="user-access-info">` propios con `width:100%` cada uno, pensados para apilarse verticalmente. Pero el `<td>` genérico del motor de tarjetas ya es `display:flex` en fila, y nada forzaba `flex-direction:column` en esta celda puntual — así que con un email medianamente largo (un caso normal, no extremo) el texto se salía de la tarjeta hacia la derecha, fuera de la pantalla, en vez de quedarse contenido. Se agregó `.user-row td[data-label="ACCESO"] { flex-direction:column !important; align-items:flex-end !important; }` y `word-break:break-word` a `.user-access-info`, en `admin-panel.css` (el motor compartido, no `admin-users.css` — el bug no era específico de esta página en el código, aunque solo la tabla de Usuarios expone una celda con esta forma de 2 líneas).
2. **Filtros**: verificados tal cual están (buscador + selector de perfil + reset), se ven bien a 375px, sin cambios.
3. Bump de cache-busting: `admin-panel.css` v33→v34.

### Verificación realizada

Repro fiel de `user-row` con datos "peor caso" a propósito (nombre largo, usuario largo, email largo — "maria.gabriela.fernandez@correo-largo-ejemplo.com") en vez de datos cortos de ejemplo, más los badges "SUSPENDIDO"/"TU SESIÓN" que no se habían probado en fases anteriores. Fue justamente usar datos largos lo que reveló el bug — con datos cortos el defecto no se nota, porque el texto nunca llega a competir por espacio en la fila.

- **Antes**: el email se salía de la tarjeta hacia la derecha, cortado por el borde del viewport.
- **Después**: usuario y email se apilan dentro de la celda, alineados a la derecha, sin salirse de la tarjeta.
- El cambio vive dentro de `@media (max-width: 991.98px)`, así que no puede afectar el layout de escritorio (la tabla de escritorio ni siquiera usa este motor de tarjetas).
- No se ejecutó `vendor/bin/phpunit` (no disponible en el sandbox) — cambio CSS puro.

### Pendiente

Fase 5 (QA en dispositivo real) — última fase del plan.

## Key Learnings (Fase 4)

1. Probar con datos "peor caso" (nombres/emails largos) en vez de datos cortos de ejemplo fue clave para encontrar este bug — los 3 repros anteriores de fases previas usaban nombres razonablemente cortos y no lo hubieran mostrado. Vale la pena que la QA manual de Fase 5 incluya, a propósito, al menos un registro con datos largos en cada pantalla.
2. Mismo patrón de causa raíz que ya apareció dos veces: una celda/elemento con contenido propio de varias líneas queda a merced de una regla "genérica" (acá, `display:flex` en fila del motor de tarjetas) que no fue pensada para ese caso particular — vale la pena, en el futuro, revisar si alguna otra celda de las tablas del panel tiene la misma forma (label implícito + 2+ líneas de contenido propio) sin su propio `flex-direction:column`.
