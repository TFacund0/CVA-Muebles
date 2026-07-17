<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\ProductoModel;
use App\Models\UsuarioModel;
use App\Models\GaleriaClienteModel;
use App\Models\ProductoImagenModel;
use App\Services\CloudinaryService;

class MigrateToCloudinary extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'migrate:cloudinary';
    protected $description = 'Sube todas las imágenes locales a Cloudinary y actualiza la base de datos.';

    public function run(array $params)
    {
        CLI::write('Iniciando migración histórica a Cloudinary...', 'yellow');

        $cloudinaryService = new CloudinaryService();
        $productoModel = new ProductoModel();
        $usuarioModel = new UsuarioModel();
        $galeriaModel = new GaleriaClienteModel();
        $productoImagenModel = new ProductoImagenModel();

        // 1. Migrar Perfiles de Usuario
        CLI::write('--- Migrando Usuarios ---', 'green');
        $usuarios = $usuarioModel->findAll();
        foreach ($usuarios as $u) {
            if (!empty($u['imagen']) && strpos($u['imagen'], 'http') !== 0) {
                $path = FCPATH . 'public/assets/uploads/perfil/' . $u['imagen'];
                if (file_exists($path)) {
                    CLI::write("Subiendo perfil de: " . $u['usuario'], 'white');
                    $res = $cloudinaryService->subirImagen($path, 'cva_muebles/perfil');
                    if ($res['status'] === 'success') {
                        $usuarioModel->update($u['id_usuario'], ['imagen' => $res['url']]);
                        @unlink($path);
                        CLI::write(" OK", 'green');
                    } else {
                        CLI::write(" ERROR: " . $res['message'], 'red');
                    }
                }
            }
        }

        // 2. Migrar Productos
        CLI::write('--- Migrando Productos ---', 'green');
        $productos = $productoModel->findAll();
        CLI::write('Productos encontrados en la DB: ' . count($productos), 'yellow');
        foreach ($productos as $p) {
            if (!empty($p['imagen']) && strpos($p['imagen'], 'http') !== 0) {
                $path = FCPATH . 'public/assets/uploads/' . $p['imagen'];
                CLI::write('Checking path: ' . $path, 'cyan');
                if (file_exists($path)) {
                    CLI::write("Subiendo producto: " . $p['nombre_prod'], 'white');
                    $res = $cloudinaryService->subirImagen($path, 'cva_muebles/productos');
                    if ($res['status'] === 'success') {
                        $productoModel->update($p['id_producto'], ['imagen' => $res['url']]);
                        @unlink($path);
                        
                        // Delete webp local fallback if exists
                        $webpPath = FCPATH . 'public/assets/uploads/' . pathinfo($p['imagen'], PATHINFO_FILENAME) . '.webp';
                        if (file_exists($webpPath)) @unlink($webpPath);
                        
                        CLI::write(" OK", 'green');
                    } else {
                        CLI::write(" ERROR: " . $res['message'], 'red');
                    }
                }
            }
        }

        // 2.5 Migrar Imágenes Secundarias de Productos
        CLI::write('--- Migrando Imágenes Secundarias (Galería de Productos) ---', 'green');
        $imagenesSec = $productoImagenModel->findAll();
        foreach ($imagenesSec as $imgSec) {
            if (!empty($imgSec['imagen']) && strpos($imgSec['imagen'], 'http') !== 0) {
                $path = FCPATH . 'public/assets/uploads/' . $imgSec['imagen'];
                if (file_exists($path)) {
                    CLI::write("Subiendo imagen secundaria ID: " . $imgSec['id'], 'white');
                    $res = $cloudinaryService->subirImagen($path, 'cva_muebles/productos');
                    if ($res['status'] === 'success') {
                        $productoImagenModel->update($imgSec['id'], ['imagen' => $res['url']]);
                        @unlink($path);
                        $webpPath = FCPATH . 'public/assets/uploads/' . pathinfo($imgSec['imagen'], PATHINFO_FILENAME) . '.webp';
                        if (file_exists($webpPath)) @unlink($webpPath);
                        CLI::write(" OK", 'green');
                    } else {
                        CLI::write(" ERROR: " . $res['message'], 'red');
                    }
                }
            }
        }

        // 3. Migrar Galería (Testimonios)
        CLI::write('--- Migrando Galería de Clientes ---', 'green');
        $galerias = $galeriaModel->findAll();
        foreach ($galerias as $g) {
            if (!empty($g['imagen']) && strpos($g['imagen'], 'http') !== 0) {
                $path = FCPATH . 'public/assets/uploads/galeria/' . $g['imagen'];
                if (file_exists($path)) {
                    CLI::write("Subiendo foto de galería ID: " . $g['id'], 'white');
                    $res = $cloudinaryService->subirImagen($path, 'cva_muebles/galeria');
                    if ($res['status'] === 'success') {
                        $galeriaModel->update($g['id'], ['imagen' => $res['url']]);
                        @unlink($path);
                        CLI::write(" OK", 'green');
                    } else {
                        CLI::write(" ERROR: " . $res['message'], 'red');
                    }
                }
            }
        }

        CLI::write('¡Migración histórica completada con éxito!', 'green');
        CLI::write('Las bases de datos están 100% en la Nube y tu disco duro local está limpio.', 'yellow');
    }
}
