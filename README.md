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
- **Vistas embebidas (legacy)**: HTML5, CSS3, JavaScript (Fetch/AJAX), Bootstrap 5
- **Frontend desacoplado (nuevo)**: Next.js 16 (App Router) + TypeScript + Tailwind, en `/frontend`
- **Patrones de Diseño**: MVC, Service Layer Pattern

---

## 📁 Backend vs Frontend: dónde está cada cosa

Este repositorio contiene **dos proyectos independientes**, cada uno con su propio gestor de dependencias y ciclo de vida, comunicados solo por HTTP:

| | Ubicación | Stack | Rol |
|---|---|---|---|
| **Backend** | [`/backend`](backend) (`app/`, `public/`, `composer.json`) | CodeIgniter 4 / PHP | Vistas embebidas legacy + API REST (`/api/v1/*`) |
| **Frontend nuevo** | [`/frontend`](frontend) | Next.js / TypeScript | Consume `/api/v1/*` vía `frontend/src/lib/api.ts`. Proyecto Node aislado, no importa código PHP. |

```
CVA-Muebles/
├── backend/         ← proyecto CodeIgniter 4 completo (antes vivía en la raíz)
│   ├── app/
│   │   ├── Controllers/          (vistas legacy: HTML + sesión + CSRF)
│   │   ├── Controllers/Api/       (API REST pública/cliente: JSON + JWT)
│   │   ├── Controllers/Api/Admin/ (API REST del panel admin: JSON + JWT + rol admin)
│   │   ├── Models/, Services/      (lógica de negocio y datos — compartida por ambas capas de transporte)
│   │   ├── Database/Migrations/    (historial de esquema versionado)
│   │   ├── Database/Scripts/       (SQL/scripts de mantenimiento puntuales, fuera de migraciones)
│   │   ├── ThirdParty/             (paquetes vendoreados a mano, ej. Cart/, convención propia de CI4)
│   │   └── Views/                  (vistas embebidas del sitio legacy)
│   ├── public/       (document root del backend)
│   ├── system/
│   ├── composer.json
│   └── spark
├── frontend/         ← proyecto Next.js
│   ├── src/
│   │   ├── app/            (rutas — App Router)
│   │   │   ├── (public)/     (route group: páginas informativas/estáticas — no afecta la URL)
│   │   │   ├── (shop)/       (route group: catálogo, carrito, favoritos, pedidos, perfil, galería)
│   │   │   ├── (auth)/       (route group: login)
│   │   │   ├── admin/        (panel administrativo, prefijo real de URL /admin/*)
│   │   │   └── api/          (Route Handlers: proxies hacia la API PHP, JWT nunca expuesto al cliente)
│   │   ├── components/
│   │   │   ├── layout/       (Navbar, Footer)
│   │   │   ├── product/      (tarjetas de producto, favoritos, agregar al carrito)
│   │   │   ├── forms/        (formularios de perfil/contraseña/contacto)
│   │   │   ├── shared/       (piezas de UI genéricas, ej. export a PDF)
│   │   │   └── admin/        (componentes exclusivos del panel admin)
│   │   ├── context/         (estado global de cliente, ej. carrito)
│   │   ├── lib/              (clientes HTTP tipados hacia la API: api.ts, auth.ts, admin.ts, adminClient.ts)
│   │   └── types/            (ambient types para paquetes sin tipos, ej. html2pdf.js)
│   └── package.json
└── README.md
```

Dentro del backend, la API vive separada de los controladores clásicos para no mezclar responsabilidades:

- `backend/app/Controllers/*Controller.php` → controllers legacy, devuelven vistas HTML (sesión + CSRF).
- `backend/app/Controllers/Api/*Controller.php` → controllers de la API REST pública/cliente, devuelven JSON (JWT vía `backend/app/Filters/JwtAuth.php`, sin CSRF).
- `backend/app/Controllers/Api/Admin/*Controller.php` → controllers de la API REST del panel admin, mismo JWT pero exigiendo rol admin (`jwtAuth:admin`).
- Los tres reutilizan los **mismos** `Models`/`Services` (`backend/app/Models`, `backend/app/Services`) — la lógica de negocio no se duplica entre capas de transporte.

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

### 3. ✉️ Automatización y Facturación (Novedad)

