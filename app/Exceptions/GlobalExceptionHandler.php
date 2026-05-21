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

class GlobalExceptionHandler extends BaseExceptionHandler implements ExceptionHandlerInterface
{
    use ResponseTrait;

    private ?RequestInterface $request = null;
    private ?ResponseInterface $response = null;

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

    private function isDisplayErrorsEnabled(): bool
    {
        return in_array(
            strtolower(ini_get('display_errors')),
            ['1', 'true', 'on', 'yes'],
            true
        );
    }
}
