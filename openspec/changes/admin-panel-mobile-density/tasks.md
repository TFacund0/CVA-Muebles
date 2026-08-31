# Tasks: Densidad Mobile del Panel Admin

Backend: manual (mismo criterio que `admin-panel-mobile-navbar-fix`, sin CLI de OpenSpec/Engram disponible). Delivery strategy: fase por fase — cada fase se implementa, se verifica (repro estático 375px) y se entrega en un commit separado antes de pasar a la siguiente. Presupuesto: sin definir aún por fase (se estima al empezar cada una, tras re-explorar esa pantalla puntualmente).

## Fase 0 — Fundación compartida [ ]

- Sequential, primero (beneficia a las 5 pantallas, evita duplicar el mismo ajuste 4 veces).
- Archivos: `admin-panel.css` (agregar/mover override de `.dashboard-icon-main` al bloque `@media max-width:767.98px` o `991.98px` existente), `admin-products.css` (quitar el override duplicado).
- Sub-tareas:
  1. Mover `.dashboard-icon-main { width: 50px; height: 50px; font-size: 1.5rem; border-radius: 0.8rem; }` de `admin-products.css:35` a un `@media` de `admin-panel.css` (evaluar si usa `767.98px` como hoy, o el `991.98px` ya existente ahí — decidir al implementar según cómo se vea en 768-991px).
  2. Revisar tamaño de avatar/imagen de fila compartido (`admin-panel.css:589-596`, hoy 60px) y del ícono de acción (`.btn-action-premium`, 40px) — bajar a un valor compacto sin perder área táctil mínima (~36-40px táctil aunque el ícono visual se vea más chico).
  3. Bump de cache-busting en los `<link>` de los CSS tocados, siguiendo la convención del proyecto.
- Criterio de hecho: repro estático 375px de Dashboard, Ventas (listado) y Usuarios muestra el ícono de encabezado ya achicado (antes no se achicaba en esas 3); Productos/Categorías se ven igual que antes (sin regresión); avatares/imágenes de fila visiblemente más compactos en las 4 pantallas con tabla, sin salirse de un tamaño tocable razonable.

## Fase 1 — Dashboard [ ]

- Sequential, depende de Fase 0 (recibe el fix del ícono de encabezado gratis).
- Archivo: `admin-sales.css` (bloque `@media max-width:767.98px` existente, líneas 166-182), posible markup en `estadisticas.php` si se necesita alguna clase nueva para la tarjeta de rendimiento.
- Sub-tareas:
  1. Re-explorar la pantalla puntualmente (medir con el repro estático a 375px lo que queda después de Fase 0, para no adivinar).
  2. Afinar `kpi-value`/`kpi-icon-container` de las 4 tarjetas de producción si siguen sintiéndose grandes.
  3. Agregar tratamiento mobile a la tarjeta "Rendimiento Histórico" (`display-4` sin override hoy).
- Criterio de hecho: repro 375px de Dashboard antes/después, confirmando reducción visible sin romper el layout en desktop.

## Fase 2 — Ventas (listado + detalle) [ ]

- Sequential, depende de Fase 0.
- Archivos: `admin-sales.css`, posible markup en `detalleVentas.php`/`gestion_pedido_admin.php`.
- Sub-tareas:
  1. Re-explorar ambas vistas puntualmente.
  2. Aplicar a los 4 KPIs de `detalleVentas.php` el mismo tratamiento que Dashboard (mismo patrón de componente, mismo ajuste).
  3. Compactar en el detalle de pedido (`gestion_pedido_admin.php`) el bloque de aprobación pendiente y el stepper de producción para mobile.
- Criterio de hecho: repro 375px de listado y detalle antes/después.

## Fase 3 — Catálogo (Productos + Categorías) [ ]

- Sequential, depende de Fase 0.
- Archivos: `admin-products.css`, posible markup en `crud_productos.php`/`crud_categorias.php`.
- Sub-tareas:
  1. Re-explorar ambas vistas puntualmente.
  2. Corregir el override de imagen de producto que hoy no reduce nada en mobile (`admin-products.css:115-118`, sigue en 80px) — bajar a un tamaño más chico.
  3. Revisar filtros y botones de acción de fila (evaluar fila compacta de íconos en vez de apilado full-width donde el ancho lo permita).
- Criterio de hecho: repro 375px de ambas pantallas antes/después; imágenes de producto visiblemente más chicas.

## Fase 4 — Usuarios [ ]

- Sequential, depende de Fase 0.
- Archivo: `admin-users.css` si hace falta algo puntual además de lo heredado de Fase 0.
- Sub-tareas:
  1. Re-explorar la vista puntualmente tras Fase 0.
  2. Ajustes específicos si quedó algo pendiente (badges, `.user-access-info`).
- Criterio de hecho: repro 375px antes/después; consistencia visual con Ventas/Productos (mismo tamaño de avatar/ícono).

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
