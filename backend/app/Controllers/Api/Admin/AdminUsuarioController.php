<?php

namespace App\Controllers\Api\Admin;

use App\Controllers\Api\BaseApiController;
use App\Services\UsuarioService;

class AdminUsuarioController extends BaseApiController
{
    protected UsuarioService $usuarioService;

    public function __construct()
    {
        $this->usuarioService = new UsuarioService();
    }

    public function index()
    {
        $search = $this->request->getGet('search');
        $perfil = $this->request->getGet('perfil');

        return $this->ok($this->usuarioService->getUsuariosConStats($search, $perfil));
    }

    public function estado($id = null)
    {
        $body = $this->getBody();
        $accion = $body['accion'] ?? null;

        $resultado = $accion === 'activar'
            ? $this->usuarioService->reactivar($id)
            : $this->usuarioService->darDeBaja($id);

        if (!$resultado) {
            return $this->fail('No se pudo actualizar el estado del usuario.', 422);
        }

        return $this->ok(null);
    }

    public function cambiarPerfil($id = null)
    {
        $resultado = $this->usuarioService->cambiarPerfil($id);

        if (!$resultado) {
            return $this->fail('No se pudo cambiar el perfil del usuario.', 422);
        }

        return $this->ok(null);
    }

    public function destroy($id = null)
    {
        $resultado = $this->usuarioService->eliminarPermanente($id);

        if ($resultado['status'] !== 'success') {
            return $this->fail($resultado['message'], 422);
        }

        return $this->ok(null);
    }
}
