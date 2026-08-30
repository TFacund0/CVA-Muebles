# Exploration: Rediseño mobile UX (navbar, botones globales, tarjetas, íconos, galería)

## Current State

Sigue directamente al change `optimizacion-mobile-imagenes` (tipografía de encabezados + 4 imágenes estáticas, ya aplicado parcialmente). El usuario reportó, tras revisar el sitio en vivo a 375-390px, que además de los encabezados hay desproporción en: home, grid/tarjetas de producto, botones en general, sección de filtros, y las páginas `comercializacion`, `informacionContacto` y `galeria_clientes`. También pidió un rediseño del navbar mobile (no solo mostrar el texto oculto).

Evidencia recogida por auditoría de código (Grep/Read, sin ejecutar nada):

### 1. Navbar (`app/Views/partials/navbar.php`)

- El texto de marca **ya existe** en el markup: `navbar.php:49` — `<h1 class="titulo-logo d-none d-lg-block">CVA Muebles</h1>`. La clase `d-none d-lg-block` lo oculta por debajo de 992px; no es un texto faltante, es un texto apagado.
- Layout mobile actual: 3 celdas flex — izquierda (`navbar.php:28-43`, hamburguesa + carrito), centro (`navbar.php:46-51`, logo centrado, sin texto visible), derecha vacía (todo lo que hay ahí — auth pill, botón de perfil — es `d-none d-lg-flex`, línea 68/79/85).
- `main-layout.css:76-83` — `.titulo-logo { font-size: 1.5rem; }`, sin ningún `@media` que lo toque.
- `main-layout.css:66-69` — `.logo-img-nav { width: 50px; }`, fijo en todos los breakpoints.
- `main-layout.css:34-64` — `.boton-icon-circle { width:48px; height:48px; }`, sin reducción mobile.
- El offcanvas lateral (menú que se abre con la hamburguesa) **ya muestra logo + texto correctamente** (`navbar.php:98-99`), es un componente aparte y no necesita cambios.
- **Decisión de diseño confirmada por el usuario**: reordenar el navbar mobile a patrón "logo a la izquierda, estilo tienda" — logo+texto a la izquierda con todo el ancho necesario, carrito + hamburguesa a la derecha. Esto invierte el orden actual de las celdas y **rompe la simetría de 3 celdas usada hoy para centrar el logo**, así que el diseño técnico debe definir el nuevo layout flex explícitamente, no asumir que basta con mover HTML.

### 2. Botones globales (`public/assets/css/base/global.css`)

- El archivo **no tiene ningún `@media` en todo su contenido** (confirmado por grep, cero coincidencias) — mismo patrón que ya se corrigió para la tipografía en el change anterior.
- `global.css:117` — `.btn-artisan { padding: 0.75rem 2rem; }`
- `global.css:147` — `.btn-vivid { padding: 0.8rem 2.5rem !important; }` (con `!important`, mayor especificidad a vencer)
- `global.css:190` — `.btn-premium-action { padding: 0.8rem 2rem; font-size: 0.8rem; }`
- `global.css:223` — `.btn-gold { padding: 0.8rem 2.5rem !important; }`
- Algunas páginas (`contacto.css:156`, `comercializacion.css:202`) ya definen su propio override mobile puntual para estos botones dentro de esa página — hay riesgo de duplicación/conflicto si se agrega una regla centralizada sin revisar esos overrides existentes primero.

### 3. Grid y tarjetas de producto (`public/assets/css/pages/productos.css`)

- **Ya tiene cobertura completa** en `@media (max-width: 991.98px)` (líneas 274-347): imagen 320px→210px, card-body 2rem→1.2rem, título 1.4rem→1.15rem, precio 1.8rem→1.35rem, botones de acción a 0.7rem/1rem con font-size 0.75rem.
- El grid (`productos.php:43-48`) ya colapsa a 1 columna en mobile con gap moderado (`row g-3`).
- **Falta**: no existe ningún `@media (max-width: 575.98px)` en este archivo — un solo escalón "mobile" cubre desde 320px hasta 991px, sin refinamiento para celulares chicos.
- `product_card.php:32-45` usa `card-body p-4` (Bootstrap, 1.5rem) y botones `w-100 py-3` — a verificar si el 991.98px de `productos.css` ya alcanza a estos o si son selectores distintos.

