<?php

namespace App\Controllers;

use App\Services\CarritoService;
use App\Services\ProductoService;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Class CarritoController
 *
 * Controlador encargado de gestionar las acciones del carrito de compras de CVA Muebles.
 * Coordina la comunicación entre las solicitudes HTTP (tanto tradicionales como AJAX) y
 * la capa de servicios de negocio (`CarritoService` y `ProductoService`).
 *
 * @package App\Controllers
 */
class CarritoController extends BaseController
{
    /**
     * @var CarritoService Servicio encargado de procesar la lógica de negocio del carrito.
     */
    protected $carritoService;

    /**
     * @var ProductoService Servicio encargado de procesar la lógica de negocio de los productos.
     */
    protected $productoService;

    /**
     * Constructor del controlador.
     * 
     * Inicializa los helpers requeridos y la capa de servicios de negocio.
     */
    public function __construct()
    {
        helper(['form', 'url', 'cart']);
        $this->carritoService = new \App\Services\CarritoService();
        $this->productoService = new \App\Services\ProductoService();
    }

    /**
     * Agrega un producto al carrito de compras.
     *
     * Obtiene los datos del formulario enviados por POST, procesa la agregación a través
     * del servicio y redirige de vuelta con un mensaje de éxito o de error.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse Redirección a la página anterior con mensajes flash.
     */
    public function add()
    {
        $resultado = $this->carritoService->agregar($this->request->getPost());
        
        if ($resultado['status'] === 'error') {
            return redirect()->back()->with('error', $resultado['message']);
        }
        return redirect()->back()->with('success', $resultado['message']);
    }

    /**
     * Elimina un producto específico del carrito por su identificador único de fila (rowid).
     *
     * @param string $rowid Identificador único de la fila del item dentro de la sesión del carrito.
     * 
     * @return \CodeIgniter\HTTP\RedirectResponse Redirección a la página anterior.
     */
    public function remove($rowid)
    {
        $this->carritoService->eliminar($rowid);
        return redirect()->back();
    }

    /**
     * Vacía por completo todos los productos contenidos en el carrito de compras.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse Redirección a la vista de catálogo o listado de productos.
     */
    public function borrar_carrito()
    {
        $this->carritoService->vaciar();
        return redirect()->to(base_url("muestro"));
    }

    /**
     * Muestra la interfaz del carrito de compras con el listado detallado de productos.
     *
     * Renderiza la vista del carrito pasando los ítems y totales actuales.
     *
     * @return string|ResponseInterface Contenido HTML de la vista del carrito.
     */
    public function muestra()
    {
        return view('front/pages/carrito', [
            'cart'  => $this->carritoService->getContenido(),
            'title' => 'Carrito de Compras'
        ]);
    }

    /**
     * Incrementa en una unidad la cantidad de un producto específico en el carrito.
     *
     * Soporta peticiones tradicionales (con redirección) y peticiones asíncronas AJAX
     * (retornando respuestas estructuradas en formato JSON).
     *
     * @param string $rowid Identificador único de la fila del item en el carrito.
     * 
     * @return ResponseInterface|\CodeIgniter\HTTP\RedirectResponse Respuesta JSON (para AJAX) o redirección.
     */
    public function suma($rowid)
    {
        $resultado = $this->carritoService->incrementar($rowid);
        
        if ($this->request->isAJAX()) {
            if ($resultado && $resultado['status'] === 'error') {
                return $this->response->setJSON(['status' => 'error', 'message' => $resultado['message']]);
            }
            return $this->response->setJSON([
                'status' => 'success', 
                'cart' => $this->carritoService->getContenido(),
                'totalItems' => \Config\Services::cart()->totalItems()
            ]);
        }

        if ($resultado && $resultado['status'] === 'error') {
            return redirect()->back()->with('error', $resultado['message']);
        }
        return redirect()->to("/muestro");
    }

    /**
     * Decrementa en una unidad la cantidad de un producto específico en el carrito.
     *
     * Si la cantidad llega a cero, el producto es eliminado automáticamente.
     * Soporta peticiones tradicionales y asíncronas (AJAX) retornando respuestas estructuradas.
     *
     * @param string $rowid Identificador único de la fila del item en el carrito.
     * 
     * @return ResponseInterface|\CodeIgniter\HTTP\RedirectResponse Respuesta JSON (para AJAX) o redirección.
     */
    public function resta($rowid)
    {
        $this->carritoService->decrementar($rowid);
        
        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'success', 
                'cart' => $this->carritoService->getContenido(),
                'totalItems' => \Config\Services::cart()->totalItems()
            ]);
        }

        return redirect()->to("/muestro");
    }
}
