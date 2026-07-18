<?php

use App\Models\CategoriaModel;
use App\Models\ProductoModel;
use App\Services\CategoriaService;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class CategoriaServiceUnitTest extends CIUnitTestCase
{
    public function testEliminarConProductosAsociadosDevuelveError(): void
    {
        $productoModel = $this->createMock(ProductoModel::class);
        $productoModel->method('countByCategoriaConArchivados')->willReturn(3);

        $categoriaModel = $this->createMock(CategoriaModel::class);
        $categoriaModel->expects($this->never())->method('delete');

        $service = new CategoriaService($categoriaModel, $productoModel);
        $resultado = $service->eliminar(1);

        $this->assertSame('error', $resultado['status']);
        $this->assertStringContainsString('3 productos', $resultado['message']);
    }

    public function testEliminarSinProductosAsociadosBorraLaCategoria(): void
    {
        $productoModel = $this->createMock(ProductoModel::class);
        $productoModel->method('countByCategoriaConArchivados')->willReturn(0);

        $categoriaModel = $this->createMock(CategoriaModel::class);
        $categoriaModel->expects($this->once())->method('delete')->with(1);

        $service = new CategoriaService($categoriaModel, $productoModel);
        $resultado = $service->eliminar(1);

        $this->assertSame('success', $resultado['status']);
    }

    public function testToggleEstadoDeActivaAInactiva(): void
    {
        $categoriaModel = $this->createMock(CategoriaModel::class);
        $categoriaModel->method('find')->willReturn(['id_categoria' => 1, 'activo' => 1]);
        $categoriaModel->expects($this->once())->method('update')->with(1, ['activo' => 0]);

        $service = new CategoriaService($categoriaModel, $this->createMock(ProductoModel::class));
        $service->toggleEstado(1);
    }

    public function testToggleEstadoDeInactivaAActiva(): void
    {
        $categoriaModel = $this->createMock(CategoriaModel::class);
        $categoriaModel->method('find')->willReturn(['id_categoria' => 1, 'activo' => 0]);
        $categoriaModel->expects($this->once())->method('update')->with(1, ['activo' => 1]);

        $service = new CategoriaService($categoriaModel, $this->createMock(ProductoModel::class));
        $service->toggleEstado(1);
    }

    public function testToggleEstadoDeCategoriaInexistenteDevuelveFalse(): void
    {
        $categoriaModel = $this->createMock(CategoriaModel::class);
        $categoriaModel->method('find')->willReturn(null);
        $categoriaModel->expects($this->never())->method('update');

        $service = new CategoriaService($categoriaModel, $this->createMock(ProductoModel::class));

        $this->assertFalse($service->toggleEstado(999));
    }
}
