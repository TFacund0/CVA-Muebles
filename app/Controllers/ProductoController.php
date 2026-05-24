<?php

namespace App\Controllers;

use App\Services\CategoriaService;
use App\Services\ProductoService;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Class ProductoController
 *
 * Controlador encargado de la gestión de productos y sus respectivas galerías de imágenes en CVA Muebles.
 * Administra el panel de control de productos, altas, modificaciones, bajas lógicas, reactivaciones,
 * y visualización del detalle interactivo para el cliente final.
 * Delega la lógica de negocio a la capa de servicios (`ProductoService` y `CategoriaService`).
 *
 * @package App\Controllers
 */
class ProductoController extends BaseController 
{
    /**
     * @var ProductoService Servicio encargado de procesar la lógica de negocio de los productos.
     */
    protected $productoService;

    /**
     * @var CategoriaService Servicio encargado de procesar la lógica de negocio de las categorías.
     */
    protected $categoriaService;

    /**
     * Constructor del controlador.
     *
     * Inicializa los helpers obligatorios y los servicios requeridos.
     */
    public function __construct() 
    {
        helper(['form', 'url', 'text']);
        $this->productoService = new ProductoService();
        $this->categoriaService = new CategoriaService();
    }

    /**
     * Muestra el panel administrativo (CRUD) de productos.
     *
     * Permite listar productos activos o inactivos de acuerdo al filtro 'vista'.
     *
     * @return string|ResponseInterface Contenido HTML de la vista CRUD de productos.
     */
    public function index() 
    {
        // Optimización de concurrencia: Libera la sesión tempranamente para evitar cuellos de botella en peticiones AJAX simultáneas.
        session_write_close();
        
        $search = $this->request->getVar('search');
        $category = $this->request->getVar('category');
        $filterMode = env('app.filterMode', 'client');
        $vista = $this->request->getVar('vista') ?? 'NO';
        
        $searchFilter = ($filterMode === 'server') ? $search : null;
        $categoryFilter = ($filterMode === 'server') ? $category : null;

        $resultado = $this->productoService->getProductosConStats($searchFilter, $categoryFilter, $filterMode);
        
        return view('back/products/crud_productos', [
            'productos'  => $resultado['productos'],
            'pager'      => $resultado['pager'],
            'counts'     => $resultado['counts'],
            'categorias' => $this->categoriaService->getCategoriasConStats(),
            'vista'      => $vista,
            'search'     => $search,
            'category'   => $category,
            'filterMode' => $filterMode,
            'title'      => 'Gestión de Productos'
        ]);
    }

    /**
     * Muestra el formulario para dar de alta un nuevo producto.
     *
     * @return string|ResponseInterface Contenido HTML de la vista de alta.
     */
    public function create_alta_producto() 
    {
        return view('back/products/alta_producto', [
            'categorias' => $this->categoriaService->getCategoriasConStats(),
            'title' => 'Alta de Producto'
        ]);
    }

    /**
     * Valida y procesa la creación de un nuevo producto en el catálogo.
     *
     * Valida estrictamente la imagen principal para mitigar riesgos de seguridad de ejecución de código (RCE).
     *
     * @return \CodeIgniter\HTTP\RedirectResponse Redirección a la vista de alta con mensajes de estado.
     */
    public function formValidation() 
    {
        // Validación estricta de la imagen para prevenir subida de archivos maliciosos (RCE)
        $rules = [
            'image' => 'is_image[image]|mime_in[image,image/jpg,image/jpeg,image/png,image/webp]|max_size[image,2048]'
        ];
        
        if ($this->request->getFile('image')->isValid() && !$this->validate($rules)) {
            return redirect()->back()->withInput()->with('fail', 'El archivo subido no es una imagen válida o supera los 2MB.');
        }

        $resultado = $this->productoService->crearProducto([
            'nombre_prod'  => $this->request->getVar('nombre_producto'),
            'categoria_id' => $this->request->getVar('categoria_id'),
            'precio'       => $this->request->getVar('precio'),
            'precio_vta'   => $this->request->getVar('precio-vta'),
            'stock'        => $this->request->getVar('stock'),
            'stock_min'    => $this->request->getVar('stock-min'),
            'descripcion'  => $this->request->getVar('descripcion'),
            'eliminado'    => $this->request->getVar('eliminado') ?? 'NO'
        ], $this->request->getFile('image'));

        if ($resultado['status'] === 'success') {
            return redirect()->to('/alta-producto')->with('success', $resultado['message']);
        } else {
            return redirect()->back()->withInput()->with('fail', $resultado['message']);
        }
    }

    /**
     * Muestra el formulario para la edición de un producto existente.
     *
     * @param int|string $id Identificador único del producto a editar.
     * 
     * @return \CodeIgniter\HTTP\RedirectResponse|string|ResponseInterface Vista de edición o redirección en caso de error.
     */
    public function index_editar_producto($id) 
    {
        $producto = $this->productoService->getProductoConGaleria($id);
        if (!$producto) {
            return redirect()->to('/crud-productos')->with('fail', 'Producto no encontrado');
        }

        return view('back/products/editar_producto', [
            'producto'   => $producto,
            'categorias' => $this->categoriaService->getCategoriasConStats(),
            'title'      => 'Editar Producto'
        ]);
    }

