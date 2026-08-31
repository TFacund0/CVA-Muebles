# Proposal: Densidad Mobile del Panel Admin (Dashboard, Ventas, Catálogo, Usuarios)

## Intent

Continuación de las mejoras mobile del panel admin. El usuario quiere que Dashboard, Ventas, Productos, Categorías y Usuarios se sientan compactos y rápidos de usar en celular — hoy varios elementos (ícono de encabezado, KPIs, avatares/imágenes de fila, botones de acción) están dimensionados para desktop y no se achican lo suficiente (o no se achican en absoluto) en mobile. Se pide un plan por fases antes de implementar.

## Scope

### In Scope — 5 fases, cada una completable y verificable por separado

**Fase 0 — Fundación compartida (bajo riesgo, beneficia a las 5 pantallas de una vez)**
- Mover el override mobile de `.dashboard-icon-main` (hoy solo en `admin-products.css`) a `admin-panel.css`, junto a la regla base, para que aplique parejo en Dashboard, Ventas y Usuarios (hoy no lo reciben) y se elimine la duplicación en Productos/Categorías.
- Revisar y, si corresponde, bajar el tamaño de avatar/imagen compartido del motor de tarjetas (`admin-panel.css:589-596`, hoy 60px fijo en mobile para las 4 pantallas con tablas) a un valor más compacto (ej. 48-52px).
- Revisar tamaño de `.btn-action-premium` (40px) para mobile.

**Fase 1 — Dashboard** (`estadisticas.php`)
- Afinar `kpi-value`/`kpi-icon-container` de las 4 tarjetas grandes de producción (más allá del `767.98px` ya existente, si el celu real lo pide).
- Compactar la tarjeta "Rendimiento Histórico" (`display-4`, sin override mobile hoy).
- Revisar grid de "Acciones Rápidas" (`action-btn-v2`, ya tiene ajuste parcial).

**Fase 2 — Ventas** (`detalleVentas.php` listado + `gestion_pedido_admin.php` detalle)
- Mismo tratamiento de KPIs que Dashboard (4 tarjetas `col-6 col-md-3`).
- Detalle de pedido: compactar el bloque de aprobación pendiente (`display-4` + botones `px-5 py-3`) y el stepper de producción en mobile.

**Fase 3 — Catálogo (Productos + Categorías)** (`crud_productos.php`, `crud_categorias.php`)
- Bajar `.product-img-container`/`.product-thumb-80` en mobile (hoy el override existente no reduce nada: sigue en 80px).
- Revisar filtros (`.admin-filter-bar`) y KPIs mini-card ya responsivos, ajuste fino si hace falta.
- Botones de acción de fila: evaluar pasar de apilado full-width a una fila compacta de íconos donde el ancho lo permita (≥360px).

**Fase 4 — Usuarios** (`crud_usuarios.php`)
- Aplica automáticamente el ajuste de Fase 0 (avatar/ícono compartido). Revisar si necesita algo adicional propio (badges, `.user-access-info`).

**Fase 5 — QA cruzado (manual, no delegable)**
- Pasada en dispositivo real por las 5 pantallas verificando consistencia visual entre ellas (mismo tamaño de ícono de encabezado, mismo tamaño de avatar/imagen de fila, mismo tamaño de botón de acción) y que nada se rompió en desktop.

### Out of Scope
- Galería, Consultas, formularios de alta/edición de producto, perfil de usuario — no fueron nombrados por el usuario; se agregan como fase futura si se piden.
- Cualquier cambio de lógica PHP/backend o de datos.
- El motor de tablas→tarjetas y las pestañas segmentadas **no se reescriben** — ya están resueltos (ver `exploration.md`), solo se tocan valores puntuales de tamaño dentro de ellos.

## Capabilities

### New Capabilities
None.

### Modified Capabilities
None — cambios presentacionales (CSS + ajustes menores de markup como tamaños de imagen), sin cambio de comportamiento/spec.

## Approach

- **Orden de ejecución**: Fase 0 primero (beneficia a las 5 pantallas y evita re-tocar el mismo selector compartido 4 veces), luego Fases 1→4 en el orden pedido por el usuario (Dashboard, Ventas, Productos/Catálogo, Usuarios), Fase 5 al cierre.
- **Cada fase es un commit independiente y revertible por separado** (mismo criterio que el fix de navbar): así el usuario puede probar en su celular fase por fase sin esperar a que estén las 5 completas.
- **Verificación por fase**: reconstrucción estática del fragmento real de la vista (mismo HTML/CSS del repo) capturada con Chromium headless a 375px, antes/después — misma técnica ya validada en el fix del navbar — más QA final en dispositivo real (Fase 5).
- Reutilizar los valores ya usados en el proyecto (breakpoints `991.98px`/`767.98px`/`575.98px`, clases utilitarias existentes) en vez de introducir un nuevo sistema de escala.

