# Proposal: Modal de Login y Registro

## Intent

Today the 8 auth entry points navigate away to full `/login` and `/registro` pages, destroying the user's context. The worst case is intent-driven: a user on `/detalle_producto/{id}` who clicks "Iniciá sesión para comprar" loses the product, and after a successful login lands on `/` (hardcoded in `LoginController::auth`), not back on the product. Exploration also confirmed a latent defect: the `error` flashdata set by `LoginController::auth` is rendered **nowhere** — not in `login.php`, not in `layout/main.php` — so a wrong password today produces a silent reload.

Goal: keep the user on the page. Open a centered Bootstrap modal with a blurred backdrop and an X to close, reusing the existing form and the existing POST endpoints.

## Scope

### In Scope

1. **Shared partials** — extract the two `<form>` bodies into `app/Views/partials/auth/_form_login.php` and `_form_registro.php`, consumed by BOTH the full pages and the modals. Single source of truth for fields, `csrf_field()`, `old()`, and validation errors.
2. **Modal markup** — `app/Views/partials/auth/modals.php`, included once in `layout/main.php` for guests only (`! session('logged_in')`). Two Bootstrap 5 `.modal` instances (`#modalLogin`, `#modalRegistro`) with `.btn-close` and a cross-link to switch between them.
3. **Blur backdrop** — new CSS in `auth.css` (bump `?v=`): `.modal-backdrop.auth-blur` with `backdrop-filter: blur(6px)`, plus a non-blur fallback for unsupported browsers.
4. **Error rendering inside the modal** — render `session('error')` / `session('fail')` and `$validation->getError()` in the shared partials. This also fixes the currently invisible login error.
5. **Reopen-on-error** — controllers add a flash flag (`reopen_modal` = `login|registro`) alongside the existing `->with('error', ...)`. A small inline script in `modals.php` reads it and calls `new bootstrap.Modal(...).show()` on load. `redirect()->back()` already returns to the origin page, so the modal reopens in place.
6. **Return-to-origin after success** — hidden `<input name="redirect_to">` in both forms, populated server-side with `current_url()` (the page rendering the modal). `LoginController::auth` redirects to that value instead of `/`; `UsuarioController::formValidation` carries it through to `/login`'s success path. Validated against an allow-list (must be a same-host relative path) to prevent open redirect.
7. **8 entry points** — swap `href="<?= base_url('login') ?>"` for `data-bs-toggle="modal" data-bs-target="#modalLogin"` in: `partials/navbar.php` (desktop auth-pill L84/L86, offcanvas L128/L129), `front/pages/mis_favoritos.php` L118, `front/pages/detalle_producto.php` L117 + L130, `components/product_card.php` L71 + L83. Also `front/pages/productos.php` L11 `data-login-url` (JS-driven redirect) must be repointed at the modal.
8. **Progressive enhancement** — every trigger keeps a real `href` to `/login` as fallback so no-JS and direct-URL access still work.

### Out of Scope

- **Admin flow**: `app/Views/back/users/crud_usuarios.php` reuses `registro.php` in `perfil_id == 1` mode. It stays a full page; the modal is guest-only. The `perfil_id == 1` branches stay in the page view, NOT in the shared form partial.
- Rewriting the backend to AJAX/JSON. POST stays classic server-side.
- Any change to `UsuarioService::autenticar`, password hashing, the throttler, or the validation rules themselves.
- Removing `/login` and `/registro` routes, or `auth-wrapper` / `auth-side-branding` markup (page-only, not duplicated into the modal).
- Password recovery, social login, "remember me".

## Capabilities

### New Capabilities

- `auth-modal`: guest-facing modal presentation of login/registro, its triggers, the reopen-on-error signal, and the return-to-origin contract.

### Modified Capabilities

None — no existing `openspec/specs/` capability covers auth today.

## Approach

Presentation-layer change on top of unchanged endpoints. Bootstrap 5 (CSS L23 + `bootstrap.bundle.min.js` L35 in `layout/main.php`) is already loaded globally and the navbar already uses `data-bs-toggle="offcanvas"`, so the modal adds **zero** new dependencies. The full pages and the modal render the same partial, so the two can never drift.

Two server-side signals do all the stateful work, both flash/POST-based and both invisible to JS:

| Need | Mechanism | Why this one |
|---|---|---|
| Reopen modal after a failed attempt | Flash `reopen_modal` set next to the existing `->with('error')` | Reuses the flashdata the controllers already set; ~3 lines per controller, zero validation logic touched |
| Return to origin after success | Hidden `redirect_to` field seeded with `current_url()` | Works with a plain `<form>` POST and survives `redirect()->back()->withInput()`; simpler than session state, which would leak across tabs. Session fallback only if a case is found where the hidden field cannot be seeded |

`previous_url()` was rejected: after the POST it points at the form's own page, not the true origin.

## Affected Areas

