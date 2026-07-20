<?php

use App\Models\ProductoModel;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * Verifica las reglas de validación reales que ProductoModel aplica en
 * insert()/update(), sin tocar la base de datos.
 *
 * @internal
 */
final class ProductoModelValidationTest extends CIUnitTestCase
{
    private function datosValidos(): array
    {
        return [
            'nombre_prod'  => 'Silla de roble',
            'categoria_id' => 1,
            'precio'       => 1000,
            'precio_vta'   => 1500,
            'stock'        => 10,
        ];
    }

    private function validar(array $data): bool
    {
        $reflection = new ReflectionProperty(ProductoModel::class, 'validationRules');
        $reflection->setAccessible(true);

        $validation = service('validation');
        $validation->setRules($reflection->getValue(new ProductoModel()));

        return $validation->run($data);
    }

    public function testDatosValidosPasanLaValidacion(): void
    {
        $this->assertTrue($this->validar($this->datosValidos()));
    }

    public function testNombreVacioFallaLaValidacion(): void
    {
        $data                 = $this->datosValidos();
        $data['nombre_prod']  = '';

        $this->assertFalse($this->validar($data));
    }

    public function testNombreCortoFallaLaValidacion(): void
    {
        $data                = $this->datosValidos();
        $data['nombre_prod'] = 'Ab';

        $this->assertFalse($this->validar($data));
    }

    public function testPrecioNegativoFallaLaValidacion(): void
    {
        $data           = $this->datosValidos();
        $data['precio'] = -100;

        $this->assertFalse($this->validar($data));
    }

    public function testStockNoNumericoFallaLaValidacion(): void
    {
        $data          = $this->datosValidos();
        $data['stock'] = 'muchos';

        $this->assertFalse($this->validar($data));
    }

    public function testCategoriaFaltanteFallaLaValidacion(): void
    {
        $data = $this->datosValidos();
        unset($data['categoria_id']);

        $this->assertFalse($this->validar($data));
    }
}
