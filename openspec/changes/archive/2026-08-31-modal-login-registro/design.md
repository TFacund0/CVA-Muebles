# Design: Modal de Login y Registro

## Technical Approach

Pure presentation layer over unchanged POST endpoints (`/enviar-login`, `/enviar-form`). Three new view partials become the single source of truth for both the full pages and the Bootstrap 5 modals rendered globally in `layout/main.php` for guests. Two server-side signals carry state: flash `reopen_modal` (reopen after failure) and POST `redirect_to` (return to origin). No new dependency, no AJAX, no JS framework.

## Architecture Decisions

| # | Decision | Choice | Alternatives rejected | Rationale |
|---|---|---|---|---|
| 1 | Form reuse | One partial per form, parameterized (`$idPrefix`, `$isModal`, `$isAdmin`, `$redirectTo`) | Copy markup into modal | CI4 `view()` partials are the existing convention; duplication guarantees drift |
| 2 | Reopen signal | Flashdata `reopen_modal` + inline vanilla script | Query string `?modal=login`; sessionStorage | Controllers already set flashdata on the same redirect; zero URL pollution, survives `redirect()->back()->withInput()` |
| 3 | Return-to-origin | Hidden `redirect_to` seeded with `current_url()` | `previous_url()`; session key | After POST, `previous_url()` is the form page itself; session state leaks across tabs |
| 4 | Registro → login handoff | Flash `redirect_to` re-flashed by `LoginController::create()` | Query string on `/login` | Keeps the URL clean and inherits the same allow-list validation |
| 5 | Modal scope | Rendered only for guests and only outside `/login` and `/registro` | Always render with prefixed ids everywhere | Removes duplicate-id risk entirely and shrinks payload for logged-in pages |
| 6 | CSRF | No change to `app/Config/Security.php` | Force `$regenerate = true` / per-modal AJAX token | Verified: `$csrfProtection='session'`, `$tokenRandomize=true`, **`$regenerate=false`** — the token stays valid across requests, so a globally rendered modal cannot go stale within `$expires=7200` |

## File Structure and Partial Contracts

```
app/Views/partials/auth/
├── _form_login.php      ← fields + error block only (no wrapper, no branding)
├── _form_registro.php   ← fields + error block only
└── modals.php           ← #modalLogin, #modalRegistro, reopen script
```

`_form_login.php` data contract (all optional, defaulted at the top of the partial):

```php
<?php
$idPrefix    = $idPrefix    ?? '';                 // '' page | 'm-' modal
$isModal     = $isModal     ?? false;
$redirectTo  = $redirectTo  ?? current_url();
$switchMode  = $switchMode  ?? 'link';             // 'link' page | 'modal'
?>
<form method="post" action="<?= base_url('enviar-login') ?>">
  <?= csrf_field() ?>
  <input type="hidden" name="redirect_to" value="<?= esc($redirectTo, 'attr') ?>">
  <?php if (session('error')): ?>
    <div class="alert alert-danger py-2 small"><?= esc(session('error')) ?></div>
  <?php endif; ?>
  <label for="<?= $idPrefix ?>email">…</label>
  <input id="<?= $idPrefix ?>email" name="email" value="<?= old('email') ?>" …>
  …
```

`_form_registro.php` adds `$isAdmin = $isAdmin ?? false`. The `perfil_id == 1` branches of `registro.php` (heading copy, hidden `terms`, "VOLVER AL PANEL" footer) stay in the **page view**; the partial only receives `$isAdmin` to decide the `terms` checkbox vs hidden input. Errors keep the existing inline `\Config\Services::validation()->getError()` pattern.

## Page Consumption

`login.php` and `registro.php` keep `auth-wrapper` / `auth-card` / `auth-side-branding` / `auth-side-form` untouched; only the `<form>…</form>` block is replaced by:

```php
<?= view('partials/auth/_form_login', [
      'idPrefix'   => '',
      'isModal'    => false,
      'redirectTo' => session('redirect_to') ?? '/',
]) ?>
```

`auth-wrapper` and the branding column are **never** rendered inside the modal.

## Reopen Mechanism

Controllers append one chained call:

```php
// LoginController::auth (both failure branches)
return redirect()->back()->withInput()
    ->with('error', $resultado['message'])
    ->with('reopen_modal', 'login');

// UsuarioController::formValidation (failure branch)
->with('fail', $resultado['message'])->with('reopen_modal', 'registro');
```

`modals.php` tail (vanilla, no dependency beyond the already-loaded bundle):

```php
<?php if ($reopen = session('reopen_modal')): ?>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    var el = document.getElementById(<?= json_encode($reopen === 'registro' ? 'modalRegistro' : 'modalLogin') ?>);
    if (el) new bootstrap.Modal(el).show();
  });
</script>
<?php endif; ?>
```