- **Motor de Notificaciones SMTP**: Sistema integrado de envío de correos electrónicos transaccionales. Los clientes reciben notificaciones instantáneas de bienvenida, confirmación de pedidos y actualizaciones de estado en tiempo real (ej. "En Producción", "Terminado") con plantillas HTML corporativas.
- **Renderizado Frontend de PDFs**: Motor de generación de Comprobantes en formato A4 implementado estrictamente en el cliente (`html2pdf.js`), eliminando la sobrecarga de procesamiento en el servidor. Exporta facturas limpias, vectoriales y adaptadas para impresión profesional, sin dependencias complejas como `Composer/Dompdf`.

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
  - Script especializado `cva_indexes_optimization.sql` (disponible en `backend/app/Database/Scripts/`) que implementa índices (B-Tree e índices únicos) estratégicamente diseñados para acelerar las consultas más críticas, JOINs complejos y ordenamientos en las tablas `favoritos`, `productos`, `ventas_detalle`, `ventas_cabecera` y `consultas`.
- **Drivers de Sesión RAM de Alto Rendimiento**:
  - Configuración preparada y documentada en el archivo `.env` para migrar transparentemente la persistencia de sesiones del disco (`FileHandler`) a almacenamiento en memoria RAM activa de baja latencia utilizando **Redis** o **Memcached** en entornos de producción.

---

## 🚀 Instalación y Configuración

1.  **Clonar el repositorio**:
    ```bash
    git clone https://github.com/TFacund0/Proyecto-CVA-Muebles.git
    cd Proyecto-CVA-Muebles/backend
    ```
    A partir de acá, todos los comandos de esta sección (`composer`, `php spark`) se ejecutan dentro de `backend/`. Para el frontend, ver el paso 6.
2.  **Configurar el Entorno Local (`.env`)**:
    Renombra el archivo `.env.example` a `.env` (o edita el `.env` existente, ambos dentro de `backend/`) y define los parámetros del sistema:

    ```env
    CI_ENVIRONMENT = development
    app.baseURL = 'http://localhost/Proyecto-CVA-Muebles/'

    # Modo Híbrido: True = E-commerce completo, False = Showroom / WhatsApp
    SHOPPING_CART_ENABLED = true
    ```

3.  **Base de Datos**:
    - Crea una base de datos MySQL local llamada `arce_acevedo` (con cotejamiento `utf8mb4_general_ci`).
    - Importa el archivo `backend/cva_muebles.sql`.
    - Actualiza las credenciales de base de datos en `backend/app/Config/Database.php` o preferentemente en `backend/.env`.
4.  **Credenciales de Demostración**:
    - **Administrador**: Email: `admin@cvamuebles.com` (o `admin`) | Contraseña: `admin123`
    - **Cliente**: Email: `cliente@gmail.com` (o `cliente`) | Contraseña: `cliente123`
5.  **Migraciones**: `php spark migrate` (aplica los ajustes de esquema descritos en `app/Database/Migrations/`: columnas monetarias `DECIMAL`, `created_at/updated_at` y `deleted_at` estándar).
6.  **Frontend Next.js** (opcional, consume la API):
    ```bash
    cd ../frontend   # desde backend/, o directamente cd frontend desde la raíz del repo
    cp .env.local.example .env.local   # ajustar NEXT_PUBLIC_API_URL
    npm install
    npm run dev
    ```

---

## 🔌 API REST (`/api/v1`)

Capa de API JSON, pensada para ser consumida por el frontend Next.js (u otros clientes: apps móviles, integraciones). Autenticación **stateless** vía JWT (`Authorization: Bearer <token>`), independiente de las sesiones de cookie que usan las vistas embebidas.

| Método | Ruta | Auth | Descripción |
|---|---|---|---|
| POST | `/api/v1/auth/login` | — | Login, devuelve `access_token` + `refresh_token` |
| POST | `/api/v1/auth/register` | — | Registro de cliente |
| POST | `/api/v1/auth/refresh` | — | Renueva el access token |
| GET | `/api/v1/auth/me` | JWT | Datos del usuario autenticado |
| GET | `/api/v1/productos` | — | Catálogo público (filtro opcional `?categoria=`) |
| GET | `/api/v1/productos/{id}` | — | Detalle de producto |
| GET | `/api/v1/categorias` | — | Categorías activas |

Alcance actual: catálogo público + autenticación. Ventas, carrito y panel administrativo aún se gestionan solo vía las vistas embebidas legacy — quedan pendientes de exponerse como API en una siguiente etapa.

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
