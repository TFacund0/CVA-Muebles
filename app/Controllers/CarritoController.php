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
     * Elimina un item o vacía el carrito.
     */
    public function remove($rowid)
    {
        $this->carritoService->eliminar($rowid);
        return redirect()->back();
    }

    /**
     * Vacía completamente el carrito.
     */
    public function borrar_carrito()
    {
        $this->carritoService->vaciar();
        return redirect()->to(base_url("muestro"));
    }

    /**
     * Muestra el contenido del carrito.
     */
    public function muestra()
    {
        return view('front/pages/carrito', [
            'cart'  => $this->carritoService->getContenido(),
            'title' => 'Carrito de Compras'
        ]);
    }

    /**
     * Incrementa la cantidad.
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
     * Decrementa la cantidad.
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