Because `redirect()->back()` returns to the origin page, the modal reopens in place with `old()` and the error already rendered by the partial.

## redirect_to Contract

Generated at render time (`current_url()` of the page hosting the modal), travels as a hidden input, validated server-side before use:

```php
// LoginController — private helper
private function safeRedirect(?string $target): string
{
    $target = trim((string) $target);
    if ($target === '' || strlen($target) > 512) return '/';
    // strip CR/LF (header-injection) and reject scheme/authority forms
    if (preg_match('/[\r\n\0]/', $target)) return '/';
    if (str_starts_with($target, '//') || str_starts_with($target, '/\\')) return '/';
    if (preg_match('#^[a-z][a-z0-9+.-]*:#i', $target)) {
        // absolute URL allowed only when same host as base_url()
        $host = parse_url($target, PHP_URL_HOST);
        if ($host === null || $host !== parse_url(base_url(), PHP_URL_HOST)) return '/';
        return $target;
    }
    return str_starts_with($target, '/') ? $target : '/';
}
```

Success path becomes `return redirect()->to($this->safeRedirect($this->request->getPost('redirect_to')))->with('success', …);` — `session()->regenerate()` order is unchanged.

## Registro → Login Handoff

`UsuarioController::formValidation` success (non-admin) path:

```php
return redirect()->to('/login')
    ->with('success', $resultado['message'])
    ->with('redirect_to', $this->request->getPost('redirect_to'));
```

`LoginController::create()` re-flashes it so it survives one more hop and passes it into the view:

```php
session()->keepFlashdata('redirect_to');
return view('front/pages/login', ['title' => 'Login', 'redirectTo' => session('redirect_to') ?? '/']);
```

The value is still validated by `safeRedirect()` on submit — the flash is a convenience, never trusted.

## Blur CSS

In `public/assets/css/pages/auth.css` (bump both `?v=3.1` → `?v=3.2`, plus the new `<link>` in `layout/main.php`):

```css
.modal-backdrop.auth-blur { background-color: rgba(33, 24, 18, .55); }
@supports ((backdrop-filter: blur(6px)) or (-webkit-backdrop-filter: blur(6px))) {
  .modal-backdrop.auth-blur { -webkit-backdrop-filter: blur(6px); backdrop-filter: blur(6px); }
}
```

The `auth-blur` class is added to the generated backdrop in the same inline script via the modal's `show.bs.modal` handler (Bootstrap creates `.modal-backdrop` itself). Base opacity stands alone when `backdrop-filter` is unsupported.

## Global Include Guard

In `layout/main.php`, right before `floating_alert`:

```php
<?php $uri = service('uri')->getPath(); ?>
<?php if (! session('logged_in') && ! in_array(trim($uri, '/'), ['login', 'registro'], true)): ?>
  <?= view('partials/auth/modals') ?>
<?php endif; ?>
```

Guest-only plus route-excluded ⇒ duplicate ids are structurally impossible, so no id prefix is strictly required (the `$idPrefix` parameter remains as defense in depth).

## Sequence Diagram

```mermaid
sequenceDiagram
  actor U as Guest
  participant B as Browser
  participant M as main.php + modals.php
  participant L as LoginController
  participant S as UsuarioService

  U->>B: GET /detalle_producto/5
  B->>M: render (guest, not /login) → #modalLogin with redirect_to=/detalle_producto/5
  U->>B: click "Iniciá sesión para comprar" (data-bs-toggle)
  B->>B: modal.show() + .modal-backdrop.auth-blur
  U->>B: submit (email, pass, csrf, redirect_to)
  B->>L: POST /enviar-login
  L->>S: autenticar()
  S-->>L: status=error
  L-->>B: 302 back + flash error + reopen_modal=login + old()
  B->>M: re-render /detalle_producto/5
  M->>B: inline script → new bootstrap.Modal(#modalLogin).show()
  U->>B: submit again (correct pass)
  B->>L: POST /enviar-login
  L->>S: autenticar() → success
  L->>L: session()->regenerate(); set(data); safeRedirect('/detalle_producto/5')
  L-->>B: 302 /detalle_producto/5 + flash success
  B-->>U: product page, logged in, modal not rendered
```

## Trigger Pattern (8 entry points)

Progressive enhancement — keep the real `href`, add the Bootstrap attributes:

```html
<a href="<?= base_url('login') ?>"
   class="auth-pill-link"
   data-bs-toggle="modal"
   data-bs-target="#modalLogin">Ingresar</a>
```

Applied to: `navbar.php` L84 (`#modalLogin`), L86 (`#modalRegistro`), L128, L129; `mis_favoritos.php` L118; `detalle_producto.php` L117, L130; `product_card.php` L71, L83. With JS off, Bootstrap attributes are inert and the `href` navigates as today.