| Area | Impact | Description |
|------|--------|-------------|
| `app/Views/partials/auth/_form_login.php` | New | Shared login form + error block |
| `app/Views/partials/auth/_form_registro.php` | New | Shared registro form + error block |
| `app/Views/partials/auth/modals.php` | New | Both modals + reopen script |
| `app/Views/layout/main.php` | Modified | Include `modals.php` for guests |
| `app/Views/front/pages/login.php` | Modified | Consume shared partial |
| `app/Views/front/pages/registro.php` | Modified | Consume shared partial; keep admin branches |
| `app/Views/partials/navbar.php` | Modified | 4 triggers (L84, L86, L128, L129) |
| `app/Views/front/pages/detalle_producto.php` | Modified | 2 triggers (L117, L130) |
| `app/Views/components/product_card.php` | Modified | 2 triggers (L71, L83) |
| `app/Views/front/pages/mis_favoritos.php` | Modified | 1 trigger (L118) |
| `app/Views/front/pages/productos.php` | Modified | `data-login-url` (L11) JS redirect |
| `app/Controllers/LoginController.php` | Modified | `reopen_modal` flag + `redirect_to` |
| `app/Controllers/UsuarioController.php` | Modified | `reopen_modal` flag + carry `redirect_to` |
| `public/assets/css/pages/auth.css` | Modified | Modal + blur styles; bump `?v=3.1` |

## Risks

| Risk | Likelihood | Mitigation |
|---|---|---|
| **CSRF**: CI4 regenerates the token per request. The modal is rendered on every guest page, so a page cached by the browser/CDN could carry a stale token and fail on submit | Med | Verify `Config/Security.php` (`$regenerate`, `$redirect`); on a CSRF failure the user lands back on the page and the reopen flag re-renders a fresh token. Confirm auth pages send no-store |
| **Checkout/cart adjacency** — per `openspec/config.yaml`, anything near `CarritoController`/`VentasController` is higher risk. `detalle_producto` and `product_card` triggers sit on the buy path | Med | Do not touch cart controllers or `add-to-cart` markup; only swap the guest-only auth `<a>`. Manually verify the logged-in branch of both views renders byte-identically |
| **Open redirect** via `redirect_to` | Low | Server-side allow-list: relative same-host paths only; fall back to `/` on anything else |
| **Admin regression**: `crud_usuarios.php` reuses `registro.php` | Med | Keep `perfil_id == 1` branches in the page view; the shared partial takes an explicit `$isAdmin` flag defaulting to false. Explicit manual check of the admin path |
| **Markup drift** if the modal duplicates fields instead of including the partial | Med | Hard rule: modal body is `<?= view('partials/auth/_form_login') ?>` — no copy-paste |
| `backdrop-filter` unsupported (older Safari/Android WebView) | Low | Keep Bootstrap's default dark opacity as the base layer; blur is additive only |
| Modal duplicates `id` attributes already present on the full page (`#email`, `#password`, `#terms`) | Med | Prefix modal ids (`modal-email`, …) via a partial parameter, or never render the modal on `/login` and `/registro` |
| Stale CSS cache | Med | Per project convention, bump the `?v=` on `auth.css` in both include sites |

**Risk level: Medium** — no DB migration, no schema, no `app/Config/Database.php`. Elevated from Low only because the auth entry points sit on the purchase path and `LoginController::auth` gains a redirect branch.

## Rollback Plan

`git revert` of the change commit. The change is view/CSS plus two small controller edits; no migration, no schema, no data, no cache invalidation beyond the `?v=` bump. Partial rollback is available and safe: reverting only the trigger markup (the 8 entry points) restores full-page navigation while leaving the new partials dormant, since `/login` and `/registro` are never removed. The `redirect_to` field is ignored by an old controller, and an old view simply never sends it — the two halves degrade independently.

## Dependencies

- Read `app/Config/Security.php` before implementing, to confirm CSRF token regeneration behavior with a globally-rendered form.
- Confirm `UsuarioController::formValidation`'s success path can carry `redirect_to` through its `/login` redirect.

## Success Criteria

- [ ] Clicking any of the 8 entry points opens a centered modal without navigating away; the page behind is visibly blurred and the X closes it with the underlying page state intact.
- [ ] A wrong password shows the error **inside** the reopened modal on the origin page (today it shows nothing anywhere).
- [ ] A successful login from `/detalle_producto/{id}` returns to `/detalle_producto/{id}`, not `/`.
- [ ] A `redirect_to` pointing at an external host is rejected and falls back to `/`.
- [ ] Direct navigation to `/login` and `/registro` still renders the full page, with JS disabled included.
- [ ] `crud_usuarios.php` → "registrar usuario" still opens the full page in admin mode, unchanged.
- [ ] Login/registro field names, endpoints, and validation rules are unchanged (`vendor/bin/phpunit` green).

## Proposal question round

The executor cannot prompt the user directly. Open product questions, each with the assumption taken if unanswered:

1. **Logged-in users**: assumed the modal partial is not rendered at all for `logged_in` sessions (smaller payload, no duplicate ids). Confirm no logged-in surface needs it.
2. **Registro success**: assumed registro still redirects to `/login`, carrying `redirect_to` forward, rather than auto-logging the user in. Auto-login would be a larger behavior change.
3. **`/login` and `/registro` pages themselves**: assumed the modal is NOT rendered on those two routes (avoids duplicate DOM ids). The existing cross-links between them stay page navigations.
4. **Modal on the `productos.php` JS path (L11 `data-login-url`)**: assumed that JS handler should open the modal instead of redirecting. Confirm no flow depends on the actual navigation.
5. **Origin scope**: assumed `redirect_to` applies to every entry point uniformly, not just the product/buy path.
