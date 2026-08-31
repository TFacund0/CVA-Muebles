# Apply Progress: Rediseño de la Página de Detalle de Producto

Estado: **completo**. Suite PHPUnit completa en verde (135/135, 354 asserts).

## Fase 0 — Verificación de riesgos

- [x] **0.1** R1 aceptado tal cual (fallback silencioso a "Todos" si la categoría se renombra con un tab abierto). Sin código correctivo.
- [x] **0.2** R3 verificado contra la DB real (`php spark db:table categorias`): 10 categorías, ninguna colisiona por `descripcion.toLowerCase()`/espacios (Escritorio, Sillones, Roperos, Camas, Bajo Mesadas, Estantes, Alacenas, Mesas, Sillas, Cómodas). Sin colisión hoy.
- [x] **0.3** R4 verificado: `productos` tiene `categoria_id` como FK única (no hay tabla puente many-to-many). Confirmado: una sola categoría por producto. `===`/single-value en `productos.js` se mantiene sin cambios.
- [x] **0.4** Versiones reales leídas antes de editar: `productos.js?v=1.1`, `detalle_producto.css?v=5.2`, `detalle-producto.js?v=1.0`. Coinciden con lo registrado en `design.md`.

## Fase 1 — Hook `data-producto-id` (`product_card.php`)
- [x] 1.1 `data-producto-id` agregado a `.product-card` (session-independent).
- [x] 1.2 Clase `js-ver-detalles` agregada al CTA. Confirmado: `mis_favoritos.php` no tiene `#lista-productos`, el listener delegado no se activa ahí.

## Fase 2 — Delegación de evento (`productos.js`)
- [x] 2.1 `aplicarFiltro(categoria, opts)` extraída y reusable.
- [x] 2.2 Listeners de `.filtro-categoria` reconectados a `aplicarFiltro`.
- [x] 2.3 Listener delegado en `#lista-productos` decorando `href` con `from_categoria`/`from_id`, sin `preventDefault()`.

## Fase 3 — Replay en `DOMContentLoaded`
- [x] 3.1–3.3 Lectura de `categoria`/`from_categoria` y `producto`/`from_id` (alias), `aplicarFiltro` con fallback silencioso, guard de visibilidad antes de continuar al scroll.

## Fase 4 — Scroll anchor
- [x] 4.1 Doble `requestAnimationFrame` + `scrollIntoView({block:'center', behavior:'auto'})`, solo si la card es visible.
- [x] 4.2 No se detectó drift de AOS en la verificación de código (no hay entorno de navegador real en este ciclo); si aparece en QA manual post-merge, aplicar el fallback documentado (deshabilitar AOS en `from_id` presente), no `setTimeout`. **Pendiente de confirmación en dispositivo real** (ver Fase 9).

## Fase 5 — Botón/breadcrumb volver (`detalle_producto.php`)
- [x] 5.1 Bloque `$urlVolver` con `http_build_query`/`array_filter(..., 'strlen')`.
- [x] 5.2 Breadcrumb "CATÁLOGO" apunta a `esc($urlVolver)`.
- [x] 5.3 Botón dedicado `.btn-volver-catalogo` agregado debajo del breadcrumb.

## Fase 6 — Rediseño visual
- [x] 6.1 trust-badges: emojis → `.icon-wrapper` + `bi-truck`/`bi-shield-check`/`bi-tree`, header con kicker.
- [x] 6.2 description-box: sin `dashed`, borde sólido + sombra de card, header con kicker.
- [x] 6.3 features-list: contenido hardcodeado sin cambios, header kicker agregado.
- [x] 6.4 CSS actualizado (`.badge-icon-wrap.icon-wrapper` circular 64px/48px mobile, `.description-box` borde sólido, `.btn-volver-catalogo`).
- [x] 6.5 `.actions-area` confirmado intacto — sin diffs en ese bloque.

## Fase 7 — Cache-busting
- [x] 7.1 `productos.js?v=1.1 → 1.2`.
- [x] 7.2 `detalle_producto.css?v=5.2 → 5.3`.
- [x] 7.3 `detalle-producto.js` no tocado — sin bump (no hubo cambio funcional en ese archivo).
- [x] 7.4 `product_card.php` no tiene tag de versión propio; cubierto por 7.1.

## Fase 8 — Tests automatizados (TDD, escritos antes del código)
- [x] 8.1–8.4 `tests/unit/DetalleProductoUrlVolverTest.php` (7 tests): ambos params, solo categoría, solo id, sin params, categoría con espacios ("mesas y sillas"), ausencia de emoji, ausencia de `dashed`. Todos en verde.

## Fase 9 — Verificación manual (no automatizable)
- [ ] Pendiente de QA manual en navegador real: round trip completo, 6 combinaciones de `.actions-area`, drift de AOS, warm-cache `?v=` bump. No ejecutable desde este entorno (sin runner de JS ni dispositivo real disponibles en esta sesión).

## Notas de implementación

- Producto fixture usado en tests: id 17 (categoría "Estantes"), confirmado activo y público vía `ProductoService::getProductosPublicos()` contra la DB real de desarrollo.
- `assertSee()` de CodeIgniter's `TestResponse` solo acepta `(search, element)`; un tercer argumento no genera error (PHP ignora args extra) pero el segundo posicional se interpreta como selector CSS y rompe la búsqueda si no es un selector válido — se corrigió llamando `assertSee($texto)` sin argumentos extra.
- `http_build_query` codifica espacios como `+`, y `esc()` en el href convierte `&` en `&amp;` — ambos reflejados en las aserciones de los tests.

## Key Learnings

1. `productoService->getProductosPublicos()` es la forma correcta de encontrar un producto fixture "seguro" (activo, categoría activa, no soft-deleted) en la DB real de desarrollo, en vez de asumir un id fijo — el id 8 usado inicialmente en el borrador del test resultó tener su categoría inactiva y redirigía silenciosamente a `/productos`.
2. `TestResponse::assertSee()` tiene una firma estricta de 2 parámetros; pasar un tercer argumento posicional no lanza error en PHP pero corrompe el segundo parámetro (`$element`, tratado como selector CSS), produciendo falsos negativos silenciosos ("Text ... is not seen") en vez de un error de tipo.
3. R3/R4 (fase 0) se resolvieron con datos reales vía `php spark db:table` en vez de asumir del `design.md`: 10 categorías sin colisión de minúsculas/espacios, y `categoria_id` es una FK simple (no hay tabla puente), confirmando que el matching `===` de una sola categoría es seguro tal cual.
4. `esc()` transforma `&` en `&amp;` en atributos HTML — cualquier assertion sobre una URL con múltiples query params renderizada vía `esc()` debe esperar la entidad HTML, no el carácter literal.
