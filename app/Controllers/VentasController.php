<?php

namespace App\Controllers;

use App\Services\VentasService;
use App\Services\UsuarioService;
use App\Services\ConsultaService;
use App\Services\GaleriaService;
use App\Services\CarritoService;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * Class VentasController
 *
 * Controlador encargado de gestionar las operaciones de ventas, pedidos activos,
 * fases de producción del taller, priorización de colas, comprobantes y reportes
 * estadísticos de CVA Muebles. Coordina los flujos de negocio delegando en la capa de servicios.
 *
 * @package App\Controllers
 */
class VentasController extends BaseController
{
    /**
     * @var VentasService Servicio encargado de procesar la lógica de negocio de ventas y pedidos.
     */
    protected $ventasService;

    /**
     * @var UsuarioService Servicio encargado de la gestión y consulta de usuarios/clientes.
     */
    protected $usuarioService;

    /**
     * @var ConsultaService Servicio para la administración y conteo de consultas.
     */
    protected $consultaService;

    /**
     * @var GaleriaService Servicio para la gestión y validación de imágenes de la galería.
     */
    protected $galeriaService;

    /**
     * Constructor del controlador.
     *
     * Carga los helpers necesarios de URL y formularios, e inicializa las instancias
     * de los servicios requeridos por las operaciones de pedidos y taller.
     */
    public function __construct()
    {
        helper(['url', 'form']);
        $this->ventasService   = new VentasService();
        $this->usuarioService  = new UsuarioService();
        $this->consultaService = new ConsultaService();
        $this->galeriaService  = new GaleriaService();
    }

