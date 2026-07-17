<?php

namespace App\Controllers\Api;

use App\Libraries\ApiAuthContext;
use App\Services\GaleriaService;

class GaleriaController extends BaseApiController
{
    protected GaleriaService $galeriaService;

    public function __construct()
    {
        $this->galeriaService = new GaleriaService();
    }

    public function index()
    {
        return $this->ok($this->galeriaService->getAprobadas());
    }

    public function store()
    {
        $rules = [
            'imagen' => [
                'rules'  => 'uploaded[imagen]|is_image[imagen]|mime_in[imagen,image/jpg,image/jpeg,image/png,image/webp]|max_size[imagen,2048]',
                'label'  => 'Foto de cliente',
                'errors' => [
                    'mime_in'   => 'Solo se permiten imágenes en formato JPG, JPEG, PNG o WEBP.',
                    'max_size'  => 'La imagen es demasiado pesada (máximo 2MB).',
                    'is_image'  => 'El archivo seleccionado no es una imagen válida.',
                ],
            ],
            'comentario' => 'permit_empty|max_length[255]',
        ];

        if (!$this->validate($rules)) {
            return $this->fail($this->validator->getError('imagen') ?? 'Error en la validación.', 422);
        }

        $usuario = ApiAuthContext::user();
        $img = $this->request->getFile('imagen');

        $resultado = $this->galeriaService->subir(
            $usuario['id_usuario'],
            $img,
            $this->request->getPost('comentario')
        );

        if (!$resultado) {
            return $this->fail('Hubo un problema al subir la imagen.', 500);
        }

        return $this->ok(null, 201);
    }
}
