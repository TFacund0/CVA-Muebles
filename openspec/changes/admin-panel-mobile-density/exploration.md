# Exploration: Densidad Mobile del Panel Admin (Dashboard, Ventas, Catálogo, Usuarios)

## Contexto

Segunda iteración de mejoras mobile al panel admin, después del fix de superposición navbar/hamburguesa (`admin-panel-mobile-navbar-fix`). El usuario pide reducir tamaños (íconos, tarjetas, tipografía) en 5 pantallas: Dashboard, Ventas, Productos, Catálogo (Categorías) y Usuarios, para que la interfaz sea más rápida de usar con el pulgar en celular. Se pide un plan por fases antes de tocar código.

Mismo sandbox sin PHP+MySQL — investigación por lectura de código (vistas + CSS reales), sin reconstrucción visual esta vez (el volumen de páginas hace que un repro-por-página no sea el mejor uso del tiempo en la fase de planificación; se reservará para la verificación de cada fase de implementación, igual que se hizo en el fix del navbar).

## Mapeo pantalla → archivos

| Pantalla (pedido usuario) | Ruta | Vista(s) | CSS |
|---|---|---|---|
| Dashboard | `/admin-dashboard` | `app/Views/back/sales/estadisticas.php` | `admin-sales.css` |
| Ventas | `/ventas-list` (listado) + `/ventas/gestionar/{id}` (detalle, vía `VentasController` línea 212) | `app/Views/back/sales/detalleVentas.php` (listado) + `app/Views/back/sales/gestion_pedido_admin.php` (detalle por pedido) | `admin-sales.css` |
| Productos | `/crud-productos` | `app/Views/back/products/crud_productos.php` | `admin-products.css` |
| Catálogo (Categorías) | `/crud-categorias` | `app/Views/back/products/crud_categorias.php` | `admin-products.css` |
| Usuarios | `/crud-usuarios` | `app/Views/back/users/crud_usuarios.php` | `admin-users.css` |

Todas extienden `layout/admin_layout.php` y comparten `admin-panel.css` como base (`.dashboard-icon-main`, `.admin-card-v2`, `.table-responsive-stack`, `.custom-segmented-tabs`, `.btn-admin-primary`, etc.).

## Qué ya está bien resuelto (no reinventar)

1. **Motor de tablas → tarjetas en mobile** (`admin-panel.css:446-735`, `@media max-width: 991.98px`): ya transforma cualquier `.table-responsive-stack` en tarjetas apiladas con `data-label`, con ajustes específicos ya escritos por tipo de fila (`.user-row`, `.product-row`, `.order-row`, `.category-row`, `.inquiry-row`). Cubre Ventas, Productos, Categorías y Usuarios por igual. **No se re-implementa, solo se afina densidad** (tamaños de avatar/imagen, padding, tipografía dentro de ese motor).
2. **Pestañas segmentadas** (`.custom-segmented-tabs`, usadas en Productos, Usuarios y en el listado de Ventas): ya tienen un bloque responsivo dedicado (`admin-panel.css:864-885`) que las fuerza a 100% de ancho, 50/50 y reduce texto a `0.65rem` en `≤991.98px`. **Ya están resueltas**, no forman parte del alcance.
3. **KPIs mini-card** (`.admin-card-v2` con `col-6 col-md-3/4`): ya usan `p-3 p-md-4` (padding responsivo) y ocultan el ícono circular decorativo bajo `576px` (`d-none d-sm-block`) — es decir, en el celu más chico el ícono ya desaparece y solo queda número + etiqueta. Buen patrón, replicable donde falte.
4. **Grid de acciones rápidas del Dashboard** (`action-btn-v2`) y **KPIs grandes del Dashboard** (`kpi-card-premium`) ya tienen un `@media (max-width: 767.98px)` en `admin-sales.css:166-182` que reduce ícono, valor y padding.

## Hallazgo principal: `.dashboard-icon-main` (ícono de encabezado) inconsistente entre páginas

Las 5 páginas usan el mismo patrón de encabezado — ícono grande (`.dashboard-icon-main`, 60×60px, `font-size: 2rem`, definido en `admin-panel.css:213-221`, **sin** query responsivo ahí) + `<h1 class="display-6 display-md-5">`.

Solo existe un override mobile de `.dashboard-icon-main` (→ 50×50px, `1.5rem`) en `admin-products.css:35` dentro de su propio `@media (max-width: 767.98px)`. Como el CSS es global (no *scoped* por archivo), ese override **solo se aplica en las páginas que cargan `admin-products.css`**:

