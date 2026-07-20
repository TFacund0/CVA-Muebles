<?php

namespace App\Controllers;

/**
 * Controlador para gestión de productos refactorizado para usar Capa de Servicios.
 */
class ProductoController extends BaseController {

    protected $productoService;
    protected $categoriaService;

    public function __construct() {
        helper(['form', 'url', 'text']);
        $this->productoService = new \App\Services\ProductoService();
        $this->categoriaService = new \App\Services\CategoriaService();
    }

    /**
     * Muestra el listado de productos para administración.
     *
     * @return string
     */
    public function index() {


        $resultado = $this->productoService->getProductosConStats();
        
        return view('back/products/crud_productos', [
            'productos' => $resultado['productos'],
            'counts'    => $resultado['counts'],
            'categorias' => $this->categoriaService->getCategoriasConStats(),
            'vista'     => $this->request->getVar('vista') ?? 'NO',
            'title'     => 'Gestión de Productos'
        ]);
    }

    /**
     * Muestra el formulario de alta de producto.
     *
     * @return string
     */
    public function create_alta_producto() {


        return view('back/products/alta_producto', [
            'categorias' => $this->categoriaService->getCategoriasConStats(),
            'title' => 'Alta de Producto'
        ]);
    }

    /**
     * Lee del request los campos comunes al alta y la edición de un producto.
     *
     * @return array
     */
    private function productoDataFromRequest(): array {
        return [
            'nombre_prod'  => $this->request->getVar('nombre_producto'),
            'categoria_id' => $this->request->getVar('categoria_id'),
            'precio'       => $this->request->getVar('precio'),
            'precio_vta'   => $this->request->getVar('precio-vta'),
            'stock'        => $this->request->getVar('stock'),
            'stock_min'    => $this->request->getVar('stock-min'),
            'descripcion'  => $this->request->getVar('descripcion')
        ];
    }

    /**
     * Valida y crea un producto delegando al servicio.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function formValidation() {

        // Validación estricta de la imagen para prevenir subida de archivos maliciosos (RCE)
        $rules = [
            'image' => $this->imageValidationRule('image')
        ];

        if ($this->request->getFile('image')->isValid() && !$this->validate($rules)) {
            return redirect()->back()->withInput()->with('fail', 'El archivo subido no es una imagen válida o supera los 2MB.');
        }

        $resultado = $this->productoService->crearProducto(
            $this->productoDataFromRequest(),
            $this->request->getFile('image')
        );

        if ($resultado['status'] === 'success') {
            return redirect()->to('/alta-producto')->with('success', $resultado['message']);
        } else {
            return redirect()->back()->withInput()->with('fail', $resultado['message']);
        }
    }

    /**
     * Muestra el formulario de edición de producto.
     *
     * @param int|string $id Identificador del producto.
     * @return string|\CodeIgniter\HTTP\RedirectResponse
     */
    public function index_editar_producto($id) {


        $producto = $this->productoService->getProductoConGaleria($id);
        if (!$producto) return redirect()->to('/crud-productos')->with('fail', 'Producto no encontrado');

        return view('back/products/editar_producto', [
            'producto'   => $producto,
            'categorias' => $this->categoriaService->getCategoriasConStats(),
            'title'      => 'Editar Producto'
        ]);
    }

    /**
     * Actualiza un producto delegando al servicio.
     *
     * @param int|string $id Identificador del producto.
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function modificar_producto($id) {


        // Validación estricta de la imagen principal
        $rulesMain = [
            'imagen' => $this->imageValidationRule('imagen')
        ];
        
        if ($this->request->getFile('imagen')->isValid() && !$this->validate($rulesMain)) {
            return redirect()->back()->with('fail', 'La imagen principal no es válida o supera los 2MB.');
        }

        // Actualizar datos básicos e imagen principal
        $resultado = $this->productoService->actualizarProducto(
            $id,
            $this->productoDataFromRequest(),
            $this->request->getFile('imagen')
        );

        // Procesar galería adicional si vienen archivos
        $galeria = $this->request->getFileMultiple('fotos_galeria');
        if ($galeria) {
            $this->productoService->subirImagenesGaleria($id, $galeria);
        }

        if ($resultado['status'] === 'success') {
            return redirect()->to('/crud-productos')->with('success', $resultado['message']);
        } else {
            return redirect()->back()->with('fail', $resultado['message']);
        }
    }

    /**
     * Elimina un producto delegando al servicio.
     *
     * @param int|string $id Identificador del producto.
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function delete_producto($id) {

        $this->productoService->eliminar($id);
        return redirect()->to('/crud-productos?vista=' . ($this->request->getGet('vista') ?? 'NO'));
    }

    /**
     * Reactiva un producto delegando al servicio.
     *
     * @param int|string $id Identificador del producto.
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function activar_producto($id) {

        $this->productoService->reactivar($id);
        return redirect()->to('/crud-productos?vista=' . ($this->request->getGet('vista') ?? 'SI'));
    }

    /**
     * Muestra el detalle del producto para el cliente.
     *
     * @param int|string $id Identificador del producto.
     * @return string|\CodeIgniter\HTTP\RedirectResponse
     */
    public function ver_detalle($id) {
        $producto = $this->productoService->getProductoConGaleria($id);
        if (!$producto || $producto['deleted_at'] !== null || $producto['categoria_activa'] == 0) {
            return redirect()->to('/productos')->with('fail', 'Producto no disponible.');
        }

        return view('front/pages/detalle_producto', [
            'producto' => $producto,
            'title'    => $producto['nombre_prod']
        ]);
    }

    /**
     * Sube fotos a la galería de un producto.
     *
     * @param int|string $id Identificador del producto.
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function subir_fotos_galeria($id) {


        // Validación estricta de imágenes múltiples
        $rulesGaleria = [
            'fotos_galeria' => $this->imageValidationRule('fotos_galeria')
        ];

        // Solo validamos si efectivamente subieron algo
        $files = $this->request->getFileMultiple('fotos_galeria');
        if ($files && $files[0]->isValid()) {
            if (!$this->validate($rulesGaleria)) {
                return redirect()->back()->with('fail', 'Una o más imágenes de la galería no son válidas o superan los 2MB.');
            }
        }

        if ($this->productoService->subirImagenesGaleria($id, $files)) {
            return redirect()->back()->with('success', 'Galería actualizada.');
        }
        return redirect()->back()->with('fail', 'No se pudieron subir las imágenes.');
    }

    /**
     * Elimina una foto de la galería.
     *
     * @param int|string $id Identificador de la foto de galería.
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function eliminar_foto_galeria($id) {


        if ($this->productoService->eliminarImagenGaleria($id)) {
            return redirect()->back()->with('success', 'Imagen eliminada.');
        }
        return redirect()->back()->with('fail', 'No se pudo eliminar la imagen.');
    }

    /**
     * Elimina permanentemente un producto delegando al servicio.
     *
     * @param int|string $id Identificador del producto.
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function eliminar_permanente($id) {

        
        $resultado = $this->productoService->eliminarPermanente($id);
        
        if ($resultado['status'] === 'success') {
            return redirect()->to('/crud-productos?vista=SI')->with('success', $resultado['message']);
        } else {
            return redirect()->to('/crud-productos?vista=SI')->with('fail', $resultado['message']);
        }
    }

}
