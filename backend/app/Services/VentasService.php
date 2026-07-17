<?php

namespace App\Services;

use App\Constants\VentaEstado;
use App\Models\VentasCabeceraModel;
use App\Models\VentasDetalleModel;
use App\Models\ProductoModel;
use App\Models\VentasPagosModel;
use App\Models\UsuarioModel;
use CodeIgniter\HTTP\Files\UploadedFile;

/**
 * Class VentasService
 *
 * Servicio encargado de orquestar toda la lógica de negocio asociada a las ventas, transacciones
 * comerciales y gestión del taller de fabricación de CVA Muebles. Administra la creación de pedidos
 * mediante carrito o de manera manual (personalizados con imágenes de referencia), el flujo de
 * estados de producción (PENDIENTE -> EN_PROCESO -> TERMINADO -> ENTREGADO), el registro de cobros y
 * amortizaciones (señas/pagos parciales), la prioridad en colas de trabajo, y paneles analíticos.
 *
 * @package App\Services
 */
class VentasService
{
    /**
     * @var VentasCabeceraModel Modelo para la cabecera de las ventas y pedidos.
     */
    protected $ventasModel;

    /**
     * @var VentasDetalleModel Modelo para el desglose e ítems incluidos en cada pedido.
     */
    protected $detalleModel;

    /**
     * @var ProductoModel Modelo para el catálogo de productos y muebles.
     */
    protected $productoModel;

    /**
     * @var VentasPagosModel Modelo para los cobros y pagos parciales asociados a los pedidos.
     */
    protected $pagosModel;

    /**
     * @var \CodeIgniter\Database\BaseConnection Instancia de conexión a base de datos para controlar transacciones.
     */
    protected $db;

    /**
     * @var PedidoPrioridadService Maneja el reordenamiento de la cola de producción (extraído de esta clase).
     */
    protected PedidoPrioridadService $prioridadService;

    /**
     * @var VentasEstadisticasService Maneja listados/reportes (extraído de esta clase).
     */
    protected VentasEstadisticasService $estadisticasService;

    /**
     * Constructor del servicio.
     *
     * Inicializa las instancias de los modelos de base de datos requeridos y establece
     * la conexión transaccional con la base de datos de CodeIgniter 4.
     */
    public function __construct()
    {
        $this->ventasModel   = new VentasCabeceraModel();
        $this->detalleModel  = new VentasDetalleModel();
        $this->productoModel = new ProductoModel();
        $this->pagosModel    = new VentasPagosModel();
        $this->db            = \Config\Database::connect();

        $this->prioridadService    = new PedidoPrioridadService();
        $this->estadisticasService = new VentasEstadisticasService();
    }

    /**
     * Recupera el listado total de pedidos (separándolos en solicitudes de presupuesto y pedidos activos en el taller),
     * computando métricas financieras consolidadas (ingresos reales acumulados por cobros) y operacionales de producción
     * para el mes calendario en curso.
     *
     * @return array Estructura enriquecida con pedidos en taller ('ventas'), presupuestos ('solicitados') y métricas ('counts').
     */
    public function getVentasConEstadisticas($search = null, $estado = null, $filterMode = 'client')
    {
        return $this->estadisticasService->getVentasConEstadisticas($search, $estado, $filterMode);
    }

