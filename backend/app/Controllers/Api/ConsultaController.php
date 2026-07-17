<?php

namespace App\Controllers\Api;

use App\Services\ConsultaService;

class ConsultaController extends BaseApiController
{
    protected ConsultaService $consultaService;

    public function __construct()
    {
        $this->consultaService = new ConsultaService();
    }

    public function store()
    {
        $throttler = \Config\Services::throttler();
        if ($throttler->check(md5($this->request->getIPAddress()), 3, 86400) === false) {
            return $this->fail('Límite de 3 consultas por día alcanzado.', 429);
        }

        $body = $this->getBody();

        // Honeypot: campo oculto que un humano nunca completa.
        if (!empty($body['middle_name'])) {
            return $this->fail('Detectamos actividad inusual. Por favor intenta más tarde.', 422);
        }

        $data = [
            'nombre'      => $body['nombre'] ?? null,
            'apellido'    => $body['apellido'] ?? null,
            'email'       => $body['email'] ?? null,
            'telefono'    => $body['telefono'] ?? null,
            'asunto'      => $body['asunto'] ?? null,
            'descripcion' => $body['descripcion'] ?? null,
        ];

        $resultado = $this->consultaService->registrar($data);

        if ($resultado['status'] !== 'success') {
            return $this->fail($resultado['message'], 422);
        }

        return $this->ok(null, 201);
    }
}