| Página | Carga `admin-products.css` | `.dashboard-icon-main` se achica en mobile |
|---|---|---|
| Dashboard (`estadisticas.php`) | No (solo `admin-sales.css`) | **No** — queda en 60px/2rem |
| Ventas listado (`detalleVentas.php`) | No | **No** |
| Ventas detalle (`gestion_pedido_admin.php`) | No | **No** |
| Productos (`crud_productos.php`) | Sí | Sí (50px/1.5rem) |
| Categorías (`crud_categorias.php`) | Sí | Sí |
| Usuarios (`crud_usuarios.php`) | No (solo `admin-users.css`) | **No** |

Es decir: **3 de las 5 pantallas pedidas por el usuario (Dashboard, Ventas, Usuarios) muestran el ícono de encabezado sin achicar en mobile**, mientras que Productos/Categorías ya lo tienen resuelto — la típica inconsistencia de estilos duplicados por archivo en vez de centralizados. Esto probablemente es parte de lo que el usuario percibe como "todo muy grande".

## Otros elementos candidatos a reducir por pantalla (relevados, sin verificación visual todavía)

- **Dashboard**: `kpi-value` a `2.8rem` (KPIs grandes de producción) ya baja a `2rem` en `≤767.98px` — evaluar si conviene bajar más (ej. `1.6-1.8rem`) dado que son 2 por fila en mobile. Tarjeta "RENDIMIENTO HISTÓRICO" usa `display-4` para el contador de entregados, sin override mobile — candidato.
- **Ventas (listado)**: 4 KPIs (`col-6 col-md-3`) con `h3`/`h4` — mismo patrón que Dashboard, mismo tratamiento a aplicar. Detalle de pedido (`gestion_pedido_admin.php`) usa `display-4` para el ícono de aprobación pendiente y botones `px-5 py-3` (grandes) — candidatos a compactar en mobile.
- **Productos**: `.product-img-container`/`.product-thumb-80` en 80px dentro de las tarjetas de fila (`admin-products.css:116,189`) — ya tiene un override a 80px explícito para mobile (`admin-products.css:115-118`, curiosamente el mismo valor que desktop, no reduce) — candidato real a bajar (ej. 56-64px). Botones de acción de fila se apilan a `width:100%` (`.product-row .btn-action-premium`, `admin-panel.css:674-678`) — en vez de 3-4 botones full-width apilados, evaluar una fila de íconos compactos.
- **Categorías/Usuarios**: `.avatar-premium` en Usuarios ya es de 45px (`admin-users.css:10-15`, razonable) — probablemente no necesita más ajuste. `.avatar-premium`/`.product-img-container` dentro del motor de tablas (`admin-panel.css:589-596`) fuerza 60px en mobile **a todos los tipos de fila por igual** (usuarios, productos, ventas, categorías) — es decir, ese es el verdadero tamaño mobile "unificado" hoy, no el que definen los CSS por página. Bajarlo ahí (ej. a 48-52px) impacta las 4 pantallas de una sola vez.
- **Botones de acción de fila** (`.btn-action-premium`, `admin-panel.css:233-248`): 40×40px en desktop, sin reducción específica en mobile salvo el forzado a `width:100%` en filas de producto. Revisar si conviene un tamaño ligeramente menor (ej. 36px) + mantenerlos en fila (no apilados a ancho completo) para pantallas ≥360px, para que se sientan menos "gigantes".

## Fuera de alcance de este relevamiento

- Galería y Consultas (Operaciones Taller) — no mencionadas por el usuario en este pedido; se dejan para una fase futura si se pide.
- Formularios de alta/edición de producto (`alta_producto.php`, `editar_producto.php`) y perfil (`perfil_config.php`) — el usuario nombró listados/paneles, no formularios; se puede sumar como fase adicional si surge.
- Cualquier cambio de lógica PHP/backend.

## Key Learnings

1. El motor de tablas→tarjetas y las pestañas segmentadas ya son sólidos y centralizados — el trabajo real no es "reconstruir" sino **afinar tamaños puntuales** y **corregir la inconsistencia del ícono de encabezado**.
2. El tamaño real de avatar/imagen en mobile para las 4 pantallas con tablas (Ventas, Productos, Categorías, Usuarios) está gobernado por una única regla compartida en `admin-panel.css` (dentro del motor de tarjetas) — un solo cambio ahí afecta a las 4 a la vez, con bajo riesgo de regresión visual si se hace con cuidado.
3. La causa del ícono de encabezado grande es la misma clase de bug que el fix anterior (navbar): una regla CSS "compartida" que en la práctica solo se activa en algunas páginas por cómo están repartidas las hojas de estilo — vale la pena, como fase 0, mover ese override a `admin-panel.css` (donde vive la regla base) para que aplique parejo a las 5 pantallas.
