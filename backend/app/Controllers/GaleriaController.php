<?php

namespace App\Controllers;

use App\Services\GaleriaService;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Class GaleriaController
 *
 * Controlador encargado de gestionar la sección "CVA en tu Hogar", permitiendo que los
 * clientes compartan fotos reales de los muebles en sus hogares. Proporciona vistas para la visualización pública,
 * métodos de subida y validación estricta de fotos de clientes, y un panel de administración para moderación de contenido.
 * Delega la lógica de negocio a la capa de servicios (`GaleriaService`).
 *
 * @package App\Controllers
 */
class GaleriaController extends BaseController 
{
    /**
     * @var GaleriaService Servicio encargado de procesar la lógica de negocio de la galería de clientes.
     */
    protected $galeriaService;

    /**
     * Constructor del controlador.
     *
     * Inicializa el servicio de gestión de galería.
     */
    public function __construct() 
    {
        $this->galeriaService = new GaleriaService();
    }

    /**
     * Muestra la galería de fotos públicas de los clientes.
     *
     * Adicionalmente, si el usuario logueado es administrador, calcula la cantidad de
     * fotos pendientes de moderación para mostrarlas como alerta.
     *
     * @return string|ResponseInterface Contenido HTML de la vista de galería pública.
     */
    public function index() 
    {
        $pendientesCount = 0;
        if (session()->get('logged_in') && session()->get('perfil_id') == 1) {
            $pendientesCount = $this->galeriaService->getPendientesCount();
        }

        return view('front/pages/galeria_clientes', [
            'fotos' => $this->galeriaService->getAprobadas(),
            'title' => 'CVA en tu Hogar - Galería de Clientes',
            'pendientesCount' => $pendientesCount
        ]);
    }

    /**
     * Procesa la subida y postulación de una nueva foto para la galería de clientes.
     *
     * Aplica validaciones estrictas al archivo subido para evitar riesgos de ejecución remota
     * de código (RCE): restringe tamaño (máx 2MB) y formatos gráficos válidos (JPG, JPEG, PNG, WEBP).
     * Las fotos postuladas se registran inicialmente en estado "Pendiente" a la espera de ser moderadas.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse Redirección a la vista anterior con mensajes de estado.
     */
    public function subir() 
    {
        $rules = [
            'imagen' => [
                'rules'  => 'uploaded[imagen]|is_image[imagen]|mime_in[imagen,image/jpg,image/jpeg,image/png,image/webp]|max_size[imagen,2048]',
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
     * Muestra el panel administrativo para la moderación, aprobación y eliminación de fotos postuladas.
     *
     * Acceso reservado a administradores.
     *
     * @return string|ResponseInterface Contenido HTML del panel de administración de galería.
     */
    public function admin_index() 
    {
        return view('back/gallery/gestion_galeria', [
            'fotos' => $this->galeriaService->getAllConUsuarios(),
            'title' => 'Moderación de Galería'
        ]);
    }

    /**
     * Aprueba una foto postulada por un cliente haciéndola visible en la galería pública.
     *
     * @param int|string $id Identificador único de la foto en la galería.
     * 
     * @return \CodeIgniter\HTTP\RedirectResponse Redirección de vuelta con mensaje flash de éxito.
     */
    public function aprobar($id) 
    {
        $this->galeriaService->aprobar($id);
        return redirect()->back()->with('success', 'Foto aprobada y publicada.');
    }

    /**
     * Elimina físicamente una foto y su correspondiente archivo del almacenamiento del servidor.
     *
     * @param int|string $id Identificador único de la foto a borrar.
     * 
     * @return \CodeIgniter\HTTP\RedirectResponse Redirección de vuelta con confirmación de borrado.
     */
    public function eliminar($id) 
    {
        $this->galeriaService->eliminar($id);
        return redirect()->back()->with('success', 'La foto ha sido eliminada.');
    }
}
