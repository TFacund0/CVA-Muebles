-- ============================================================================
-- SQL Script: Optimización de Índices de Base de Datos para CVA Muebles
-- Autor: Antigravity AI Coding Assistant
-- Fecha: 2026-05-21
-- Descripción: Este script crea índices de alto rendimiento en las tablas críticas
--              del sistema para acelerar consultas complejas, operaciones de unión (JOIN)
--              y búsquedas frecuentes en el catálogo, pedidos y lista de favoritos.
-- ============================================================================

USE `cva_muebles`; -- Reemplaza con el nombre real de tu base de datos si es diferente

-- ----------------------------------------------------------------------------
-- 🎨 1. TABLA: favoritos (Optimización de Lista de Deseos / Favoritos)
-- ----------------------------------------------------------------------------
-- Explicación:
-- - Buscamos constantemente si un usuario tiene un producto específico como favorito 
--   con `where(['usuario_id' => $usuario, 'producto_id' => $prod])`.
-- - Hacemos joins entre favoritos, productos y usuarios.
-- Solución:
-- - Un índice compuesto único en (usuario_id, producto_id) acelera drásticamente 
--   esta búsqueda en microsegundos y, a nivel de integridad de base de datos, 
--   previene el error de registrar un mismo favorito dos veces por error.
-- ----------------------------------------------------------------------------

-- Eliminar índice compuesto previo si existiera
ALTER TABLE `favoritos` DROP INDEX IF EXISTS `idx_favoritos_usuario_producto`;

-- Crear índice compuesto único de alto rendimiento
CREATE UNIQUE INDEX `idx_favoritos_usuario_producto` 
ON `favoritos` (`usuario_id`, `producto_id`);


-- ----------------------------------------------------------------------------
-- 🌲 2. TABLA: productos (Optimización del Catálogo y Grillas)
-- ----------------------------------------------------------------------------
-- Explicación:
-- - La consulta principal del catálogo (`getProductosPublicos`) filtra por
--   `eliminado = 'NO'` y hace un JOIN con `categorias.id_categoria = productos.categoria_id`.
-- - Además, las consultas administrativas a menudo filtran o agrupan por `categoria_id`.
-- Solución:
-- - Un índice compuesto en (categoria_id, eliminado) optimiza al máximo el plan
--   de ejecución del motor MySQL al cruzar datos y filtrar de una sola pasada.
-- ----------------------------------------------------------------------------

ALTER TABLE `productos` DROP INDEX IF EXISTS `idx_productos_categoria_eliminado`;

CREATE INDEX `idx_productos_categoria_eliminado` 
ON `productos` (`categoria_id`, `eliminado`);


-- ----------------------------------------------------------------------------
-- 📦 3. TABLA: ventas_detalle (Optimización del Detalle de Pedidos)
-- ----------------------------------------------------------------------------
-- Explicación:
-- - Cuando se consulta un pedido, se hace un JOIN sobre `venta_id` y `producto_id`.
-- - Estas son claves foráneas (FK) y en bases de datos con gran volumen, no tener
--   índices aquí hace que la base de datos lea toda la tabla completa secuencialmente (Table Scan).
-- Solución:
-- - Creamos índices individuales y compuestos sobre estas llaves de relación.
-- ----------------------------------------------------------------------------

ALTER TABLE `ventas_detalle` DROP INDEX IF EXISTS `idx_detalle_venta_producto`;
ALTER TABLE `ventas_detalle` DROP INDEX IF EXISTS `idx_detalle_producto`;

-- Acelera la búsqueda de ítems pertenecientes a una factura/venta
CREATE INDEX `idx_detalle_venta_producto` 
ON `ventas_detalle` (`venta_id`, `producto_id`);

-- Acelera el cruce de estadísticas de cuáles productos se venden más
CREATE INDEX `idx_detalle_producto` 
ON `ventas_detalle` (`producto_id`);


-- ----------------------------------------------------------------------------
-- 📈 4. TABLA: ventas_cabecera (Optimización del Panel de Ventas y Cola de Trabajo)
-- ----------------------------------------------------------------------------
-- Explicación:
-- - Para mostrar los pedidos activos en el taller, filtramos por `estado_aprobacion != 'RECHAZADO'`
--   y ordenamos por `prioridad DESC` y `fecha DESC`.
-- - Para la cuenta del cliente, filtramos por `usuario_id`.
-- Solución:
-- - Un índice en `usuario_id` acelera el panel del cliente.
-- - Un índice compuesto en `(prioridad, fecha)` acelera el ordenamiento dinámico
--   de la cola de fabricación del taller evitando la ordenación lenta en memoria (Filesort).
-- ----------------------------------------------------------------------------

ALTER TABLE `ventas_cabecera` DROP INDEX IF EXISTS `idx_ventas_usuario`;
ALTER TABLE `ventas_cabecera` DROP INDEX IF EXISTS `idx_ventas_cola_prioridad`;
ALTER TABLE `ventas_cabecera` DROP INDEX IF EXISTS `idx_ventas_estado_aprobacion`;

-- Optimiza el historial de compras del cliente
CREATE INDEX `idx_ventas_usuario` 
ON `ventas_cabecera` (`usuario_id`);

-- Optimiza la visualización de la cola de fabricación según la urgencia/prioridad
CREATE INDEX `idx_ventas_cola_prioridad` 
ON `ventas_cabecera` (`prioridad`, `fecha`);

-- Acelera el filtrado de presupuestos en moderación
CREATE INDEX `idx_ventas_estado_aprobacion` 
ON `ventas_cabecera` (`estado_aprobacion`, `estado`);


-- ----------------------------------------------------------------------------
-- ✉️ 5. TABLA: consultas (Optimización del Panel de Contacto y Moderación)
-- ----------------------------------------------------------------------------
-- Explicación:
-- - En el panel de administración, el recepcionista filtra constantemente por
--   mensajes no contestados (`activo = 'SI'`).
-- - También se busca frecuentemente por el correo del remitente (`email`).
-- Solución:
-- - Creamos un índice compuesto sobre el estado del mensaje y su email.
-- ----------------------------------------------------------------------------

ALTER TABLE `consultas` DROP INDEX IF EXISTS `idx_consultas_activo_email`;

CREATE INDEX `idx_consultas_activo_email` 
ON `consultas` (`activo`, `email`);

-- ============================================================================
-- ¡LISTO!
-- INSTRUCCIONES DE EJECUCIÓN:
-- 1. Iniciá tu cliente SQL (ej: phpMyAdmin, DBeaver, MySQL Workbench o HeidiSQL).
-- 2. Copiá y pegá este script en una pestaña de consulta de tu servidor local.
-- 3. Ejecutalo. MySQL optimizará las consultas al instante de forma automática.
-- ============================================================================
