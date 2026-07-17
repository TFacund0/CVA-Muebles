<?php

namespace App\Controllers\Api;

use App\Libraries\ApiAuthContext;
use App\Services\FavoritosService;

class FavoritoController extends BaseApiController
{
    protected FavoritosService $favoritosService;

    public function __construct()
    {
        $this->favoritosService = new FavoritosService();
    }

    public function index()
    {
        $usuario = ApiAuthContext::user();
        return $this->ok($this->favoritosService->getFavoritosConDetalle($usuario['id_usuario']));
    }

    public function toggle($producto_id = null)
    {
        $usuario = ApiAuthContext::user();
        $resultado = $this->favoritosService->toggle($usuario['id_usuario'], $producto_id);

        return $this->ok($resultado);
    }
}
