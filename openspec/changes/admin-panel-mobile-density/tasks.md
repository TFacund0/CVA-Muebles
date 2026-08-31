# Tasks: Densidad Mobile del Panel Admin

Backend: manual (mismo criterio que `admin-panel-mobile-navbar-fix`, sin CLI de OpenSpec/Engram disponible). Delivery strategy: fase por fase — cada fase se implementa, se verifica (repro estático 375px) y se entrega en un commit separado antes de pasar a la siguiente. Presupuesto: sin definir aún por fase (se estima al empezar cada una, tras re-explorar esa pantalla puntualmente).

## Fase 0 — Fundación compartida [x]

- Sequential, primero (beneficia a las 5 pantallas, evita duplicar el mismo ajuste 4 veces).
- Archivos: `admin-panel.css` (movido override de `.dashboard-icon-main` a `@media max-width:991.98px`, agregado override de `.btn-action-premium`, achicado avatar/imagen compartido, eliminado selector CSS genérico que rompía el avatar), `admin-products.css` (quitado el override duplicado + limpiada regla muerta de imagen de producto).
- Sub-tareas:
  1. [x] `.dashboard-icon-main` centralizado en `admin-panel.css` dentro del `@media max-width: 991.98px` ya existente (60px→50px, 2rem→1.5rem) — se eligió `991.98px` en vez de `767.98px` para alinear con el breakpoint "modo mobile" real del layout (sidebar/topbar ya cambian ahí).
  2. [x] Avatar/imagen de fila compartido 60px→50px, `.btn-action-premium` 40px→36px.
  3. [x] Bump de cache-busting (`admin-panel.css` v32→v33, `admin-products.css` v3→v4).
  4. [x] **No planeado originalmente**: la verificación visual encontró que el selector `div.d-flex.justify-content-center` (pensado solo para las pestañas segmentadas) también capturaba `.avatar-premium` (que usa esas mismas clases Bootstrap para centrar sus iniciales) y lo forzaba a `width:100%`, deformándolo en una elipse en mobile — afectaba Usuarios, Ventas y Categorías. Se quitó el selector genérico (el otro selector del mismo bloque, `.d-flex:has(> .custom-segmented-tabs)`, ya cubre los 4 usos reales de pestañas del proyecto).
- Criterio de hecho: repro estático 375px confirmó ícono de encabezado achicado (antes no se achicaba en Dashboard/Ventas/Usuarios), avatares circulares correctos (antes elípticos en Usuarios/Ventas/Categorías), botones de acción 36px, y las pestañas segmentadas siguen ocupando 100% de ancho sin regresión. Detalle completo en `apply-progress.md`.

## Fase 1 — Dashboard [x]

- Sequential, depende de Fase 0 (recibe el fix del ícono de encabezado gratis).
- Archivos: `admin-sales.css`, `estadisticas.php`, `global.css` (bug no planeado, ver abajo).
- Sub-tareas:
  1. [x] Re-explorada la pantalla con repro estático fiel (header + 4 KPIs + acciones rápidas + tarjeta de rendimiento) a 375px.
  2. [x] `kpi-value` 2rem→1.75rem, `kpi-icon-container` 50px→44px, `kpi-body` padding más ajustado.
  3. [x] Tarjeta "Rendimiento Histórico": `.production-display-compact` padding 1.25rem + `.display-4` interno a 2.75rem en mobile.
  4. [x] **No planeado — bug real encontrado al verificar**: `.bg-cva-brown` (global.css) no tenía `!important`, así que `.admin-card-v2 { background: white; }` (admin-panel.css, carga después) le ganaba en cascada — la tarjeta "Rendimiento Histórico" se veía **blanca en vez de marrón, con su párrafo descriptivo invisible** (texto blanco sobre fondo blanco). Bug de **desktop y mobile por igual**, no específico de esta iniciativa, pero descubierto acá. Se agregó `!important` (mismo criterio que `.text-gold`, que ya lo tenía). Beneficia también a `perfil_config.php` (modal) y a `navbar.php`/`beneficios.php` (sitio público), que comparten la misma clase.
  5. [x] **Otro hallazgo menor en el mismo bloque**: `bg-opacity-5` en el panel interior del número "142" no es una clase válida de Bootstrap (los pasos son 10/25/50/75/100) — el panel se veía sólido blanco en vez de un panel translúcido "vidrio esmerilado". Cambiado a `bg-opacity-10` (el paso más chico real, consistente con el `border-opacity-10` ya usado ahí mismo).
