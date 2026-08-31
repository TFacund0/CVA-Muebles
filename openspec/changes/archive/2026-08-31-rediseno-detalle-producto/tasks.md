# Tasks: Rediseño de la Página de Detalle de Producto

Input: `proposal.md`, `design.md`, `specs/catalog-navigation-state/spec.md`.

## 0. Verificación de riesgos (bloqueante, antes de tocar código)

- [x] **0.1** Confirmar R1 (categoría renombrada en DB con tab de detalle abierto → fallback silencioso a "Todos") como comportamiento **aceptado**, no un bug a arreglar. No requiere código; solo decisión registrada. Ref: `design.md` Open Questions R1.
- [x] **0.2** Verificar R3: correr manualmente (o con un snippet temporal en consola) `productos.php:45–49` (de-dup por `descripcion.toLowerCase()`) contra `productos.php:61` (`data-categorias`) y confirmar que ninguna categoría real de la DB actual produce colisión de mayúsculas/espacios. Documentar el resultado en el PR; si hay colisión, es **pre-existente y fuera de alcance** (no arreglar en esta change), solo debe quedar constatado antes de cablear el matching sobre el mismo dato.
- [x] **0.3** Verificar R4: confirmar en el código actual de `productos.js` (línea ~93/97, comparación `===` sobre `data-categorias`) que hoy solo existe **una** categoría por producto en la DB real (no multi-categoría). Si se confirma, seguir usando `===`/single-value tal cual (D4 en `design.md`); si aparece un producto multi-categoría, marcarlo como riesgo pre-existente fuera de alcance, no bloqueante para esta tarea.
- [x] **0.4** Registrar versiones actuales de assets antes de editar (re-verificar, no asumir del design): `productos.js?v=`, `detalle_producto.css?v=`, `detalle-producto.js?v=`. Anotar los valores exactos leídos del código en este momento (pueden haber cambiado desde `design.md`).

## 1. Hook `data-producto-id` session-independent (`app/Views/components/product_card.php`)

- [x] **1.1** Agregar `data-producto-id="<?= esc($producto['id_producto']) ?>"` al contenedor `.product-card` (no solo en `.btn-fav-artisan`, que hoy depende de sesión). Ref: `design.md` Interfaces §1.
- [x] **1.2** Agregar clase `js-ver-detalles` al `<a>` "VER DETALLES". Confirmar que `mis_favoritos.php` (que reusa este componente) no tiene `#lista-productos`, por lo que el nuevo listener delegado no se activa ahí (verificación, no cambio de código).
- Satisface: spec Requirement "Detail link carries origin filter state" (precondición del hook).
- Paralelizable: sí, independiente de fase 2 en términos de escritura, pero fase 2 depende de que esto exista para probar end-to-end.

## 2. Delegación de evento + armado de query params al click (`public/assets/js/pages/productos.js`)

- [x] **2.1** Extraer `aplicarFiltro(categoria, opts)` reusable desde el handler inline actual (líneas ~88–114): toggle `.active`, loop de `display`, sync `.filtro-activo-label`; `opts.cerrarOffcanvas` opcional y no ejecutado en el replay de carga. Ref: `design.md` Interfaces §2.
- [x] **2.2** Reconectar los listeners de `.filtro-categoria` para llamar a `aplicarFiltro(btn.dataset.categoria, { cerrarOffcanvas: true })`.
- [x] **2.3** Agregar listener delegado en `#lista-productos` sobre `click`, filtrando `a.js-ver-detalles`; leer categoría activa (`.filtro-categoria.active`), leer `data-producto-id` del card más cercano, mutar `link.href` con `URL`/`searchParams.set('from_categoria', …)` y `set('from_id', …)` sin `preventDefault()`. Ref: `design.md` Interfaces §3, D5.
- Satisface: spec Requirement "Detail link carries origin filter state" (ambos escenarios: con filtro activo y con "Todos").
- Secuencial respecto a fase 1 (necesita el hook). Puede ejecutarse en la misma sesión que fase 3 (mismo archivo).

## 3. Extracción de replay + lectura de `URLSearchParams` en `DOMContentLoaded`

- [x] **3.1** En el bloque de carga de `productos.js`, leer `categoria`/`from_categoria` y `producto`/`from_id` de `location.search` (alias, D3 en `design.md`).
- [x] **3.2** Si hay categoría, llamar `aplicarFiltro(catVuelta)` (sin `cerrarOffcanvas`); fallback silencioso si `aplicarFiltro` devuelve `null` (categoría desconocida).
- [x] **3.3** Si hay id, buscar `#lista-productos [data-producto-id="..."]` con `CSS.escape`, confirmar que la columna padre no tiene `display:none`, y solo entonces continuar a fase 4.
- Satisface: spec Requirement "Detail page restores filter and scroll on return" + "Missing or invalid params fall back to normal behavior" (todos los escenarios de fallback).
- Secuencial respecto a fase 2 (mismo archivo, mismo flujo lógico). No paralelizable con fase 2.

