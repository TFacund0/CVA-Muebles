<?php

namespace App\Commands;

use App\Models\ProductoModel;
use App\Models\ProductoImagenModel;
use App\Services\CloudinaryService;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * Sube a Cloudinary las imágenes que hoy están guardadas localmente en
 * public/assets/uploads y actualiza la BD (productos y producto_imagenes)
 * con la nueva URL. Es seguro correrlo más de una vez: las filas que ya
 * tienen una URL de Cloudinary se saltean.
 */
class MigrarImagenesCloudinary extends BaseCommand
{
    protected $group       = 'CVA';
    protected $name        = 'cva:migrar-imagenes-cloudinary';
    protected $description = 'Sube las imágenes locales de productos a Cloudinary y actualiza sus URLs en la BD.';

    public function run(array $params)
    {
        $productoModel = new ProductoModel();
        $imagenModel   = new ProductoImagenModel();
        $cloudinary    = new CloudinaryService();

        $migradas = 0;
        $saltadas = 0;
        $errores  = 0;

        // Imagen principal de productos (incluye archivados/eliminados)
        $productos = $productoModel->withDeleted()->findAll();
        foreach ($productos as $producto) {
            $imagen = $producto['imagen'] ?? null;

            if (empty($imagen)) {
                continue;
            }

            if (str_starts_with($imagen, 'http')) {
                $saltadas++;
                continue;
            }

            $path = FCPATH . 'assets/uploads/' . $imagen;

            if (!file_exists($path)) {
                CLI::write("Producto #{$producto['id_producto']}: archivo no encontrado ({$imagen})", 'yellow');
                $errores++;
                continue;
            }

            try {
                $subida = $cloudinary->subir($path);
                $productoModel->skipValidation(true)->update($producto['id_producto'], ['imagen' => $subida['secure_url']]);
                CLI::write("Producto #{$producto['id_producto']}: migrado -> {$subida['secure_url']}", 'green');
                $migradas++;
            } catch (\Exception $e) {
                CLI::write("Producto #{$producto['id_producto']}: error subiendo ({$e->getMessage()})", 'red');
                $errores++;
            }
        }

        // Galería de imágenes secundarias
        $galeria = $imagenModel->findAll();
        foreach ($galeria as $img) {
            $imagen = $img['imagen'] ?? null;

            if (empty($imagen)) {
                continue;
            }

            if (str_starts_with($imagen, 'http')) {
                $saltadas++;
                continue;
            }

            $path = FCPATH . 'assets/uploads/' . $imagen;

            if (!file_exists($path)) {
                CLI::write("Galería #{$img['id']}: archivo no encontrado ({$imagen})", 'yellow');
                $errores++;
                continue;
            }

            try {
                $subida = $cloudinary->subir($path);
                $imagenModel->update($img['id'], ['imagen' => $subida['secure_url']]);
                CLI::write("Galería #{$img['id']}: migrada -> {$subida['secure_url']}", 'green');
                $migradas++;
            } catch (\Exception $e) {
                CLI::write("Galería #{$img['id']}: error subiendo ({$e->getMessage()})", 'red');
                $errores++;
            }
        }

        CLI::write('');
        CLI::write("Migradas: {$migradas} | Ya en Cloudinary: {$saltadas} | Errores: {$errores}", 'blue');
    }
}
