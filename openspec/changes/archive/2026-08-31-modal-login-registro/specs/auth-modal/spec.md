# Auth Modal Specification

## Purpose

Guest-facing modal presentation of login and registro, reusing the existing
`/enviar-login` and `/enviar-form` POST endpoints, form partials, CSRF, and
validation unchanged. Covers modal triggers at all 8 entry points, the
reopen-on-error signal, and the return-to-origin redirect contract.

## Requirements

### Requirement: Modal Trigger Coverage
The system MUST open `#modalLogin` or `#modalRegistro` via
`data-bs-toggle="modal"` from each of the 8 guest entry points: navbar
desktop auth-pill, navbar offcanvas, `mis_favoritos.php`, `detalle_producto.php`
(x2), `product_card.php` (x2), and the `productos.php` JS handler bound to
`data-login-url`.

#### Scenario: Navbar desktop opens login modal
- GIVEN a guest viewing any page with the navbar
- WHEN they click the desktop auth-pill "Iniciar sesión" trigger
- THEN `#modalLogin` opens centered without a page navigation

#### Scenario: Product detail trigger opens login modal
- GIVEN a guest on `/detalle_producto/{id}`
- WHEN they click either "Iniciá sesión para comprar" trigger
- THEN `#modalLogin` opens and the underlying page state is preserved

#### Scenario: productos.php JS trigger opens modal instead of redirecting
- GIVEN a guest on `/productos` interacting with the JS handler bound to `data-login-url`
- WHEN the handler fires
- THEN it MUST open `#modalLogin` instead of navigating the browser to `/login`

### Requirement: Modal Close Preserves Page State
The system MUST allow the guest to close an open modal via `.btn-close` (X)
without reloading or losing the underlying page's scroll position, filters,
or form state.

#### Scenario: Closing the modal keeps the page intact
- GIVEN `#modalLogin` or `#modalRegistro` is open over `/productos` with active filters
- WHEN the user clicks the X
- THEN the modal closes and the filters remain applied with no page reload

### Requirement: Blurred Backdrop
The system MUST render a blurred backdrop (`backdrop-filter: blur(6px)`) behind
an open modal, with a non-blur opaque fallback for browsers without
`backdrop-filter` support.

#### Scenario: Backdrop is blurred while modal is open
- GIVEN any modal is open
- WHEN the backdrop renders
- THEN it SHOULD show `backdrop-filter: blur(6px)` and MUST at minimum show Bootstrap's default dark overlay

### Requirement: Reopen Modal On Error
The system MUST set a `reopen_modal` flash flag (`login` or `registro`)
alongside the existing `error`/`fail` flashdata whenever `LoginController::auth`
or `UsuarioController::formValidation` fails, and MUST render that error
message inside the corresponding shared form partial when the modal reopens.

#### Scenario: Wrong password reopens login modal with visible error
- GIVEN a guest submits invalid credentials via `#modalLogin` on any origin page
- WHEN `LoginController::auth` rejects the credentials
- THEN the response MUST redirect back to the origin page with `reopen_modal=login`
- AND `#modalLogin` MUST reopen automatically showing the error message inside the modal

#### Scenario: Registro validation failure reopens registro modal
- GIVEN a guest submits an invalid registro form via `#modalRegistro`
- WHEN `UsuarioController::formValidation` returns validation errors
- THEN `#modalRegistro` MUST reopen on the origin page showing `$validation->getError()` per field

### Requirement: Return To Origin After Login
The system MUST redirect to the `redirect_to` value after a successful login
instead of the hardcoded `/`, and MUST validate `redirect_to` as a relative,
same-host path, falling back to `/` otherwise.

#### Scenario: Successful login returns to product detail page
- GIVEN a guest opens `#modalLogin` from `/detalle_producto/42`
- WHEN login succeeds
- THEN the response MUST redirect to `/detalle_producto/42`

#### Scenario: External redirect_to is rejected
- GIVEN `redirect_to` is set to an external absolute URL (e.g. `https://evil.example`)
- WHEN login succeeds
- THEN the system MUST reject it and redirect to `/` instead

### Requirement: Registro Success Keeps Redirecting To Login
The system MUST continue redirecting a successful registro submission to
`/login`, without auto-login, and MUST carry the original `redirect_to` value
forward so it survives into the login step.

#### Scenario: Registro success carries redirect_to into login
- GIVEN a guest registers via `#modalRegistro` opened from `/mis_favoritos`
- WHEN registro succeeds
- THEN the system MUST redirect to `/login` with `redirect_to` still pointing at `/mis_favoritos`
- AND the user MUST NOT be automatically logged in

### Requirement: Modal Not Rendered For Logged-In Users
The system MUST NOT render `modals.php` when `session('logged_in')` is true.

#### Scenario: Logged-in user's page omits modal markup
- GIVEN a logged-in user views any guest-facing page
- WHEN the page renders
- THEN `#modalLogin` and `#modalRegistro` MUST NOT be present in the DOM

### Requirement: Modal Not Rendered On Auth Pages Themselves
The system MUST NOT render `modals.php` on `/login` or `/registro`, to avoid
duplicate DOM ids with the full-page forms. Cross-links between `/login` and
`/registro` remain full page navigations.

#### Scenario: /login page has no modal markup
- GIVEN a guest navigates directly to `/login`
- WHEN the page renders
- THEN `#modalLogin` and `#modalRegistro` MUST NOT be present, and the full-page form MUST render using the shared partial

#### Scenario: Cross-link from /login to /registro is a real navigation
- GIVEN a guest is on `/login`
- WHEN they click the link to registro
- THEN the browser MUST navigate to `/registro` as a full page load

### Requirement: Progressive Enhancement Without JS
The system MUST keep a real `href` (`/login`, `/registro`) on every trigger so
direct navigation and no-JS access render the equivalent full page unchanged.

#### Scenario: No-JS access still reaches the login page
- GIVEN JavaScript is disabled in the browser
- WHEN a guest clicks any of the 8 triggers
- THEN the browser MUST navigate to `/login` or `/registro` and render the full page correctly

### Requirement: Admin Registro Flow Out Of Scope
The system MUST NOT alter `crud_usuarios.php`'s admin registration behavior
(`perfil_id == 1`); it remains a full page unaffected by the modal.

#### Scenario: Admin user creation is unchanged
- GIVEN an admin opens "registrar usuario" from `crud_usuarios.php`
- WHEN the page renders
- THEN it MUST show the full-page registro form in admin mode, with no modal involved

### Requirement: Unchanged Endpoints And Validation
The system MUST NOT change the `/enviar-login` and `/enviar-form` endpoint
paths, field names, `csrf_field()`, `old()` usage, or existing validation
rules.

#### Scenario: Existing backend tests remain green
- GIVEN the modal and shared partials are in place
- WHEN the existing PHPUnit suite runs
- THEN all login/registro endpoint, field-name, and validation tests MUST pass unchanged

## Key Learnings

1. The proposal's 5 open questions were all confirmed by the user, so the spec encodes them as MUST requirements rather than open risks.
2. The login error flashdata is currently rendered nowhere in the app, making "reopen with visible error" both a new requirement and a bugfix.
3. `redirect_to` needs an explicit same-host allow-list requirement because the hardcoded `/` redirect in `LoginController::auth` is being replaced.
4. Modal exclusion from `/login`/`/registro` and from logged-in sessions are both spec-level MUST NOTs to prevent duplicate DOM ids and unnecessary payload.
5. No existing `openspec/specs/` capability covers auth, so this is a full new spec rather than a delta.
