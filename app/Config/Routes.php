<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// -------------------- RUTA PRINCIPAL --------------------
$routes->get('/', 'HomeController::index');


// ====================================================================
//                      RUTAS SIN FILTRO DE AUTENTICACIÓN
// ====================================================================

// -------------------- Páginas informativas --------------------
$routes->get('/quienesSomos', 'PagesController::quienesSomos');
$routes->get('/comercializacion', 'PagesController::comercializacion');
$routes->get('/informacionContacto', 'PagesController::informacionContacto');
$routes->get('/terminosYCondiciones', 'PagesController::terminosYCondiciones');
$routes->get('/beneficios', 'PagesController::beneficios');
$routes->get('/productos', 'PagesController::productos');
$routes->get('/galeria', 'GaleriaController::index');
$routes->get('/producto/detalle/(:num)', 'ProductoController::ver_detalle/$1');

// -------------------- Registro de usuarios --------------------
$routes->get('/registro', 'UsuarioController::index_registrar');
$routes->post('/enviar-form', 'UsuarioController::formValidation');

// -------------------- Login de usuarios --------------------
$routes->get('/login', 'LoginController::create');
$routes->post('/enviar-login', 'LoginController::auth');
$routes->get('/logout', 'LoginController::logout');

// -------------------- Consultas públicas --------------------
$routes->post('/enviar-consulta', 'ConsultaController::cargarConsulta');


// ====================================================================
//                      RUTAS CON FILTRO DE AUTENTICACIÓN (usuario logueado)
// ====================================================================

$routes->group('', ['filter' => 'auth'], static function (RouteCollection $routes) {
    // -------------------- Galería (subida por usuarios) --------------------
    $routes->post('/galeria/subir', 'GaleriaController::subir');

    // -------------------- Ventas / Facturas del usuario --------------------
    $routes->get('/ventas_lista', 'VentasController::ver_facturas_usuario');
    $routes->get('/factura/(:num)', 'VentasController::ver_factura/$1');
    $routes->post('/carrito_comprar', 'VentasController::registrar_venta');

    // -------------------- Perfil de Usuario --------------------
    $routes->get('/perfil', 'UsuarioController::index_perfil');
    $routes->post('/guardarCambios', 'UsuarioController::guardarCambios');
    $routes->post('/cambiarPassword', 'UsuarioController::cambiarPassword');

    // -------------------- Funcionalidad del Carrito --------------------
    $routes->get('/muestro', 'CarritoController::muestra');
    $routes->post('/carrito/add', 'CarritoController::add');
    $routes->post('/carrito_elimina/(:any)', 'CarritoController::remove/$1');
    $routes->post('/borrar', 'CarritoController::borrar_carrito');
    $routes->match(['get', 'post'], 'carrito_suma/(:any)', 'CarritoController::suma/$1');
    $routes->match(['get', 'post'], 'carrito_resta/(:any)', 'CarritoController::resta/$1');

    // -------------------- Gestión de Favoritos (Wishlist) --------------------
    $routes->post('/favoritos/toggle/(:num)', 'FavoritosController::toggleFavorito/$1');
    $routes->get('/mis-favoritos', 'FavoritosController::misFavoritos');
});


// ====================================================================
//                      RUTAS CON FILTRO DE ADMINISTRADOR
// ====================================================================

$routes->group('', ['filter' => 'adminAuth'], static function (RouteCollection $routes) {
    // -------------------- Galería (moderación) --------------------
    $routes->get('/admin/galeria', 'GaleriaController::admin_index');
    $routes->post('/admin/galeria/aprobar/(:num)', 'GaleriaController::aprobar/$1');
    $routes->post('/admin/galeria/eliminar/(:num)', 'GaleriaController::eliminar/$1');

    // -------------------- Gestión de Ventas --------------------
    $routes->get('/ventas-list', 'VentasController::index_ventas');
    $routes->get('/ventas/subir/(:num)', 'VentasController::subir_prioridad/$1');
    $routes->get('/ventas/bajar/(:num)', 'VentasController::bajar_prioridad/$1');
    $routes->post('/ventas/actualizar_estado/(:num)', 'VentasController::actualizar_estado/$1');
    $routes->get('/admin-dashboard', 'VentasController::estadisticas');
    $routes->get('/ventas/gestion/(:num)', 'VentasController::ver_gestion_pedido/$1');
    $routes->post('/ventas/registrar_pago', 'VentasController::registrar_pago');
    $routes->post('/ventas/guardar_observaciones', 'VentasController::guardar_observaciones');
    $routes->get('/ventas/nuevo-personalizado', 'VentasController::nuevo_pedido_personalizado');
    $routes->post('/ventas/guardar-personalizado', 'VentasController::guardar_pedido_personalizado');

    // -------------------- Gestión de Usuarios --------------------
    $routes->get('/crud-usuarios', 'UsuarioController::index');
    $routes->post('/editar-usuario/(:num)', 'UsuarioController::editar_usuario/$1');
    $routes->post('/delete-usuario/(:num)', 'UsuarioController::delete_usuario/$1');
    $routes->post('/activar-usuario/(:num)', 'UsuarioController::activar_usuario/$1');
    $routes->post('/eliminar-usuario-permanente/(:num)', 'UsuarioController::eliminar_permanente/$1');

    // -------------------- Gestión de Productos --------------------
    $routes->get('/crud-productos', 'ProductoController::index');
    $routes->get('/alta-producto', 'ProductoController::create_alta_producto');
    $routes->post('/enviar-alta-producto', 'ProductoController::formValidation');
    $routes->post('/delete-producto/(:num)', 'ProductoController::delete_producto/$1');
    $routes->post('/activar-producto/(:num)', 'ProductoController::activar_producto/$1');
    $routes->post('/eliminar-producto-permanente/(:num)', 'ProductoController::eliminar_permanente/$1');
    $routes->get('/editar-producto/(:num)', 'ProductoController::index_editar_producto/$1');
    $routes->post('modificar-producto/(:num)', 'ProductoController::modificar_producto/$1');
    $routes->post('/admin/productos/subir-fotos/(:num)', 'ProductoController::subir_fotos_galeria/$1');
    $routes->post('/admin/productos/eliminar-foto/(:num)', 'ProductoController::eliminar_foto_galeria/$1');

    // -------------------- Gestión de Categorías --------------------
    $routes->get('/crud-categorias', 'CategoriaController::index');
    $routes->post('/admin/categorias/guardar', 'CategoriaController::guardar');
    $routes->post('/admin/categorias/editar/(:num)', 'CategoriaController::editar/$1');
    $routes->post('/admin/categorias/eliminar/(:num)', 'CategoriaController::eliminar/$1');
    $routes->post('/admin/categorias/toggle/(:num)', 'CategoriaController::toggle/$1');

    // -------------------- Gestión de Consultas --------------------
    $routes->get('/consultas', 'ConsultaController::index');
    $routes->post('/consultas/eliminar/(:num)', 'ConsultaController::eliminarConsulta/$1');
    $routes->post('/consultas/restaurar/(:num)', 'ConsultaController::restaurarConsulta/$1');
    $routes->post('/consultas/eliminar-permanente/(:num)', 'ConsultaController::eliminarPermanente/$1');
});