    /**
     * Procesa transaccionalmente el registro de un nuevo pedido en el sistema a partir de
     * los productos seleccionados en el carrito de compras.
     *
     * @param int|string $usuario_id Identificador único del cliente.
     * @param array $items_seleccionados Detalle de ítems a adquirir (id, cantidad, precio).
     * @param string $observaciones Notas complementarias asociadas a la entrega o diseño.
     * 
     * @return array Resumen de estado ('status' => 'success'|'error', 'venta_id' => int si es exitoso).
     */
    public function procesarVenta($usuario_id, $items_seleccionados, $observaciones = '')
    {
        if (empty($items_seleccionados)) {
            return ['status' => 'error', 'message' => 'No hay productos seleccionados para el pedido.'];
        }

        $total = 0;
        foreach ($items_seleccionados as $item) {
            $total += $item['price'] * $item['qty'];
        }

        $this->db->transStart();
        try {
            $venta_id = $this->ventasModel->insert([
                'usuario_id'         => $usuario_id,
                'fecha'              => date('Y-m-d H:i:s'),
                'total_venta'        => $total,
                'estado'             => VentaEstado::PENDIENTE,
                'estado_aprobacion'  => 'SOLICITUD',
                'observaciones'      => $observaciones,
                'tipo_pedido'        => 'CARRITO'
            ]);

            if (!$venta_id) {
                throw new \Exception("No se pudo crear la cabecera de la venta.");
            }

            foreach ($items_seleccionados as $item) {
                $this->detalleModel->insert([
                    'venta_id'    => $venta_id,
                    'producto_id' => $item['id'],
                    'cantidad'    => $item['qty'],
                    'precio'      => $item['price'],
                ]);
            }

            $this->db->transComplete();

            try {
                $usuarioService = new \App\Services\UsuarioService();
                $usuario = $usuarioService->getUsuario($usuario_id);
                if ($usuario && !empty($usuario['email'])) {
                    $emailService = new \App\Services\EmailService();
                    $articulosFormatted = [];
                    foreach ($items_seleccionados as $item) {
                        $articulosFormatted[] = [
                            'nombre'   => $item['name'],
                            'cantidad' => $item['qty'],
                            'subtotal' => $item['price'] * $item['qty']
                        ];
                    }
                    $nombreCompleto = $usuario['nombre'] . ' ' . $usuario['apellido'];
                    $emailService->enviarConfirmacionPedido($usuario['email'], $nombreCompleto, $venta_id, $total, $articulosFormatted);
                }
            } catch (\Exception $emailEx) {
                log_message('error', '[VentasService::procesarVenta] Error al enviar email: ' . $emailEx->getMessage());
            }

            return ['status' => 'success', 'total' => $total, 'venta_id' => $venta_id];
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', '[VentasService::procesarVenta] ' . $e->getMessage());
            return ['status' => 'error', 'message' => 'Ocurrió un error interno al procesar su solicitud. Intente nuevamente.'];
        }
    }

    /**
     * Recupera el detalle analítico consolidado de un pedido específico.
     * Combina la cabecera de la venta, lista de muebles adquiridos, pagos amortizados y saldo pendiente de cobro.
     *
     * @param int|string $venta_id Identificador del pedido/venta.
     * 
     * @return array|null Estructura consolidada del pedido o null si el identificador es inexistente.
     */
    public function getGestionDetalle($venta_id)
    {
        $venta = $this->ventasModel->getVentas($venta_id)[0] ?? null;
        if (!$venta) {
            return null;
        }

        $detalles = $this->detalleModel->getDetalles($venta_id);
        $pagos = $this->pagosModel->getPagosPorVenta($venta_id);
        $total_pagado = $this->pagosModel->getTotalPagado($venta_id);

        return [
            'venta'           => $venta,
            'detalles'        => $detalles,
            'pagos'           => $pagos,
            'total_pagado'    => $total_pagado,
            'saldo_pendiente' => $venta['total_venta'] - $total_pagado
        ];
    }

