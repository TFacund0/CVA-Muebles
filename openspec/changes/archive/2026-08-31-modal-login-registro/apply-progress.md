# Apply Progress: Modal de Login y Registro

## 0. Pre-flight Verification
- [x] 0.1 `detalle_producto.php` L117/L130 and `product_card.php` L71/L83 all point at `base_url('login')` today — none point at `/registro`. Both L130 and L83 wired to `#modalLogin`, not `#modalRegistro`.
- [x] 0.2 No CDN/reverse-proxy layer found in the repo (no `render.yaml`, no proxy config). `app/Config/Security.php` confirms `$regenerate = false`, `$expires = 7200`. No `Cache-Control` header change applied — none found necessary; recorded as the finding, task 3.7 skipped as a no-op.

## 1. Shared Partials
- [x] 1.1 Created `app/Views/partials/auth/_form_login.php`
- [x] 1.2 Created `app/Views/partials/auth/_form_registro.php`
- [x] 1.3 `login.php` consumes `_form_login`, wrapper/branding untouched
- [x] 1.4 `registro.php` consumes `_form_registro`, admin branches (heading, VOLVER AL PANEL) kept in the page view

## 2. Modals + Blur CSS
- [x] 2.1 Created `app/Views/partials/auth/modals.php` (#modalLogin, #modalRegistro, cross-links)
- [x] 2.2 Reopen-on-error inline script added
- [x] 2.3 `show.bs.modal` handler adds `.auth-blur` to Bootstrap's backdrop
- [x] 2.4 `.modal-backdrop.auth-blur` CSS added; `?v=3.1` -> `?v=3.2` bumped in `login.php`, `registro.php`, and the new global `<link>` in `layout/main.php`
- [x] 2.5 Guarded include added in `layout/main.php` (`uri_string()`-based guard, not `service('uri')->getPath()` — the latter does not reflect the request path in this app and broke the guard in feature tests)

## 3. Backend
- [x] 3.1 `LoginController::safeRedirect()` implemented (private method, allow-list per design.md)
- [x] 3.2 `LoginController::auth` success path uses `safeRedirect()` instead of hardcoded `/`
- [x] 3.3 Both `auth()` failure branches (throttle + bad credentials) chain `->with('reopen_modal', 'login')`
- [x] 3.4 `LoginController::create()` calls `session()->keepFlashdata('redirect_to')` and passes `redirectTo` to the view
- [x] 3.5 `UsuarioController::formValidation` failure branch chains `->with('reopen_modal', 'registro')`
- [x] 3.6 `UsuarioController::formValidation` success (non-admin) branch carries `redirect_to` forward; admin branch untouched
- [x] 3.7 No Cache-Control change needed (see 0.2) — task closed as a no-op

## 4. Update 8 Triggers
- [x] 4.1 navbar.php desktop auth-pill (L84 -> #modalLogin, L86 -> #modalRegistro)
- [x] 4.2 navbar.php offcanvas triggers (L128 -> #modalLogin, L129 -> #modalRegistro)
- [x] 4.3 mis_favoritos.php trigger -> #modalLogin
- [x] 4.4 detalle_producto.php both triggers -> #modalLogin (per 0.1 finding)
- [x] 4.5 product_card.php both triggers -> #modalLogin (per 0.1 finding)
- [x] 4.6 productos.php `data-login-modal="#modalLogin"` added, `data-login-url` kept
- [x] 4.7 productos.js handler opens `#modalLogin` via bootstrap.Modal, falls back to `window.location.href` when unavailable; bumped `productos.js?v=1.0` -> `?v=1.1`

## 5. Verification / Automated Tests
- [x] 5.1 `tests/unit/LoginControllerSafeRedirectTest.php` — table-driven `safeRedirect()` coverage (relative ok, null/empty/oversize/CRLF/protocol-relative/backslash/external scheme -> `/`, same-host absolute URL passes through)
- [x] 5.2 `LoginControllerFeatureTest::testAuthConPasswordIncorrectaNoAutentica` extended to assert `reopen_modal=login`
- [x] 5.3 `LoginControllerFeatureTest::testAuthConRedirectToValidoRedirigeAlOrigen` / `testAuthConRedirectToExternoCaeARaiz` added
- [x] 5.4 `UsuarioControllerFeatureTest::testEnviarFormAnonimoConRedirectToLoCarraAlLogin` and `testEnviarFormEmailDuplicadoReabreModalRegistro` added
- [x] 5.5 `tests/unit/AuthModalGuardTest.php` — GET `/login`, `/registro`, logged-in `/` assert no modal markup; guest `/` asserts modal markup present
- [x] 5.6 Full suite green: `vendor/bin/phpunit` — 128 tests, 338 assertions, 0 failures

## 6. Manual Verification
- [ ] 6.1 Not manually verified in this session (no browser available) — structurally confirmed `crud_usuarios.php` still renders `registro.php` unchanged and `_form_registro.php`'s `$isAdmin` branch is covered by `UsuarioControllerFeatureTest::testEnviarFormAdminRegistraYRedirigeACrudUsuarios`
- [ ] 6.2 Not manually verified (no browser) — logged-in branches of `detalle_producto.php`/`product_card.php` were not touched by any edit (only the guest `else` branches got `data-bs-*` attributes)
- [ ] 6.3 Not manually verified (no browser) — every trigger keeps its original `href`, `data-bs-toggle`/`data-bs-target` are additive only
- [ ] 6.4 Not manually verified (no browser) — reopen flow covered at the controller/session level by 5.2-5.4, not end-to-end in a real browser

## Notes
- Task 0.1's premise ("L130/L83 may point at /registro") did not hold: both already pointed at `/login`, so no ambiguity to resolve — all 4 buy-path triggers wire to `#modalLogin`.
- `service('uri')->getPath()` from design.md's Global Include Guard snippet does not return the current request path in this app's test harness; switched to the `uri_string()` helper, which resolved the guard failing to hide the modal on `/login`/`/registro` in `AuthModalGuardTest`.
- Two new test fixtures used usernames that exceeded `UsuarioModel`'s `max_length[20]` validation rule (`registro_redirect_xyz`, 22 chars) — this silently redirected registration to a generic failure page instead of the expected `/login`. Fixed by shortening to `reg_redirect_xyz`.
