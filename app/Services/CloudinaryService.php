<?php

namespace App\Services;

use Exception;

/**
 * Class CloudinaryService
 *
 * Servicio encargado de abstraer la conexión y operaciones con la API REST pura de Cloudinary.
 * Permite subir imágenes a la nube liberando el almacenamiento local del servidor.
 * Se ha diseñado usando cURL nativo (REST API pura) para evitar depender de paquetes externos (Composer),
 * optimizando al máximo el peso del proyecto.
 *
 * @package App\Services
 */
class CloudinaryService
{
    protected $cloudName;
    protected $apiKey;
    protected $apiSecret;

    public function __construct()
    {
        $this->cloudName = env('CLOUDINARY_CLOUD_NAME');
        $this->apiKey = env('CLOUDINARY_API_KEY');
        $this->apiSecret = env('CLOUDINARY_API_SECRET');

        if (empty($this->cloudName) || empty($this->apiKey) || empty($this->apiSecret)) {
            log_message('error', '[CloudinaryService] Faltan credenciales de Cloudinary en el archivo .env');
        }
    }

    /**
     * Sube un archivo físico a la nube de Cloudinary vía API REST.
     *
     * @param string $filePath Ruta física temporal del archivo en el servidor (ej. $_FILES['...']['tmp_name']).
     * @param string $folder   Carpeta de destino dentro del dashboard de Cloudinary.
     * 
     * @return array Estructura con status y la URL pública ('url') o el error ('message').
     */
    public function subirImagen($filePath, $folder = 'cva_muebles')
    {
        try {
            $timestamp = time();

            // Parámetros a firmar (deben estar en orden alfabético)
            $paramsToSign = [
                'folder' => $folder,
                'timestamp' => $timestamp
            ];

            // Crear la firma (Signature) requerida por Cloudinary
            $signatureString = http_build_query($paramsToSign) . $this->apiSecret;
            $signatureString = urldecode($signatureString); // Cloudinary pide string plano
            $signature = sha1($signatureString);

            // Preparar el archivo (cURL requiere CURLFile)
            $cfile = new \CURLFile($filePath);

            // Parámetros finales para la petición POST
            $postFields = [
                'file' => $cfile,
                'folder' => $folder,
                'api_key' => $this->apiKey,
                'timestamp' => $timestamp,
                'signature' => $signature
            ];

            $url = "https://api.cloudinary.com/v1_1/{$this->cloudName}/image/upload";

            // Ejecutar la petición cURL
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Para evitar problemas en XAMPP local

            $response = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            $result = json_decode($response, true);

            if ($httpcode >= 200 && $httpcode < 300 && isset($result['secure_url'])) {
                return [
                    'status'    => 'success',
                    'url'       => $result['secure_url'],
                    'public_id' => $result['public_id'] ?? null
                ];
            } else {
                log_message('error', '[CloudinaryService::subirImagen] Error API: ' . $response);
                return [
                    'status'  => 'error',
                    'message' => 'El servidor en la nube rechazó la imagen. Detalles técnicos en los logs.'
                ];
            }
        } catch (Exception $e) {
            log_message('error', '[CloudinaryService::subirImagen] Excepción: ' . $e->getMessage());
            return [
                'status'  => 'error',
                'message' => 'Ocurrió un error interno de red al subir la imagen a la nube.'
            ];
        }
    }

    /**
     * Elimina una imagen de la nube de Cloudinary vía API REST.
     *
     * @param string $publicId El identificador público de la imagen en Cloudinary.
     * 
     * @return array Estructura con status y message.
     */
    public function eliminarImagen($publicId)
    {
        try {
            $timestamp = time();

            // Parámetros a firmar (deben estar en orden alfabético)
            $paramsToSign = [
                'public_id' => $publicId,
                'timestamp' => $timestamp
            ];

            // Crear la firma
            $signatureString = http_build_query($paramsToSign) . $this->apiSecret;
            $signatureString = urldecode($signatureString);
            $signature = sha1($signatureString);

            $postFields = [
                'public_id' => $publicId,
                'api_key'   => $this->apiKey,
                'timestamp' => $timestamp,
                'signature' => $signature
            ];

            $url = "https://api.cloudinary.com/v1_1/{$this->cloudName}/image/destroy";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            $result = json_decode($response, true);

            if ($httpcode >= 200 && $httpcode < 300 && isset($result['result']) && $result['result'] === 'ok') {
                return ['status' => 'success'];
            } else {
                log_message('error', '[CloudinaryService::eliminarImagen] Error API: ' . $response);
                return ['status' => 'error', 'message' => 'No se pudo eliminar la imagen de la nube.'];
            }
        } catch (Exception $e) {
            log_message('error', '[CloudinaryService::eliminarImagen] Excepción: ' . $e->getMessage());
            return ['status' => 'error', 'message' => 'Excepción de red al eliminar imagen.'];
        }
    }

    /**
     * Extrae el `public_id` de una URL completa de Cloudinary.
     */
    public function extractPublicIdFromUrl($url)
    {
        if (strpos($url, 'res.cloudinary.com') === false) {
            return null;
        }

        $parts = parse_url($url);
        $path = $parts['path'];
        $uploadPos = strpos($path, '/upload/');
        if ($uploadPos !== false) {
            $path = substr($path, $uploadPos + 8);
            $path = preg_replace('/^v\d+\//', '', $path);
            $pathInfo = pathinfo($path);
            return ltrim($pathInfo['dirname'] . '/' . $pathInfo['filename'], '/');
        }
        return null;
    }
}
