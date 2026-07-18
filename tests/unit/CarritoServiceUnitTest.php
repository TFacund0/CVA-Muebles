<?php

use App\Models\ProductoModel;
use App\Services\CarritoService;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class CarritoServiceUnitTest extends CIUnitTestCase
{
    public function testAgregarSinIdProductoDevuelveError(): void
    {
        $service = new CarritoService($this->createMock(\CodeIgniterCart\Cart::class), $this->createMock(ProductoModel::class));

        $resultado = $service->agregar([]);

        $this->assertSame('error', $resultado['status']);
    }

    public function testAgregarConProductoInexistenteDevuelveError(): void
    {
        $productoModel = $this->createMock(ProductoModel::class);
        $productoModel->method('find')->willReturn(null);

        $service = new CarritoService($this->createMock(\CodeIgniterCart\Cart::class), $productoModel);

        $resultado = $service->agregar(['id_producto' => 999]);

        $this->assertSame('error', $resultado['status']);
    }

    public function testAgregarConProductoValidoLoInsertaEnElCarrito(): void
    {
        $productoModel = $this->createMock(ProductoModel::class);
        $productoModel->method('find')->willReturn([
            'id_producto' => 5, 'nombre_prod' => 'Mesa', 'precio_vta' => 1500, 'imagen' => 'mesa.jpg',
        ]);

        $cart = $this->createMock(\CodeIgniterCart\Cart::class);
        $cart->expects($this->once())->method('insert')->with($this->callback(
            fn ($data) => $data['id'] === 5 && $data['qty'] === 1
        ));

        $service = new CarritoService($cart, $productoModel);
        $resultado = $service->agregar(['id_producto' => 5]);

        $this->assertSame('success', $resultado['status']);
    }

    public function testDecrementarConCantidadMayorAUnoSoloResta(): void
    {
        $cart = $this->createMock(\CodeIgniterCart\Cart::class);
        $cart->method('getItem')->willReturn(['rowid' => 'abc', 'qty' => 3]);
        $cart->expects($this->once())->method('update')->with(['rowid' => 'abc', 'qty' => 2]);
        $cart->expects($this->never())->method('remove');

        $service = new CarritoService($cart, $this->createMock(ProductoModel::class));
        $service->decrementar('abc');
    }

    public function testDecrementarConCantidadUnoEliminaElItem(): void
    {
        $cart = $this->createMock(\CodeIgniterCart\Cart::class);
        $cart->method('getItem')->willReturn(['rowid' => 'abc', 'qty' => 1]);
        $cart->expects($this->never())->method('update');
        $cart->expects($this->once())->method('remove')->with('abc');

        $service = new CarritoService($cart, $this->createMock(ProductoModel::class));
        $service->decrementar('abc');
    }

    public function testEliminarConAllDestruyeElCarritoCompleto(): void
    {
        $cart = $this->createMock(\CodeIgniterCart\Cart::class);
        $cart->expects($this->once())->method('destroy');
        $cart->expects($this->never())->method('remove');

        $service = new CarritoService($cart, $this->createMock(ProductoModel::class));
        $service->eliminar('all');
    }
}
