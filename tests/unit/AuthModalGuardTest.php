<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * Verifica que #modalLogin/#modalRegistro solo se rendericen para invitados
 * fuera de /login y /registro, per design.md's "Global Include Guard".
 *
 * @internal
 */
final class AuthModalGuardTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    public function testLoginPageNoContieneMarkupDeModal(): void
    {
        $result = $this->withSession([])->get('/login');

        $result->assertOK();
        $result->assertDontSee('id="modalLogin"');
        $result->assertDontSee('id="modalRegistro"');
    }

    public function testRegistroPageNoContieneMarkupDeModal(): void
    {
        $result = $this->withSession([])->get('/registro');

        $result->assertOK();
        $result->assertDontSee('id="modalLogin"');
        $result->assertDontSee('id="modalRegistro"');
    }

    public function testUsuarioLogueadoNoRecibeMarkupDeModalEnInicio(): void
    {
        $result = $this->withSession(['logged_in' => true, 'perfil_id' => 2, 'id_usuario' => 1])->get('/');

        $result->assertOK();
        $result->assertDontSee('id="modalLogin"');
        $result->assertDontSee('id="modalRegistro"');
    }

    public function testInvitadoEnInicioRecibeMarkupDeModal(): void
    {
        $result = $this->withSession([])->get('/');

        $result->assertOK();
        $result->assertSee('id="modalLogin"');
        $result->assertSee('id="modalRegistro"');
    }
}
