<?php

namespace App\Controllers;

/**
 * Controlador para el carrito refactorizado para usar Capa de Servicios.
 */
class CarritoController extends BaseController
{
    protected $carritoService;
    protected $productoService;

    public function __construct()
    {
        helper(['form','url','cart']);
        $this->carritoService = new \App\Services\CarritoService();
        $this->productoService = new \App\Services\ProductoService();
    }

    /**
     * Agrega un producto al carrito.
     *
     * Responde JSON si la petición es AJAX; en caso contrario redirige
     * con flashdata (compatibilidad con envíos de formulario tradicionales).
     *
     * @return \CodeIgniter\HTTP\RedirectResponse|\CodeIgniter\HTTP\ResponseInterface
     */
    public function add()
    {
        $resultado = $this->carritoService->agregar($this->request->getPost());

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status'     => $resultado['status'],
                'message'    => $resultado['message'],
                'totalItems' => \Config\Services::cart()->totalItems()
            ]);
        }

        if ($resultado['status'] === 'error') {
            return redirect()->back()->with('error', $resultado['message']);
        }
        return redirect()->back()->with('success', $resultado['message']);
    }

    /**
     * Elimina un item del carrito.
     *
     * @param string $rowid Identificador de fila del carrito (CI4 cart)
     * @return \CodeIgniter\HTTP\RedirectResponse|\CodeIgniter\HTTP\ResponseInterface
     */
    public function remove($rowid)
    {
        $this->carritoService->eliminar($rowid);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status'     => 'success',
                'totalItems' => \Config\Services::cart()->totalItems(),
                'empty'      => \Config\Services::cart()->totalItems() === 0
            ]);
        }

        return redirect()->back();
    }

    /**
     * Vacía completamente el carrito.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function borrar_carrito()
    {
        $this->carritoService->vaciar();
        return redirect()->to(base_url("muestro"));
    }

    /**
     * Muestra el contenido del carrito.
     *
     * @return string
     */
    public function muestra()
    {
        return view('front/pages/carrito', [
            'cart'  => $this->carritoService->getContenido(),
            'title' => 'Carrito de Compras'
        ]);
    }

    /**
     * Incrementa la cantidad de un item del carrito.
     *
     * @param string $rowid Identificador de fila del carrito (CI4 cart)
     * @return \CodeIgniter\HTTP\RedirectResponse|\CodeIgniter\HTTP\ResponseInterface
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
     * Decrementa la cantidad de un item del carrito.
     *
     * @param string $rowid Identificador de fila del carrito (CI4 cart)
     * @return \CodeIgniter\HTTP\RedirectResponse|\CodeIgniter\HTTP\ResponseInterface
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
