<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * Cubre la construcción de $urlVolver en detalle_producto.php (breadcrumb +
 * botón "Volver al catálogo") y el rediseño visual de trust-badges /
 * description-box (sin emojis, sin borde dashed). Producto 17 (categoría
 * "Estantes") es un producto real, público y no archivado (confirmado vía
 * ProductoService::getProductosPublicos en la DB de desarrollo).
 *
 * @internal
 */
final class DetalleProductoUrlVolverTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    private const ID_PRODUCTO_ACTIVO = 17;

    public function testUrlVolverConAmbosParametros(): void
    {
        $result = $this->get('/producto/detalle/' . self::ID_PRODUCTO_ACTIVO . '?from_categoria=estantes&from_id=17');

        $result->assertOK();
        $result->assertSee('/productos?categoria=estantes&amp;producto=17');
    }

    public function testUrlVolverConSoloFromCategoria(): void
    {
        $result = $this->get('/producto/detalle/' . self::ID_PRODUCTO_ACTIVO . '?from_categoria=estantes');

        $result->assertOK();
        $result->assertSee('/productos?categoria=estantes');
    }

    public function testUrlVolverConSoloFromId(): void
    {
        $result = $this->get('/producto/detalle/' . self::ID_PRODUCTO_ACTIVO . '?from_id=17');

        $result->assertOK();
        $result->assertSee('/productos?producto=17');
    }

    public function testUrlVolverSinParametrosCaeAUrlPlana(): void
    {
        $result = $this->get('/producto/detalle/' . self::ID_PRODUCTO_ACTIVO);

        $result->assertOK();
        $response = $result->response();
        $body = (string) $response->getBody();

        $this->assertStringNotContainsString('?categoria=', $body);
        $this->assertStringNotContainsString('?producto=', $body);
    }

    public function testUrlVolverConCategoriaConEspaciosYAcentos(): void
    {
        $result = $this->get('/producto/detalle/' . self::ID_PRODUCTO_ACTIVO . '?from_categoria=' . rawurlencode('mesas y sillas') . '&from_id=17');

        $result->assertOK();
        $result->assertSee('/productos?categoria=mesas+y+sillas&amp;producto=17');
    }

    public function testNoQuedaNingunEmojiEnElRenderDelDetalle(): void
    {
        $result = $this->get('/producto/detalle/' . self::ID_PRODUCTO_ACTIVO);
        $result->assertOK();

        $body = (string) $result->response()->getBody();

        foreach (['🚚', '🛡️', '🌿'] as $emoji) {
            $this->assertStringNotContainsString($emoji, $body, "El emoji {$emoji} no debería seguir en el detalle.");
        }
    }

    public function testNoQuedaBordeDashedEnElCssDeDetalle(): void
    {
        $css = file_get_contents(APPPATH . '../public/assets/css/pages/detalle_producto.css');

        $this->assertStringNotContainsString('dashed', $css, 'No debería quedar ninguna regla dashed en detalle_producto.css.');
    }
}