- Criterio de hecho: repro 375px de Dashboard antes/después — tarjeta de rendimiento ahora se ve marrón con texto legible y panel translúcido coherente; KPIs visiblemente más compactos. Detalle en `apply-progress.md`.

## Fase 2 — Ventas (listado + detalle) [x]

- Sequential, depende de Fase 0.
- Archivos: `admin-sales.css`, `gestion_pedido_admin.php`.
- Sub-tareas:
  1. [x] Re-exploradas ambas vistas con repro estático fiel (fila de `order-row`/`solicitud-row` con avatar+badge+botones, y el bloque de aprobación pendiente).
  2. [x] `detalleVentas.php` (listado): las filas de tabla (order-row/solicitud-row) ya quedaban bien tras la Fase 0 (avatar circular correcto, botones 36px/32px) — verificado, sin cambios adicionales necesarios. Los 4 KPIs del listado usan un componente más liviano (`admin-card-v2` + `h3`/`h4`, ícono oculto bajo 576px) que ya es compacto por diseño, no el mismo `kpi-card-premium` del Dashboard — no se tocó.
  3. [x] **Bug real encontrado al verificar (no estaba en el plan)**: el bloque "Pedido Pendiente de Aprobación" (`gestion_pedido_admin.php`) tenía dos botones (`ACEPTAR Y EMPEZAR OBRA` / `RECHAZAR PEDIDO`) con `px-5 py-3 rounded-pill` uno al lado del otro sin wrap — en 375px el texto largo se partía en 4 líneas dentro de un pill angosto, deformando el botón en un óvalo/círculo con texto desbordando el borde. Se creó `.approval-actions`/`.btn-approval` para apilarlos a ancho completo en `≤575.98px` (y mantener el layout side-by-side original en desktop).
  4. [x] **Otro hallazgo menor en la misma página**: el ícono decorativo de billetera en la tarjeta financiera usaba `opacity-05` (clase inexistente en Bootstrap, que solo define 0/25/50/75/100) — el watermark tenue pensado por el diseño se veía a opacidad completa. Movido a `opacity: 0.05` directo en `.wallet-icon-bg` (su clase propia).
- Criterio de hecho: repro 375px y desktop (900px) del bloque de aprobación antes/después — ya no se deforma en mobile y el layout desktop original se mantiene intacto. Detalle en `apply-progress.md`.

## Fase 3 — Catálogo (Productos + Categorías) [x]

- Sequential, depende de Fase 0.
- Sub-tareas:
  1. [x] Re-exploradas ambas vistas con repros fieles: fila de producto (imagen+título+badge, columna GESTIÓN de 3 botones full-width), barra de filtros (buscador+categoría+reset), fila de categoría (avatar+3 botones ícono).
  2. [x] La imagen de producto (`product-img-container img`, clase `product-thumb-80`) **ya quedó resuelta por la Fase 0** — el override "muerto" de 80px que se había limpiado del CSS en esa fase confirma que el tamaño real ya es 50px (medido: contenedor + `<img>` a 50×50px, título ya no se superpone con el badge de ID).
  3. [x] Barra de filtros: verificada tal cual está — buscador full-width, categoría+reset en la misma fila (`col-6`/`col-6`), se ve prolija y compacta a 375px, **sin cambios necesarios**.
  4. [x] Botones de acción de fila: en Productos (texto+ícono, apilados `w-100`, ya con altura 36px por Fase 0) y en Categorías (solo ícono, fila centrada, mismo patrón ya verificado en Ventas) — ambos se ven bien, **sin cambios necesarios**. No se forzó el rediseño a "fila compacta de íconos" planteado como posible en la propuesta: el apilado actual ya es legible y no está roto, cambiarlo sería una decisión de gusto sin un defecto concreto detrás.
