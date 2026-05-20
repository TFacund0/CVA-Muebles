<?php

namespace App\Controllers;

/**
 * Controlador para gestión de ventas refactorizado para usar Capa de Servicios.
 */
class VentasController extends BaseController {

    protected $ventasService;
    protected $usuarioService;
    protected $consultaService;

    public function __construct() {
        helper(['url', 'form']);
        $this->ventasService  = new \App\Services\VentasService();
        $this->usuarioService = new \App\Services\UsuarioService();
        $this->consultaService = new \App\Services\ConsultaService();
    }

    /**
     * Muestra el listado de ventas con estadísticas procesadas por el servicio.
     */
    public function index_ventas() {


        $resultado = $this->ventasService->getVentasConEstadisticas();
        
        $meses = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
        
        return view('back/sales/detalleVentas', [
            'ventas'      => $resultado['ventas'],
            'solicitados' => $resultado['solicitados'],
            'counts'      => $resultado['counts'],
            'nombreMes'   => $meses[(int)date('m') - 1],
            'title'       => 'Control de Pedidos'
        ]);
    }

    /**
     * Procesa el registro de una nueva venta delegando al servicio.
     */
    public function registrar_venta() {
        $items_seleccionados_ids = $this->request->getPost('selected_items');
        $observaciones = $this->request->getPost('observaciones');
        
        if (empty($items_seleccionados_ids)) {
            return redirect()->to('/muestro')->with('error', 'Debes seleccionar al menos un producto para generar el pedido.');
        }

        $cartService = new \App\Services\CarritoService();
        $carrito_completo = $cartService->getContenido();
        
        $items_a_procesar = [];
        foreach ($items_seleccionados_ids as $rowid) {
            if (isset($carrito_completo[$rowid])) {
                $items_a_procesar[] = $carrito_completo[$rowid];
            }
        }

        $resultado = $this->ventasService->procesarVenta(session()->get('id_usuario'), $items_a_procesar, $observaciones);

        if ($resultado['status'] === 'success') {
            // Eliminar solo los items procesados del carrito
            $cartService->eliminarVarios($items_seleccionados_ids);
            
            return redirect()->to('/ventas_lista')->with('success', '¡Pedido Recibido! Tu orden ha sido registrada con éxito y está en espera de ser aceptada por nuestro taller. Podrás seguir el progreso aquí mismo.');
        } else {
            return redirect()->to('/muestro')->with('error', $resultado['message']);
        }
    }

    /**
     * Muestra la factura de una venta específica.
     */
    public function ver_factura($venta_id) {
        $data = $this->ventasService->getGestionDetalle($venta_id);
        if (!$data) return redirect()->to('/productos')->with('error', 'Pedido no encontrado.');

        $isAdmin = session()->get('perfil_id') == 1;

        // Seguridad: Verificar que el pedido sea del usuario o que el usuario sea Admin
        if (!$isAdmin && $data['venta']['usuario_id'] != session()->get('id_usuario')) {
            return redirect()->to('/productos')->with('error', 'No tienes permiso para ver este pedido.');
        }

        $layout = $isAdmin ? 'layout/admin_layout' : 'layout/main';

        return view('back/sales/ver_factura_usuario', array_merge($data, [
            'title' => $isAdmin ? 'Comprobante Pedido #' . $venta_id : 'Detalle de mi Pedido #' . $venta_id,
            'layout' => $layout,
            'isAdmin' => $isAdmin
        ]));
    }

    /**
     * Muestra todas las facturas del usuario actual.
     */
    public function ver_facturas_usuario() {
        $id_usuario = session()->get('id_usuario');
        $ventas = $this->ventasService->getVentasPorUsuario($id_usuario);

        return view('back/sales/vistaCompras', [
            'ventas' => $ventas,
            'title'  => 'Todas mis Compras'
        ]);
    }

