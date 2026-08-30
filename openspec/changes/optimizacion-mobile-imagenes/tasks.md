# Tasks: optimizacion-mobile-imagenes

Ordered lowest-risk to highest-risk. Each numbered group is one commit, independently revertible. No task in group N starts before group N-1's "hecho" criteria are met.

## 1. Centralized responsive typography (CSS only, no images touched)

### 1.1 Add `@media (max-width: 991.98px)` block to `public/assets/css/base/global.css`
- Append at EOF: `h1.display-1, h2.display-1 { font-size: 3rem; }` / `display-2` → `2.5rem` / `display-3` → `2.2rem` / `display-4` → `2rem`.
- **Hecho cuando**: el bloque existe, los selectores están calificados por `h1`/`h2` (no `.display-*` suelto), y los valores de `display-2`/`display-3` coinciden con `carrusel.css:207-208`.
- Satisface: spec `responsive-typography` → escenario "A single centralized media rule governs heading display classes".

### 1.2 Add `@media (max-width: 575.98px)` block to `public/assets/css/base/global.css`
- Mismo archivo, mismo patrón de selectores; valores `2.4rem/2rem/1.8rem/1.6rem` para `display-1..4`.
- **Hecho cuando**: el segundo bloque existe con los mismos selectores calificados y los valores de `display-2`/`display-3` coinciden con `carrusel.css:221-222`.
- Satisface: mismo escenario que 1.1.

*Puede ejecutarse en paralelo con nada más — es la única tarea de este grupo y bloquea a todas las siguientes.*

## 2. QA manual — verificación visual (NO ES CÓDIGO)

### 2.1 [QA MANUAL — requiere confirmación humana, no auto-completable por un sub-agente]
- Verificar en navegador a 375px y 390px, y en los breakpoints 575.98px/991.98px:
  - Hero (`#heroCarousel`) y las 4 páginas públicas: home, `quienesSomos`, `informacionContacto`, `comercializacion` → el `display-3`/`display-2` se ve más chico que el default de Bootstrap (5rem) y consistente con el hero actual.
  - Íconos de estado vacío en admin: `crud_productos`, `crud_usuarios`, `vistaCompras` (`<i class="bi bi-inbox display-1">` u similar) → el tamaño NO cambia respecto al default de Bootstrap.
  - `back/sales/estadisticas.php` → los números `display-4` NO cambian de tamaño.
- **Hecho cuando**: un humano confirma visualmente los 6 puntos anteriores en al menos un navegador. Sin esta confirmación explícita del usuario, el sub-agente de implementación NO debe avanzar al grupo 3.
- Satisface: spec `responsive-typography` → escenarios "display-3 heading is smaller...", "Bootstrap Icon...is visually unaffected", "Admin statistics numbers...remain untouched".

## 3. Eliminar duplicación en carrusel.css (solo después de 2.1 confirmado)

### 3.1 Borrar las 4 líneas `#heroCarousel .display-2`/`.display-3` de `public/assets/css/pages/carrusel.css`
- Eliminar líneas 207-208 y 221-222 (los overrides duplicados).
- **Hecho cuando**: no quedan reglas `#heroCarousel .display-2`/`.display-3` en `carrusel.css`, y el hero sigue viéndose igual (regresión visual = 0) tras el borrado.
- Satisface: spec `responsive-typography` → escenario "Hero headline sizing is governed by exactly one rule set".

### 3.2 Agregar regla `#heroCarousel .carousel-item > picture` a `carrusel.css`
- Agregar en el mismo archivo: `#heroCarousel .carousel-item > picture { display: block; width: 100%; height: 100%; }`.
- **Hecho cuando**: la regla existe en `carrusel.css` (misma sección que 3.1, mismo commit).
- Nota: esta regla es prerequisito del grupo 5 (sin ella, el `<picture>` inline colapsa la altura del slide).

*3.1 y 3.2 van en el mismo commit; no tiene sentido dividirlos porque ambos tocan el mismo archivo y el segundo depende del contexto del primero.*

## 4. Generación de WebP (assets binarios, script no commiteado)

### 4.1 Generar `hero/taller.webp` desde `hero/taller.jpg` (Pillow, quality=80, method=6)
- **Hecho cuando**: el archivo existe junto al original, el original no fue tocado/renombrado, y el peso del WebP es razonable (a validar en conjunto en 4.5).
- Satisface: spec `mobile-asset-delivery` → "Each in-use image has a committed WebP sibling".

### 4.2 Generar `hero/muebles-22.webp` desde `hero/Muebles 22.jpeg`
- Mismo criterio que 4.1, con nombre hyphenated lowercase (sin espacios).

### 4.3 Generar `hero/muebles-69.webp` desde `hero/Muebles 69.jpeg`
- Mismo criterio que 4.1.

### 4.4 Generar `products/muebles-10.webp` desde `products/Muebles 10.jpeg`
- Mismo criterio que 4.1.

### 4.5 Verificar peso combinado de los 4 WebP ≤400KB
- Sumar el tamaño en disco de los 4 archivos `.webp` generados en 4.1-4.4.
- **Hecho cuando**: la suma es ≤400KB (baseline ~1.4MB de los JPG originales). Si se excede, volver a 4.1-4.4 ajustando `quality` antes de continuar.
- Satisface: spec `mobile-asset-delivery` → escenario "Combined weight of the 4 images drops below 400KB".

