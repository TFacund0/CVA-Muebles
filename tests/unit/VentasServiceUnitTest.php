<?php

use App\Models\ProductoModel;
use App\Models\VentasCabeceraModel;
use App\Models\VentasDetalleModel;
use App\Models\VentasPagosModel;
use App\Services\VentasService;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * Tests unitarios puros de VentasService: los Models y la conexión a BD
 * se mockean por completo, no hay conexión real.
 *
 * @internal
 */
final class VentasServiceUnitTest extends CIUnitTestCase
{
    private function dbMock()
    {
        $db = $this->createMock(\CodeIgniter\Database\BaseConnection::class);
        $db->method('transStart')->willReturn(true);
        $db->method('transComplete')->willReturn(true);
        $db->method('transStatus')->willReturn(true);

        return $db;
    }

    public function testProcesarVentaSinItemsDevuelveError(): void
    {
        $service = new VentasService(
            $this->createMock(VentasCabeceraModel::class),
            $this->createMock(VentasDetalleModel::class),
            $this->createMock(ProductoModel::class),
            $this->createMock(VentasPagosModel::class),
            $this->dbMock()
        );

        $resultado = $service->procesarVenta(1, []);

        $this->assertSame('error', $resultado['status']);
    }

    public function testProcesarVentaCalculaElTotalCorrectamente(): void
    {
        $ventasModel = $this->createMock(VentasCabeceraModel::class);
        $ventasModel->expects($this->once())
            ->method('insert')
            ->with($this->callback(fn ($data) => $data['total_venta'] === 6500))
            ->willReturn(99);

        $detalleModel = $this->createMock(VentasDetalleModel::class);
        $detalleModel->expects($this->exactly(2))->method('insert');

        $service = new VentasService(
            $ventasModel,
            $detalleModel,
            $this->createMock(ProductoModel::class),
            $this->createMock(VentasPagosModel::class),
            $this->dbMock()
        );

        $items = [
            ['id' => 1, 'price' => 1500, 'qty' => 3], // 4500
            ['id' => 2, 'price' => 2000, 'qty' => 1], // 2000
        ];

        $resultado = $service->procesarVenta(7, $items);

        $this->assertSame('success', $resultado['status']);
        $this->assertSame(6500, $resultado['total']);
        $this->assertSame(99, $resultado['venta_id']);
    }

    public function testGetGestionDetalleCalculaSaldoPendiente(): void
    {
        $ventasModel = $this->createMock(VentasCabeceraModel::class);
        $ventasModel->method('getVentas')->willReturn([
            ['id' => 10, 'total_venta' => 10000],
        ]);

        $detalleModel = $this->createMock(VentasDetalleModel::class);
        $detalleModel->method('getDetalles')->willReturn([]);

        $pagosModel = $this->createMock(VentasPagosModel::class);
        $pagosModel->method('getPagosPorVenta')->willReturn([]);
        $pagosModel->method('getTotalPagado')->willReturn(4000);

        $service = new VentasService(
            $ventasModel,
            $detalleModel,
            $this->createMock(ProductoModel::class),
            $pagosModel,
            $this->dbMock()
        );

        $resultado = $service->getGestionDetalle(10);

        $this->assertSame(6000, $resultado['saldo_pendiente']);
    }

    public function testGetGestionDetalleConVentaInexistenteDevuelveNull(): void
    {
        $ventasModel = $this->createMock(VentasCabeceraModel::class);
        $ventasModel->method('getVentas')->willReturn([]);

        $service = new VentasService(
            $ventasModel,
            $this->createMock(VentasDetalleModel::class),
            $this->createMock(ProductoModel::class),
            $this->createMock(VentasPagosModel::class),
            $this->dbMock()
        );

        $this->assertNull($service->getGestionDetalle(999));
    }

    public function testSubirPrioridadIntercambiaConElDeArribaSiTienenPrioridadDistinta(): void
    {
        $ventasModel = $this->createMock(VentasCabeceraModel::class);
        $ventasModel->method('getVentasActivas')->willReturn([
            ['id' => 1, 'prioridad' => 5],
            ['id' => 2, 'prioridad' => 3],
            ['id' => 3, 'prioridad' => 1],
        ]);
        $ventasModel->expects($this->exactly(2))
            ->method('update')
            ->willReturnMap([
                [2, ['prioridad' => 5], true],
                [1, ['prioridad' => 3], true],
            ]);

        $service = new VentasService(
            $ventasModel,
            $this->createMock(VentasDetalleModel::class),
            $this->createMock(ProductoModel::class),
            $this->createMock(VentasPagosModel::class),
            $this->dbMock()
        );

        $service->subirPrioridad(2);
    }

    public function testSubirPrioridadDelPrimeroDeLaListaSoloLaIncrementa(): void
    {
        $ventasModel = $this->createMock(VentasCabeceraModel::class);
        $ventasModel->method('getVentasActivas')->willReturn([
            ['id' => 1, 'prioridad' => 5],
            ['id' => 2, 'prioridad' => 3],
        ]);
        $ventasModel->expects($this->once())->method('update')->with(1, ['prioridad' => 6]);

        $service = new VentasService(
            $ventasModel,
            $this->createMock(VentasDetalleModel::class),
            $this->createMock(ProductoModel::class),
            $this->createMock(VentasPagosModel::class),
            $this->dbMock()
        );

        $service->subirPrioridad(1);
    }

    public function testBajarPrioridadDelUltimoDeLaListaSoloLaDecrementa(): void
    {
        $ventasModel = $this->createMock(VentasCabeceraModel::class);
        $ventasModel->method('getVentasActivas')->willReturn([
            ['id' => 1, 'prioridad' => 5],
            ['id' => 2, 'prioridad' => 3],
        ]);
        $ventasModel->expects($this->once())->method('update')->with(2, ['prioridad' => 2]);

        $service = new VentasService(
            $ventasModel,
            $this->createMock(VentasDetalleModel::class),
            $this->createMock(ProductoModel::class),
            $this->createMock(VentasPagosModel::class),
            $this->dbMock()
        );

        $service->bajarPrioridad(2);
    }

    public function testSubirPrioridadDeUnaVentaQueNoEstaEnLaListaNoHaceNada(): void
    {
        $ventasModel = $this->createMock(VentasCabeceraModel::class);
        $ventasModel->method('getVentasActivas')->willReturn([
            ['id' => 1, 'prioridad' => 5],
        ]);
        $ventasModel->expects($this->never())->method('update');

        $service = new VentasService(
            $ventasModel,
            $this->createMock(VentasDetalleModel::class),
            $this->createMock(ProductoModel::class),
            $this->createMock(VentasPagosModel::class),
            $this->dbMock()
        );

        $service->subirPrioridad(999);
    }
}