- Criterio de hecho: repros 375px de fila de producto, filtro y fila de categoría — todo correcto tras la Fase 0, **no se necesitó ningún cambio de código adicional en esta fase** (a diferencia de las Fases 1 y 2, acá la verificación no encontró bugs nuevos). Detalle en `apply-progress.md`.

## Fase 4 — Usuarios [x]

- Sequential, depende de Fase 0.
- Archivo: `admin-panel.css` (el bug encontrado está en el motor de tarjetas compartido, no en `admin-users.css`).
- Sub-tareas:
  1. [x] Re-explorada la vista con repros fieles: barra de filtros (buscador+perfil+reset) y fila de usuario, esta última probada a propósito con datos "peor caso" (nombre largo, usuario y email largos) en vez de datos cortos — fue justamente lo que reveló el bug.
  2. [x] Filtros: verificados tal cual están, se ven prolijos a 375px, sin cambios necesarios.
  3. [x] **Bug real encontrado al verificar (no estaba en el plan)**: la celda `ACCESO` (usuario + email) no usa el `::before` genérico como única etiqueta — tiene 2 divs propios con `width:100%`, pero el `<td>` genérico ya es `display:flex` en fila (herencia del motor de tarjetas), así que esos divs competían por espacio en una fila en vez de apilarse — con un email medianamente largo (nada extremo, un caso muy común en datos reales), el texto se salía de la tarjeta hacia la derecha, fuera de la pantalla. Se agregó `flex-direction:column` + `align-items:flex-end` a `.user-row td[data-label="ACCESO"]` y `word-break:break-word` a `.user-access-info` (por si aparece un string aún más largo sin espacios).
- Criterio de hecho: repro 375px con datos largos antes/después — el texto ya no se sale de la tarjeta, queda apilado y alineado a la derecha como el resto de la celda. El cambio vive en el `@media` mobile del motor compartido, así que no toca desktop en absoluto (no aplica fuera de `≤991.98px`). Detalle en `apply-progress.md`.

## Fase 5 — QA cruzado (manual, no delegable) [ ]

- Sequential, al final, después de las 5 fases anteriores.
- Verificaciones en dispositivo real:
  1. Las 5 pantallas se sienten consistentes entre sí (mismo tamaño de ícono de encabezado, avatar/imagen, botón de acción).
  2. Nada se rompió en desktop (`≥992px`).
  3. Con datos reales (nombres largos, montos grandes, muchos ítems) el layout se sostiene.
- Criterio de hecho: confirmación del usuario en dispositivo real.

## Review Workload Forecast

- **Alcance total**: no estimado en líneas todavía — se calcula al iniciar cada fase, tras la re-exploración puntual de esa pantalla (mismo criterio que se usó para todo el relevamiento de este documento).
- **Riesgo por fase**: Fase 0 es la de mayor radio de explosión (toca una regla compartida por 4-5 pantallas) pero de bajo riesgo técnico (son valores de tamaño, no lógica); Fases 1-4 son bajo riesgo y aisladas entre sí; Fase 5 es 100% manual.
- **Delivery strategy = fase por fase**: se entrega y se muestra cada fase (capturas antes/después) antes de arrancar la siguiente, para poder ajustar el rumbo si el usuario quiere algo distinto a mitad de camino.

## Key Learnings

1. Este change tampoco tiene `spec.md`/`design.md` — son ajustes de tamaño CSS, sin cambio de capability ni decisiones de arquitectura (mismo criterio que `admin-panel-mobile-navbar-fix` y `refinamiento-mobile-home`).
2. Fase 0 se separó del resto porque toca una regla compartida por varias pantallas — agruparla con una fase específica hubiera obligado a re-tocar el mismo selector 4 veces o a que una fase "cargara" con un cambio que en realidad beneficia a todas.
