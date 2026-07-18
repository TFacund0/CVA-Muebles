<?php

namespace App\Controllers\Api;

use App\Libraries\ApiAuthContext;
use App\Services\UsuarioService;

class PerfilController extends BaseApiController
{
    protected UsuarioService $usuarioService;

    public function __construct()
    {
        $this->usuarioService = new UsuarioService();
    }

    public function update()
    {
        $usuario = ApiAuthContext::user();
        $body = $this->getBody();

        $data = [
            'usuario'  => $body['usuario'] ?? null,
            'nombre'   => $body['nombre'] ?? null,
            'apellido' => $body['apellido'] ?? null,
            'email'    => $body['email'] ?? null,
        ];

        $imagen = $this->request->getFile('imagen');
        $imagenValida = $imagen && $imagen->isValid() && !$imagen->hasMoved() ? $imagen : null;

        $resultado = $this->usuarioService->actualizarPerfil($usuario['id_usuario'], $data, $imagenValida);

        if ($resultado['status'] !== 'success') {
            return $this->failJson($resultado['message'], 422);
        }

        return $this->ok($resultado['updated_data']);
    }

    public function changePassword()
    {
        $usuario = ApiAuthContext::user();
        $body = $this->getBody();

        $resultado = $this->usuarioService->cambiarPassword(
            $usuario['id_usuario'],
            $body['current_password'] ?? '',
            $body['new_password'] ?? '',
            $body['confirm_password'] ?? ''
        );

        if ($resultado['status'] !== 'success') {
            return $this->failJson($resultado['message'], 422);
        }

        return $this->ok(null);
    }
}
