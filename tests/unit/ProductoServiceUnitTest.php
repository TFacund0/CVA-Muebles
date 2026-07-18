<?php

use App\Models\ProductoImagenModel;
use App\Models\ProductoModel;
use App\Services\ProductoService;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * Tests unitarios puros de ProductoService: ProductoModel se mockea por
 * completo, no hay conexión a base de datos.
 *
 * @internal
 */
final class ProductoServiceUnitTest extends CIUnitTestCase
{
    public function testGetProductosConStatsCuentaActivosSinStockYEliminados(): void
    {
        $model = $this->createMock(ProductoModel::class);
        $model->method('getProductoAll')->willReturn([
            ['id_producto' => 1, 'stock' => 5, 'deleted_at' => null],
            ['id_producto' => 2, 'stock' => 0, 'deleted_at' => null],
            ['id_producto' => 3, 'stock' => 3, 'deleted_at' => '2026-01-01 00:00:00'],
        ]);

        $service = new ProductoService($model, $this->createMock(ProductoImagenModel::class));
        $resultado = $service->getProductosConStats();

        $this->assertSame(3, $resultado['counts']['total']);
        $this->assertSame(2, $resultado['counts']['activos']);
        $this->assertSame(1, $resultado['counts']['sin_stock']);
        $this->assertSame(1, $resultado['counts']['eliminados']);
    }

    public function testEliminarDelegaElSoftDeleteAlModel(): void
    {
        $model = $this->createMock(ProductoModel::class);
        $model->expects($this->once())->method('delete')->with(42)->willReturn(true);

        $service = new ProductoService($model, $this->createMock(ProductoImagenModel::class));

        $this->assertTrue($service->eliminar(42));
    }

    public function testGetProductosPublicosDelegaAlModel(): void
    {
        $model = $this->createMock(ProductoModel::class);
        $model->expects($this->once())
            ->method('getProductosPublicos')
            ->willReturn([['id_producto' => 5]]);

        $service = new ProductoService($model, $this->createMock(ProductoImagenModel::class));

        $this->assertSame([['id_producto' => 5]], $service->getProductosPublicos());
    }
}