## 4. Scroll anchor con `scrollIntoView` + guard AOS

- [x] **4.1** Implementar el guard de doble `requestAnimationFrame` antes de `card.scrollIntoView({ block: 'center', behavior: 'auto' })`, ejecutado solo si la card está visible (resultado de 3.3). Ref: `design.md` Interfaces §4, R2.
- [x] **4.2** Verificación manual de posible drift por AOS `fade-up` (R2 en Open Questions). Si se detecta drift en el E2E manual (fase 8), aplicar el fallback documentado en `design.md` (deshabilitar AOS en cards cuando `from_id` está presente), **no** agregar un `setTimeout`.
- Satisface: spec Requirement "Detail page restores filter and scroll on return", escenario "Scroll only after filter application".
- Secuencial respecto a fase 3 (depende de su resultado).

## 5. Botón/breadcrumb "volver" (`app/Views/front/pages/detalle_producto.php`)

- [x] **5.1** Agregar bloque PHP al inicio de la sección de contenido: leer `from_categoria`/`from_id` vía `service('request')->getGet()`, armar `$urlVolver` con `http_build_query` filtrando vacíos (`array_filter(..., 'strlen')`). Ref: `design.md` Interfaces §5.
- [x] **5.2** Cambiar el `href` del breadcrumb "CATÁLOGO" (línea ~17) de `base_url('productos')` a `esc($urlVolver)`.
- [x] **5.3** Agregar el botón dedicado `<a class="btn btn-volver-catalogo">` con `esc($urlVolver)`, inmediatamente debajo del breadcrumb, dentro del mismo `.container`, antes de `.main-artisan-card`.
- Satisface: spec Requirement "Detail page restores filter and scroll on return" (ambos escenarios: breadcrumb y botón dedicado) + "Missing or invalid params fall back to normal behavior".
- Paralelizable con fases 2–4 (archivo distinto); secuencial respecto a fase 1 solo en cuanto a testing conjunto.

## 6. Rediseño visual de trust-badges / description-box / features-list

- [x] **6.1** `trust-badges`: reemplazar emojis por `.icon-wrapper` + `bi-truck` / `bi-shield-check` / `bi-tree`; agregar header de sección con `kicker-spacing-2`, `text-vivid`, `font-lora`, `divider-artisan`. Ref: `design.md` Section Redesign Markup.
- [x] **6.2** `description-box`: quitar `border: 1px dashed …`; aplicar `border: 1px solid rgba(62,39,35,.08)` + sombra de card del sitio; adoptar patrón kicker+`font-lora`.
- [x] **6.3** `features-list`: mantener contenido hardcodeado (decisión D7, confirmada en proposal), restylear geometría `.feature-item`/`.feature-icon` (ya usa `bi-*`), agregar header kicker y ajustar `h6` a `font-lora`.
- [x] **6.4** Aplicar los deltas de CSS en `public/assets/css/pages/detalle_producto.css` según la tabla de `design.md` (`.badge-icon-wrap`, `.description-box`, `.features-list`), incluyendo el override mobile de 48px.
- [x] **6.5** Confirmar explícitamente que `.actions-area` y todo lo que está debajo (líneas ~96–142) queda intacto: mismo markup, mismo `csrf_field()`, mismos `submit` targets.
- Satisface: spec Requirement "Visual realignment preserves actions-area behavior" (ambos escenarios).
- Paralelizable con fases 2–5 (archivos disjuntos: CSS + porción de vista no relacionada con `$urlVolver`).

## 7. Cache-busting (R5, obligatorio en el mismo commit que cada archivo tocado)

- [x] **7.1** Bump `productos.js?v=` (valor leído en 0.4, incrementar).
- [x] **7.2** Bump `detalle_producto.css?v=` (valor leído en 0.4, incrementar).
- [x] **7.3** Bump `detalle-producto.js?v=` **solo si** ese archivo fue tocado (confirmar en 0.4 si aplica; según `design.md` el JS de detalle no tiene cambios funcionales previstos, pero si se modifica por consistencia visual, bumpear).
- [x] **7.4** `product_card.php` no tiene tag de versión propio (se sirve inline vía el layout que lo incluye) — confirmar que no hace falta bump adicional; si el layout referencia una versión de assets compartida, verificar que ya quedó cubierta por 7.1/7.2.
- Satisface: spec Requirement "Cache-busting on touched assets" (escenario "Asset version bump").
- Secuencial: debe ser la última fase de código, después de que todos los archivos de fases 1–6 estén finalizados en esa sesión.

## 8. Verificación automatizada / tests (PHPUnit, CI-enforced, strict_tdd)

