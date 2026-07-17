<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class ProcessImage extends BaseCommand
{
    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group = 'App';

    /**
     * The Command's Name
     *
     * @var string
     */
    protected $name = 'image:process';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'Procesa asincrónicamente una imagen (redimensiona y convierte a webp).';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'image:process [source] [destination] [max_width] [max_height] [quality]';

    /**
     * The Command's Arguments
     *
     * @var array
     */
    protected $arguments = [
        'source'      => 'Ruta absoluta del archivo de origen temporal.',
        'destination' => 'Ruta absoluta del archivo destino final (.webp).',
        'max_width'   => 'Ancho máximo permitido.',
        'max_height'  => 'Alto máximo permitido.',
        'quality'     => 'Calidad de compresión (1-100).'
    ];

    /**
     * Actually execute a command.
     *
     * @param array $params
     */
    public function run(array $params)
    {
        if (count($params) < 2) {
            CLI::error('Faltan argumentos requeridos: source y destination.');
            return;
        }

        $source      = $params[0];
        $destination = $params[1];
        $maxWidth    = isset($params[2]) ? (int) $params[2] : 800;
        $maxHeight   = isset($params[3]) ? (int) $params[3] : 800;
        $quality     = isset($params[4]) ? (int) $params[4] : 80;

        if (!file_exists($source)) {
            CLI::error("El archivo original no existe: {$source}");
            return;
        }

        try {
            $imageService = \Config\Services::image();
            $imageService->withFile($source);

            $width = $imageService->getWidth();
            $height = $imageService->getHeight();

            // Solo redimensionar si supera el tamaño máximo
            if ($width > $maxWidth || $height > $maxHeight) {
                $imageService->resize($maxWidth, $maxHeight, true, 'auto');
            }

            // Guardar en destino (webp usualmente)
            $imageService->save($destination, $quality);

            // Eliminar el archivo temporal si es distinto al final
            if ($source !== $destination && file_exists($source)) {
                @unlink($source);
            }

            CLI::write('Imagen procesada correctamente.', 'green');
        } catch (\Exception $e) {
            // En caso de error crítico, eliminar archivo temporal para no llenar el disco
            if (file_exists($source)) {
                @unlink($source);
            }
            log_message('error', 'Error en background worker de imagen: ' . $e->getMessage());
            CLI::error('Error procesando imagen: ' . $e->getMessage());
        }
    }
}