    /**
     * Actualiza el estado de aprobación administrativa o fase operacional de fabricación del taller.
     *
     * @param int|string $venta_id Identificador único del pedido.
     * @param string $estado Nuevo estado (ACEPTADO, RECHAZADO, PENDIENTE, EN_PROCESO, TERMINADO, ENTREGADO).
     * 
     * @return bool True si la operación se procesó exitosamente; false en caso contrario.
     */
    public function actualizarEstado($venta_id, $estado)
    {
        $venta_actual = $this->ventasModel->find($venta_id);
        if (!$venta_actual) {
            return false;
        }

        // --- FLUJO DE APROBACIÓN (SOLICITUD -> ACEPTADO/RECHAZADO) ---
        if ($estado == 'ACEPTADO' || $estado == 'RECHAZADO') {
            $this->db->transStart();
            try {
                // Lógica de stock removida ya que se trabaja a medida y bajo demanda.
                $this->ventasModel->update($venta_id, [
                    'estado_aprobacion' => $estado,
                    'estado'            => ($estado == 'ACEPTADO') ? VentaEstado::PENDIENTE : $venta_actual['estado']
                ]);

                $this->db->transComplete();

                // Notificar Aceptación o Rechazo
                try {
                    $usuarioService = new \App\Services\UsuarioService();
                    $usuario = $usuarioService->getUsuario($venta_actual['usuario_id']);
                    if ($usuario && !empty($usuario['email'])) {
                        $emailService = new \App\Services\EmailService();
                        $nombreCompleto = $usuario['nombre'] . ' ' . $usuario['apellido'];
                        // Si es aceptado, el estado del pedido pasa a PENDIENTE
                        $estadoEmail = ($estado == 'ACEPTADO') ? VentaEstado::PENDIENTE : 'RECHAZADO';
                        $emailService->enviarActualizacionEstado($usuario['email'], $nombreCompleto, $venta_id, $estadoEmail);
                    }
                } catch (\Exception $emailEx) {
                    log_message('error', '[VentasService::actualizarEstado] Error al enviar email de aprobacion: ' . $emailEx->getMessage());
                }

                return true;
            } catch (\Exception $e) {
                $this->db->transRollback();
                return false;
            }
        }

        // --- FLUJO DE PRODUCCIÓN (PENDIENTE -> EN_PROCESO -> TERMINADO -> ENTREGADO) ---
        $resultado = $this->ventasModel->update($venta_id, ['estado' => $estado]);
        
        if ($resultado) {
            try {
                $usuarioService = new \App\Services\UsuarioService();
                $usuario = $usuarioService->getUsuario($venta_actual['usuario_id']);
                if ($usuario && !empty($usuario['email'])) {
                    $emailService = new \App\Services\EmailService();
                    $nombreCompleto = $usuario['nombre'] . ' ' . $usuario['apellido'];
                    $emailService->enviarActualizacionEstado($usuario['email'], $nombreCompleto, $venta_id, $estado);
                }
            } catch (\Exception $emailEx) {
                log_message('error', '[VentasService::actualizarEstado] Error al enviar email de estado: ' . $emailEx->getMessage());
            }
        }
        
        return $resultado;
    }

    /**
     * Registra un pago parcial (seña) o amortización total a cuenta de un pedido en el taller.
     *
     * @param int|string $venta_id Identificador de la venta vinculada.
     * @param float $monto Importe del abono registrado.
     * @param string $nota Detalle descriptivo o comprobante del cobro.
     * 
     * @return bool|int|string Retorna el resultado de la inserción en base de datos.
     */
    public function registrarPago($venta_id, $monto, $nota = '')
    {
        return $this->pagosModel->insert([
            'venta_id' => $venta_id,
            'monto'    => $monto,
            'nota'     => $nota
        ]);
    }

    /**
     * Modifica las especificaciones u observaciones constructivas escritas asociadas a un pedido.
     *
     * @param int|string $venta_id Identificador del pedido.
     * @param string $observaciones Texto descriptivo de los detalles actualizados.
     * 
     * @return bool|int|string Retorna el resultado del update en base de datos.
     */
    public function actualizarObservaciones($venta_id, $observaciones)
    {
        return $this->ventasModel->update($venta_id, ['observaciones' => $observaciones]);
    }

    /**
     * Recupera el volumen agregado de pedidos clasificados por su fase activa de producción
     * (PENDIENTE, EN_PROCESO, TERMINADO, ENTREGADO) a través de una consulta única agrupada.
     *
     * @return array Histograma de cantidades por estado operacional de fabricación.
     */
    public function getDashboardStats()
    {
        return $this->estadisticasService->getDashboardStats();
    }

