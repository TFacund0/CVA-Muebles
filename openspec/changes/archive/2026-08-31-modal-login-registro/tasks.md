# Tasks: Modal de Login y Registro

## 0. Pre-flight Verification (do before touching any trigger or CSS)

- [x] 0.1 Read `app/Views/front/pages/detalle_producto.php` line 130 and `app/Views/components/product_card.php` line 83 (current source, not the proposal's assumption) and record whether each `href` today points at `base_url('login')` or `base_url('registro')`. This decides whether the trigger at that exact line gets wired to `#modalLogin` or `#modalRegistro` in phase 4. (Design "Open Questions" item 2.)
- [x] 0.2 Check for an existing CDN/reverse-proxy layer in front of guest HTML (deployment config, `app/Config/*.php` headers, hosting docs/Render config). If one exists, or cannot be ruled out, add `Cache-Control: no-store` (or confirm CI4's default is already `no-store` for these routes) on `/login`, `/registro`, and any guest page rendering `modals.php`, so the session-bound CSRF token in the form is never served from a shared cache. Record the finding either way — this gates whether phase 3 needs a header change. (Design "Open Questions" item 1.)

## 1. Shared Partials

- [x] 1.1 Create `app/Views/partials/auth/_form_login.php`: fields + `csrf_field()` + hidden `redirect_to` + error block, per the data contract in design.md (`$idPrefix`, `$isModal`, `$redirectTo`). No wrapper/branding markup.
- [x] 1.2 Create `app/Views/partials/auth/_form_registro.php`: same contract plus `$isAdmin` flag controlling the `terms` checkbox vs hidden input branch.
- [x] 1.3 Update `app/Views/front/pages/login.php` to consume `_form_login` via `view()`, keeping `auth-wrapper`/`auth-card`/`auth-side-branding` untouched.
- [x] 1.4 Update `app/Views/front/pages/registro.php` to consume `_form_registro` via `view()`, keeping the `perfil_id == 1` admin branches (heading copy, hidden `terms`, "VOLVER AL PANEL" footer) in the page view, passing `$isAdmin` through.

## 2. Modals + Blur CSS

- [x] 2.1 Create `app/Views/partials/auth/modals.php`: `#modalLogin` and `#modalRegistro` Bootstrap 5 `.modal` instances, `.btn-close`, cross-links between them, each including the corresponding shared partial with `isModal=true`.
- [x] 2.2 Add the reopen-on-error inline script to `modals.php` per design.md's `reopen_modal` snippet (reads `session('reopen_modal')`, calls `bootstrap.Modal(...).show()`).
- [x] 2.3 Add the `show.bs.modal` handler in the same script that appends the `auth-blur` class to Bootstrap's generated `.modal-backdrop`.
- [x] 2.4 Add `.modal-backdrop.auth-blur` rules to `public/assets/css/pages/auth.css` (base opaque color + `@supports` blur per design.md), and bump `?v=3.1` → `?v=3.2` on every `<link>` to `auth.css` (must include the one in `layout/main.php`).
- [x] 2.5 Add the guarded include in `app/Views/layout/main.php` right before `floating_alert`: render `modals.php` only when `! session('logged_in')` and the current path is not `login`/`registro`.

## 3. Backend (reopen_modal + redirect_to + safeRedirect)

- [x] 3.1 Add `private function safeRedirect(?string $target): string` to `app/Controllers/LoginController.php` implementing the exact allow-list from design.md (empty/length check, CR/LF/NUL rejection, `//` and `/\` rejection, same-host absolute-URL check, default `/`).
- [x] 3.2 In `LoginController::auth`, replace the hardcoded `/` success redirect with `redirect()->to($this->safeRedirect($this->request->getPost('redirect_to')))`, keeping `session()->regenerate()` ordering unchanged.
- [x] 3.3 In both `LoginController::auth` failure branches, chain `->with('reopen_modal', 'login')` alongside the existing `->with('error', ...)`.
- [x] 3.4 In `LoginController::create()` (the `/login` GET action), add `session()->keepFlashdata('redirect_to')` and pass `'redirectTo' => session('redirect_to') ?? '/'` into the view data.
- [x] 3.5 In `app/Controllers/UsuarioController.php::formValidation` failure branch, chain `->with('reopen_modal', 'registro')` alongside the existing `->with('fail', ...)`.
- [x] 3.6 In `UsuarioController::formValidation` success (non-admin) branch, change the redirect to carry `redirect_to` forward: `redirect()->to('/login')->with('success', ...)->with('redirect_to', $this->request->getPost('redirect_to'))`. Do not touch the admin (`perfil_id == 1`) branch.
- [x] 3.7 Apply the `Cache-Control: no-store` finding from task 0.2 if it determined a header change is needed.

## 4. Update 8 Triggers

- [x] 4.1 `app/Views/partials/navbar.php`: add `data-bs-toggle="modal" data-bs-target="#modalLogin"` (L84) and `data-bs-target="#modalRegistro"` (L86) to the desktop auth-pill, keeping existing `href`.
- [x] 4.2 `app/Views/partials/navbar.php`: same treatment for the offcanvas triggers (L128 → `#modalLogin`, L129 → `#modalRegistro`).
- [x] 4.3 `app/Views/front/pages/mis_favoritos.php` L118: add `data-bs-toggle="modal" data-bs-target="#modalLogin"`.
- [x] 4.4 `app/Views/front/pages/detalle_producto.php` L117: add `data-bs-target="#modalLogin"`. L130: wire to `#modalLogin` or `#modalRegistro` per the task 0.1 finding.
- [x] 4.5 `app/Views/components/product_card.php` L71: add `data-bs-target="#modalLogin"`. L83: wire to `#modalLogin` or `#modalRegistro` per the task 0.1 finding.
- [x] 4.6 `app/Views/front/pages/productos.php` L11: keep `data-login-url`, add `data-login-modal="#modalLogin"`.
- [x] 4.7 `public/assets/js/pages/productos.js` L56: change the handler to open `#modalLogin` via `bootstrap.Modal` when present, falling back to `window.location.href = loginUrl` per design.md's snippet.

## 5. Verification / Automated Tests

- [x] 5.1 PHPUnit unit test for `safeRedirect()`: table-driven cases — `/x` → `/x`; `//evil.com` → `/`; `https://evil.com` → `/`; `javascript:...` → `/`; `"/x\r\nSet-Cookie: a=b"` → `/`; string > 512 chars → `/`; same-host absolute URL → passes through.
- [x] 5.2 PHPUnit feature test (`FeatureTestTrait`) for login failure: POST `/enviar-login` with bad credentials asserts `reopen_modal=login` flash is present on the redirect response.
- [x] 5.3 PHPUnit feature test for login success: POST `/enviar-login` with valid credentials and `redirect_to=/detalle_producto/5` asserts the 302 target is `/detalle_producto/5`, not `/`.
- [x] 5.4 PHPUnit feature test for registro handoff: POST `/enviar-form` success asserts redirect to `/login` with `redirect_to` flash carried forward.
- [x] 5.5 View-level test/assertion: GET `/login` (and any logged-in guest page) does not contain `id="modalLogin"` or `id="modalRegistro"` in the response body.
- [x] 5.6 Run the full suite (`vendor/bin/phpunit`) and confirm all existing login/registro endpoint, field-name, and validation tests still pass unchanged (Success Criteria item from proposal.md).

## 6. Manual Verification

- [ ] 6.1 Manually verify `crud_usuarios.php` → "registrar usuario" still renders the full-page registro form in admin mode, byte-identical to before, with no modal involved.
- [ ] 6.2 Manually verify the logged-in branch of `detalle_producto.php` and `product_card.php` renders unchanged (no modal markup, no stray `data-bs-toggle` remnants affecting logged-in users).
- [ ] 6.3 Manually verify no-JS access: disable JavaScript, click each of the 8 triggers, confirm each navigates to the full `/login` or `/registro` page correctly.
- [ ] 6.4 Manually verify the reopen flow end-to-end on at least one non-trivial origin page (e.g. `/detalle_producto/{id}` with an active filter or scroll position): wrong password → modal reopens in place with error visible and `old()` preserved → correct password → redirected back to that same origin page, logged in.

## Review Workload Forecast

| Phase | Est. changed lines |
|---|---|
| 0. Pre-flight verification | 0 (read-only, findings recorded in commit message / follow-up task notes) |
| 1. Shared partials | ~90 (2 new partials ~40 each + login.php/registro.php edits ~10) |
| 2. Modals + blur CSS | ~110 (modals.php ~70 including script, CSS ~15, main.php guard ~5, version bump ~2x1) |
| 3. Backend | ~55 (safeRedirect ~20, LoginController edits ~15, UsuarioController edits ~15, cache header if needed ~5) |
| 4. 8 triggers | ~25 (mostly one-attribute additions across 6 files + JS handler ~10) |
| 5. Automated tests | ~90 (unit table test ~25, 3 feature tests ~45, view assertion ~20) |
| 6. Manual verification | 0 (no diff, checklist only) |
| **Total** | **~370 lines** |

- Estimate sits under the 400-line review budget as a single change, so **one PR** is recommended rather than chaining — splitting would fragment the partial/modal/backend contract that's designed to land atomically (per design.md's "no copy-paste" rule and the reopen/redirect_to signals being tightly coupled to the trigger wiring).
- Strategy is ask-on-risk: this change is flagged Medium risk (buy-path adjacency, redirect handling) per proposal.md and config.yaml, so route the review request explicitly rather than auto-approving on line count alone, even though it is under budget.
- Decision needed before applying: task 0.1's finding for `detalle_producto.php` L130 and `product_card.php` L83 must be resolved and written into phase 4 before those two specific trigger edits are made — do not guess the modal target from the proposal's assumption.

## Key Learnings

1. Phase 0 must run before phase 4, not in parallel with it — the trigger wiring for 2 of the 8 entry points is genuinely unknown until the current `href` targets are read, per design.md's own "Open Questions."
2. The admin `perfil_id == 1` branches of `registro.php` are excluded from the shared partial by design, so phase 1 and phase 6.1 both need explicit checks that this boundary wasn't crossed.
3. `LoginController::create()` needs a `keepFlashdata('redirect_to')` call (task 3.4) that isn't part of the failure-branch `reopen_modal` work — easy to miss since it lives in the success/GET path, not the POST handler.
4. Total estimated diff (~370 lines) fits the 400-line review budget as one PR; splitting into chained PRs would be counterproductive here since partials, modals, and backend signals are interdependent by design.
5. `strict_tdd: true` in config.yaml means phase 5's tests (especially 5.1 `safeRedirect()` and 5.2/5.3 feature tests) should be written before or alongside the corresponding phase 3 backend code, not after.
