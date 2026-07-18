<?php

use App\Models\FavoritoModel;
use App\Services\FavoritosService;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class FavoritosServiceUnitTest extends CIUnitTestCase
{
    public function testToggleAgregaSiNoExistiaAntes(): void
    {
        $model = $this->createMock(FavoritoModel::class);
        $model->method('findFavorito')->willReturn(null);
        $model->expects($this->once())->method('insert');
        $model->expects($this->never())->method('delete');

        $service = new FavoritosService($model);
        $resultado = $service->toggle(1, 5);

        $this->assertSame('added', $resultado['status']);
    }

    public function testToggleEliminaSiYaExistia(): void
    {
        $model = $this->createMock(FavoritoModel::class);
        $model->method('findFavorito')->willReturn(['id' => 10, 'usuario_id' => 1, 'producto_id' => 5]);
        $model->expects($this->once())->method('delete')->with(10);
        $model->expects($this->never())->method('insert');

        $service = new FavoritosService($model);
        $resultado = $service->toggle(1, 5);

        $this->assertSame('removed', $resultado['status']);
    }

    public function testGetFavoritosIdsDevuelveSoloLosIdsDeProducto(): void
    {
        $model = $this->createMock(FavoritoModel::class);
        $model->method('findByUsuario')->willReturn([
            ['id' => 1, 'producto_id' => 5],
            ['id' => 2, 'producto_id' => 8],
        ]);

        $service = new FavoritosService($model);

        $this->assertSame([5, 8], $service->getFavoritosIds(1));
    }
}
