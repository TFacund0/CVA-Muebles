<?php

namespace App\Controllers;

/**
 * Controlador para la galería refactorizado para usar Capa de Servicios.
 */
class GaleriaController extends BaseController {

    protected $galeriaService;

    public function __construct() {
        $this->galeriaService = new \App\Services\GaleriaService();
    }

    /**
     * Muestra la galería pública de fotos de clientes aprobadas.
     *
     * @return string
     */
    public function index() {
        $pendientesCount = 0;
        if ($this->isAdmin()) {
            $pendientesCount = $this->galeriaService->getPendientesCount();
        }

        return view('front/pages/galeria_clientes', [
            'fotos' => $this->galeriaService->getAprobadas(),
            'title' => 'CVA en tu Hogar - Galería de Clientes',
            'pendientesCount' => $pendientesCount
        ]);
    }

    /**
     * Procesa la subida de una foto de cliente a la galería.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function subir() {
        $rules = [
            'imagen' => [
                'rules'  => 'uploaded[imagen]|' . $this->imageValidationRule('imagen'),
                'label'  => 'Foto de cliente',
                'errors' => [
                    'mime_in' => 'Solo se permiten imágenes en formato JPG, JPEG, PNG o WEBP.',
                    'max_size' => 'La imagen es demasiado pesada (máximo 2MB).',
                    'is_image' => 'El archivo seleccionado no es una imagen válida.'
                ]
            ],
            'comentario' => 'permit_empty|max_length[255]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', $this->validator->getError('imagen') ?? 'Error en la validación.');
        }

        $img = $this->request->getFile('imagen');
        $resultado = $this->galeriaService->subir(
            session()->get('id_usuario'),
            $img,
            $this->request->getPost('comentario')
        );

        if ($resultado) {
            return redirect()->back()->with('success', '¡Gracias! Tu foto ha sido enviada y será publicada tras ser revisada.');
        }

        return redirect()->back()->with('error', 'Hubo un problema al subir la imagen.');
    }

    /**
     * Muestra el listado de fotos para moderación por parte del administrador.
     *
     * @return string
     */
    public function admin_index() {


        return view('back/gallery/gestion_galeria', [
            'fotos' => $this->galeriaService->getAllConUsuarios(),
            'title' => 'Moderación de Galería'
        ]);
    }

    /**
     * Aprueba una foto pendiente y la publica en la galería.
     *
     * @param int|string $id Identificador de la foto.
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function aprobar($id) {

        $this->galeriaService->aprobar($id);
        return redirect()->back()->with('success', 'Foto aprobada y publicada.');
    }

    /**
     * Elimina una foto de la galería.
     *
     * @param int|string $id Identificador de la foto.
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function eliminar($id) {

        $this->galeriaService->eliminar($id);
        return redirect()->back()->with('success', 'La foto ha sido eliminada.');
    }

}
