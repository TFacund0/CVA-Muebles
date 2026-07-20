<?php

use App\Filters\AdminAuth;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\Test\CIUnitTestCase;
use Config\Services;

/**
 * @internal
 */
final class AdminAuthFilterTest extends CIUnitTestCase
{
    private function makeRequest(bool $isAjax = false): IncomingRequest
    {
        $request = Services::request(null, false);

        if ($isAjax) {
            $request->setHeader('X-Requested-With', 'XMLHttpRequest');
        }

        return $request;
    }

    public function testUsuarioNoLogueadoEsRedirigidoALogin(): void
    {
        session()->set(['logged_in' => false]);

        $filter = new AdminAuth();
        $result = $filter->before($this->makeRequest());

        $this->assertInstanceOf(RedirectResponse::class, $result);
        $this->assertSame(site_url('login'), $result->getHeaderLine('Location'));
    }

    public function testUsuarioNoLogueadoViaAjaxRecibe401(): void
    {
        session()->set(['logged_in' => false]);

        $filter = new AdminAuth();
        $result = $filter->before($this->makeRequest(true));

        $this->assertSame(401, $result->getStatusCode());
    }

    public function testUsuarioLogueadoSinPerfilAdminEsRedirigidoAlInicio(): void
    {
        session()->set(['logged_in' => true, 'perfil_id' => 2]);

        $filter = new AdminAuth();
        $result = $filter->before($this->makeRequest());

        $this->assertInstanceOf(RedirectResponse::class, $result);
        $this->assertSame(site_url('/'), $result->getHeaderLine('Location'));
    }

    public function testUsuarioLogueadoSinPerfilAdminViaAjaxRecibe403(): void
    {
        session()->set(['logged_in' => true, 'perfil_id' => 2]);

        $filter = new AdminAuth();
        $result = $filter->before($this->makeRequest(true));

        $this->assertSame(403, $result->getStatusCode());
    }

    public function testAdminLogueadoPasaSinRedireccion(): void
    {
        session()->set(['logged_in' => true, 'perfil_id' => 1]);

        $filter = new AdminAuth();
        $result = $filter->before($this->makeRequest());

        $this->assertNull($result);
    }
}
