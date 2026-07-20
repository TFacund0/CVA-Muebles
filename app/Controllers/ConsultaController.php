<?php

namespace App\Controllers;

/**
 * Controlador para consultas refactorizado para usar Capa de Servicios.
 */
class ConsultaController extends BaseController {

    protected $consultaService;

    public function __construct() {
        helper(['form', 'url']);
        $this->consultaService = new \App\Services\ConsultaService();
    }

    /**
     * Muestra el listado de consultas para administración.
     *
     * @return string
     */
    public function index() {


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
     * Procesa el envío de una nueva consulta.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function cargarConsulta() {
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
     * Desactiva (archiva) una consulta delegando al servicio.
     *
     * @param int|string $id Identificador de la consulta.
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function eliminarConsulta($id) {

        $this->consultaService->desactivar($id);
        $vista = $this->request->getGet('vista') ?? 'SI';
        return redirect()->to('/consultas?vista=' . $vista)->with('success', 'Consulta archivada correctamente.');
    }

    /**
     * Restaura una consulta a pendientes delegando al servicio.
     *
     * @param int|string $id Identificador de la consulta.
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function restaurarConsulta($id) {

        $this->consultaService->restaurar($id);
        $vista = $this->request->getGet('vista') ?? 'NO';
        return redirect()->to('/consultas?vista=' . $vista)->with('success', 'Consulta restaurada a pendientes.');
    }

    /**
     * Elimina permanentemente una consulta delegando al servicio.
     *
     * @param int|string $id Identificador de la consulta.
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function eliminarPermanente($id) {

        
        $razon = $this->request->getPost('razon_eliminacion') ?? 'No especificada';
        $this->consultaService->eliminarPermanente($id);
        
        $vista = $this->request->getGet('vista') ?? 'NO';
        return redirect()->to('/consultas?vista=' . $vista)->with('success', 'Consulta eliminada permanentemente de forma segura (Motivo: ' . esc($razon) . ').');
    }
}
