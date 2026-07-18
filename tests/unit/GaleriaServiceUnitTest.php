<?php

use App\Models\GaleriaClienteModel;
use App\Services\GaleriaService;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class GaleriaServiceUnitTest extends CIUnitTestCase
{
    public function testSubirConImagenInvalidaDevuelveFalse(): void
    {
        $model = $this->createMock(GaleriaClienteModel::class);
        $model->expects($this->never())->method('insert');

        $img = $this->createMock(\CodeIgniter\HTTP\Files\UploadedFile::class);
        $img->method('isValid')->willReturn(false);

        $service = new GaleriaService($model);

        $this->assertFalse($service->subir(1, $img, 'comentario'));
    }

    public function testAprobarActualizaElCampoActivo(): void
    {
        $model = $this->createMock(GaleriaClienteModel::class);
        $model->expects($this->once())->method('update')->with(5, ['activo' => 'SI']);

        $service = new GaleriaService($model);
        $service->aprobar(5);
    }

    public function testEliminarFotoInexistenteDevuelveFalse(): void
    {
        $model = $this->createMock(GaleriaClienteModel::class);
        $model->method('find')->willReturn(null);
        $model->expects($this->never())->method('delete');

        $service = new GaleriaService($model);

        $this->assertFalse($service->eliminar(999));
    }
}
