<?php

namespace App\Exceptions;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Debug\BaseExceptionHandler;
use CodeIgniter\Debug\ExceptionHandlerInterface;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\Exceptions\HTTPException;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Paths;
use Throwable;

/**
 * Class GlobalExceptionHandler
 *
 * Manejador global de excepciones personalizado para el sistema CVA Muebles.
 * Se encarga de centralizar la captura de errores no controlados, evaluar si la
 * solicitud fue realizada mediante AJAX o solicita JSON, y retornar la respuesta
 * en el formato apropiado (JSON estructurado para API/AJAX, o una vista HTML elegante).
 *
 * En entornos de desarrollo, expone detalles técnicos para facilitar el debug.
 * En producción, oculta información sensible y muestra pantallas amigables alineadas con la marca.
 *
 * @package App\Exceptions
 */
class GlobalExceptionHandler extends BaseExceptionHandler implements ExceptionHandlerInterface
{
    use ResponseTrait;

    /**
     * @var RequestInterface|null Petición HTTP entrante capturada.
     */
    private ?RequestInterface $request = null;

    /**
     * @var ResponseInterface|null Respuesta HTTP saliente a configurar.
     */
    private ?ResponseInterface $response = null;

    /**
     * Procesa la excepción capturada y despacha la respuesta correspondiente.
     *
     * Detecta si la petición es de tipo AJAX o solicita formato JSON para responder
     * de manera estructurada. En caso contrario, delega la renderización a las vistas
     * HTML de error (production.php en producción o error_exception.php en desarrollo).
     *
     * @param Throwable         $exception  La excepción que fue lanzada.
     * @param RequestInterface  $request    Petición HTTP actual.
     * @param ResponseInterface $response   Respuesta HTTP para configurar.
     * @param int               $statusCode Código de estado HTTP de error asociado.
     * @param int               $exitCode   Código de salida en caso de terminar la ejecución.
     * 
     * @return void
     */
    public function handle(
        Throwable $exception,
        RequestInterface $request,
        ResponseInterface $response,
        int $statusCode,
        int $exitCode
    ): void {
        $this->request  = $request;
        $this->response = $response;

        if ($request instanceof IncomingRequest) {
            try {
                $response->setStatusCode($statusCode);
            } catch (HTTPException $e) {
                $statusCode = 500;
                $response->setStatusCode($statusCode);
            }

            if (! headers_sent()) {
                header(
                    sprintf(
                        'HTTP/%s %s %s',
                        $request->getProtocolVersion(),
                        $response->getStatusCode(),
                        $response->getReasonPhrase()
                    ),
                    true,
                    $statusCode
                );
            }

            // Detección de petición AJAX o no HTML
            $isAjax = $request->isAJAX();
            $isJsonRequested = str_contains($request->getHeaderLine('accept'), 'application/json');

            if ($isAjax || $isJsonRequested || !str_contains($request->getHeaderLine('accept'), 'text/html')) {
                
                // Formateo de respuesta JSON estándar
                $data = [
                    'status' => 'error',
                    'message' => 'Ocurrió un error inesperado. Por favor, intenta de nuevo.'
                ];

                if ($this->isDisplayErrorsEnabled()) {
                    // En desarrollo, enviamos la traza completa
                    $data = $this->collectVars($exception, $statusCode);
                }

                $this->respond($data, $statusCode)->send();

                if (ENVIRONMENT !== 'testing') {
                    exit($exitCode);
                }
                return;
            }
        }

        // Peticiones web normales (HTML)
        $addPath = ($request instanceof IncomingRequest ? 'html' : 'cli') . DIRECTORY_SEPARATOR;
        $path    = $this->viewPath . $addPath;
        $altPath = rtrim((new Paths())->viewDirectory, '\\/ ') . DIRECTORY_SEPARATOR . 'errors' . DIRECTORY_SEPARATOR . $addPath;

        $view    = $this->determineView($exception, $path, $statusCode);
        $altView = $this->determineView($exception, $altPath, $statusCode);

        $viewFile = null;
        if (is_file($path . $view)) {
            $viewFile = $path . $view;
        } elseif (is_file($altPath . $altView)) {
            $viewFile = $altPath . $altView;
        }

        $this->render($exception, $statusCode, $viewFile);

        if (ENVIRONMENT !== 'testing') {
            exit($exitCode);
        }
    }

    /**
     * Determina qué archivo de vista se debe renderizar según el entorno y estado HTTP.
     *
     * Permite seleccionar 'production.php' para ocultar trazas sensibles en producción
     * o 'error_exception.php' para mostrar el depurador detallado en desarrollo.
     * Soporta vistas específicas para códigos de error (ej: error_404.php).
     *
     * @param Throwable $exception    La excepción capturada.
     * @param string    $templatePath Directorio donde se almacenan las plantillas de vista.
     * @param int       $statusCode   Código de estado HTTP de la respuesta.
     * 
     * @return string Nombre del archivo de la vista a cargar.
     */
    protected function determineView(
        Throwable $exception,
        string $templatePath,
        int $statusCode = 500
    ): string {
        $view = 'production.php';

        if ($this->isDisplayErrorsEnabled()) {
            $view = 'error_exception.php';
        }

        if ($exception instanceof PageNotFoundException) {
            return 'error_404.php';
        }

        $templatePath = rtrim($templatePath, '\\/ ') . DIRECTORY_SEPARATOR;

        if (is_file($templatePath . 'error_' . $statusCode . '.php')) {
            return 'error_' . $statusCode . '.php';
        }

        return $view;
    }

    /**
     * Verifica si la directiva 'display_errors' de PHP está habilitada.
     *
     * Utilizado para saber si el servidor está configurado para mostrar los errores
     * de manera directa (típico en desarrollo local).
     *
     * @return bool True si 'display_errors' está activo; false en caso contrario.
     */
    private function isDisplayErrorsEnabled(): bool
    {
        return in_array(
            strtolower(ini_get('display_errors')),
            ['1', 'true', 'on', 'yes'],
            true
        );
    }
}
