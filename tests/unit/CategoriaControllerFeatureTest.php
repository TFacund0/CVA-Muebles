<?php

use App\Models\CategoriaModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * Prueba de feature contra la base de datos real de desarrollo: cubre la
 * regla is_unique de CategoriaController::guardar/editar, que no se puede
 * verificar con un mock porque depende de una consulta real a la DB.
 *
 * @internal
 */
final class CategoriaControllerFeatureTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    private const NOMBRE_TEST = 'Categoria de Test Automatizado XYZ';

    private const SESION_ADMIN = ['logged_in' => true, 'perfil_id' => 1, 'id_usuario' => 1];

    protected function tearDown(): void
    {
        parent::tearDown();

        // Por si el test de creación insertó la fila, la limpiamos siempre.
        (new CategoriaModel())->where('descripcion', self::NOMBRE_TEST)->delete();
    }

    private function conCsrf(array $data): array
    {
        return $data + [csrf_token() => csrf_hash()];
    }

    public function testGuardarConNombreNuevoCreaLaCategoria(): void
    {
        $model = new CategoriaModel();
        $this->assertNull($model->where('descripcion', self::NOMBRE_TEST)->first());

        $result = $this->withSession(self::SESION_ADMIN)
            ->post('/admin/categorias/guardar', $this->conCsrf(['descripcion' => self::NOMBRE_TEST]));

        $result->assertRedirectTo('/crud-categorias');
        $this->assertNotNull($model->where('descripcion', self::NOMBRE_TEST)->first());
    }

    public function testGuardarConNombreDuplicadoFalla(): void
    {
        // "Escritorio" ya existe en la base (categoría real preexistente).
        $result = $this->withSession(self::SESION_ADMIN)
            ->post('/admin/categorias/guardar', $this->conCsrf(['descripcion' => 'Escritorio']));

        $result->assertRedirect();
        $result->assertSessionHas('error');

        $model = new CategoriaModel();
        $this->assertSame(1, $model->where('descripcion', 'Escritorio')->countAllResults());
    }

    public function testGuardarSinLoginRedirigeALogin(): void
    {
        $result = $this->withSession(['logged_in' => false])
            ->post('/admin/categorias/guardar', $this->conCsrf(['descripcion' => self::NOMBRE_TEST]));

        $result->assertRedirectTo('/login');
    }
}
