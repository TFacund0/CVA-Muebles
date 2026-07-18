<?php

use App\Models\ConsultaModel;
use App\Services\ConsultaService;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class ConsultaServiceUnitTest extends CIUnitTestCase
{
    public function testGetConsultasConStatsCuentaActivosYPresupuestos(): void
    {
        $hoy = date('Y-m-d H:i:s');
        $model = $this->createMock(ConsultaModel::class);
        $model->method('getAllOrdenadas')->willReturn([
            ['fecha' => $hoy, 'activo' => 'SI', 'asunto' => 'Pedido de Presupuesto', 'nombre' => 'A', 'apellido' => 'B', 'email' => 'a@b.com'],
            ['fecha' => $hoy, 'activo' => 'NO', 'asunto' => 'Consulta general', 'nombre' => 'C', 'apellido' => 'D', 'email' => 'c@d.com'],
            ['fecha' => '2020-01-01 00:00:00', 'activo' => 'SI', 'asunto' => 'Otro presupuesto', 'nombre' => 'E', 'apellido' => 'F', 'email' => 'e@f.com'],
        ]);

        $service = new ConsultaService($model);
        $resultado = $service->getConsultasConStats();

        $this->assertSame(3, $resultado['counts']['total']);
        $this->assertSame(2, $resultado['counts']['activos']);
        $this->assertSame(2, $resultado['counts']['presupuestos']);
        // Solo las 2 primeras son del mes/año actual (fecha = $hoy).
        $this->assertSame(2, $resultado['counts']['mensuales']);
    }

    public function testRegistrarSeteaFechaYActivoAutomaticamente(): void
    {
        $model = $this->createMock(ConsultaModel::class);
        $model->expects($this->once())
            ->method('insert')
            ->with($this->callback(fn ($data) => $data['activo'] === 'SI' && isset($data['fecha'])))
            ->willReturn(1);

        $service = new ConsultaService($model);
        $resultado = $service->registrar(['nombre' => 'Test', 'email' => 'test@test.com']);

        $this->assertSame('success', $resultado['status']);
    }
}