### 4. Filtros (`productos.php:26-41`, `productos.css:54-98,280-306`)

- **Ya está resuelto**: convierte a scroll horizontal con chips reducidos en el breakpoint 991.98px existente. Si sigue viéndose grande, es probablemente el mismo síntoma del punto 3 (falta el escalón 575.98px), no un defecto propio.
- **Fuera de alcance de este change** salvo que, tras aplicar el punto 3, el usuario confirme que sigue habiendo un problema específico de filtros.

### 5. Íconos y badges sin cobertura mobile

- `comercializacion.css:73-84` — `.badge-logistica { width:50px; height:50px; }`, sin ningún `@media` que lo toque en todo el archivo (los únicos bloques `@media` del archivo, líneas 169 y 198, no lo mencionan).
- `galeria.css:124-129` — `.icon-badge-gold { width:50px; height:50px; }`, mismo problema (bloques 152/170 no lo cubren).
- `galeria.css:27-29` y `147-149` — `.empty-gallery-icon` / `.gallery-empty-icon`, `font-size: 5rem` en ambos, sin `@media`. Son dos clases distintas con el mismo propósito — posible duplicado, una probablemente muerta (a confirmar antes de tocar, no asumir cuál).
- Nota: `.icon-wrap` en `comercializacion.css` (línea 53-64) **sí** tiene reducción en 991.98px (línea 182) — no es parte de este hallazgo, solo `.badge-logistica` quedó afuera.

### 6. Galería — imagen con altura fija (`galeria.css:70-76`)

- `.gallery-item img { height: 320px; }` se mantiene igual en las 3 columnas de desktop y en la única columna de mobile (el grid sí colapsa de 3→2→1 columnas, líneas 157/172, pero la altura de imagen nunca se ajusta). En mobile de 1 columna, una imagen de 320px de alto sobre un ancho angosto se ve desproporcionadamente alta.

## Affected Areas

- `app/Views/partials/navbar.php` — reestructurar el orden de celdas mobile (logo+texto izquierda, iconos derecha)
- `public/assets/css/layout/main-layout.css` — nuevo layout flex del navbar mobile, tamaño del `.titulo-logo` visible en mobile
- `public/assets/css/base/global.css` — reglas mobile para `.btn-artisan`, `.btn-vivid`, `.btn-premium-action`, `.btn-gold` (reconciliando con overrides de página existentes)
- `public/assets/css/pages/productos.css` — nuevo escalón `@media (max-width: 575.98px)`
- `public/assets/css/pages/comercializacion.css` — regla mobile para `.badge-logistica`
- `public/assets/css/pages/galeria.css` — regla mobile para `.icon-badge-gold`, `.empty-gallery-icon`/`.gallery-empty-icon`, y ajuste de `.gallery-item img` height en mobile

## Out of Scope

- Filtros de productos (ya resueltos; revisar solo si persiste el problema tras el punto 3)
- Cualquier cambio no-CSS/no-navbar (lógica de PHP, CloudinaryService, etc. — no aplica aquí)
- El resto del change `optimizacion-mobile-imagenes` (grupos 2, 3 y 5 pendientes ahí) sigue su propio curso, no se mezcla con este

## Recommendation

Seis fases, en orden de menor a mayor superficie de cambio:
1. Navbar (rediseño estructural elegido por el usuario)
2. Botones globales centralizados en `global.css`
3. Escalón 575.98px en `productos.css`
4. Íconos/badges sueltos sin cobertura (3 selectores puntuales)
5. Altura de imagen en galería
6. (Filtros — condicional, solo si persiste tras fase 3)

## Risks

- El nuevo layout del navbar rompe la simetría de 3 celdas iguales que hoy centra el logo — el diseño debe definir el nuevo comportamiento flex explícitamente (ancho del texto de marca variable vs. iconos de ancho fijo a la derecha).
- Reglas de botones en `global.css` pueden entrar en conflicto de especificidad con overrides ya existentes en `contacto.css`/`comercializacion.css` (algunos con `!important`) — requiere reconciliación, no solo agregar reglas nuevas.
- `.empty-gallery-icon` vs `.gallery-empty-icon` parecen duplicados — no asumir cuál está muerta sin confirmar en las vistas que las usan.
