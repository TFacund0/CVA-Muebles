<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Cloudinary\Api\Upload\UploadApi;
use Config\Cloudinary as CloudinaryConfig;

/**
 * Servicio para subir y eliminar imágenes en Cloudinary.
 */
class CloudinaryService
{
    protected UploadApi $uploadApi;
    protected string $uploadFolder;

    public function __construct(?CloudinaryConfig $config = null)
    {
        $config ??= config(CloudinaryConfig::class);

        $cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => $config->cloudName,
                'api_key'    => $config->apiKey,
                'api_secret' => $config->apiSecret,
            ],
        ]);

        $this->uploadApi    = $cloudinary->uploadApi();
        $this->uploadFolder = $config->uploadFolder;
    }

    /**
     * Sube un archivo local (path absoluto) a Cloudinary.
     *
     * @param string $path Ruta absoluta del archivo a subir
     * @return array{secure_url: string, public_id: string} Datos de la imagen subida
     */
    public function subir(string $path): array
    {
        $resultado = $this->uploadApi->upload($path, [
            'folder' => $this->uploadFolder,
        ]);

        return [
            'secure_url' => $this->optimizarUrl($resultado['secure_url']),
            'public_id'  => $resultado['public_id'],
        ];
    }

    /**
     * Inserta la transformación "q_auto,f_auto" en una secure_url de Cloudinary,
     * para que sirva la imagen comprimida y en el mejor formato (WebP/AVIF)
     * según el navegador, sin generar copias adicionales por adelantado.
     *
     * @param string $url secure_url original devuelta por Cloudinary
     * @return string URL con la transformación aplicada (o la misma URL si ya la tenía)
     */
    public function optimizarUrl(string $url): string
    {
        if (!str_contains($url, 'res.cloudinary.com') || str_contains($url, 'q_auto')) {
            return $url;
        }

        return str_replace('/upload/', '/upload/q_auto,f_auto/', $url);
    }

    /**
     * Elimina una imagen de Cloudinary a partir de su URL guardada en la BD.
     * No hace nada si la URL no pertenece a Cloudinary (p. ej. imágenes locales viejas).
     *
     * @param string|null $url URL completa guardada en la base de datos
     */
    public function eliminarPorUrl(?string $url): void
    {
        $publicId = $this->publicIdDesdeUrl($url);

        if ($publicId !== null) {
            $this->uploadApi->destroy($publicId);
        }
    }

    /**
     * Extrae el public_id de Cloudinary a partir de una secure_url estándar, ej.:
     * https://res.cloudinary.com/{cloud}/image/upload/v1234567890/cva_muebles/archivo.jpg
     * → cva_muebles/archivo
     *
     * @param string|null $url
     * @return string|null null si la URL no es de Cloudinary
     */
    protected function publicIdDesdeUrl(?string $url): ?string
    {
        if (empty($url) || !str_contains($url, 'res.cloudinary.com')) {
            return null;
        }

        $partes = explode('/upload/', $url, 2);
        if (count($partes) !== 2) {
            return null;
        }

        // $partes[1] puede traer un segmento de transformación antes de la versión,
        // ej. "q_auto,f_auto/v1234567890/cva_muebles/archivo.jpg"
        $segmentos = explode('/', $partes[1]);
        while (count($segmentos) > 1 && !preg_match('#^v\d+$#', $segmentos[0])) {
            array_shift($segmentos);
        }
        // Descartar el segmento de versión ("v1234567890")
        if (preg_match('#^v\d+$#', $segmentos[0] ?? '')) {
            array_shift($segmentos);
        }

        $resto = implode('/', $segmentos);

        // Quitar la extensión del archivo
        return preg_replace('#\.[a-zA-Z0-9]+$#', '', $resto);
    }
}