    /**
     * Actualiza el estado de una venta delegando al servicio.
     */
    public function actualizar_estado($venta_id) {


        $nuevo_estado = $this->request->getPost('estado');
        $this->ventasService->actualizarEstado($venta_id, $nuevo_estado);

        // Si se rechaza, volvemos al listado general de solicitudes.
        // Si se acepta o cambia de fase de producción, nos quedamos en la gestión.
        if ($nuevo_estado == 'RECHAZADO') {
            return redirect()->to('/ventas-list')->with('success', 'Pedido rechazado correctamente.');
        }

        return redirect()->back()->with('success', 'Estado de pedido actualizado a: ' . $nuevo_estado);
    }

    /**
     * Muestra estadísticas agregadas del taller.
     */
    public function estadisticas() {


        return view('back/sales/estadisticas', [
            'stats'           => $this->ventasService->getDashboardStats(),
            'total_consultas' => $this->consultaService->countActivas(),
            'title'           => 'Estadísticas del Taller'
        ]);
    }

    /**
     * Muestra el formulario para registrar un pedido manual.
     */
    public function nuevo_pedido_personalizado() {
        return view('back/sales/nuevo_pedido_personalizado', [
            'clientes' => $this->usuarioService->getClientesActivos(),
            'title'    => 'Nuevo Pedido Personalizado'
        ]);
    }

    /**
     * Procesa el registro de un pedido manual delegando al servicio.
     */
    public function guardar_pedido_personalizado() {


        $file = $this->request->getFile('imagen_referencia');

        // Validación estricta de la imagen de referencia
        if ($file && $file->isValid()) {
            $rulesRef = [
                'imagen_referencia' => 'is_image[imagen_referencia]|mime_in[imagen_referencia,image/jpg,image/jpeg,image/png,image/webp]|max_size[imagen_referencia,2048]'
            ];
            if (!$this->validate($rulesRef)) {
                return redirect()->back()->withInput()->with('error', 'La imagen de referencia no es válida o supera los 2MB.');
            }
        }

        $resultado = $this->ventasService->registrarPedidoPersonalizado($this->request->getPost(), $file);

        if ($resultado['status'] === 'success') {
            return redirect()->to('/ventas/gestion/' . $resultado['venta_id'])->with('success', 'Pedido personalizado registrado correctamente.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Error: ' . $resultado['message']);
        }
    }

    /**
     * Vista de gestión detallada para el administrador.
     */
    public function ver_gestion_pedido($venta_id) {


        $data = $this->ventasService->getGestionDetalle($venta_id);
        if (!$data) return redirect()->to('/ventas-list')->with('error', 'Pedido no encontrado.');

        $data['title'] = 'Gestión de Pedido #' . $venta_id;
        return view('back/sales/gestion_pedido_admin', $data);
    }

    /**
     * Registra un pago delegando al servicio.
     */
    public function registrar_pago() {


        $this->ventasService->registrarPago(
            $this->request->getPost('venta_id'),
            $this->request->getPost('monto'),
            $this->request->getPost('nota')
        );

        return redirect()->back()->with('success', 'Pago registrado exitosamente.');
    }

    /**
     * Actualiza las observaciones delegando al servicio.
     */
    public function guardar_observaciones() {


        $observaciones = $this->request->getPost('observaciones');
        $img_ref_tag = $this->request->getPost('img_ref_tag');
        
        if ($img_ref_tag) {
            $observaciones .= "\n" . $img_ref_tag;
        }

        $this->ventasService->actualizarObservaciones(
            $this->request->getPost('venta_id'),
            $observaciones
        );

        return redirect()->back()->with('success', 'Detalles del pedido actualizados.');
    }

    /**
     * Sube un pedido en el orden de prioridad del listado activo.
     */
    public function subir_prioridad($venta_id) {


        $this->ventasService->subirPrioridad($venta_id);
        return redirect()->back()->with('success', 'Prioridad de pedido actualizada correctamente.');
    }

    /**
     * Baja un pedido en el orden de prioridad del listado activo.
     */
    public function bajar_prioridad($venta_id) {


        $this->ventasService->bajarPrioridad($venta_id);
        return redirect()->back()->with('success', 'Prioridad de pedido actualizada correctamente.');
    }
}