## Affected Areas

| Area | Fases | Impact |
|------|-------|--------|
| `public/assets/css/admin/admin-panel.css` | 0 | Mover/agregar override de `.dashboard-icon-main`, ajustar avatar/imagen e ícono de acción compartidos |
| `public/assets/css/admin/admin-products.css` | 0, 3 | Quitar override duplicado de `.dashboard-icon-main`; bajar tamaño real de imagen de producto en mobile |
| `public/assets/css/admin/admin-sales.css` | 1, 2 | Afinar KPIs de Dashboard/Ventas, tarjeta de rendimiento, bloque de aprobación |
| `public/assets/css/admin/admin-users.css` | 4 | Ajustes puntuales si hacen falta tras Fase 0 |
| `app/Views/back/sales/estadisticas.php` | 1 | Markup si se requiere alguna clase nueva |
| `app/Views/back/sales/detalleVentas.php`, `gestion_pedido_admin.php` | 2 | ídem |
| `app/Views/back/products/crud_productos.php`, `crud_categorias.php` | 3 | ídem |
| `app/Views/back/users/crud_usuarios.php` | 4 | ídem |

## Success Criteria

- [ ] **Fase 0**: el ícono de encabezado mide lo mismo (achicado) en las 5 pantallas en `≤767.98px`; sin duplicación de la regla entre archivos CSS.
- [ ] **Fase 1**: Dashboard se siente compacto en 375px — KPIs, acciones rápidas y tarjeta de rendimiento visibles sin scroll excesivo ni elementos desproporcionados.
- [ ] **Fase 2**: Ventas (listado + detalle) con la misma densidad que Dashboard; stepper y botones de aprobación no dominan la pantalla en mobile.
- [ ] **Fase 3**: imágenes de producto en tabla claramente más chicas que hoy (80px) en mobile; filtros usables sin scroll horizontal.
- [ ] **Fase 4**: Usuarios consistente con el resto (mismo tamaño de avatar/ícono que Ventas/Productos).
- [ ] **Fase 5**: confirmado en dispositivo real por el usuario, sin regresiones en desktop (`≥992px` byte-equivalente donde no se tocó a propósito).

## Risks

| Risk | Likelihood | Mitigation |
|------|------------|------------|
| El cambio de Fase 0 al avatar/imagen compartido del motor de tarjetas impacta 4 pantallas a la vez — un error ahí se propaga | Media | Es justamente la ventaja de tocarlo una sola vez; verificar visualmente las 4 antes de dar la fase por cerrada, no solo una |
| Reducir texto/íconos de más puede afectar legibilidad/accesibilidad (objetivos táctiles) | Media | Mantener botones de acción en ≥36-40px de área tocable (guía táctil habitual), no bajar de ahí aunque el ícono visual se achique |
| Alcance real por fase puede crecer al ver cada pantalla en detalle (elementos no relevados en la exploración inicial) | Media | Cada fase se re-explora puntualmente antes de tocar código, igual que se hizo para este relevamiento inicial |
| Verificación solo estática (sin PHP+MySQL) puede no capturar casos con datos reales (nombres largos, montos grandes, muchos ítems) | Media | Usar datos de prueba representativos "peores casos" (nombres largos, 4+ dígitos) en los repros; QA final en Fase 5 con datos reales |

## Rollback Plan

Cada fase es un commit CSS+markup aislado y revertible con `git revert` individual, sin impacto en datos/esquema. Fase 0 es la única con "radio de explosión" mayor (afecta 4-5 pantallas) — por eso va primero y sola, para poder revertirla sin arrastrar cambios de fases posteriores si algo no se ve bien.

## Dependencies

Ninguna técnica. Depende de que el usuario confirme el orden/alcance de las fases antes de empezar a implementar (este documento es esa confirmación).

## Decisions (a confirmar con el usuario)

- Orden de fases: 0 → 1 (Dashboard) → 2 (Ventas) → 3 (Productos/Catálogo) → 4 (Usuarios) → 5 (QA), siguiendo el orden en que el usuario nombró las pantallas.
- Cada fase se implementa y se entrega (commit + capturas antes/después) por separado, no todo junto al final — para poder ajustar el rumbo entre fases si algo no convence.

## Key Learnings

(ver `exploration.md` para el detalle completo del relevamiento)