`productos.php` L11 keeps `data-login-url` as a fallback and gains `data-login-modal="#modalLogin"`; `public/assets/js/pages/productos.js` L56 becomes:

```js
const el = document.querySelector(page.dataset.loginModal || '');
if (el && window.bootstrap) { new bootstrap.Modal(el).show(); } else { window.location.href = loginUrl; }
```

## File Changes

| File | Action | Description |
|---|---|---|
| `app/Views/partials/auth/_form_login.php` | Create | Shared login form, error block, `redirect_to` |
| `app/Views/partials/auth/_form_registro.php` | Create | Shared registro form, `$isAdmin` terms branch |
| `app/Views/partials/auth/modals.php` | Create | Both modals + reopen/blur script |
| `app/Views/layout/main.php` | Modify | Guarded include + `auth.css?v=3.2` link |
| `app/Views/front/pages/login.php`, `registro.php` | Modify | Consume partials; keep wrapper/branding/admin branches |
| `app/Views/partials/navbar.php` | Modify | 4 triggers |
| `app/Views/front/pages/detalle_producto.php`, `mis_favoritos.php` | Modify | 3 triggers |
| `app/Views/components/product_card.php` | Modify | 2 triggers |
| `app/Views/front/pages/productos.php` + `public/assets/js/pages/productos.js` | Modify | Modal-first JS path |
| `app/Controllers/LoginController.php` | Modify | `reopen_modal`, `safeRedirect()`, `keepFlashdata` |
| `app/Controllers/UsuarioController.php` | Modify | `reopen_modal`, carry `redirect_to` |
| `public/assets/css/pages/auth.css` | Modify | Modal + blur styles, `?v=3.2` |

## Testing Strategy

| Layer | What | Approach |
|---|---|---|
| Unit | `safeRedirect()` allow-list | PHPUnit table test: `/x` ok; `//evil.com`, `https://evil.com`, `javascript:`, `\r\n` header injection, `>512` chars → `/` |
| Integration | Login failure/success | `FeatureTestTrait`: POST `/enviar-login` asserts `reopen_modal` flash on failure and redirect to `redirect_to` on success |
| Integration | Registro handoff | POST `/enviar-form` success → redirect `/login` with `redirect_to` flash present |
| View | Guard | GET `/login` and a logged-in page must NOT contain `id="modalLogin"` |
| Manual | Admin path | `crud_usuarios.php` → registrar usuario renders full page unchanged |

## Threat Matrix

Applicable rows only (this change touches routing/redirect handling, not shell, subprocess, VCS automation, or executable classification):

| Threat | Applicable | Safe behavior | RED test |
|---|---|---|---|
| Open redirect via `redirect_to` | Yes | Non-same-host/absolute-scheme/protocol-relative values fall back to `/` | Unit table above |
| Header/response splitting via CR/LF in `redirect_to` | Yes | CR/LF/NUL rejected before `redirect()->to()` | Unit test with `"/x\r\nSet-Cookie: a=b"` |
| XSS via reflected `redirect_to` in hidden input | Yes | `esc($redirectTo, 'attr')` at render | View assertion on a crafted value |
| CSRF on the globally-rendered form | Yes | `$regenerate=false` keeps tokens valid; on failure CI4 redirects back and reopen re-renders a fresh token | Feature test posting without token → 403/redirect |
| Shell / subprocess / VCS automation / executable classification | N/A | No such boundary in this change | — |

## Migration / Rollout

No migration. Single commit; `git revert` restores prior behavior. Reverting only the trigger markup restores full-page navigation while the partials stay dormant.

## Open Questions

- [ ] Confirm no CDN/proxy caches guest HTML — if it does, auth pages need `Cache-Control: no-store` so the CSRF token is not shared between visitors (mitigated but not eliminated by `$regenerate=false`).
- [ ] `detalle_producto.php` L130 / `product_card.php` L83 may point at `/registro`; verify each target before assigning `#modalLogin` vs `#modalRegistro` during apply.

## Key Learnings

1. `app/Config/Security.php` sets `$regenerate = false`, so a globally rendered modal form cannot carry a stale CSRF token within the 7200s expiry — no config change is needed.
2. `previous_url()` is unusable for return-to-origin because after the POST it resolves to the form's own page, forcing the hidden-input approach.
3. Restricting the modal to guests outside `/login` and `/registro` removes duplicate-DOM-id risk structurally instead of relying on id prefixing.
4. `redirect_to` needs CR/LF rejection in addition to the same-host allow-list, since CI4 writes the value into a `Location` header.
5. Keeping the real `href` on every trigger makes the Bootstrap `data-bs-toggle` attributes purely additive, so the no-JS path is unchanged.