*4.1-4.4 pueden generarse en paralelo (son 4 archivos independientes); 4.5 depende de que los 4 existan.*

## 5. Swap de `<img>` a `<picture>` en las vistas (depende de grupos 3 y 4)

### 5.1 Reemplazar el `<img>` de slide 1 (taller) en `app/Views/front/home/section-carrusel.php`
- `<picture><source type="image/webp" srcset="...taller.webp"><img src="...taller.jpg" class="..." alt="..." fetchpriority="high"></picture>` — SIN `loading="lazy"` (debe permanecer eager).
- **Hecho cuando**: el markup coincide con el contrato de `design.md` (alt/class/src del `<img>` sin cambios, sólo se agrega `<picture>`/`<source>`), y el navegador sigue mostrando la imagen si se deshabilita WebP en DevTools.
- Satisface: spec `mobile-asset-delivery` → "Hero slide 1 remains eager and is not lazy-loaded", "Each image is served via `<picture>`...", "Fallback path renders correctly without WebP support".

### 5.2 Reemplazar el `<img>` de slides 2-3 (muebles-22, muebles-69) en `section-carrusel.php`
- Mismo patrón que 5.1, pero con `loading="lazy"` (sin `fetchpriority`).
- **Hecho cuando**: mismos criterios que 5.1, aplicados a ambos slides.
- Satisface: spec `mobile-asset-delivery` → "Hero slides 2–3 and Especialidades images are lazy-loaded", "Each image is served via `<picture>`...", "Fallback path renders correctly...".

### 5.3 Confirmar en el archivo real qué WebP corresponde a cada card de Especialidades en `section-catalogo.php`
- Antes de editar: leer `section-catalogo.php` y determinar el mapeo real imagen→card (el diseño asume que reutiliza `muebles-22.webp`, `muebles-69.webp` y `muebles-10.webp`, pero debe confirmarse contra el `src` actual de cada `<img>`).
- **Hecho cuando**: el mapeo está confirmado por lectura directa del archivo (no asumido).

### 5.4 Reemplazar los 3 `<img>` de Especialidades en `section-catalogo.php`
- Usar el mapeo confirmado en 5.3; las 3 imágenes llevan `loading="lazy"`.
- **Hecho cuando**: mismos criterios de contrato que 5.1/5.2 (alt/class/src sin cambios, sólo se agrega `<picture>`/`<source>` + `loading="lazy"`).
- Satisface: spec `mobile-asset-delivery` → "Hero slides 2–3 and Especialidades images are lazy-loaded", "Each image is served via `<picture>`...", "Fallback path renders correctly...".

*5.1 y 5.2 tocan el mismo archivo (`section-carrusel.php`) y van en el mismo commit. 5.3 debe completarse antes que 5.4, pero ambos pueden ir en un commit separado del anterior ya que tocan otro archivo.*

## Fuera de alcance (explícitamente NO tareas de este change)
- Borrar imágenes huérfanas (`estante.jpg`, `silla.jpg`, `mesa.jpg`, `banco-carpintero.jpeg`).
- `CloudinaryService`.
- Editar las otras 17 vistas con `display-*` en íconos/stats (se corrigen automáticamente por el CSS centralizado del grupo 1, sin tarea de edición propia).

## Review Workload Forecast

| Grupo | Archivos | Líneas estimadas cambiadas |
|---|---|---|
| 1 | `global.css` | ~20 (dos bloques `@media`, solo adición) |
| 3 | `carrusel.css` | ~5 (4 borradas + 1 agregada) |
| 4 | 4 `.webp` binarios | 0 líneas de texto (assets binarios, no cuentan como "líneas cambiadas" de revisión de código) |
| 5 | `section-carrusel.php`, `section-catalogo.php` | ~40-60 (6 `<img>`→`<picture>`, contrato mínimo por imagen) |
| **Total estimado** | | **~65-85 líneas de código de texto** |

- **Presupuesto de revisión**: 400 líneas cambiadas.
- **Riesgo de exceder el presupuesto**: bajo. La estimación total (~65-85 líneas) está muy por debajo de las 400 líneas, incluso sumando el grupo 2 (QA manual, no genera diff) y los binarios WebP (no cuentan como líneas de texto).
- **¿Dividir en PRs encadenados?**: no es necesario por presupuesto, pero se recomienda igual mantener los 4 commits separados del plan (1 → 3 → 4 → 5) porque cada uno es independientemente revertible y el grupo 2 (QA manual) es un gate obligatorio entre el commit 1 y el commit 3. Esto no es una división por riesgo de revisión, es una división por seguridad del rollout.
- **Delivery strategy aplicada**: `ask-on-risk` — no se detectó riesgo de superar el presupuesto, por lo tanto no se generó pregunta al usuario sobre partir el cambio en PRs adicionales.

## Key Learnings

1. El presupuesto de revisión de 400 líneas no está en riesgo para este cambio.
2. Los archivos WebP binarios no cuentan como líneas de texto revisadas.
3. El paso de QA manual bloquea el borrado en carrusel.css antes de verificar.
4. Los selectores deben calificarse por h1 o h2 para no afectar íconos.
5. El mapeo de imágenes en section-catalogo.php requiere confirmación directa del archivo.
