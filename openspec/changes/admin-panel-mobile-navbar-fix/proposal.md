# Proposal: Fix Superposición Navbar Mobile — Panel Admin

## Intent

Es el primer paso de una serie de mejoras mobile al panel de administrador. El usuario reportó, en un dispositivo real, que al abrir el menú lateral el título del panel ("CVA ADMIN") y el botón de hamburguesa se ven superpuestos y desprolijos. La exploración confirmó la causa raíz: un `z-index` del botón hamburguesa (`10001`) por encima del propio panel abierto (`10000`) y su overlay (`999`), haciendo que el botón del topbar se siga renderizando encima del header del sidebar cuando este se despliega.

## Scope

### In Scope
1. **Fix de stacking**: bajar `z-index` de `.btn-admin-toggle` (`public/assets/css/admin/admin-panel.css`) por debajo del overlay (999) y del sidebar mobile (10000), para que quede correctamente tapado cuando el menú está abierto.
2. **Cache-busting**: bump de `?v=` en el link de `admin-panel.css` (`admin_layout.php`), siguiendo la convención ya usada en el proyecto (ver commit `55d63cb`).

### Out of Scope
- El título del sidebar (`sidebar-logo`, "CVA ADMIN") **ya es un link** a `/admin-dashboard` (`admin_sidebar.php:4`) — no requiere cambio de código, solo quedó verificado en la exploración.
- Reducir tamaño de íconos/elementos del resto del panel (sidebar nav, tablas, botones por pestaña) — iteración explícitamente posterior y separada, pedida por el usuario para después de este fix.
- Cualquier cambio de lógica PHP/backend.

## Capabilities

### New Capabilities
None.

### Modified Capabilities
None — fix presentacional (CSS), sin cambio de comportamiento/spec.

## Approach

Cambio mínimo de una sola propiedad CSS (`z-index: 10001` → `z-index: 1` en `.btn-admin-toggle`), sin tocar estructura HTML ni el resto del sistema de stacking (`.admin-sidebar`, `.sidebar-overlay` quedan intactos). Validado visualmente reconstruyendo el layout real (mismo HTML/CSS/Bootstrap) en un repro estático capturado con Chromium headless a 375px, antes y después del cambio (ver `exploration.md`).

## Affected Areas

| Area | Impact | Description |
|------|--------|--------------|
| `public/assets/css/admin/admin-panel.css` | Modified | `.btn-admin-toggle` línea ~416: `z-index: 10001` → `z-index: 1` |
| `app/Views/layout/admin_layout.php` | Modified | Bump `admin-panel.css?v=31.0` → `?v=32.0` |

## Success Criteria

- [ ] Al abrir el menú lateral en mobile (≤991.98px), el botón hamburguesa del topbar queda tapado por el overlay/panel — no se ve flotando sobre el header "CVA ADMIN".
- [ ] El header del sidebar (martillo + "CVA ADMIN" + botón X de cierre) se ve limpio, sin elementos superpuestos, en capturas a 375px.
- [ ] En estado cerrado, el botón hamburguesa sigue siendo visible y clickeable con el mismo aspecto que antes (no debe verse afectado por la baja de z-index, ya que no compite con nada en ese estado).
- [ ] Tocar el ícono o el texto "CVA ADMIN" del sidebar navega a `/admin-dashboard` (ya funcionaba antes del cambio; se re-confirma que sigue funcionando).
- [ ] Sin cambios en desktop (≥992px): el sidebar es siempre visible ahí (no aplica el estado `sidebar-visible`/mobile), por lo que el `z-index` del toggle es irrelevante en ese breakpoint.

## Risks

| Risk | Likelihood | Mitigation |
|------|------------|------------|
| Bajar el z-index del toggle podría dejarlo tapado por algún otro elemento del topbar en el estado cerrado | Baja | Revisado: no hay otros elementos con z-index competitivo dentro de `.admin-topbar` en ese estado (confirmado por grep de `z-index` en `admin-panel.css`); el toggle sigue siendo el único elemento posicionado ahí. |
| Verificación solo visual/estática (sin PHP+MySQL corriendo) | Media | Reconstrucción fiel del HTML/CSS real (no un mockup aproximado) + captura antes/después; QA final en dispositivo real queda como tarea pendiente de usuario (ver `tasks.md`). |

## Rollback Plan

Cambio de una sola línea en un archivo CSS + un bump de versión en un `<link>`. `git revert` del commit restaura el comportamiento anterior sin impacto en datos, esquema ni caché de servidor.

## Dependencies

Ninguna. Cambio aislado, no bloquea ni depende de otro trabajo en curso.

## Decisions (asumidas, a confirmar con el usuario si corresponde)

- Se interpretó "el título de CVA Muebles" como el logo real del sidebar ("CVA ADMIN") — es el único texto+ícono que aparece junto a un control de menú en el panel admin mobile.
- El pedido de "que el título direccione al inicio al tocarlo" se documenta como ya satisfecho por el código existente, no como una tarea nueva.

## Key Learnings

(ver `exploration.md` para el detalle completo de la investigación)
