<?php

use App\Services\ProductoService;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

/**
 * Tests de ProductoService (soft-delete) contra la base de datos real
 * de test (cva_muebles_test). Cada test corre en una transacción que
 * se revierte automáticamente al finalizar (DatabaseTestTrait).
 *
 * @internal
 */
final class ProductoServiceTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $namespace = false;
    protected $migrate = false;
    protected $refresh = false;
    private ProductoService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ProductoService();
    }

    private function crearProducto(array $overrides = []): int
    {
        $categoriaId = $this->db->table('categorias')
            ->select('id_categoria')
            ->where('activo', 1)
            ->get()
            ->getRow()
            ->id_categoria;

        $data = array_merge([
            'nombre_prod'  => 'Producto Test ' . uniqid(),
            'imagen'       => '',
            'categoria_id' => $categoriaId,
            'precio'       => 1000,
            'precio_vta'   => 1500,
            'stock'        => 5,
            'stock_min'    => 1,
        ], $overrides);

        $this->db->table('productos')->insert($data);

        return (int) $this->db->insertID();
    }

    private function idsDe(array $productos): array
    {
        return array_map('intval', array_column($productos, 'id_producto'));
    }

    public function testEliminarEsSoftDeleteNoBorraLaFila(): void
    {
        $id = $this->crearProducto();

        $this->service->eliminar($id);

        $fila = $this->db->table('productos')->where('id_producto', $id)->get()->getRowArray();
        $this->assertNotNull($fila, 'La fila no debería borrarse físicamente con un soft-delete.');
        $this->assertNotNull($fila['deleted_at']);
    }

    public function testProductoEliminadoNoApareceEnElListadoPublico(): void
    {
        $id = $this->crearProducto();
        $this->service->eliminar($id);

        $ids = $this->idsDe($this->service->getProductosPublicos());

        $this->assertNotContains($id, $ids);
    }

    public function testReactivarLoDevuelveAlListadoPublico(): void
    {
        $id = $this->crearProducto();
        $this->service->eliminar($id);
        $this->service->reactivar($id);

        $fila = $this->db->table('productos')->where('id_producto', $id)->get()->getRowArray();
        $this->assertNull($fila['deleted_at'], 'deleted_at debería quedar en NULL tras reactivar().');

        $ids = $this->idsDe($this->service->getProductosPublicos());

        $this->assertContains($id, $ids);
    }

    public function testGetProductosConStatsCuentaEliminadosPorSeparado(): void
    {
        $antes = $this->service->getProductosConStats()['counts'];

        $idActivo = $this->crearProducto();
        $idEliminado = $this->crearProducto();
        $this->service->eliminar($idEliminado);

        $resultado = $this->service->getProductosConStats();
        $ids = $this->idsDe($resultado['productos']);

        // getProductoAll() debe seguir mostrando ambos (incl. archivados) al admin.
        $this->assertContains($idActivo, $ids);
        $this->assertContains($idEliminado, $ids);
        $this->assertSame($antes['eliminados'] + 1, $resultado['counts']['eliminados']);
        $this->assertSame($antes['activos'] + 1, $resultado['counts']['activos']);
    }
}
