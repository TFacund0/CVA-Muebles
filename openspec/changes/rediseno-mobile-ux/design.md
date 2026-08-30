# Design: Rediseño mobile UX

## Context

CodeIgniter 4 / PHP 8.1, Bootstrap 5.3.3 vendorizado en `public/assets/vendor/bootstrap/`. Todo el CSS del proyecto es plano (sin build step), cargado por página. Breakpoints establecidos: `991.98px` y `575.98px`. Este documento define el CÓMO arquitectónico de las 5 fases aprobadas en `proposal.md`.

Evidencia leída para este diseño (no supuestos):

| Archivo | Hallazgo verificado |
|---|---|
| `main-layout.css:207-213` | `.navbar-flex-cell { flex: 1 }` y `.navbar-flex-cell-nav { flex: 2 }` — el centrado del logo hoy es "3 celdas iguales" |
| `main-layout.css:34-39, 66-69, 76-83` | `.boton-icon-circle` 48x48, `.logo-img-nav` 50px, `.titulo-logo` 1.5rem — sin `@media` |
| `global.css:143-155, 219-231` | `.btn-vivid` y `.btn-gold` declaran `padding` **con `!important`** |
| `global.css:115-127, 186-202` | `.btn-artisan` y `.btn-premium-action` **sin** `!important` |
| `comercializacion.css:202` | `#container-comercializacion-wrapper .btn-vivid { padding: 1rem; ... }` sin `!important` |
| `contacto.css:156` | `#container-contacto-wrapper .btn-vivid-premium { padding: 1rem; ... }` — `.btn-vivid-premium` base no declara `padding` |
| `galeria.css:170-174` | El grid colapsa a **1 columna en `575.98px`**, no en 991.98px |
| `galeria.css:27, 147` | `.empty-gallery-icon` y `.gallery-empty-icon` **ambas existen**, ambas `#galeria-page`-scoped, 5rem |
| `productos.css:274-347` | Único escalón mobile (991.98px), con los valores base citados en la propuesta |

## Goals / Non-Goals

**Goals**: navbar mobile legible con marca visible y truncado seguro; dos escalones de botones globales; segundo escalón de tarjeta; íconos y altura de galería proporcionados en mobile; cero regresión ≥992px.

**Non-Goals**: filtros de producto, lógica PHP, offcanvas (`navbar.php:94-160`), unificar el duplicado `.empty-gallery-icon`/`.gallery-empty-icon`, `aspect-ratio` en galería.

---

## Decision 1 — Navbar: conservar las 3 celdas del DOM y reordenar con `order`

**Decisión**: no se reescribe el markup fila por fila. Se conservan las tres celdas y se les añade **una clase semántica a cada una** (`nav-cell-actions`, `nav-cell-brand`, `nav-cell-auth`), más la eliminación de `d-none d-lg-block` en el `<h1 class="titulo-logo">`. Total: 4 ediciones de atributo en `navbar.php`, cero líneas movidas. El reordenamiento visual vive íntegramente en CSS dentro de `@media (max-width: 991.98px)`.

**Por qué**: es el approach que toca menos líneas, mantiene intacto el orden de tabulación y el DOM que el offcanvas ya referencia, y el revert es "borrar el bloque `@media` + restaurar 4 atributos".

**Rechazado — reescribir el HTML en el orden visual**: obligaría a duplicar el bloque del carrito (hoy existe una copia mobile en la celda 1 y otra desktop en la celda 3) o a reestructurar la lógica PHP de `$env_cart_enabled`, lo que sale de alcance y complica el revert.

**Rechazado — `flex-direction: row-reverse`**: invertiría también el orden interno del par carrito/hamburguesa y dejaría el DOM y el orden visual desalineados sin control fino.

### Trampa crítica identificada

La celda 3 (`navbar.php:66-89`) tiene todos sus hijos con `d-none d-lg-flex`: en mobile queda **visualmente vacía pero sigue siendo un flex item con `flex: 1`**, robando un tercio del ancho. Si sólo se reordena sin neutralizarla, la marca pierde ~33% del espacio disponible. Debe colapsarse explícitamente.

### Mecanismo flex exacto