    /**
     * Valida y procesa la modificación de un producto existente.
     *
     * Valida de manera estricta la imagen principal y las imágenes múltiples de la galería
     * para evitar subidas de archivos maliciosos.
     *
     * @param int|string $id Identificador único del producto a modificar.
     * 
     * @return \CodeIgniter\HTTP\RedirectResponse Redirección al CRUD con mensajes del estado de la transacción.
     */
    public function modificar_producto($id) 
    {
        // Validación estricta de la imagen principal
        $rulesMain = [
            'imagen' => 'is_image[imagen]|mime_in[imagen,image/jpg,image/jpeg,image/png,image/webp]|max_size[imagen,2048]'
        ];
        
        if ($this->request->getFile('imagen')->isValid() && !$this->validate($rulesMain)) {
            return redirect()->back()->with('fail', 'La imagen principal no es válida o supera los 2MB.');
        }

        // Actualizar datos básicos e imagen principal
        $resultado = $this->productoService->actualizarProducto($id, [
            'nombre_prod'  => $this->request->getVar('nombre_producto'),
            'categoria_id' => $this->request->getVar('categoria_id'),
            'precio'       => $this->request->getVar('precio'),
            'precio_vta'   => $this->request->getVar('precio-vta'),
            'stock'        => $this->request->getVar('stock'),
            'stock_min'    => $this->request->getVar('stock-min'),
            'descripcion'  => $this->request->getVar('descripcion')
        ], $this->request->getFile('imagen'));

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
     * Realiza la baja lógica (desactivación) de un producto en el sistema.
     *
     * @param int|string $id Identificador único del producto.
     * 
     * @return \CodeIgniter\HTTP\RedirectResponse Redirección al CRUD conservando la vista de filtro.
     */
    public function delete_producto($id) 
    {
        $this->productoService->eliminar($id);
        return redirect()->to('/crud-productos?vista=' . ($this->request->getGet('vista') ?? 'NO'));
    }

    /**
     * Reactiva o activa nuevamente un producto anteriormente dado de baja.
     *
     * @param int|string $id Identificador único del producto.
     * 
     * @return \CodeIgniter\HTTP\RedirectResponse Redirección al CRUD de productos.
     */
    public function activar_producto($id) 
    {
        $this->productoService->reactivar($id);
        return redirect()->to('/crud-productos?vista=' . ($this->request->getGet('vista') ?? 'SI'));
    }

    /**
     * Muestra la pantalla detallada de un producto para los clientes en la tienda pública.
     *
     * Valida que el producto no esté eliminado y que su categoría asociada se encuentre activa.
     *
     * @param int|string $id Identificador único del producto.
     * 
     * @return \CodeIgniter\HTTP\RedirectResponse|string|ResponseInterface Vista detallada o redirección con advertencia.
     */
    public function ver_detalle($id) 
    {
        $producto = $this->productoService->getProductoConGaleria($id);
        if (!$producto || $producto['eliminado'] == 'SI' || $producto['categoria_activa'] == 0) {
            return redirect()->to('/productos')->with('fail', 'Producto no disponible.');
        }

        return view('front/pages/detalle_producto', [
            'producto' => $producto,
            'title'    => $producto['nombre_prod']
        ]);
    }

    /**
     * Sube múltiples imágenes secundarias a la galería de un producto en particular.
     *
     * Valida estrictamente las extensiones y el tamaño máximo del lote de archivos subidos.
     *
     * @param int|string $id Identificador único del producto al cual asociar las fotos.
     * 
     * @return \CodeIgniter\HTTP\RedirectResponse Redirección de vuelta con el mensaje final de estado.
     */
    public function subir_fotos_galeria($id) 
    {
        // Validación estricta de imágenes múltiples
        $rulesGaleria = [
            'fotos_galeria' => 'is_image[fotos_galeria]|mime_in[fotos_galeria,image/jpg,image/jpeg,image/png,image/webp]|max_size[fotos_galeria,2048]'
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
     * Elimina físicamente una imagen secundaria de la galería del producto y su archivo en el disco.
     *
     * @param int|string $id Identificador único de la imagen en la galería.
     * 
     * @return \CodeIgniter\HTTP\RedirectResponse Redirección con estado de borrado.
     */
    public function eliminar_foto_galeria($id) 
    {
        if ($this->productoService->eliminarImagenGaleria($id)) {
            return redirect()->back()->with('success', 'Imagen eliminada.');
        }
        return redirect()->back()->with('fail', 'No se pudo eliminar la imagen.');
    }

    /**
     * Elimina permanentemente de la base de datos a un producto seleccionado.
     *
     * @param int|string $id Identificador único del producto a borrar definitivamente.
     * 
     * @return \CodeIgniter\HTTP\RedirectResponse Redirección al CRUD con estado final.
     */
    public function eliminar_permanente($id) 
    {
        $resultado = $this->productoService->eliminarPermanente($id);
        
        if ($resultado['status'] === 'success') {
            return redirect()->to('/crud-productos?vista=SI')->with('success', $resultado['message']);
        } else {
            return redirect()->to('/crud-productos?vista=SI')->with('fail', $resultado['message']);
        }
    }
}