    /**
     * Muestra el panel administrativo de listado de ventas/pedidos con estadísticas
     * procesadas por el taller para el mes actual.
     *
     * @return string Contenido HTML renderizado de la vista de administración de ventas.
     */
    public function index_ventas()
    {
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
     * Procesa y registra la compra de uno o varios productos contenidos en el carrito del cliente,
     * delegando la transacción al servicio de ventas y vaciando el carrito tras un registro exitoso.
     *
     * @return RedirectResponse Redirección a la lista de pedidos con mensaje flash de éxito o de error.
     */
    public function registrar_venta()
    {
        // Guard: bloquear checkout de carrito cuando el modo WhatsApp está activo
        if (!env('SHOPPING_CART_ENABLED')) {
            return redirect()->to(base_url('productos'))->with('error', 'El carrito de compras no está habilitado.');
        }

        $items_seleccionados_ids = $this->request->getPost('selected_items');
        $observaciones = $this->request->getPost('observaciones');
        
        if (empty($items_seleccionados_ids)) {
            return redirect()->to('/muestro')->with('error', 'Debes seleccionar al menos un producto para generar el pedido.');
        }

        $cartService = new CarritoService();
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
     * Permite visualizar el comprobante o factura de un pedido específico.
     * Implementa validaciones de seguridad para garantizar que los clientes solo accedan a sus
     * propios comprobantes, mientras que los administradores gozan de acceso global.
     *
     * @param int $venta_id Identificador único de la venta/pedido.
     * 
     * @return string|RedirectResponse Vista HTML del comprobante o redirección si no hay accesos o datos.
     */
    public function ver_factura($venta_id)
    {
        $data = $this->ventasService->getGestionDetalle($venta_id);
        if (!$data) {
            return redirect()->to('/productos')->with('error', 'Pedido no encontrado.');
        }

        $isAdmin = session()->get('perfil_id') == 1;

        // Seguridad: Verificar que el pedido sea del usuario o que el usuario sea Admin
        if (!$isAdmin && $data['venta']['usuario_id'] != session()->get('id_usuario')) {
            return redirect()->to('/productos')->with('error', 'No tienes permiso para ver este pedido.');
        }

        if ($isAdmin) {
            return view('back/sales/ver_factura_admin', array_merge($data, [
                'title' => 'Comprobante Pedido #' . $venta_id
            ]));
        } else {
            return view('back/sales/ver_factura_usuario', array_merge($data, [
                'title' => 'Detalle de mi Pedido #' . $venta_id,
                'layout' => 'layout/main'
            ]));
        }
    }

    /**
     * Recupera y muestra el listado histórico de pedidos realizados por el usuario autenticado.
     *
     * @return string Contenido HTML de la vista de compras de usuario.
     */
    public function ver_facturas_usuario()
    {
        $id_usuario = session()->get('id_usuario');
        $ventas = $this->ventasService->getVentasPorUsuario($id_usuario);

        return view('back/sales/vistaCompras', [
            'ventas' => $ventas,
            'title'  => 'Todas mis Compras'
        ]);
    }

    /**
     * Actualiza el estado actual o fase de producción de un pedido específico.
     *
     * @param int $venta_id Identificador único del pedido a actualizar.
     * 
     * @return RedirectResponse Redirección al módulo de gestión con mensajes de estado.
     */
    public function actualizar_estado($venta_id)
    {
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
     * Renderiza el dashboard analítico de estadísticas e indicadores de rendimiento
     * del taller de muebles, incluyendo peticiones activas, tareas y galerías pendientes.
     *
     * @return string Contenido HTML renderizado del panel de estadísticas.
     */
    public function estadisticas()
    {
        return view('back/sales/estadisticas', [
            'stats'                    => $this->ventasService->getDashboardStats(),
            'total_consultas'          => $this->consultaService->countActivas(),
            'total_galeria_pendientes' => $this->galeriaService->getPendientesCount(),
            'title'                    => 'Estadísticas del Taller'
        ]);
    }

    /**
     * Muestra el formulario administrativo para el registro manual de un pedido
     * personalizado/a medida para un cliente específico.
     *
     * @return string Contenido HTML de la vista de registro de pedido personalizado.
     */
    public function nuevo_pedido_personalizado()
    {
        return view('back/sales/nuevo_pedido_personalizado', [
            'clientes' => $this->usuarioService->getClientesActivos(),
            'title'    => 'Nuevo Pedido Personalizado'
        ]);
    }

    /**
     * Procesa la creación y persistencia de un pedido personalizado para un cliente seleccionado.
     * Realiza validaciones estrictas sobre el archivo de imagen de referencia subido para
     * garantizar la seguridad del servidor antes de derivar el proceso a la capa de servicios.
     *
     * @return RedirectResponse Redirección con mensaje flash de éxito o de error.
     */
    public function guardar_pedido_personalizado()
    {
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
     * Muestra la interfaz de administración para la gestión en tiempo real de las fases
     * de fabricación, detalles de pago y notas de un pedido en curso.
     *
     * @param int $venta_id Identificador único del pedido.
     * 
     * @return string|RedirectResponse Contenido HTML de la vista de administración o redirección si no existe.
     */
    public function ver_gestion_pedido($venta_id)
    {
        $data = $this->ventasService->getGestionDetalle($venta_id);
        if (!$data) {
            return redirect()->to('/ventas-list')->with('error', 'Pedido no encontrado.');
        }

        $data['title'] = 'Gestión de Pedido #' . $venta_id;
        return view('back/sales/gestion_pedido_admin', $data);
    }

    /**
     * Registra un cobro parcial o pago total sobre un pedido activo.
     * Valida que el monto ingresado cumpla con criterios de formato numérico mayor que cero.
     *
     * @return RedirectResponse Redirección de regreso con mensaje flash.
     */
    public function registrar_pago()
    {
        $monto = $this->request->getPost('monto');

        if (!is_numeric($monto) || (float) $monto <= 0) {
            return redirect()->back()->with('fail', 'El monto ingresado no es válido. Debe ser un número mayor a 0.');
        }

        $this->ventasService->registrarPago(
            $this->request->getPost('venta_id'),
            (float) $monto,
            $this->request->getPost('nota')
        );

        return redirect()->back()->with('success', 'Pago registrado exitosamente.');
    }

    /**
     * Guarda modificaciones y observaciones sobre los detalles constructivos de un pedido.
     * Sanitiza etiquetas de referencia a imágenes mediante expresiones regulares para
     * evitar posibles inyecciones de código.
     *
     * @return RedirectResponse Redirección de regreso con mensaje flash de éxito.
     */
    public function guardar_observaciones()
    {
        $observaciones = $this->request->getPost('observaciones');
        $img_ref_tag   = $this->request->getPost('img_ref_tag');

        // Seguridad: solo se permite el formato exacto [IMG_REF:nombre_de_archivo].
        // Descarta cualquier otro contenido para prevenir XSS persistente.
        if ($img_ref_tag && preg_match('/^\[IMG_REF:[a-zA-Z0-9_\-.]+\]$/', $img_ref_tag)) {
            $observaciones .= "\n" . $img_ref_tag;
        }

        $this->ventasService->actualizarObservaciones(
            $this->request->getPost('venta_id'),
            $observaciones
        );

        return redirect()->back()->with('success', 'Detalles del pedido actualizados.');
    }

    /**
     * Incrementa la prioridad de producción de un pedido, subiéndolo en la cola de trabajo del taller.
     *
     * @param int $venta_id Identificador único del pedido.
     * 
     * @return RedirectResponse Redirección de regreso con mensaje flash de éxito.
     */
    public function subir_prioridad($venta_id)
    {
        $this->ventasService->subirPrioridad($venta_id);
        return redirect()->back()->with('success', 'Prioridad de pedido actualizada correctamente.');
    }

    /**
     * Disminuye la prioridad de producción de un pedido, bajándolo en la cola de trabajo del taller.
     *
     * @param int $venta_id Identificador único del pedido.
     * 
     * @return RedirectResponse Redirección de regreso con mensaje flash de éxito.
     */
    public function bajar_prioridad($venta_id)
    {
        $this->ventasService->bajarPrioridad($venta_id);
        return redirect()->back()->with('success', 'Prioridad de pedido actualizada correctamente.');
    }
}
