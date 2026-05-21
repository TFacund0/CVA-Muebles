<?php

namespace App\Controllers;

use App\Services\ConsultaService;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Class ConsultaController
 *
 * Controlador encargado de gestionar las consultas de contacto de los clientes en CVA Muebles.
 * Proporciona listados interactivos para la administración, filtros, mecanismos de
 * protección contra spam (Throttler y Honeypot), y acciones para archivar, restaurar
 * o eliminar permanentemente consultas.
 * Delega la lógica de negocio a la capa de servicios (`ConsultaService`).
 *
 * @package App\Controllers
 */
class ConsultaController extends BaseController 
{
    /**
     * @var ConsultaService Servicio que gestiona la lógica de las consultas de contacto.
     */
    protected $consultaService;

    /**
     * Constructor del controlador.
     *
     * Inicializa los helpers requeridos y la capa de servicios de consulta.
     */
    public function __construct() 
    {
        helper(['form', 'url']);
        $this->consultaService = new ConsultaService();
    }

    /**
     * Muestra el panel de administración de consultas de contacto recibidas.
     *
     * Carga estadísticas mensuales y totales para la visualización del administrador.
     *
     * @return string|ResponseInterface Contenido HTML de la vista de listado de consultas.
     */
    public function index() 
    {    
        $resultado = $this->consultaService->getConsultasConStats();
        $meses = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];

        return view('back/messages/lista_consultas', [
            'consultas' => $resultado['consultas'],
            'counts'    => $resultado['counts'],
            'nombreMes' => $meses[(int)date('m') - 1],
            'title'     => 'Gestión de Consultas'
        ]);
    }

    /**
     * Valida y procesa el envío de una nueva consulta desde el formulario público de contacto.
     *
     * Implementa medidas de seguridad críticas:
     * 1. **Throttler (Limitador de Tasa):** Restringe a un máximo de 3 consultas por día por dirección IP.
     * 2. **Honeypot:** Detecta campos ocultos autorellenados por bots de spam para rechazar la petición inmediatamente.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse Redirección de vuelta con mensajes de estado.
     */
    public function cargarConsulta() 
    {
        $throttler = \Config\Services::throttler();
        if ($throttler->check(md5($this->request->getIPAddress()), 3, 86400) === false) {
            return redirect()->back()->withInput()->with('error', 'Límite de 3 consultas por día alcanzado.');
        }

        // Honeypot check
        if (!empty($this->request->getPost('middle_name'))) {
            return redirect()->to('/')->with('error', 'Detectamos actividad inusual. Por favor intenta más tarde.');
        }

        $resultado = $this->consultaService->registrar($this->request->getPost());
        
        if ($resultado['status'] === 'error') {
            return redirect()->back()->withInput()->with('error', $resultado['message']);
        }
        
        return redirect()->back()->with('success', $resultado['message']);
    }

    /**
     * Desactiva de manera lógica (archiva) una consulta seleccionada en el sistema.
     *
     * @param int|string $id Identificador único de la consulta a archivar.
     * 
     * @return \CodeIgniter\HTTP\RedirectResponse Redirección a la lista de consultas conservando el estado de la vista.
     */
    public function eliminarConsulta($id) 
    {
        $this->consultaService->desactivar($id);
        $vista = $this->request->getGet('vista') ?? 'SI';
        return redirect()->to('/consultas?vista=' . $vista)->with('success', 'Consulta archivada correctamente.');
    }

    /**
     * Restaura una consulta previamente archivada al listado de pendientes.
     *
     * @param int|string $id Identificador único de la consulta a restaurar.
     * 
     * @return \CodeIgniter\HTTP\RedirectResponse Redirección al panel de consultas.
     */
    public function restaurarConsulta($id) 
    {
        $this->consultaService->restaurar($id);
        $vista = $this->request->getGet('vista') ?? 'NO';
        return redirect()->to('/consultas?vista=' . $vista)->with('success', 'Consulta restaurada a pendientes.');
    }

    /**
     * Elimina permanentemente una consulta de la base de datos de manera física y segura.
     *
     * Requiere que el administrador proporcione un motivo de eliminación para la auditoría interna.
     *
     * @param int|string $id Identificador único de la consulta a eliminar definitivamente.
     * 
     * @return \CodeIgniter\HTTP\RedirectResponse Redirección al panel con confirmación de eliminación.
     */
    public function eliminarPermanente($id) 
    {
        $razon = $this->request->getPost('razon_eliminacion') ?? 'No especificada';
        $this->consultaService->eliminarPermanente($id);
        
        $vista = $this->request->getGet('vista') ?? 'NO';
        return redirect()->to('/consultas?vista=' . $vista)->with('success', 'Consulta eliminada permanentemente de forma segura (Motivo: ' . esc($razon) . ').');
    }
}