```css
/* main-layout.css — bloque nuevo */
@media (max-width: 991.98px) {
    /* La marca toma todo el ancho sobrante; los iconos reservan ancho fijo. */
    .artisan-main-nav .nav-cell-brand {
        order: 1;
        flex: 1 1 auto;
        min-width: 0;               /* rompe el min-width:auto por defecto del flex item */
        justify-content: flex-start;
    }

    .artisan-main-nav .nav-cell-actions {
        order: 2;
        flex: 0 0 auto;             /* ancho intrínseco: nunca se comprime */
    }

    /* Celda vacía en mobile: deja de reservar 1fr */
    .artisan-main-nav .nav-cell-auth {
        order: 3;
        flex: 0 0 auto;
    }

    /* La cadena de min-width:0 debe llegar hasta el texto */
    .artisan-main-nav .navbar-brand {
        display: flex;
        align-items: center;
        min-width: 0;
        max-width: 100%;
        overflow: hidden;
    }

    .artisan-main-nav .logo-img-nav {
        flex: 0 0 auto;             /* la imagen no se deforma al truncar */
        width: 40px;
    }

    .artisan-main-nav .titulo-logo {
        display: block;             /* neutraliza el d-lg-block eliminado */
        min-width: 0;
        font-size: 1.15rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .artisan-main-nav .boton-icon-circle {
        width: 42px;
        height: 42px;
    }
}

@media (max-width: 575.98px) {
    .artisan-main-nav .logo-img-nav { width: 36px; }
    .artisan-main-nav .titulo-logo  { font-size: 1rem; }
    .artisan-main-nav .boton-icon-circle { width: 40px; height: 40px; }
}
```

**Sobre el patrón `min-width: 0`**: sí aplica aquí, con una condición. `text-overflow: ellipsis` sólo actúa sobre un contenedor de bloque con `overflow` no visible; el `<h1>` cumple. Pero como el ancestro que restringe es el `<a class="navbar-brand">` que envuelve **imagen + texto**, la cadena de `min-width: 0` debe declararse en los tres niveles (celda → `.navbar-brand` → `.titulo-logo`): un solo flex item con `min-width: auto` en cualquier eslabón anula el truncado y produce overflow horizontal. Por eso la imagen se fija con `flex: 0 0 auto` y sólo el texto es comprimible.

**Presupuesto a 320px** (peor caso, usuario logueado): 32px de padding lateral (`px-3` = 1rem cada lado) + 36px logo + 8px gap + 2 botones de 40px + 8px gap ≈ 164px reservados → quedan ~156px para el texto. "CVA Muebles" a 1rem en la fuente heading entra o trunca limpiamente. Comportamiento aceptado por el usuario.

**Validación requerida**: 320 / 375 / 390 / 768 / 992 / 1440px, con y sin sesión iniciada, y con `$cartCount > 0` (el badge `position-absolute start-100` sobresale del círculo y es el primer candidato a desbordar por la derecha).

---

## Decision 2 — Botones globales: `!important` selectivo, no uniforme

**Decisión**: el nuevo bloque `@media` de `global.css` usa `!important` **sólo** en `.btn-vivid` y `.btn-gold`, y **no** en `.btn-artisan` ni `.btn-premium-action`.

**Justificación por cascada** (dos reglas distintas, no una):

1. **Especificidad**: `.btn-vivid-premium` (1 clase) no es más específico que `.btn-vivid` (1 clase) — son clases hermanas, no anidadas; el razonamiento de "2 clases gana" no aplica a este par. Lo que sí protege a los overrides de página es que **todos son ID-scoped** (`#container-contacto-wrapper .btn-vivid-premium`, `#container-comercializacion-wrapper .btn-vivid`, `#galeria-page .btn-vivid-premium`): ID (1-0-0) gana sobre clase (0-1-0) sin necesidad de `!important`. Los overrides de página quedan intactos por especificidad.
2. **`!important` vence a la especificidad**: `.btn-vivid` y `.btn-gold` ya declaran `padding: ... !important` en `global.css`. Una regla nueva sin `!important`, aunque esté dentro de un `@media` y más abajo en el archivo, **pierde** contra esa declaración base. Por eso aquí `!important` es obligatorio, no estilístico.

