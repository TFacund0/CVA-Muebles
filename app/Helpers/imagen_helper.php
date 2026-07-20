<?php

if (!function_exists('imagen_url')) {
    /**
     * Resuelve la URL de una imagen guardada en la BDD.
     * Soporta tanto URLs absolutas (ej. Cloudinary) como nombres de
     * archivo local subidos a assets/uploads.
     */
    function imagen_url(?string $imagen, string $subcarpeta = ''): string
    {
        if (empty($imagen)) {
            return '';
        }

        if (preg_match('#^https?://#i', $imagen)) {
            return $imagen;
        }

        $subcarpeta = trim($subcarpeta, '/');
        $ruta = 'assets/uploads/' . ($subcarpeta !== '' ? $subcarpeta . '/' : '') . $imagen;

        return base_url($ruta);
    }
}
