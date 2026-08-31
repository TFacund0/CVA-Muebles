# Tasks: Refinamiento Mobile del Home

Backend: OpenSpec. Delivery strategy: ask-on-risk. Presupuesto: 400 líneas.
Fuente: `design.md` (no existe `spec.md` en este change). Orden de aplicación: 5 → 2 → 1 → 3 → 4.

## Task 1 — Item 5: Maps/footer separation (HTML+CSS atómico) [x]

- Sequential. Debe ir en un único commit (no separar markup de CSS: sacar `py-0` sin el CSS reintroduce un gap de 100px arriba).
- Archivos: `app/Views/front/home/section-catalogo.php` (línea 133), `public/assets/css/pages/catalogo.css` (líneas 296-301 + bloque `@media 991.98px`).
- Cambios:
  1. Markup: `<section class="section-ubicacion py-0">` → `<section class="section-ubicacion">`.
  2. CSS regla base: reemplazar `padding: 100px 0;` por `padding: 0 0 100px;` + `background-color: #faf7f2;` + `width: 100%;` + `border-top: 1px solid rgba(0,0,0,0.03);` (bloque completo indicado en design.md).
  3. CSS dentro de `@media (max-width: 991.98px)` "Ubicación Responsivo": agregar `#home-page .section-ubicacion { padding-bottom: 60px; }`.
- Criterio de hecho: en viewport ≤991px, `.section-ubicacion` no tiene padding-top; el padding-bottom medido en DevTools box model es ≥40px (diseño fija 60px). En desktop ≥1200px el render es byte-equivalente al actual (padding-top 0, padding-bottom 100px).

## Task 2 — Item 2: Padding consistency (HTML+CSS atómico) [x]

- Sequential, depende de Task 1 (mismo archivo PHP, mismo bloque `@media 991.98px` en catalogo.css — aplicar en este orden evita un segundo pase de merge).
- Archivos: `app/Views/front/home/section-catalogo.php` (línea 137), `public/assets/css/pages/catalogo.css` (nueva regla ~línea 301 + reemplazo de líneas 399-401).
- Cambios:
  1. Markup: `<div class="p-5 p-xl-5 w-100 mx-auto ubicacion-content">` → `<div class="w-100 mx-auto ubicacion-content">`.
  2. CSS nueva regla base junto a `.section-ubicacion`: `#home-page .section-ubicacion .ubicacion-content { padding: 3rem; }`.
  3. CSS dentro de `@media (max-width: 991.98px)`, reemplazar líneas 399-401 por:
     ```css
     #home-page .section-ubicacion .col-lg-5 { padding: 40px 0; }
     #home-page .section-ubicacion .ubicacion-content { padding: 0 12px; }
     ```
- Criterio de hecho: inset lateral efectivo de `.ubicacion-content` en mobile = 12px (medido en DevTools box model), igual al de las otras 3 secciones del home. En desktop ≥1200px, padding = 3rem (equivalente exacto al `p-5` removido).

## Task 3 — Item 1: Hero typography/height (solo carrusel.css) [x]

- Sequential, independiente en efecto de Tasks 1-2 (se agrupa después solo por orden de aplicación acordado, no por dependencia técnica).
- Archivo: `public/assets/css/pages/carrusel.css` únicamente. `global.css` NO se toca.
- Cambios:
  1. Dentro de `@media (max-width: 991.98px)`, reemplazar el comentario en líneas 207-208 por:
     ```css
     #heroCarousel h1.display-2, #heroCarousel h2.display-2 { font-size: 2.1rem; }
     #heroCarousel h1.display-3, #heroCarousel h2.display-3 { font-size: 1.9rem; }
     ```
  2. Dentro de `@media (max-width: 575.98px)`, reemplazar la línea de `.carousel-inner` (línea 227) y agregar tipografía:
     ```css
     #heroCarousel .carousel-inner { height: 55vh; min-height: 350px; }
     #heroCarousel h1.display-2, #heroCarousel h2.display-2 { font-size: 1.75rem; }
     #heroCarousel h1.display-3, #heroCarousel h2.display-3 { font-size: 1.55rem; }
     ```
- Criterio de hecho: en ≤991px el H1/H2 del hero renderiza en 2.1rem/1.9rem; en ≤575px en 1.75rem/1.55rem, sin editar `global.css`. El resto del sitio (fuera de `#heroCarousel`) conserva la escala `display-2`/`display-3` centralizada.

## Task 4 — Item 3: Swiper arrows (solo catalogo.css, bloque nuevo) [x]