**Hallazgo colateral (bug preexistente, no introducido por este change)**: `comercializacion.css:202` declara `padding: 1rem` sin `!important` sobre `.btn-vivid`, cuya base sí lo tiene → ese `padding` **hoy no se aplica** (el `font-size` sí, porque la base no lo marca). Tras esta fase, ese botón pasará a tomar el padding mobile global, que es más cercano a la intención original. No se toca `comercializacion.css` para arreglarlo en este change; queda documentado como seguimiento.

```css
/* global.css — primer @media del archivo, al final */
@media (max-width: 991.98px) {
    .btn-artisan        { padding: 0.65rem 1.5rem; font-size: 0.85rem; }
    .btn-vivid          { padding: 0.7rem 1.8rem !important; font-size: 0.85rem; }
    .btn-gold           { padding: 0.7rem 1.8rem !important; font-size: 0.85rem; }
    .btn-premium-action { padding: 0.7rem 1.5rem; font-size: 0.75rem; letter-spacing: 1.5px; }
}

@media (max-width: 575.98px) {
    .btn-artisan        { padding: 0.6rem 1.2rem; font-size: 0.8rem; }
    .btn-vivid          { padding: 0.65rem 1.4rem !important; font-size: 0.8rem; }
    .btn-gold           { padding: 0.65rem 1.4rem !important; font-size: 0.8rem; }
    .btn-premium-action { padding: 0.65rem 1.2rem; font-size: 0.7rem; letter-spacing: 1px; }
}
```

`letter-spacing` se reduce junto con el padding porque `.btn-premium-action` usa `2px` y es la causa principal de que sus etiquetas rompan a dos líneas en pantallas chicas.

---

## Decision 3 — `productos.css`: escalón 575.98px aditivo, mismo prefijo `#productos`

Se agrega un bloque nuevo al final del archivo, después del `@media (max-width: 991.98px)` existente (`productos.css:274-347`), reusando el mismo prefijo `#productos` para heredar la especificidad y no competir con nada global.

```css
@media (max-width: 575.98px) {
    #productos .header-productos h2 { font-size: 1.5rem; }
    #productos .header-productos p  { font-size: 0.8rem; }

    #productos .product-card .img-wrapper { height: 175px; }   /* 210 → 175 */
    #productos .product-card .card-body   { padding: 1rem; }    /* 1.2rem → 1rem */
    #productos .product-card .card-title  { font-size: 1.05rem; } /* 1.15 → 1.05 */
    #productos .product-card p.small      { font-size: 0.75rem; }
    #productos .precio-tag                { font-size: 1.2rem; } /* 1.35 → 1.2 */

    #productos .filtro-categoria { padding: 0.4rem 0.9rem; font-size: 0.7rem; } /* menor que 0.5/1.2/0.75 */

    #productos .btn-artisan-gold,
    #productos .btn-brown-solid,
    #productos .btn-whatsapp-artisan { padding: 0.6rem 0.8rem; font-size: 0.7rem; }

    #productos .card-footer { padding: 0.8rem; }
}
```

Todos los valores son estrictamente menores que su contraparte de 991.98px (criterio de éxito 3 de la propuesta). `.filtro-categoria` se incluye porque cae dentro del mismo bloque de tarjeta y su reducción es geométrica, no un cambio de comportamiento de filtrado (que sigue fuera de alcance).

---

## Decision 4 — Íconos/badges: cubrir ambas clases de galería sin borrar ninguna

`.empty-gallery-icon` (`galeria.css:27`) y `.gallery-empty-icon` (`galeria.css:147`) existen ambas y ambas son `#galeria-page`-scoped. Se listan como selector agrupado en la regla mobile; ninguna se elimina.

```css
/* comercializacion.css — dentro del @media 991.98px existente */
#container-comercializacion-wrapper .badge-logistica { width: 40px; height: 40px; font-size: 0.9rem; }

/* galeria.css — dentro del @media 991.98px existente */
#galeria-page .icon-badge-gold { width: 40px; height: 40px; }
#galeria-page .empty-gallery-icon,
#galeria-page .gallery-empty-icon { font-size: 3.5rem; }
```

