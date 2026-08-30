<?php

namespace App\Commands;

use App\Models\ProductoModel;
use App\Models\ProductoImagenModel;
use App\Services\CloudinaryService;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * Agrega la transformación "q_auto,f_auto" a las URLs de Cloudinary que ya
 * están guardadas en la BD (productos y producto_imagenes), para que se
 * sirvan comprimidas y en el mejor formato sin volver a subir el archivo.
 * Es seguro correrlo más de una vez: las URLs que ya tienen la transformación
 * se saltean.
 */
class OptimizarImagenesCloudinary extends BaseCommand
{
    protected $group       = 'CVA';
    protected $name        = 'cva:optimizar-imagenes-cloudinary';
    protected $description = 'Aplica compresión automática (q_auto,f_auto) a las imágenes ya subidas a Cloudinary.';

    public function run(array $params)
    {
        $productoModel = new ProductoModel();
        $imagenModel   = new ProductoImagenModel();
        $cloudinary    = new CloudinaryService();

        $actualizadas = 0;
        $saltadas     = 0;

        $productos = $productoModel->withDeleted()->findAll();
        foreach ($productos as $producto) {
            $imagen = $producto['imagen'] ?? null;
            if (empty($imagen)) {
                continue;
            }

            $optimizada = $cloudinary->optimizarUrl($imagen);
            if ($optimizada === $imagen) {
                $saltadas++;
                continue;
            }

            $productoModel->skipValidation(true)->update($producto['id_producto'], ['imagen' => $optimizada]);
            CLI::write("Producto #{$producto['id_producto']}: optimizada -> {$optimizada}", 'green');
            $actualizadas++;
        }

        $galeria = $imagenModel->findAll();
        foreach ($galeria as $img) {
            $imagen = $img['imagen'] ?? null;
            if (empty($imagen)) {
                continue;
            }

            $optimizada = $cloudinary->optimizarUrl($imagen);
            if ($optimizada === $imagen) {
                $saltadas++;
                continue;
            }

            $imagenModel->update($img['id'], ['imagen' => $optimizada]);
            CLI::write("Galería #{$img['id']}: optimizada -> {$optimizada}", 'green');
            $actualizadas++;
        }

        CLI::write('');
        CLI::write("Optimizadas: {$actualizadas} | Ya optimizadas / no-Cloudinary: {$saltadas}", 'blue');
    }
}
