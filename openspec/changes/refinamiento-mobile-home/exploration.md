# Exploration: Refinamiento Mobile Home (6 problemas post-QA en dispositivo real)

## Contexto
Investigación de 6 problemas reportados tras revisar el home en un celular real, luego de fases previas de rediseño mobile (navbar reordenado, tipografía centralizada en `global.css`, imágenes WebP, botones globales, tarjetas de producto). Bootstrap 5.3.3 vendorizado local en `public/assets/vendor/bootstrap/`. Breakpoints del proyecto: `991.98px` (tablet/mobile) y `575.98px` (mobile chico), consistentes con los breakpoints nativos de Bootstrap.

---

## 1. Offcanvas del navbar — ¿se abre a la derecha?

**Hallazgo: YA está configurado para abrir a la derecha. No hay bug de posicionamiento del panel.**

- `app/Views/partials/navbar.php:95` — `<div class="offcanvas offcanvas-end menu-lateral shadow-lg border-0" ... id="offcanvasNavbar">`. La clase `offcanvas-end` es la clase nativa de Bootstrap 5 que desliza el panel desde la derecha.
- `public/assets/css/layout/main-layout.css:173-187` — reglas de `.menu-lateral` solo tocan `width` (350px mobile / 450px desktop en `@media (min-width: 992px)`, línea 178-182) y el header (`background-color`, `border-bottom`). No hay ninguna regla que toque `left`, `right`, `transform` ni anule `.offcanvas-end`.
- Grep de `offcanvas|menu-lateral` en `main-layout.css` no arrojó ningún override adicional fuera de ese bloque.

**Conclusión real del problema**: el panel offcanvas sí sale por la derecha. Lo que el usuario probablemente percibe como "desubicado" es que el botón hamburguesa (trigger) está del lado izquierdo (`navbar.php:28-31`, dentro de `.nav-cell-actions`), mientras el panel que dispara aparece del lado derecho — un salto visual trigger-izquierda → panel-derecha. Esto es un tema de UX/consistencia espacial, no un bug de CSS. Alternativas para la propuesta: (a) mantener como está, (b) mover el botón hamburguesa al lado derecho del navbar para alinear con el panel.

---

## 2. Carrusel hero — tamaño de letra y altura en mobile

**Valores actuales confirmados:**

`public/assets/css/pages/carrusel.css`:
- Desktop (base): `#heroCarousel .carousel-inner { height: 85vh; min-height: 600px; }` (línea 42-45).
- `@media (max-width: 991.98px)` (línea 204-224): `#heroCarousel .carousel-inner { height: 70vh; min-height: 500px; }` (línea 205). `.glass-caption { padding: 2rem; margin: 0 1.5rem; max-width: calc(100% - 3rem); }` (línea 206).
- `@media (max-width: 575.98px)` (línea 226-237): `#heroCarousel .carousel-inner { height: 60vh; min-height: 400px; }` (línea 227). Flechas de control ocultas (`display: none`, línea 229-230).

**Tipografía (centralizada en `global.css`):**
- `@media (max-width: 991.98px)`: `h1.display-2, h2.display-2 { font-size: 2.5rem; }` / `h1.display-3, h2.display-3 { font-size: 2.2rem; }`.
- `@media (max-width: 575.98px)`: `h1.display-2, h2.display-2 { font-size: 2rem; }` / `h1.display-3, h2.display-3 { font-size: 1.8rem; }`.

El comentario en `carrusel.css:207-208` documenta que display-2/3 ya se resuelven en `global.css` y no se duplican ahí — patrón a mantener si se ajustan tamaños de fuente (tocar `global.css`, no `carrusel.css`).

**Para "se siente grande"**: con `min-height: 400px` en 575.98px más `font-size: 2rem`/`1.8rem`, en un viewport típico de 375-414px de alto visible, 400px de carrusel ocupa la mayor parte del viewport inicial. Candidatos de reducción: bajar `min-height` a ~340-360px y/o usar un `vh` más chico (ej. 50vh en vez de 60vh) en 575.98px; considerar un tamaño de `display-2/3` específico para el hero si se quiere diferenciar de otros usos de esas clases en el sitio (hoy la regla es global y afecta a cualquier página que use esas clases).

---

## 3. Inconsistencia de padding lateral entre secciones del home

| Sección | Contenedor HTML | Padding lateral | Archivo:línea |
|---|---|---|---|
| `.section-categorias` | `.container` (Bootstrap) | Heredado del gutter estándar de Bootstrap | `section-catalogo.php:12` |
| `.mission-statement-premium` | `.container` | Heredado del gutter estándar | `section-catalogo.php:64` |
| `.section-destacados-dinamica` | `.container` | Heredado del gutter estándar | `section-catalogo.php:82` |
| `.section-ubicacion` | `.container-fluid p-0` | `0` lateral — columna izquierda compensa con `padding: 40px 20px` propio en mobile (`catalogo.css:399-401`); columna derecha (iframe) sin ningún padding | `section-catalogo.php:134-136` |

