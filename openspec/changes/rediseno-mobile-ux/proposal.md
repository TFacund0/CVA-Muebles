# Proposal: Rediseño mobile UX (navbar, botones, tarjetas, íconos, galería)

## Intent

A 375-390px el sitio se ve desproporcionado: el navbar mobile no muestra la marca (el texto existe pero está apagado con `d-none d-lg-block`), los botones globales conservan padding de desktop, las tarjetas de producto tienen un único escalón mobile (991.98px) que cubre desde 320px, y varios íconos/badges y la imagen de galería tienen tamaños fijos sin ninguna cobertura responsive. El usuario ya confirmó la dirección de diseño del navbar: patrón "logo a la izquierda, estilo tienda".

## Scope

### In Scope

1. **Navbar mobile**: reordenar celdas a logo+texto de marca a la izquierda, carrito+hamburguesa a la derecha; definir el nuevo layout flex (texto de ancho variable vs. iconos de ancho fijo) y tamaños mobile de `.titulo-logo`, `.logo-img-nav`, `.boton-icon-circle`.
2. **Botones globales**: agregar cobertura mobile a `.btn-artisan`, `.btn-vivid`, `.btn-premium-action`, `.btn-gold` en `global.css` (hoy sin ningún `@media`), reconciliando con overrides existentes en `contacto.css:156` y `comercializacion.css:202`.
3. **Escalón 575.98px** en `productos.css` con valores menores a los del 991.98px existente.
4. **Íconos/badges**: `.badge-logistica` (50x50), `.icon-badge-gold` (50x50), `.empty-gallery-icon`/`.gallery-empty-icon` (5rem) — confirmar en las vistas cuál de las dos clases de galería vive antes de tocar.
5. **Altura de imagen de galería**: `.gallery-item img { height: 320px }` ajustada en el layout de 1 columna.

### Out of Scope

- Filtros de productos (ya resueltos en 991.98px). Posible seguimiento solo si tras la fase 3 el usuario confirma que persiste.
- Cualquier cambio de lógica PHP.
- Grupos 2/3/5 pendientes del change `optimizacion-mobile-imagenes` (curso independiente).

## Capabilities

### New Capabilities

- `mobile-navbar-layout`: estructura y tamaños del navbar en viewports mobile.
- `global-button-responsiveness`: escalado mobile de los botones compartidos.
- `product-card-small-viewport`: segundo escalón de tarjeta de producto en 575.98px.
- `mobile-icon-scaling`: reducción mobile de badges e íconos de estado vacío.
- `gallery-image-aspect`: altura de imagen de galería según columnas.

### Modified Capabilities

None — no existen specs previas de estas áreas.

## Approach

Seis fases en orden de menor a mayor superficie, alineadas con la exploración. Fases 2-5 son CSS aditivo dentro de bloques `@media` (991.98px y/o 575.98px) siguiendo los breakpoints ya usados en el proyecto. La fase 1 es la única con cambio de markup: reestructuración del orden de celdas en `navbar.php` más un layout flex nuevo en `main-layout.css`. Antes de escribir reglas de botones se auditan los overrides de página existentes para decidir override centralizado vs. ajuste local, evitando duplicación y guerras de `!important`.

## Affected Areas

| Área | Impacto | Descripción |
|------|---------|-------------|
| `app/Views/partials/navbar.php` | Modified | Reordenar celdas mobile; exponer texto de marca |
| `public/assets/css/layout/main-layout.css` | Modified | Layout flex mobile, `.titulo-logo`, `.logo-img-nav`, `.boton-icon-circle` |
| `public/assets/css/base/global.css` | Modified | Primer `@media` del archivo para los 4 botones |
| `public/assets/css/pages/productos.css` | Modified | Nuevo `@media (max-width: 575.98px)` |
| `public/assets/css/pages/comercializacion.css` | Modified | `.badge-logistica` mobile |
| `public/assets/css/pages/galeria.css` | Modified | `.icon-badge-gold`, ícono vacío, altura de `.gallery-item img` |

## Risks

| Riesgo | Probabilidad | Mitigación |
|--------|--------------|------------|
| El nuevo navbar rompe la simetría de 3 celdas que hoy centra el logo | Alta | Definir el layout flex explícitamente en design; validar a 320/375/390px |
| Conflicto de especificidad con overrides de página que usan `!important` | Media | Auditar `contacto.css`/`comercializacion.css` antes de escribir; reconciliar, no duplicar |
| Eliminar la clase de ícono vacío equivocada (`.empty-gallery-icon` vs `.gallery-empty-icon`) | Media | Confirmar uso real en las vistas; ante la duda, cubrir ambas sin borrar ninguna |
| Regresión visual en desktop | Baja | Todo el CSS nuevo vive dentro de `@media max-width`; sin cambios en reglas base |

No toca checkout, carrito, migraciones ni `Config/Database.php`.

## Rollback Plan

Revertir el commit del change. Todos los cambios son CSS aditivo dentro de `@media` más una reestructuración de markup acotada a `navbar.php`; no hay migraciones, estado persistido ni cambios de datos, así que el revert es total e inmediato. Por fase: eliminar el bloque `@media` agregado en el archivo correspondiente.

## Dependencies

- Bootstrap 5.3.3 vendorizado local (sin cambio de versión).
- Breakpoints del proyecto: 991.98px y 575.98px.

## Success Criteria

- [ ] A 375px el navbar muestra el texto de marca legible, sin desbordar ni superponerse con carrito/hamburguesa.
- [ ] Los 4 botones globales tienen padding/font-size reducidos en al menos un breakpoint mobile, sin romper los overrides ya existentes de `contacto.css` y `comercializacion.css`.
- [ ] `productos.css` tiene un `@media (max-width: 575.98px)` con valores estrictamente menores a los del bloque 991.98px.
- [ ] `.badge-logistica`, `.icon-badge-gold` y el ícono de galería vacía tienen valores reducidos en mobile.
- [ ] La imagen de galería no supera ~220-240px de alto en el layout de 1 columna.
- [ ] Sin regresión visual en desktop (≥992px) en home, productos, comercialización, contacto y galería.

## Proposal question round

Preguntas abiertas para el usuario (responder, corregir o saltear):

1. ¿El texto "CVA Muebles" debe verse completo a 320px, o se acepta truncado/una sola línea reducida en los teléfonos más chicos?
2. Para los botones globales, ¿preferís un único escalón (991.98px) o dos (991.98 + 575.98) alineados con las tarjetas?
3. Si `.empty-gallery-icon` y `.gallery-empty-icon` resultan ambas vivas, ¿querés unificarlas en este change o dejar la limpieza para después?
4. ¿La altura de galería debe ser fija (~220px) o proporcional (`aspect-ratio`) en mobile?

Supuestos vigentes si no hay respuesta: una sola línea con truncado permitido a 320px; dos escalones para botones; sin borrar clases duplicadas; altura fija de 220px.

## Decisions (user-confirmed)

- Texto de marca en navbar: trunca (ellipsis) si no entra a 320px; no se prioriza mostrar el nombre completo por sobre romper el layout.
- Botones globales: dos escalones, `991.98px` y `575.98px`, mismo patrón que las tarjetas de producto.
- `.empty-gallery-icon` / `.gallery-empty-icon`: se cubren ambas con la regla mobile sin borrar ninguna; la limpieza del duplicado queda fuera de este change.
- Altura de imagen en galería: fija en mobile (~220px), no `aspect-ratio`.
