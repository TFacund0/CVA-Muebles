<?php

use App\Controllers\BaseController;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class BaseControllerTest extends CIUnitTestCase
{
    private function makeController(): BaseController
    {
        return new class () extends BaseController {
            public function callIsAdmin(): bool
            {
                return $this->isAdmin();
            }

            public function callImageValidationRule(string $field, int $maxKb = 2048): string
            {
                return $this->imageValidationRule($field, $maxKb);
            }
        };
    }

    public function testIsAdminFalseSinSesion(): void
    {
        session()->set(['logged_in' => false]);

        $this->assertFalse($this->makeController()->callIsAdmin());
    }

    public function testIsAdminFalseParaUsuarioNoAdmin(): void
    {
        session()->set(['logged_in' => true, 'perfil_id' => 2]);

        $this->assertFalse($this->makeController()->callIsAdmin());
    }

    public function testIsAdminTrueParaPerfilAdmin(): void
    {
        session()->set(['logged_in' => true, 'perfil_id' => 1]);

        $this->assertTrue($this->makeController()->callIsAdmin());
    }

    public function testImageValidationRuleUsaElCampoYElTamanoIndicados(): void
    {
        $rule = $this->makeController()->callImageValidationRule('foto', 1024);

        $this->assertSame(
            'is_image[foto]|mime_in[foto,image/jpg,image/jpeg,image/png,image/webp]|max_size[foto,1024]',
            $rule,
        );
    }

    public function testImageValidationRuleUsaElMaximoPorDefecto(): void
    {
        $rule = $this->makeController()->callImageValidationRule('imagen');

        $this->assertStringContainsString('max_size[imagen,2048]', $rule);
    }
}