**Conclusión**: las 3 primeras secciones son consistentes entre sí (todas usan `.container`). La única que rompe el patrón es `.section-ubicacion`, que usa `container-fluid p-0` a propósito para que el mapa llegue a los bordes — pero eso hace que el texto de la izquierda dependa solo de su padding interno en vez del gutter estándar, dando una sensación de look distinto.

**Patrón reusable**: si se unifica, el patrón ya usado en 3 de 4 secciones es envolver el contenido en `.container`. Para `.section-ubicacion` (que necesita el mapa full-bleed), conviene mantener `container-fluid` solo para la fila con el mapa, pero llevar el padding interno de la columna de texto a algo comparable al gutter del resto.

---

## 4. Flechas del Swiper (tamaño/posición) + hover pegado en touch

### 4a. Flechas Swiper
- `public/assets/css/pages/catalogo.css:198-208` — `#home-page .swiper-button-next, #home-page .swiper-button-prev { width: 50px !important; height: 50px !important; ... }`.
- **No existe ningún `@media` que reduzca ese tamaño en mobile** — confirmado revisando el bloque completo de media queries (línea 317-409): no hay ninguna entrada para `.swiper-button-next/prev`.
- Posicionamiento: Swiper centra verticalmente las flechas sobre el track mediante su propio CSS de librería (CDN, `section-catalogo.php:8,176`), sin override de `top`/`left`/`right` en `catalogo.css`. Con 50px fijos y slides angostos en mobile, las flechas caen encima del borde de la imagen del producto.

### 4b. Hover "pegado" en touch (product-card-vivid)
- `catalogo.css:136-140` — `#home-page .product-card-vivid:hover { transform: scale(1.05) translateY(-10px); ... }`. Sin guard de `@media (hover: hover)`.
- Grep de `hover: hover` en todo `public/assets/css` → **0 resultados**. No existe ningún patrón ya implementado en el proyecto para distinguir touch de mouse. También afecta a `.catalogo-card-premium:hover` (`catalogo.css:85-88`) y a los botones del hero (`carrusel.css:153-166`), aunque el reporte del usuario es específico sobre las tarjetas de producto.

**No hay patrón reusable existente** — habría que introducir `@media (hover: hover) and (pointer: fine)` por primera vez en el proyecto.

---

## 5. Sección Ubicación — mismo problema de bordes que el punto 3

Cubierto en el punto 3: `container-fluid p-0` + `row g-0` + columnas sin padding simétrico. En mobile, `catalogo.css:395-409` fuerza columna única y da padding solo a la columna de texto, no a la del iframe — confirma la inconsistencia respecto a las otras 3 secciones.

---

## 6. Iframe de Google Maps pegado al footer

**Causa raíz encontrada:**

- `section-catalogo.php:133` — `<section class="section-ubicacion py-0">`. La clase Bootstrap `py-0` (`bootstrap.css:8167-8170`) define `padding-top/bottom: 0 !important`, anulando el `padding: 100px 0` que `catalogo.css:296-301` define para esa sección.
- El iframe está en `.col-lg-7` sin padding, dentro de `container-fluid p-0` + `row g-0` — cero espacio en todos los niveles.
- El footer (`main-layout.css:354-361`) tiene `padding: 4rem 0 1rem` (sin `margin-top`), solo un `border-top: 4px solid var(--cva-gold)` como separador visual.
- En mobile, `catalogo.css:403-405` fija `iframe { min-height: 400px }`, pero eso no agrega separación con el footer.

**Conclusión**: la falta de separación es causada por `py-0` (con `!important`) combinado con que el footer no compensa con `margin-top`. La solución de menor riesgo es agregar un padding-bottom específico solo en mobile a `.section-ubicacion` con especificidad/orden suficiente para vencer a `py-0`, o quitar `py-0` del HTML y manejar el padding cero solo donde corresponde (arriba) vía CSS propio.

---

## Resumen de archivos relevantes

- `app/Views/partials/navbar.php` (offcanvas, botón hamburguesa)
- `app/Views/front/home/section-catalogo.php` (todas las secciones del home)
- `public/assets/css/pages/carrusel.css` (hero carousel)
- `public/assets/css/pages/catalogo.css` (secciones home, Swiper, ubicación)
- `public/assets/css/base/global.css` (tipografía display-*, variables `--space-*`, `--cva-gold`)
- `public/assets/css/layout/main-layout.css` (`.menu-lateral`, `footer.artisan-footer`)
- `public/assets/vendor/bootstrap/bootstrap.css:8167-8170` (`.py-0`)

## Key Learnings

1. El offcanvas ya usa `offcanvas-end` y abre correctamente hacia la derecha en navbar.php línea 95.
2. El botón hamburguesa está del lado izquierdo del navbar mientras el panel abre a la derecha.
3. Ninguna regla CSS del proyecto usa actualmente `@media (hover: hover)` para touch devices.
4. Las flechas Swiper de 50px carecen de todo ajuste responsive para viewports mobile angostos.
5. La clase Bootstrap `py-0` con `!important` anula el padding vertical de la sección ubicación.