- [x] **8.1** Feature test PHPUnit sobre `producto/detalle/{id}` que asserte el `href` renderizado de `$urlVolver` en 4 casos: ambos params, solo `from_categoria`, solo `from_id`, ninguno. Escribir el test **antes** de fase 5.1 (strict_tdd). Ref: `design.md` Testing Strategy.
- [x] **8.2** Test PHPUnit con categoría conteniendo espacios/acentos (ej. "Mesas y Sillas") para confirmar `http_build_query`/`esc()` round-trip seguro.
- [x] **8.3** Test de string assertion: no queda ningún carácter emoji en `detalle_producto.php` renderizado.
- [x] **8.4** Test de string assertion: no queda ninguna regla `dashed` en `detalle_producto.css`.
- Satisface: spec Requirement "Visual realignment preserves actions-area behavior" (escenario "No emoji or dashed border remains") + soporte de "Detail page restores filter and scroll on return".
- Secuencial respecto a fases 5 y 6 en cuanto a ejecución final, pero los tests en sí deben escribirse antes (TDD) del código que verifican.

## 9. Verificación manual (no automatizable — sin runner de JS en el proyecto)

- [ ] **9.1** E2E completo del round trip: filtrar por categoría → abrir detalle → volver por breadcrumb → volver por botón dedicado. Confirmar filtro reaplicado + card en viewport.
- [ ] **9.2** Confirmar `/productos` sin params se comporta exactamente igual que hoy.
- [ ] **9.3** Entrada sin contexto de filtro: abrir detalle desde `mis_favoritos.php` (o URL directa) y confirmar que "volver" cae a catálogo sin filtrar, sin error de consola.
- [ ] **9.4** Categoría inexistente/renombrada (`?from_categoria=descontinuada`) → catálogo cae a "Todos" sin excepción JS.
- [ ] **9.5** `from_id` inexistente/removido → no hay scroll, no hay error.
- [ ] **9.6** Verificar drift de AOS (R2): si aparece, aplicar el fallback de deshabilitar AOS en return navigation (no `setTimeout`), documentado en fase 4.2.
- [ ] **9.7** Verificar las 6 combinaciones de `.actions-area` (sesión logueada/no × cart habilitado/no × stock >0/=0): markup, CSRF y submit sin cambios.
- [ ] **9.8** Verificación real-device con cache tibia (`?v=` bump) para confirmar que el bump de fase 7 efectivamente evita servir CSS/JS viejo.
- Satisface: spec Requirement completo (todos), como regresión final antes de merge.
- Secuencial: última fase, depende de todo lo anterior.

## Review Workload Forecast

| Fase | Archivo(s) | Líneas estimadas |
|---|---|---|
| 0 | ninguno (verificación) | 0 |
| 1 | `product_card.php` | ~4 |
| 2 | `productos.js` | ~35 |
| 3 | `productos.js` | ~15 |
| 4 | `productos.js` | ~10 |
| 5 | `detalle_producto.php` | ~20 |
| 6 | `detalle_producto.php` + `detalle_producto.css` | ~90 |
| 7 | `productos.js`, `detalle_producto.css`, `detalle_producto.php` (tags `<link>`/`<script>`) | ~4 |
| 8 | nuevo archivo de test PHPUnit | ~60 |
| 9 | ninguno (manual) | 0 |
| **Total** | | **~238 líneas** |

- Presupuesto acordado: 400 líneas. Total estimado (~238) queda dentro del presupuesto con margen (~40%).
- Estrategia `ask-on-risk`: dado que el total no se acerca al límite y las fases 1–5 (round-trip) y fase 6 (visual) tocan archivos disjuntos según `design.md` ("dos mitades disjuntas, revertibles independientemente"), **se recomienda un único PR** en lugar de encadenar PRs — no hay necesidad de partir el review porque ningún archivo individual supera ~90 líneas de delta y el presupuesto tiene margen amplio.
- Si en el E2E manual (fase 9.6) se activa el fallback de deshabilitar AOS, eso añade ~5–10 líneas extra a `productos.js`, sin romper el presupuesto.
- **Decisión requerida antes de aplicar**: ninguna decisión de producto pendiente (proposal.md ya cerró las 3 preguntas con "Assumed"); solo falta ejecutar y registrar el resultado de las verificaciones de fase 0 (R1/R3/R4) antes de escribir código de fases 2–4, porque esas verificaciones pueden confirmar o descartar si el matching de categorías necesita un ajuste antes de cablear el filtro.

## Key Learnings

1. El round trip (fases 1–5) y el rediseño visual (fase 6) son mitades disjuntas por archivo, confirmando la recomendación de `proposal.md`/`design.md` de que pueden revertirse independientemente — esto también permite ejecutarlas en paralelo dentro de la misma sesión sin conflicto de merge.
2. Los riesgos R1/R3/R4 no requieren código correctivo: son verificaciones de que el comportamiento actual (pre-existente) no empeora con el round trip, y deben quedar documentadas como aceptadas antes de fase 2, no como bugs a resolver.
3. R5 (cache-busting) se modela como su propia fase final (7) en lugar de repetirlo inline en cada fase, para forzar una revisión explícita de "¿qué toqué y bumpeé todo?" antes del commit.
4. `strict_tdd: true` obliga a que los tests de fase 8 (especialmente 8.1, `$urlVolver`) se escriban antes que el código PHP de fase 5.1, no después.