Se insertan **dentro de los bloques `@media` ya existentes** en cada archivo en lugar de crear bloques nuevos, para no fragmentar la sección responsive de esos ficheros.

---

## Decision 5 — Galería: la altura fija va en 575.98px, no en 991.98px

**Hallazgo que corrige el supuesto de la propuesta**: el grid pasa a 1 columna en `galeria.css:172`, dentro de `@media (max-width: 575.98px)` — no en 991.98px, donde todavía hay 2 columnas (`galeria.css:157`). Bajar la altura a 220px en el escalón de 2 columnas achataría tarjetas que aún son angostas y verticales.

**Decisión**: escalonado en dos pasos, con 220px sólo donde el layout ya es de 1 columna.

```css
/* galeria.css — @media 991.98px existente (2 columnas) */
#galeria-page .gallery-item img { height: 240px; }

/* galeria.css — @media 575.98px existente (1 columna) */
#galeria-page .gallery-item img { height: 220px; }
```

Altura fija con `object-fit: cover` ya declarado en la regla base (`galeria.css:70-76`); no se introduce `aspect-ratio`, según la decisión del usuario.

---

## Orden de aplicación

**Las fases 2, 3, 4 y 5 son mutuamente independientes**: tocan archivos distintos (`global.css`, `productos.css`, `comercializacion.css` + `galeria.css`) o selectores disjuntos, y ninguna lee estado de otra. Pueden aplicarse y revisarse en cualquier orden o en paralelo.

**La fase 1 es la única con acoplamiento interno** y debe ejecutarse como una unidad atómica: el cambio de `navbar.php` (clases + quitar `d-none`) sin el bloque CSS deja el texto de marca visible en un layout de 3 celdas iguales, es decir, el estado roto que este change existe para evitar. Markup y CSS van en la misma unidad de trabajo, nunca separados.

**Orden recomendado**: 1 → 2 → 3 → 4 → 5. La fase 1 primero porque es la de mayor riesgo visual y la única con markup: si se descarta o se revierte, el resto del change sigue siendo válido de forma independiente. Las fases 2-5 después, de mayor a menor superficie, para que cada revisión visual sea acotada.

**Dependencia cruzada única a vigilar**: la fase 2 cambia `.btn-vivid`/`.btn-gold` globalmente y `productos.css` usa sus propias clases (`.btn-artisan-gold`, `.btn-brown-solid`, `.btn-whatsapp-artisan`), así que no hay colisión con la fase 3. La verificación de la fase 2 sí debe incluir contacto, comercialización y galería, que son las páginas con overrides ID-scoped de esos botones.

## Riesgos abiertos

| Riesgo | Mitigación |
|---|---|
| El badge del carrito (`.navbar-cart-badge`, `start-100 translate-middle`) desborda por la derecha al reducir el círculo a 40px | Validar a 320px con `$cartCount > 0`; si desborda, añadir `padding-right` a la celda de acciones (no reducir más el ícono) |
| El truncado no se activa si algún ancestro conserva `min-width: auto` | Verificar los tres eslabones de la cadena en DevTools, no sólo el `<h1>` |
| El `padding` muerto de `comercializacion.css:202` cambia de valor efectivo al aplicar la fase 2 | Es una mejora, no una regresión; verificar visualmente la página de comercialización a 375px |
| Contraste/legibilidad del texto de marca sobre el navbar en mobile | Se hereda `text-shadow` de la regla base; validar sobre el fondo real |

## Key Learnings

1. La celda de auth del navbar queda vacía en mobile pero conserva `flex: 1`, robando un tercio del ancho disponible para la marca.
2. `!important` en las declaraciones base de `.btn-vivid` y `.btn-gold` vence a cualquier especificidad, incluidos los selectores ID-scoped de las páginas.
3. El override `padding: 1rem` de `comercializacion.css:202` es código muerto hoy, porque la base lo marca como `!important`.
4. La galería colapsa a una sola columna en 575.98px, no en 991.98px como asumía la propuesta original.
5. El truncado con ellipsis exige `min-width: 0` en toda la cadena de flex items, no solamente en el elemento de texto.
