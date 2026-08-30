<?php

use App\Models\ProductoModel;
use App\Models\VentasCabeceraModel;
use App\Models\VentasDetalleModel;
use App\Models\VentasPagosModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * Prueba de feature contra la base de datos real de desarrollo: cubre
 * VentasController::registrar_venta/ver_factura/registrar_pago/
 * actualizar_estado/guardar_observaciones y el filtro adminAuth aplicado a
 * las rutas de gestión de ventas.
 *
 * NOTA sobre la ruta de siembra de carrito: la especificación menciona
 * '/agregar-carrito', pero la ruta real verificada en app/Config/Routes.php
 * es POST '/carrito/add' -> CarritoController::add (que delega en
 * CarritoService::agregar(), campo POST 'id_producto'). Se usa la ruta real.
 *
 * @internal
 */
final class VentasControllerFeatureTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    private const SESION_ADMIN = ['logged_in' => true, 'perfil_id' => 1, 'id_usuario' => 1];

    private const SESION_CLIENTE = ['logged_in' => true, 'perfil_id' => 2, 'id_usuario' => 999];

    private const OBS_SENTINEL = '[TEST_VENTAS_XYZ]';

    /** @var array<int, int> IDs de ventas_cabecera creadas durante un test, a purgar en tearDown() */
    private array $ventaIdsACrear = [];

    private ?array $productoActivo = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ventaIdsACrear = [];

        $producto = (new ProductoModel())->first();
        if (empty($producto)) {
            $this->fail('No existe ningún producto activo en la base de datos de test; no se puede sembrar el carrito para VentasControllerFeatureTest.');
        }
        $this->productoActivo = $producto;

        // Purga cualquier fila residual de una corrida anterior fallida,
        // identificada por el sentinel en observaciones.
        $this->purgarVentasSentinel();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // Evita que items de carrito sembrados por este test (vía la ruta
        // real /carrito/add) contaminen la sesión/carrito de un test
        // posterior: cada test debe empezar con un carrito vacío.
        (new \App\Services\CarritoService())->vaciar();

        $this->purgarVentasSentinel();

        foreach ($this->ventaIdsACrear as $ventaId) {
            (new VentasPagosModel())->where('venta_id', $ventaId)->delete();
            (new VentasDetalleModel())->where('venta_id', $ventaId)->delete();
            (new VentasCabeceraModel())->delete($ventaId);
        }
    }

    /**
     * Elimina en orden seguro por FK (pagos -> detalle -> cabecera) cualquier
     * venta cuyas observaciones contengan el sentinel de esta suite.
     */
    private function purgarVentasSentinel(): void
    {
        $cabeceraModel = new VentasCabeceraModel();
        $ventas = $cabeceraModel->like('observaciones', self::OBS_SENTINEL)->findAll();

        foreach ($ventas as $venta) {
            (new VentasPagosModel())->where('venta_id', $venta['id'])->delete();
            (new VentasDetalleModel())->where('venta_id', $venta['id'])->delete();
            $cabeceraModel->delete($venta['id']);
        }
    }

    private function conCsrf(array $data): array
    {
        return $data + [csrf_token() => csrf_hash()];
    }

    /**
     * Siembra el carrito a través de la ruta real POST /carrito/add (per el
     * hallazgo empírico documentado en el spec: instanciar CarritoService
     * directamente NO es seguro para sembrar estado que una request HTTP
     * posterior necesita). Devuelve el array $_SESSION capturado
     * inmediatamente después, listo para ser reproducido explícitamente en
     * la siguiente llamada ->withSession($sessionCapturada)->post(...).
     */
    private function sembrarCarrito(): array
    {
        $this->withSession(self::SESION_ADMIN)
            ->post('/carrito/add', $this->conCsrf([
                'id_producto' => $this->productoActivo['id_producto'],
            ]));

        return $_SESSION;
    }

    private function crearVentaSeeded(float $total = 100.0, string $estado = 'PENDIENTE', string $estadoAprobacion = 'SOLICITUD'): int
    {
        $model = new VentasCabeceraModel();
        $ventaId = $model->insert([
            'usuario_id'        => 999,
            'fecha'             => date('Y-m-d H:i:s'),
            'total_venta'       => $total,
            'estado'            => $estado,
            'estado_aprobacion' => $estadoAprobacion,
            'observaciones'     => self::OBS_SENTINEL,
            'tipo_pedido'       => 'CARRITO',
        ]);

        if (empty($ventaId)) {
            $this->fail('No se pudo insertar la venta seed: ' . implode(', ', $model->errors() ?? []));
        }

        $this->ventaIdsACrear[] = (int) $ventaId;

        return (int) $ventaId;
    }

    // -------------------- VTA-01 --------------------

    public function testCarritoComprarSinSeleccionNoCreaVenta(): void
    {
        $model = new VentasCabeceraModel();
        $antes = $model->like('observaciones', self::OBS_SENTINEL)->countAllResults();

        $result = $this->withSession(self::SESION_ADMIN)
            ->post('/carrito_comprar', $this->conCsrf([
                'observaciones' => self::OBS_SENTINEL,
            ]));

        $result->assertRedirectTo('/muestro');
        $result->assertSessionHas('error');

        $despues = $model->like('observaciones', self::OBS_SENTINEL)->countAllResults();
        $this->assertSame($antes, $despues);
    }

    // -------------------- VTA-02 --------------------

    public function testCarritoComprarConCarritoSembradoCreaVentaConTotalCorrecto(): void
    {
        $sesionConCarrito = $this->sembrarCarrito();

        $carrito = (new \App\Services\CarritoService())->getContenido();
        $this->assertNotEmpty($carrito, 'El carrito debería contener el item sembrado.');
        $rowid = array_key_first($carrito);
        $item  = $carrito[$rowid];
        $totalEsperado = $item['price'] * $item['qty'];

        $result = $this->withSession($sesionConCarrito)
            ->post('/carrito_comprar', $this->conCsrf([
                'selected_items' => [$rowid],
                'observaciones'  => self::OBS_SENTINEL,
            ]));

        $result->assertRedirectTo('/ventas_lista');
        $result->assertSessionHas('success');

        $model = new VentasCabeceraModel();
        $venta = $model->like('observaciones', self::OBS_SENTINEL)->first();
        $this->assertNotNull($venta);
        $this->ventaIdsACrear[] = (int) $venta['id'];

        $this->assertEquals($totalEsperado, (float) $venta['total_venta']);

        $detalle = (new VentasDetalleModel())->where('venta_id', $venta['id'])->findAll();
        $this->assertCount(1, $detalle);
        $this->assertSame((int) $this->productoActivo['id_producto'], (int) $detalle[0]['producto_id']);
    }

    // -------------------- VTA-03 --------------------

    public function testCarritoComprarConRowidInexistenteEsFiltradoSilenciosamente(): void
    {
        $sesionConCarrito = $this->sembrarCarrito();

        $result = $this->withSession($sesionConCarrito)
            ->post('/carrito_comprar', $this->conCsrf([
                'selected_items' => ['rowid_que_no_existe_en_el_carrito'],
                'observaciones'  => self::OBS_SENTINEL,
            ]));

        // El controlador filtra el rowid inválido: $items_a_procesar queda
        // vacío -> VentasService::procesarVenta recibe un array vacío ->
        // responde 'error' (no crashea) y no crea ninguna venta.
        $result->assertRedirectTo('/muestro');
        $result->assertSessionHas('error');

        $model = new VentasCabeceraModel();
        $venta = $model->like('observaciones', self::OBS_SENTINEL)->first();
        $this->assertNull($venta);
    }

    // -------------------- VTA-04 (revalidación de precio del lado servidor) --------------------

    public function testTotalVentaUsaPrecioServidorNoPrecioCliente(): void
    {
        $sesionConCarrito = $this->sembrarCarrito();

        $carrito = (new \App\Services\CarritoService())->getContenido();
        $rowid = array_key_first($carrito);

        // Simula drift de precio: sube el precio_vta real del producto
        // DESPUÉS de que el item ya fue agregado al carrito con el precio
        // viejo. El carrito en sesión sigue teniendo el precio congelado al
        // momento de agregar.
        $productoModel = new ProductoModel();
        $precioOriginal = $this->productoActivo['precio_vta'];
        $precioActualizado = $precioOriginal + 500;
        $productoModel->update($this->productoActivo['id_producto'], ['precio_vta' => $precioActualizado]);

        try {
            $precioCarrito = $carrito[$rowid]['price'];

            $result = $this->withSession($sesionConCarrito)
                ->post('/carrito_comprar', $this->conCsrf([
                    'selected_items' => [$rowid],
                    'observaciones'  => self::OBS_SENTINEL,
                ]));

            $result->assertRedirectTo('/ventas_lista');

            $model = new VentasCabeceraModel();
            $venta = $model->like('observaciones', self::OBS_SENTINEL)->first();
            $this->assertNotNull($venta);
            $this->ventaIdsACrear[] = (int) $venta['id'];

            // El total y el detalle deben reflejar el precio_vta REAL del
            // servidor al momento de comprar, no el precio congelado que
            // traía el carrito (que podría haber sido manipulado por el
            // cliente).
            $this->assertEquals((float) $precioActualizado, (float) $venta['total_venta']);
            $this->assertNotEquals((float) $precioCarrito, (float) $venta['total_venta']);

            $detalle = (new VentasDetalleModel())->where('venta_id', $venta['id'])->first();
            $this->assertEquals((float) $precioActualizado, (float) $detalle['precio']);
        } finally {
            $productoModel->update($this->productoActivo['id_producto'], ['precio_vta' => $precioOriginal]);
        }
    }

    // -------------------- VTA-05 --------------------

    public function testCarritoComprarParcialEliminaSoloLosItemsProcesadosDelCarrito(): void
    {
        // Siembra el primer item.
        $this->withSession(self::SESION_ADMIN)
            ->post('/carrito/add', $this->conCsrf([
                'id_producto' => $this->productoActivo['id_producto'],
            ]));

        // Busca un segundo producto activo distinto, si existe.
        $segundoProducto = (new ProductoModel())
            ->where('id_producto !=', $this->productoActivo['id_producto'])
            ->first();

        if (empty($segundoProducto)) {
            $this->markTestSkipped('Se necesita un segundo producto activo distinto para probar la eliminación parcial del carrito.');
        }

        $this->withSession($_SESSION)
            ->post('/carrito/add', $this->conCsrf([
                'id_producto' => $segundoProducto['id_producto'],
            ]));

        $sesionConCarrito = $_SESSION;

        $carritoAntes = (new \App\Services\CarritoService())->getContenido();
        $this->assertCount(2, $carritoAntes, 'El carrito debería tener 2 items sembrados.');

        $rowids = array_keys($carritoAntes);
        $rowidAProcesar = $rowids[0];
        $rowidRestante  = $rowids[1];

        $result = $this->withSession($sesionConCarrito)
            ->post('/carrito_comprar', $this->conCsrf([
                'selected_items' => [$rowidAProcesar],
                'observaciones'  => self::OBS_SENTINEL,
            ]));

        $result->assertRedirectTo('/ventas_lista');

        $model = new VentasCabeceraModel();
        $venta = $model->like('observaciones', self::OBS_SENTINEL)->first();
        $this->assertNotNull($venta);
        $this->ventaIdsACrear[] = (int) $venta['id'];

        // Tras el post(), $_SESSION refleja el estado del carrito luego de
        // que el controlador eliminó únicamente los items procesados
        // (CarritoController::registrar_venta -> eliminarVarios()).
        $carritoDespues = (new \App\Services\CarritoService())->getContenido();

        $this->assertArrayNotHasKey($rowidAProcesar, $carritoDespues);
        $this->assertArrayHasKey($rowidRestante, $carritoDespues);
    }

    // -------------------- VTA-06 --------------------

    public function testVerFacturaPermiteAlDuenioYAlAdminYBloqueaAOtroUsuario(): void
    {
        $ventaId = $this->crearVentaSeeded();

        // (a) el dueño (usuario 999) puede verla.
        $result = $this->withSession(self::SESION_CLIENTE)->get('/factura/' . $ventaId);
        $result->assertOK();

        // (b) otro usuario no-admin no puede.
        $otroCliente = ['logged_in' => true, 'perfil_id' => 2, 'id_usuario' => 12345];
        $result = $this->withSession($otroCliente)->get('/factura/' . $ventaId);
        $result->assertRedirectTo('/productos');
        $result->assertSessionHas('error');

        // (c) el admin puede verla sin ser el dueño.
        $result = $this->withSession(self::SESION_ADMIN)->get('/factura/' . $ventaId);
        $result->assertOK();

        // (d) id inexistente.
        $result = $this->withSession(self::SESION_ADMIN)->get('/factura/999999999');
        $result->assertRedirectTo('/productos');
        $result->assertSessionHas('error');
    }

    // -------------------- VTA-07 / VTA-08 --------------------

    public function testRegistrarPagoRechazaMontoInvalido(): void
    {
        $ventaId = $this->crearVentaSeeded();

        $model = new VentasPagosModel();
        $antes = $model->where('venta_id', $ventaId)->countAllResults();

        $result = $this->withSession(self::SESION_ADMIN)
            ->post('/ventas/registrar_pago', $this->conCsrf([
                'venta_id' => $ventaId,
                'monto'    => '-10',
            ]));

        $result->assertRedirect();
        $result->assertSessionHas('fail');

        $despues = $model->where('venta_id', $ventaId)->countAllResults();
        $this->assertSame($antes, $despues);
    }

    public function testRegistrarPagoAceptaMontoValido(): void
    {
        $ventaId = $this->crearVentaSeeded();

        $result = $this->withSession(self::SESION_ADMIN)
            ->post('/ventas/registrar_pago', $this->conCsrf([
                'venta_id' => $ventaId,
                'monto'    => '50.00',
            ]));

        $result->assertRedirect();
        $result->assertSessionHas('success');

        $pago = (new VentasPagosModel())->where('venta_id', $ventaId)->first();
        $this->assertNotNull($pago);
        $this->assertEquals(50.0, (float) $pago['monto']);
    }

    // -------------------- VTA-09 --------------------

    public function testActualizarEstadoRechazadoRedirigeAVentasListYNoModificaStock(): void
    {
        $ventaId = $this->crearVentaSeeded();

        $productoModel = new ProductoModel();
        $stockOriginal = $productoModel->find($this->productoActivo['id_producto'])['stock'];

        try {
            $result = $this->withSession(self::SESION_ADMIN)
                ->post('/ventas/actualizar_estado/' . $ventaId, $this->conCsrf([
                    'estado' => 'RECHAZADO',
                ]));

            $result->assertRedirectTo('/ventas-list');
            $result->assertSessionHas('success');

            $venta = (new VentasCabeceraModel())->find($ventaId);
            $this->assertSame('RECHAZADO', $venta['estado_aprobacion']);

            $stockDespues = $productoModel->find($this->productoActivo['id_producto'])['stock'];
            $this->assertEquals($stockOriginal, $stockDespues, 'El stock no debe modificarse: la lógica de stock fue removida (ver comentario en VentasService::actualizarEstado).');
        } finally {
            $productoModel->update($this->productoActivo['id_producto'], ['stock' => $stockOriginal]);
        }
    }

    public function testActualizarEstadoOtroValorRedirigeAtrasYNoModificaStock(): void
    {
        $ventaId = $this->crearVentaSeeded();

        $productoModel = new ProductoModel();
        $stockOriginal = $productoModel->find($this->productoActivo['id_producto'])['stock'];

        try {
            $result = $this->withSession(self::SESION_ADMIN)
                ->withHeaders(['Referer' => 'https://example.test/ventas/gestion/' . $ventaId])
                ->post('/ventas/actualizar_estado/' . $ventaId, $this->conCsrf([
                    'estado' => 'EN_PROCESO',
                ]));

            $result->assertRedirect();
            $result->assertNotRedirectTo('/ventas-list');
            $result->assertSessionHas('success');

            $venta = (new VentasCabeceraModel())->find($ventaId);
            $this->assertSame('EN_PROCESO', $venta['estado']);

            $stockDespues = $productoModel->find($this->productoActivo['id_producto'])['stock'];
            $this->assertEquals($stockOriginal, $stockDespues);
        } finally {
            $productoModel->update($this->productoActivo['id_producto'], ['stock' => $stockOriginal]);
        }
    }

    // -------------------- VTA-10 --------------------

    public function testGuardarObservacionesRechazaImgRefTagConFormatoInvalidoPeroActualizaObservaciones(): void
    {
        $ventaId = $this->crearVentaSeeded();

        $result = $this->withSession(self::SESION_ADMIN)
            ->post('/ventas/guardar_observaciones', $this->conCsrf([
                'venta_id'      => $ventaId,
                'observaciones' => self::OBS_SENTINEL . ' actualizado',
                'img_ref_tag'   => '<script>alert(1)</script>',
            ]));

        $result->assertRedirect();
        $result->assertSessionHas('success');

        $venta = (new VentasCabeceraModel())->find($ventaId);
        $this->assertStringNotContainsString('<script>', $venta['observaciones']);
        $this->assertStringContainsString(self::OBS_SENTINEL . ' actualizado', $venta['observaciones']);
    }

    public function testGuardarObservacionesAceptaImgRefTagConFormatoValido(): void
    {
        $ventaId = $this->crearVentaSeeded();

        $result = $this->withSession(self::SESION_ADMIN)
            ->post('/ventas/guardar_observaciones', $this->conCsrf([
                'venta_id'      => $ventaId,
                'observaciones' => self::OBS_SENTINEL . ' actualizado',
                'img_ref_tag'   => '[IMG_REF:foto_test.png]',
            ]));

        $result->assertRedirect();
        $result->assertSessionHas('success');

        $venta = (new VentasCabeceraModel())->find($ventaId);
        $this->assertStringContainsString('[IMG_REF:foto_test.png]', $venta['observaciones']);
    }

    // -------------------- VTA-11 --------------------

    public function testActualizarEstadoSinSesionRedirigeALogin(): void
    {
        $ventaId = $this->crearVentaSeeded();

        $result = $this->withSession([])
            ->post('/ventas/actualizar_estado/' . $ventaId, $this->conCsrf([
                'estado' => 'RECHAZADO',
            ]));

        $result->assertRedirectTo('/login');

        $venta = (new VentasCabeceraModel())->find($ventaId);
        $this->assertSame('SOLICITUD', $venta['estado_aprobacion']);
    }

    public function testActualizarEstadoNoAdminRedirigeAInicio(): void
    {
        $ventaId = $this->crearVentaSeeded();

        $result = $this->withSession(self::SESION_CLIENTE)
            ->post('/ventas/actualizar_estado/' . $ventaId, $this->conCsrf([
                'estado' => 'RECHAZADO',
            ]));

        $result->assertRedirectTo('/');
        $result->assertSessionHas('error');

        $venta = (new VentasCabeceraModel())->find($ventaId);
        $this->assertSame('SOLICITUD', $venta['estado_aprobacion']);
    }

    public function testRegistrarPagoSinSesionRedirigeALogin(): void
    {
        $ventaId = $this->crearVentaSeeded();

        $result = $this->withSession([])
            ->post('/ventas/registrar_pago', $this->conCsrf([
                'venta_id' => $ventaId,
                'monto'    => '50.00',
            ]));

        $result->assertRedirectTo('/login');

        $pago = (new VentasPagosModel())->where('venta_id', $ventaId)->first();
        $this->assertNull($pago);
    }

    public function testRegistrarPagoNoAdminRedirigeAInicio(): void
    {
        $ventaId = $this->crearVentaSeeded();

        $result = $this->withSession(self::SESION_CLIENTE)
            ->post('/ventas/registrar_pago', $this->conCsrf([
                'venta_id' => $ventaId,
                'monto'    => '50.00',
            ]));

        $result->assertRedirectTo('/');
        $result->assertSessionHas('error');

        $pago = (new VentasPagosModel())->where('venta_id', $ventaId)->first();
        $this->assertNull($pago);
    }
}
