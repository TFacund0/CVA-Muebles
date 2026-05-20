<?php

namespace App\Controllers;

/**
 * Controlador para la gestión de categorías por parte del administrador.
 */
class CategoriaController extends BaseController {

    protected $categoriaService;

    public function __construct() {
        helper(['form', 'url']);
        $this->categoriaService = new \App\Services\CategoriaService();
    }

    /**
     * Listado de categorías.
     */
    public function index() {


        return view('back/products/crud_categorias', [
            'categorias' => $this->categoriaService->getCategoriasConStats(),
            'title'      => 'Gestión de Categorías'
        ]);
    }

    /**
     * Procesa la creación de una categoría.
     */
    public function guardar() {


        $rules = ['descripcion' => 'required|min_length[3]|is_unique[categorias.descripcion]'];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Descripción inválida o ya existente.');
        }

        $this->categoriaService->crear(['descripcion' => $this->request->getPost('descripcion')]);
        return redirect()->to('/crud-categorias')->with('success', 'Categoría creada con éxito.');
    }

    /**
     * Procesa la edición de una categoría.
     */
    public function editar($id) {


        $rules = [
            'descripcion' => "required|min_length[3]|is_unique[categorias.descripcion,id_categoria,{$id}]"
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Esa descripción ya está siendo utilizada por otra categoría.');
        }

        $this->categoriaService->actualizar($id, ['descripcion' => $this->request->getPost('descripcion')]);
        return redirect()->to('/crud-categorias')->with('success', 'Categoría actualizada.');
    }

    /**
     * Alterna el estado de una categoría.
     */
    public function toggle($id) {

        $this->categoriaService->toggleEstado($id);
        return redirect()->to('/crud-categorias');
    }

    /**
     * Elimina una categoría si es seguro.
     */
    public function eliminar($id) {

        
        $resultado = $this->categoriaService->eliminar($id);
        if ($resultado['status'] === 'error') {
            return redirect()->to('/crud-categorias')->with('error', $resultado['message']);
        }
        
        return redirect()->to('/crud-categorias')->with('success', $resultado['message']);
    }
}
