<?php

namespace App\Controllers;

use App\Services\CategoriaService;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Class CategoriaController
 *
 * Controlador encargado de la gestión de categorías de productos en el sistema CVA Muebles.
 * Facilita las operaciones de visualización, creación, actualización, alternado de estado
 * y eliminación lógica/física de categorías por parte de administradores.
 * Delega la lógica de negocio a la capa de servicios (`CategoriaService`).
 *
 * @package App\Controllers
 */
class CategoriaController extends BaseController 
{
    /**
     * @var CategoriaService Servicio para procesar la lógica de negocio de las categorías.
     */
    protected $categoriaService;

    /**
     * Constructor del controlador.
     *
     * Inicializa los helpers obligatorios y la capa de servicios de categoría.
     */
    public function __construct() 
    {
        helper(['form', 'url']);
        $this->categoriaService = new CategoriaService();
    }

    /**
     * Muestra el panel de administración (CRUD) de categorías.
     *
     * @return string|ResponseInterface Contenido HTML de la vista CRUD de categorías.
     */
    public function index() 
    {
        return view('back/products/crud_categorias', [
            'categorias' => $this->categoriaService->getCategoriasConStats(),
            'title'      => 'Gestión de Categorías'
        ]);
    }

    /**
     * Valida y procesa la creación de una nueva categoría de productos.
     *
     * Requiere que el nombre o descripción sea obligatorio, tenga una longitud mínima de 3 caracteres
     * y sea único dentro de la base de datos para evitar registros duplicados.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse Redirección de vuelta con mensajes flash de éxito o error.
     */
    public function guardar() 
    {
        $rules = ['descripcion' => 'required|min_length[3]|is_unique[categorias.descripcion]'];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Descripción inválida o ya existente.');
        }

        $resultado = $this->categoriaService->crear(['descripcion' => $this->request->getPost('descripcion')]);
        if ($resultado['status'] === 'error') {
            return redirect()->back()->withInput()->with('error', $resultado['message']);
        }
        return redirect()->to('/crud-categorias')->with('success', $resultado['message']);
    }

    /**
     * Valida y procesa la edición o actualización de una categoría existente.
     *
     * Permite cambiar la descripción validando que siga siendo única (excluyendo a la categoría actual).
     *
     * @param int|string $id Identificador único de la categoría a editar.
     * 
     * @return \CodeIgniter\HTTP\RedirectResponse Redirección de vuelta con mensajes de estado.
     */
    public function editar($id) 
    {
        $rules = [
            'descripcion' => "required|min_length[3]|is_unique[categorias.descripcion,id_categoria,{$id}]"
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Esa descripción ya está siendo utilizada por otra categoría.');
        }

        $resultado = $this->categoriaService->actualizar($id, ['descripcion' => $this->request->getPost('descripcion')]);
        if ($resultado['status'] === 'error') {
            return redirect()->back()->withInput()->with('error', $resultado['message']);
        }
        return redirect()->to('/crud-categorias')->with('success', $resultado['message']);
    }

    /**
     * Alterna de forma lógica el estado activo/inactivo de una categoría seleccionada.
     *
     * @param int|string $id Identificador único de la categoría a modificar.
     * 
     * @return \CodeIgniter\HTTP\RedirectResponse Redirección al CRUD de categorías.
     */
    public function toggle($id) 
    {
        $this->categoriaService->toggleEstado($id);
        return redirect()->to('/crud-categorias');
    }

    /**
     * Procesa la eliminación física de una categoría de la base de datos de manera segura.
     *
     * El servicio verifica previamente que no existan dependencias o productos asociados a esta
     * categoría antes de proceder a borrarla para salvaguardar la integridad de la base de datos.
     *
     * @param int|string $id Identificador único de la categoría a eliminar.
     * 
     * @return \CodeIgniter\HTTP\RedirectResponse Redirección con estado final de la operación.
     */
    public function eliminar($id) 
    {
        $resultado = $this->categoriaService->eliminar($id);
        if ($resultado['status'] === 'error') {
            return redirect()->to('/crud-categorias')->with('error', $resultado['message']);
        }
        
        return redirect()->to('/crud-categorias')->with('success', $resultado['message']);
    }
}