- Sequential, independiente en efecto de Tasks 1-3.
- Archivo: `public/assets/css/pages/catalogo.css`, nuevo bloque dentro de la sección "3. SWIPER CAROUSEL CUSTOM".
- Cambio: agregar el bloque `@media (max-width: 575.98px)` completo con `#home-page .swiper-button-next/prev` (38px, `bottom: 0.35rem`, `left/right: 12px`, `font-size: 0.95rem` en `:after`) tal como está especificado en design.md / en el mensaje del usuario.
- Criterio de hecho (parcial, ver Task 6 QA): las flechas dejan de superponerse a la foto en el emulador de DevTools a 375px; tamaño ≥36px tappable.

## Task 5 — Item 4: Touch hover guard (refactor de bloques existentes) [x]

- Sequential, último por diseño (aísla el diff visual). Depende de haber confirmado los selectores/valores reales en el archivo antes de tocarlo.
- Archivo: `public/assets/css/pages/catalogo.css`.
- Pre-paso obligatorio: leer los bloques reales en `catalogo.css:85-88`, `:97-99`, `:113-119`, `:136-140`, `:157-159` y usar los valores EXACTOS presentes en el archivo (pueden diferir levemente de los citados en design.md) — no inventar ni asumir.
- Cambio: reemplazar esos 5 bloques `:hover` sueltos por un único bloque agrupado dentro de `@media (hover: hover) and (pointer: fine)`, manteniendo el orden de origen y los selectores/valores reales verificados en el pre-paso.
- Criterio de hecho: no queda ningún `:hover` de `.catalogo-card-premium` o `.product-card-vivid` fuera del guard; el bloque agrupado reproduce exactamente los mismos valores (transform, box-shadow, background) que tenían las reglas originales.

## Task 6 — QA manual (no delegable, no reproducible en emulador) [ ] PENDIENTE

- Sequential, después de Tasks 4 y 5. Bloquea el merge/delivery de este change hasta completarse en dispositivo real.
- Verificaciones:
  1. **Item 3 (colisión de flechas)**: en un teléfono real a ≤575px, confirmar que `bottom: 0.35rem` no choca con `.swiper-pagination mt-4`. Si choca: aplicar plan B — mover las flechas a `left/right: 4px` o subir la posición de la paginación (decisión de diseño abierta, documentada en `design.md` → Open Questions).
  2. **Item 4 (comportamiento táctil real)**: en un teléfono real, tocar una card, luego tocar en otro lugar de la pantalla, confirmar que NO queda un `scale(1.05)`/`translateY(-15px)` pegado (sticky hover). No es reproducible de forma confiable en el emulador de DevTools.
  3. **Item 1 (landscape corto)**: verificar a 375×667 landscape que `55vh` no recorte el caption del hero (Open Question del diseño).
- Criterio de hecho: las 3 verificaciones documentadas con captura/nota de resultado; si el plan B de item 3 se activa, actualizar `catalogo.css` en un commit de seguimiento antes de cerrar el change.

## Review Workload Forecast

- **Alcance total**: ~120-150 líneas de diff (2 archivos CSS + 1 vista PHP), repartidas en 5 commits atómicos + 1 tarea de QA manual.
- **Riesgo por tarea**: Tasks 1 y 2 (bajo, pares HTML+CSS atómicos y reversibles en conjunto) · Task 3 (bajo, un solo archivo, sin `!important` nuevo fuera del ya existente en el archivo) · Task 4 (medio, uso de `!important` documentado por colisión con librería CDN de Swiper — requiere QA en dispositivo real, ítem con riesgo abierto de colisión con paginación) · Task 5 (bajo-medio, refactor puro pero exige verificar valores reales antes de reemplazar, sin cambio de comportamiento visual esperado en desktop/mouse).
- **Delivery strategy = ask-on-risk**: dado el presupuesto de 400 líneas y que el diff total estimado (~150 líneas) está muy por debajo, no se prevé necesidad de tramos adicionales; el único punto que podría escalar a pregunta es el plan B de Task 6.1 si la colisión de flechas/paginación se confirma en dispositivo real (cambio de CSS no cubierto en el diseño aprobado).
- **Nada requiere backend, JS ni cambios de lógica** — fuera de alcance explícito: navbar, filtros, lógica PHP.

## Key Learnings

1. Este change carece de spec.md; tasks.md se derivó únicamente de design.md, que ya estaba aprobado.
2. El orden de aplicación acordado es cinco, dos, uno, tres, cuatro, no el orden numérico original.
3. Los items cinco y dos comparten el mismo archivo PHP y deben permanecer atómicos por commit.
4. El item tres queda con riesgo de colisión documentado, requiere verificación en dispositivo real.
5. El item cuatro exige leer los valores reales del archivo antes de reemplazar bloques existentes.
</content>
