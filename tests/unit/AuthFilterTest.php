<?php

use App\Filters\Auth;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\Test\CIUnitTestCase;
use Config\Services;

/**
 * @internal
 */
final class AuthFilterTest extends CIUnitTestCase
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

        $filter = new Auth();
        $result = $filter->before($this->makeRequest());

        $this->assertInstanceOf(RedirectResponse::class, $result);
        $this->assertSame(site_url('login'), $result->getHeaderLine('Location'));
    }

    public function testUsuarioNoLogueadoViaAjaxRecibe401(): void
    {
        session()->set(['logged_in' => false]);

        $filter = new Auth();
        $result = $filter->before($this->makeRequest(true));

        $this->assertSame(401, $result->getStatusCode());
    }

    public function testUsuarioLogueadoPasaSinRedireccion(): void
    {
        session()->set(['logged_in' => true]);

        $filter = new Auth();
        $result = $filter->before($this->makeRequest());

        $this->assertNull($result);
    }
}
