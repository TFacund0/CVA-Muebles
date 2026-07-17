<?php

namespace App\Controllers\Api;

use App\Models\CategoriaModel;

class CategoriaController extends BaseApiController
{
    protected CategoriaModel $categoriaModel;

    public function __construct()
    {
        $this->categoriaModel = new CategoriaModel();
    }

    public function index()
    {
        $categorias = $this->categoriaModel->where('activo', 1)->findAll();
        return $this->ok($categorias);
    }
}