    /**
     * Procesa el alta transaccional de un pedido personalizado (mueble a medida) de forma manual
     * por la administración, manejando opcionalmente imágenes físicas de referencia (ej: planos o croquis),
     * vinculando un cliente genérico por defecto de ser requerido y asentando la seña inicial de cobro.
     *
     * @param array $data Atributos de pedido manual (cliente_id, total, seña, descripción constructiva).
     * @param UploadedFile|null $file Imagen física de referencia cargada (croquis/diseño).
     * 
     * @return array Resumen de estado ('status' => 'success'|'error', 'venta_id' => int si tiene éxito).
     */
    public function registrarPedidoPersonalizado($data, UploadedFile $file = null)
    {
        $usuarioModel = new UsuarioModel();
        
        // Si viene un usuario_id, lo usamos. Si no, usamos el genérico para WhatsApp
        $usuario_id = $data['usuario_id'] ?? null;
        
        if (empty($usuario_id)) {
            $usuario_gen = $usuarioModel->where('usuario', 'cliente_whatsapp')->first();
            if (!$usuario_gen) {
                return ['status' => 'error', 'message' => 'No se encontró el usuario genérico.'];
            }
            $usuario_id = $usuario_gen['id_usuario'];
        }

        $this->db->transStart();
        try {
            // Manejo de Imagen Opcional de croquis o referencia constructiva
            $img_ref = "";
            if ($file && $file->isValid() && !$file->hasMoved()) {
                $cloudinaryService = new \App\Services\CloudinaryService();
                $tmpPath = $file->getTempName();
                $resultadoCloud = $cloudinaryService->subirImagen($tmpPath, 'cva_muebles/referencias');
                if ($resultadoCloud['status'] === 'success') {
                    $img_ref = $resultadoCloud['url'];
                }
            }

            $observaciones = "CLIENTE: " . $data['nombre_cliente'] . "\n" . $data['detalles_obra'];
            if ($img_ref) {
                $observaciones .= "\n[IMG_REF:" . $img_ref . "]";
            }

            $venta_id = $this->ventasModel->insert([
                'usuario_id'         => $usuario_id,
                'total_venta'        => $data['total_venta'],
                'estado'             => VentaEstado::PENDIENTE,
                'estado_aprobacion'  => 'ACEPTADO',
                'observaciones'      => $observaciones,
                'fecha'              => date('Y-m-d H:i:s'),
                'tipo_pedido'        => 'MANUAL'
            ]);
            
            $this->detalleModel->insert([
                'venta_id' => $venta_id, 'producto_id' => null, 'cantidad' => 1, 'precio' => $data['total_venta']
            ]);

            if ($data['monto_sena'] > 0) {
                $this->registrarPago($venta_id, $data['monto_sena'], 'Seña inicial - Pedido Manual');
            }

            $this->db->transComplete();
            return ['status' => 'success', 'venta_id' => $venta_id];
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', '[VentasService::registrarPago] ' . $e->getMessage());
            return ['status' => 'error', 'message' => 'Ocurrió un error interno al procesar el pago. Intente nuevamente.'];
        }
    }

    public function getVentasPorUsuario($usuario_id, $filter = null, $sort = null)
    {
        $ventas = $this->ventasModel->getVentasPorUsuarioFiltradas($usuario_id, $filter, $sort);
        
        // Solución Problema N+1: Cargar todos los ítems en 1 sola consulta
        $venta_ids = array_column($ventas, 'id');
        $detalles_agrupados = $this->detalleModel->getDetallesBatch($venta_ids);

        foreach ($ventas as &$v) {
            $v['items'] = $detalles_agrupados[$v['id']] ?? [];
        }
        return $ventas;
    }

    /**
     * Incrementa la prioridad constructiva de un pedido en la cola de producción activa
     * de los artesanos en el taller.
     * Intercambia la prioridad con el pedido inmediatamente superior.
     *
     * @param int|string $venta_id Identificador único del pedido.
     * 
     * @return void
     */
    public function subirPrioridad($venta_id)
    {
        $this->prioridadService->subir($venta_id);
    }

    /**
     * Disminuye la prioridad constructiva de un pedido en la cola de producción activa
     * de los artesanos en el taller.
     * Intercambia la prioridad con el pedido inmediatamente inferior.
     *
     * @param int|string $venta_id Identificador único del pedido.
     *
     * @return void
     */
    public function bajarPrioridad($venta_id)
    {
        $this->prioridadService->bajar($venta_id);
    }

    /**
     * Elimina permanentemente un pedido del sistema.
     * Esta función está diseñada para limpiar pedidos de prueba.
     * 
     * @param int|string $venta_id Identificador del pedido.
     * @return array Status
     */
    public function eliminarPedidoPermanente($venta_id)
    {
        $venta_actual = $this->ventasModel->find($venta_id);
        if (!$venta_actual) {
            return ["status" => "error", "message" => "Pedido no encontrado."];
        }

        $this->db->transStart();
        try {
            // Eliminar dependencias
            $this->db->table('ventas_pagos')->where('venta_id', $venta_id)->delete();
            $this->db->table('ventas_detalle')->where('venta_id', $venta_id)->delete();
            
            // Eliminar cabecera
            $this->ventasModel->delete($venta_id);

            $this->db->transComplete();
            return ["status" => "success", "message" => "Pedido de prueba eliminado correctamente de la base de datos."];
        } catch (\Exception $e) {
            $this->db->transRollback();
            return ["status" => "error", "message" => "Error al eliminar el pedido: " . $e->getMessage()];
        }
    }
}
