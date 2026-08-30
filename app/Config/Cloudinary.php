<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Cloudinary extends BaseConfig
{
    /**
     * Cloud Name de la cuenta de Cloudinary.
     */
    public string $cloudName = '';

    /**
     * API Key de la cuenta de Cloudinary.
     */
    public string $apiKey = '';

    /**
     * API Secret de la cuenta de Cloudinary.
     */
    public string $apiSecret = '';

    /**
     * Carpeta dentro de Cloudinary donde se suben las imágenes del proyecto.
     */
    public string $uploadFolder = 'cva_muebles';
}
