<?php

namespace App\Constants;

/**
 * Constantes de los estados de un pedido/venta (columna ventas_cabecera.estado).
 * Antes vivían como strings sueltos repetidos en VentasService, VentasController
 * y AdminVentaController — centralizarlos evita typos silenciosos (ej. 'ENTREGADO'
 * vs 'Entregado') que un `where('estado', ...)` no detectaría en tiempo de ejecución.
 */
final class VentaEstado
{
    public const PENDIENTE = 'PENDIENTE';
    public const EN_PROCESO = 'EN_PROCESO';
    public const TERMINADO = 'TERMINADO';
    public const ENTREGADO = 'ENTREGADO';

    /** @return list<string> Todos los estados de la fase de producción (no incluye la aprobación). */
    public static function all(): array
    {
        return [self::PENDIENTE, self::EN_PROCESO, self::TERMINADO, self::ENTREGADO];
    }
}
