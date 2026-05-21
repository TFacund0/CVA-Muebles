# 🪵 CVA Muebles - Carpintería de Autor & Showroom Manager

[<img src="https://img.shields.io/badge/Demo_Disponible-blue?style=for-the-badge&logo=web" />](#-instalación-y-configuración)
[![PHP Version](https://img.shields.io/badge/PHP-8.1%2B-777bb4?style=for-the-badge&logo=php)](https://www.php.net/)
[![Framework](https://img.shields.io/badge/Framework-CodeIgniter--4-ee4323?style=for-the-badge&logo=codeigniter)](https://codeigniter.com/)
[![Database](https://img.shields.io/badge/Database-MySQL-4479a1?style=for-the-badge&logo=mysql)](https://www.mysql.com/)
[![Design](https://img.shields.io/badge/Design-100%25--Responsive-blueviolet?style=for-the-badge&logo=css3)](https://caniuse.com/)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

## 🌟 Descripción General

Este proyecto es una **Plataforma Web Híbrida (E-Commerce y Showroom)** diseñada a medida para la gestión estratégica y exhibición del taller de carpintería artesanal de **César Víctor Acevedo**. Fue desarrollado con un enfoque en alto rendimiento, diseño premium y buenas prácticas de ingeniería de software.

El sistema permite gestionar todo el ciclo operativo del taller, desde la exhibición interactiva de productos, gestión de pedidos personalizados y ventas, hasta un monitoreo estadístico detallado a través de un panel de administración robusto.

<p align="center">
  <!-- Reemplazar con screenshot real -->
  <img src="https://via.placeholder.com/800x400.png?text=Pantalla+Principal+CVA+Muebles" width="70%" alt="Pantalla Principal CVA Muebles" />
</p>

---

## 🛠️ Arquitectura y Tecnologías

El sistema sigue el patrón de diseño **MVC (Modelo-Vista-Controlador)**, potenciado con la introducción de una **Capa de Servicios (Service Layer)** para maximizar la mantenibilidad, escalabilidad y testabilidad:

- **Capa de Presentación (UI)**: Desarrollada bajo la filosofía **Mobile-First**, utilizando **Bootstrap 5** y **Vanilla CSS** altamente optimizado. El diseño ofrece transiciones premium, _Glassmorphism_ y tipografías estilizadas (Outfit/Inter).
- **Controladores (Controllers)**: Componentes delgados (_Slim Controllers_) que se encargan exclusivamente del protocolo HTTP (recibir parámetros de entrada y retornar vistas o respuestas JSON).
- **Capa de Servicios (BLL)**: Aísla y centraliza toda la lógica de negocio (procesamiento de ventas, control de inventario, algoritmos de prioridad de pedidos), evitando acoplamientos innecesarios.
- **Capa de Datos (DAL)**: Interactúa de forma segura con la base de datos MySQL mediante los Modelos y el **Query Builder** integrado en el framework.

### Tecnologías Clave:

- **Lenguaje**: PHP 8.1+
- **Framework Backend**: CodeIgniter 4
- **Base de Datos**: MySQL
- **Frontend**: HTML5, CSS3, JavaScript (Fetch/AJAX), Bootstrap 5
- **Patrones de Diseño**: MVC, Service Layer Pattern

---

## 📋 Módulos del Sistema

El sistema ofrece un **Modo Dual** gestionado por variables de entorno y está dividido en dos grandes ecosistemas:

### 1. 🛋️ Catálogo y Showroom (Frontend / Cliente)

- **Modo Híbrido**: El administrador puede alternar dinámicamente entre un e-commerce completamente funcional (con checkout) o un catálogo de exhibición (_Showroom Mode_), donde los botones de compra se transforman en enlaces directos y parametrizados a WhatsApp.
- **Experiencia Responsiva**: Menús colapsables táctiles, cuadrículas fluidas para catálogos y animaciones de zoom inmersivas en productos de alta resolución.
- **Galería Moderada**: Exhibición de los muebles instalados en los hogares de los clientes.

<p align="center">
  <!-- Reemplazar con screenshots reales -->
  <img src="https://via.placeholder.com/400x200.png?text=Catalogo+de+Productos" width="45%" alt="Catálogo de Productos" />
  <img src="https://via.placeholder.com/400x200.png?text=Modo+Carrito" width="45%" alt="Carrito de Compras" />
</p>

### 2. 🛠️ Panel Administrativo (Backend / Gestor)

- **Dashboard Estadístico**: Monitoreo en tiempo real de métricas de facturación, pedidos pendientes e inventario crítico.
- **Algoritmo de Prioridad Atómica**: Sistema de arrastre (drag-and-drop) para organizar el orden de fabricación en el taller de forma atómica en la base de datos.
- **Gestión de Pedidos Personalizados**: Interfaz para el registro de ventas de muebles a medida, con notas detalladas que escapan al catálogo estándar.
- **Moderación de Interacciones**: Control total sobre la aprobación y publicación de fotografías de clientes.

<p align="center">
  <!-- Reemplazar con screenshots reales -->
  <img src="https://via.placeholder.com/400x200.png?text=Dashboard+Administrativo" width="45%" alt="Dashboard Estadístico" />
  <img src="https://via.placeholder.com/400x200.png?text=Gestion+de+Pedidos" width="45%" alt="Gestión de Pedidos" />
</p>

---

## 🔒 Auditoría y Seguridad

El proyecto cuenta con un esquema de protección integral contra vulnerabilidades, mitigando riesgos basándose en el estándar **OWASP Top 10**:

- **Prevención de Inyecciones SQL (SQLi)**: Todas las consultas a la base de datos utilizan el _Query Builder_ de CodeIgniter 4 y _PDO Bindings_ parametrizados.
- **Mitigación Cross-Site Scripting (XSS)**: Escapado riguroso de todo dato dinámico renderizado en vistas mediante la función `esc()`.
- **Protección CSRF (Cross-Site Request Forgery)**: Filtro global activo que inyecta y valida tokens en cada formulario y llamada AJAX o Fetch de forma obligatoria.
- **Control contra Fuerza Bruta**: El sistema de login emplea un limitador de tasa (_Throttler_) que bloquea temporalmente IPs con más de 5 intentos fallidos por minuto.
- **Hardening de Sesión**: Invocación a `session()->regenerate()` post-autenticación y almacenamiento de cookies bajo directivas `HTTPOnly` y `SameSite = Lax`.
- **Carga Segura de Archivos**: Validación física y del tipo MIME en subidas de imágenes, implementando renombrado criptográfico aleatorio (`getRandomName()`) para prevenir la carga de binarios maliciosos.
- **Prevención de IDOR**: Restricción activa y validación en controladores para asegurar que solo el propietario de un recurso (o un administrador) tenga acceso a datos sensibles (facturas, detalles de perfil).

---

## ⚡ Optimización de Rendimiento y Velocidad (WPO)

La plataforma incorpora una suite integral de optimización de rendimiento en la web (Web Performance Optimization) a nivel de backend, frontend y base de datos:

- **Procesamiento de Imágenes en Caliente (WebP Backend Transcoding)**:
  - Todas las subidas de imágenes del catálogo (`ProductoService`) y de la galería social (`GaleriaService`) son interceptadas dinámicamente en caliente.
  - Se realiza una redimensión proporcional inteligente (máximo **800px** para fichas y **1200px** para fotos de clientes) y se transcodifican nativamente al formato de última generación **WebP** con compresión de calidad balanceada al **80%**, reduciendo drásticamente el peso de transferencia sin perder fidelidad visual.
- **Carga Perezosa Nativa (Lazy Loading)**:
  - Implementación sistemática del atributo `loading="lazy"` en todas las vistas de grillas (`productos`, `section-catalogo`, `galeria_clientes`) y miniaturas de detalles de productos, posponiendo la descarga de imágenes fuera de la pantalla de visualización activa (viewport) y acelerando el renderizado inicial de la página.
- **Arquitectura de Doble Capa de Caché (Dual-Layer Caching)**:
  - **Caché de Páginas Completas (Page Caching)**: Reducción del TTFB (Time to First Byte) a milisegundos mediante almacenamiento en caché estática (600s) en controladores públicos para páginas informativas estáticas (`quienesSomos`, `comercializacion`, `terminosYCondiciones`, `beneficios`). Se excluyen de forma segura páginas dinámicas de sesión o formularios con validaciones CSRF activas.
  - **Caché de Consultas SQL (Query Caching)**: Almacenamiento en caché por 1 hora de las estadísticas de productos por categorías en `CategoriaService` para mitigar el problema de consultas duplicadas N+1.
  - **Coherencia e Invalidación Dinámica**: Purga atómica y automatizada del estado de la caché (`$cache->delete(...)`) en todas las operaciones de escritura/modificación de categorías y productos en los servicios de negocio, asegurando consistencia de datos en tiempo real.
- **Indexación y Optimización de Base de Datos**:
  - Script especializado `cva_indexes_optimization.sql` (disponible en la carpeta `scratch/`) que implementa índices (B-Tree e índices únicos) estratégicamente diseñados para acelerar las consultas más críticas, JOINs complejos y ordenamientos en las tablas `favoritos`, `productos`, `ventas_detalle`, `ventas_cabecera` y `consultas`.
- **Drivers de Sesión RAM de Alto Rendimiento**:
  - Configuración preparada y documentada en el archivo `.env` para migrar transparentemente la persistencia de sesiones del disco (`FileHandler`) a almacenamiento en memoria RAM activa de baja latencia utilizando **Redis** o **Memcached** en entornos de producción.

---

## 🚀 Instalación y Configuración

1.  **Clonar el repositorio**:
    ```bash
    git clone https://github.com/TFacund0/Proyecto-CVA-Muebles.git
    cd Proyecto-CVA-Muebles
    ```
2.  **Configurar el Entorno Local (`.env`)**:
    Renombra el archivo `.env.example` a `.env` (o edita el `.env` existente) y define los parámetros del sistema:

    ```env
    CI_ENVIRONMENT = development
    app.baseURL = 'http://localhost/Proyecto-CVA-Muebles/'

    # Modo Híbrido: True = E-commerce completo, False = Showroom / WhatsApp
    SHOPPING_CART_ENABLED = true
    ```

3.  **Base de Datos**:
    - Crea una base de datos MySQL local llamada `arce_acevedo` (con cotejamiento `utf8mb4_general_ci`).
    - Importa el archivo `arce_acevedo.sql` localizado en la raíz del proyecto.
    - Actualiza las credenciales de base de datos en `app/Config/Database.php` o preferentemente en `.env`.
4.  **Credenciales de Demostración**:
    - **Administrador**: Email: `admin@cvamuebles.com` (o `admin`) | Contraseña: `admin123`
    - **Cliente**: Email: `cliente@gmail.com` (o `cliente`) | Contraseña: `cliente123`

---

## 💡 Aprendizajes y Evolución

El desarrollo de este sistema representa la aplicación de buenas prácticas de ingeniería de software en entornos PHP modernos:

- **Desacoplamiento Avanzado**: La implementación de la _Service Layer_ permitió lograr Controladores puramente dedicados al flujo de red, extrayendo las reglas de negocio complejas hacia módulos testeables.
- **Adaptabilidad Real**: El desarrollo de variables lógicas (Modo Showroom) ha dotado al proyecto de versatilidad empresarial.
- **Conciencia de Seguridad Web**: Implementar los lineamientos de OWASP Top 10 fue clave para solidificar un sistema de ventas en línea preparado contra vectores de ataque contemporáneos.

> [!NOTE]
> **Filosofía del Proyecto:** Al igual que en la carpintería de autor, donde cada veta de madera cuenta una historia, la arquitectura detrás de esta plataforma está diseñada de forma artesanal. Meticulosa en el código, segura en sus procesos y fluida en su experiencia visual.

---

## 📄 Licencia

Este proyecto está bajo la Licencia MIT. Consulta el archivo [LICENSE](LICENSE) para más detalles.

---

_Diseñado y programado por **[Tobías César Facundo Acevedo](https://github.com/TFacund0)**._
