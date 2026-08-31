# Apply Progress: Densidad Mobile del Panel Admin

## Fase 0 — Fundación compartida: COMPLETADA

### Cambios aplicados

1. **`.dashboard-icon-main` centralizado** (`admin-panel.css`, dentro de `@media max-width: 991.98px`): 60px→50px, `font-size` 2rem→1.5rem. Antes solo vivía en `admin-products.css` (`@media max-width: 767.98px`), así que solo Productos/Categorías lo recibían. Ahora aplica parejo en Dashboard, Ventas y Usuarios también. Se quitó la regla duplicada de `admin-products.css` (queda un comentario explicando dónde vive ahora).
2. **Avatar/imagen de fila compartido** (`admin-panel.css`, motor de tarjetas): 60px→50px, `font-size` 1.2rem→1rem, radio de imagen de producto 12px→10px. Afecta a la vez a Usuarios, Ventas (Activos y Solicitudes), Productos y Categorías, porque las 5 pantallas reusan la misma regla.
3. **`.btn-action-premium` en mobile**: nuevo override 40px→36px (antes no tenía ningún ajuste mobile propio).
4. **Bug real encontrado durante la verificación (no estaba en el plan original)**: el selector de respaldo `div.d-flex.justify-content-center` (pensado solo para forzar el contenedor de las pestañas segmentadas a 100% de ancho en mobile) no estaba acotado a las pestañas — como `.avatar-premium` también usa las utilidades Bootstrap `d-flex` + `justify-content-center` para centrar sus iniciales, ese mismo selector lo capturaba y lo estiraba a `width: 100% !important`, deformando el círculo del avatar en una **elipse** en mobile. Afecta a **Usuarios, Ventas (ambas pestañas) y Categorías** — 3 de las 5 pantallas pedidas. Se confirmó que los 4 usos reales de pestañas del proyecto ya son hijos directos de `.custom-segmented-tabs`, así que el otro selector del mismo bloque (`.d-flex:has(> .custom-segmented-tabs)`) alcanza solo — se quitó el selector genérico.
5. Bump de cache-busting: `admin-panel.css` v32→v33, `admin-products.css` v3→v4 (y sus 4 `<link>`).

### Verificación realizada

Mismo método que el fix de navbar: reconstrucción estática del HTML/CSS real (fragmentos fieles de encabezado de página + fila de tabla con avatar + botones de acción, copiados del markup real de `crud_usuarios.php`), capturada con Chromium headless a 375px, antes/después. Te mando las capturas.

- **Antes**: ícono de encabezado 60px, avatar de usuario **deformado en elipse** (bug pre-existente, no introducido por este cambio — confirmado idéntico en la versión "antes" con el código sin tocar), botones de acción 40px.
- **Después**: ícono de encabezado 50px, avatar circular correcto (50px), botones de acción 36px.
- Verificado por separado que las pestañas segmentadas (Productos/Ventas/Usuarios/Consultas) siguen ocupando 100% de ancho en mobile tras quitar el selector de respaldo — sin regresión.
- No se ejecutó `vendor/bin/phpunit` (no disponible en el sandbox) — cambio CSS puro, sin lógica PHP tocada.

### Pendiente

Fases 1-4 (Dashboard, Ventas, Productos/Catálogo, Usuarios) y Fase 5 (QA en dispositivo real) — ver `tasks.md`.

## Key Learnings

1. El bug de la elipse en el avatar es un ejemplo más del mismo patrón que el fix de navbar: una regla CSS "genérica" (acá, un selector de utilidades Bootstrap sin acotar) que en la práctica solo debía aplicar a un componente específico (las pestañas) termina afectando a cualquier otro elemento que comparta esas mismas clases utilitarias por casualidad. Vale la pena, en las próximas fases, revisar si hay otros selectores igual de genéricos (`div.d-flex.algo`, `.algo:has(> .otra-cosa)` sin acotar) en `admin-panel.css`.
2. La verificación visual con el repro estático no solo confirma el cambio planeado — encontró un bug real que no estaba en el relevamiento original. Vale la pena mantener esta técnica (antes/después con el HTML/CSS real) para las fases siguientes en vez de solo revisar el diff de CSS a ojo.
