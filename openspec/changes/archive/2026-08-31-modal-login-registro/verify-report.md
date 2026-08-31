# Verify Report: Modal de Login y Registro

## Veredicto: PASS CON OBSERVACIONES

## 1. Requisitos del spec (specs/auth-modal/spec.md)

| # | Requirement | Estado | Evidencia |
|---|---|---|---|
| 1 | Modal Trigger Coverage (8 puntos) | Cumplido | navbar.php:84,86,128,129; mis_favoritos.php:118; detalle_producto.php:117,130; product_card.php:71,83; productos.php:12 (data-login-modal); productos.js:11-22 |
| 2 | Modal Close Preserves Page State | Cumplido (estructural) | modals.php:7 usa .btn-close estandar de Bootstrap 5, sin JS custom que recargue o resetee estado. Sin verificacion manual en navegador (tasks 6.3/6.4 sin marcar). |
| 3 | Blurred Backdrop | Cumplido | auth.css:220-229: base opaca + @supports(backdrop-filter) para el blur. Fallback correcto. |
| 4 | Reopen Modal On Error | Parcial | LoginController::auth() L37 y L50, ambas ramas de fallo, encadenan reopen_modal=login. UsuarioController::formValidation() L52 (fallo de validacion) si encadena reopen_modal=registro, pero la rama de throttle L39 NO lo hace. |
| 5 | Return To Origin After Login | Cumplido | LoginController::auth() L48 usa safeRedirect(); tests de exito y externo verifican ambos escenarios. |
| 6 | Registro Success Keeps Redirecting To Login | Cumplido | UsuarioController::formValidation() L48-50 redirige a /login con redirect_to reenviado, sin autologin. |
| 7 | Modal Not Rendered For Logged-In Users | Cumplido | layout/main.php:58 guard con session logged_in. |
| 8 | Modal Not Rendered On Auth Pages Themselves | Cumplido | layout/main.php:57-58: uri_string() comparado contra login/registro. |
| 9 | Progressive Enhancement Without JS | Cumplido | Todos los triggers conservan el href real, data-bs-* es aditivo. |
| 10 | Admin Registro Flow Out Of Scope | Cumplido | crud_usuarios.php:26 enlaza a ruta real /registro hacia index_registrar() -> registro.php, excluido del guard. _form_registro.php:68-75 respeta isAdmin sin checkbox de terminos. |
| 11 | Unchanged Endpoints And Validation | Cumplido | Formularios postean a enviar-login y /enviar-form, mantienen csrf_field y old(). Suite completa verde. |

### Escenarios Given/When/Then (20)
Corresponden 1:1 a los puntos anteriores; todos cumplidos salvo el escenario implicito de reopen en fallo por throttling de registro, no cubierto explicitamente por el spec pero inconsistente con la redaccion MUST del requirement 4.

## 2. Guard del modal en logged-in / /login / /registro

app/Views/layout/main.php:56-60 usa uri_string() comparado contra session(logged_in) negado y contra los paths login/registro. Verificado correcto y cubierto por tests/unit/AuthModalGuardTest.php. apply-progress.md documenta que usaron uri_string() en vez de service('uri')->getPath() porque este ultimo no refleja el path real en el harness de tests - consistente con el codigo leido.

## 3. Triggers - href real + data-bs-toggle/target

Los 8 puntos verificados: navbar.php (L84,86,128,129), mis_favoritos.php (L118), detalle_producto.php (L117,130), product_card.php (L71,83), productos.php (L11-12) + productos.js (L11-22). Todos conservan el href real de fallback y usan data-bs-toggle=modal con el target correcto (#modalLogin o #modalRegistro segun corresponda). Coincide con lo registrado en apply-progress.md (task 0.1: ninguno apuntaba a /registro).

## 4. LoginController::safeRedirect()

Codigo real, LoginController.php lineas 63-89:
- Host externo: el regex de esquema matchea, compara el host contra base_url() y lo rechaza si difiere, retornando /.
- CR/LF/NUL: preg_match de control chars rechaza y retorna /.
- Protocolo-relativo //evil.com o backslash /\: rechazado explicitamente, retorna /.
- Path relativo valido /x: no matchea ninguna regla de rechazo y se acepta tal cual.
Cubierto por tests/unit/LoginControllerSafeRedirectTest.php, confirmado indirectamente por la suite verde corrida en este verify.

## 5. Flujo admin (crud_usuarios -> registro con perfil_id==1)

crud_usuarios.php:26 enlaza a la ruta real /registro sin atributos de modal. La ruta /registro llama a UsuarioController::index_registrar() sin cambios de comportamiento. registro.php calcula isAdmin a partir de la sesion y lo pasa a _form_registro. _form_registro.php:68-75 renderiza un input oculto terms=checked en vez del checkbox cuando isAdmin es true, cumpliendo proposal.md lineas 108-109.

## 6. CSS blur fallback (auth.css)

auth.css:220-229 define background-color opaco como base y un bloque @supports para backdrop-filter con prefijo webkit. Fallback presente y correcto.

## 7. PHPUnit - corrida real en este verify

Ejecutado vendor/bin/phpunit directamente en este verify: PHPUnit 11.5.56, PHP 8.2.12, Tests 128, Assertions 338, 0 failures, 0 errors (1 warning de coverage driver ausente, 3 deprecations de PHPUnit sin impacto funcional). Coincide exactamente con lo declarado en apply-progress.md.

## 8. Discrepancias tasks.md/apply-progress.md vs codigo real

1. Reopen-on-throttle para registro no implementado: la tarea 3.5 se marca [x] pero solo cubre la rama de validacion fallida, no la rama de throttle de UsuarioController::formValidation. No es una mentira del apply-progress (la tarea no distinguia ambas ramas) pero es una laguna funcional frente al MUST del requirement 4 del spec.
2. Tareas de verificacion manual (fase 6) correctamente sin marcar y documentadas con honestidad como no verificadas por falta de navegador.
3. El resto de lo declarado en apply-progress.md (fases 0-5) fue confirmado linea por linea contra el codigo fuente sin discrepancias.

## Veredicto Final: PASS CON OBSERVACIONES

La implementacion cumple 10 de 11 requisitos del spec de forma completa y verificable en codigo. El requisito 4 se cumple para el caso de validacion pero no para el throttling en registro. La suite de PHPUnit esta verde (128/128, confirmado con corrida propia). No se detectaron regresiones en el flujo admin ni en los endpoints/validacion existentes. Las verificaciones manuales en navegador (6.1-6.4) siguen pendientes, documentado honestamente.

### Accion recomendada (no bloqueante para archive)
Agregar el flash reopen_modal=registro tambien a la rama de throttle de UsuarioController::formValidation (linea 39) para cumplir la letra del requirement 4 en todos los casos de fallo.

## Key Learnings

1. El guard de exclusion del modal usa uri_string() y no service('uri')->getPath(), decision no anticipada por el design pero documentada y testeada correctamente.
2. El requirement de reopen-on-error es mas amplio que lo implementado: la rama de throttle de registro no dispara reopen_modal, mientras que en login si se cubrieron ambas ramas de fallo.
3. El flujo admin permanece intacto: /registro nunca pasa por el guard del modal, y _form_registro.php respeta isAdmin ocultando el checkbox de terminos como pedia proposal.md.
4. La suite de PHPUnit corrida de forma independiente en este verify confirma exactamente el numero reportado en apply-progress.md (128 tests, 338 assertions, 0 failures).
5. Los 8 triggers conservan su href real de fallback intacto en todos los casos, cumpliendo progressive enhancement sin excepciones.
