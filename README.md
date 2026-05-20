# 🪵 CVA Muebles - Carpintería de Autor & Showroom Manager

[![PHP Version](https://img.shields.io/badge/PHP-8.1%2B-777bb4?style=for-the-badge&logo=php)](https://www.php.net/)
[![Framework](https://img.shields.io/badge/Framework-CodeIgniter--4-ee4323?style=for-the-badge&logo=codeigniter)](https://codeigniter.com/)
[![Database](https://img.shields.io/badge/Database-MySQL-4479a1?style=for-the-badge&logo=mysql)](https://www.mysql.com/)
[![Design](https://img.shields.io/badge/Design-100%25--Responsive-blueviolet?style=for-the-badge&logo=css3)](https://caniuse.com/)
[![Architecture](https://img.shields.io/badge/Architecture-MVC%20%2B%20Service%20Layer-brightgreen?style=for-the-badge)](https://en.wikipedia.org/wiki/Service_layer_pattern)
[![Security](https://img.shields.io/badge/Security-Hardened%20%26%20Protected-success?style=for-the-badge&logo=dependabot)](https://owasp.org/)

**CVA Muebles** es una plataforma web de alto rendimiento y diseño premium diseñada a medida para la gestión estratégica y exhibición del taller de carpintería artesanal de **César Víctor Acevedo** en Mantilla, Corrientes. 

El proyecto va más allá de un e-commerce tradicional: implementa un innovador **Modo Híbrido (Showroom / Venta directa)** y una arquitectura de software robusta basada en estándares de la industria, ideal como caso de estudio de ingeniería de software para procesos de reclutamiento y entrevistas técnicas.

---

## 📸 Arquitectura y Experiencia Responsiva

El frontend ha sido desarrollado bajo la filosofía **Mobile-First** utilizando una combinación fluida de **Bootstrap 5** y **Vanilla CSS** altamente optimizado. El diseño se adapta perfectamente y de forma dinámica a cualquier dispositivo:
* 📱 **Smartphones:** Menús colapsables táctiles, tarjetas apiladas fluidas y controles simplificados para una experiencia fluida con una sola mano.
* 📟 **Tablets:** Cuadrículas autoajustables y aprovechamiento dinámico del espacio para catálogos y paneles de control.
* 💻 **Desktops de Alta Resolución:** Tipografías estilizadas (Google Fonts Outfit/Inter), transiciones premium con efectos de desenfoque (*Glassmorphic Carousel*), animaciones de zoom suave al pasar el cursor y modales inmersivos.

---

## 🏗️ Patrón de Arquitectura: MVC + Capa de Servicios (*Service Layer*)

Para maximizar la mantenibilidad, escalabilidad y testabilidad del sistema, el backend de CodeIgniter 4 ha sido desacoplado del patrón MVC estándar mediante la introducción de una **Capa de Servicios (`Service Layer`)**. 

```text
       [ Cliente / Navegador ]
                 │ (Peticiones HTTP / AJAX)
                 ▼
          [ Controladores ]  <─── (Filtros de Seguridad: Auth, AdminAuth)
                 │ (Reciben datos, controlan flujo, retornan respuestas JSON/Vistas)
                 ▼
         [ Capa de Servicios ]  <─── (Lógica de Negocio, validaciones complejas)
                 │
                 ▼
            [ Modelos ]
                 │ (Consultas usando Query Builder con bind-parameters)
                 ▼
        [ Base de Datos SQL ]
```

### ¿Por qué esta arquitectura destaca en una entrevista técnica?
1. **Controladores Delgados (*Slim Controllers*):** Los controladores solo se encargan del protocolo HTTP (recibir parámetros de entrada y retornar vistas o respuestas JSON). No contienen lógica de base de datos ni reglas de negocio.
2. **Reutilización y Aislamiento:** Toda la lógica comercial (procesamiento de ventas, control de inventario, algoritmos de prioridad de pedidos) reside en servicios independientes (`app/Services`).
3. **Mantenibilidad:** Si las reglas de negocio cambian, solo se modifica la clase de servicio correspondiente, sin alterar la interfaz gráfica ni los controladores.

---

## 🔒 Auditoría y Hardening de Seguridad (OWASP Top 10)

El proyecto cuenta con un esquema de protección integral contra vulnerabilidades de seguridad en aplicaciones web:

* **Protección contra Inyecciones SQL (SQLi):** Uso exclusivo del **Query Builder de CodeIgniter 4**. Toda interacción con la base de datos se realiza a través de consultas parametrizadas (PDO Bindings) nativas, anulando cualquier intento de inyección de código.
* **Mitigación de Cross-Site Scripting (XSS):** Todo dato dinámico ingresado por el usuario se escapa en las vistas contextuales utilizando de forma rigurosa la función helper `esc()`.
* **Protección CSRF (Cross-Site Request Forgery):** Filtro global activo para peticiones POST. Todos los formularios HTML dinámicos incluyen `<?= csrf_field() ?>`, y las llamadas asíncronas vía Fetch/AJAX inyectan dinámicamente el token `csrf_hash()` en las cabeceras HTTP.
* **Control contra Ataques de Fuerza Bruta:** El inicio de sesión en `LoginController` implementa un limitador de tasa de peticiones (*Throttler*) que bloquea temporalmente las IPs que realicen más de 5 intentos fallidos por minuto.
* **Mitigación de Secuestro y Fijación de Sesión:** Tras una autenticación exitosa, el sistema invoca `session()->regenerate()` para cambiar el ID de sesión activo. Además, las cookies del sistema están configuradas como `HTTPOnly` y `SameSite = Lax`.
* **Carga Segura de Archivos (Unrestricted File Upload):** El sistema de subida de fotos (productos y galería de clientes) valida físicamente el archivo con `is_image[file]`, restringe la extensión por tipo MIME estricto (`image/jpg,image/jpeg,image/png,image/webp`) y renombra automáticamente el archivo en el servidor mediante criptografía aleatoria con `$img->getRandomName()` para evitar ejecuciones maliciosas remotas.
* **Prevención de IDOR (Insecure Direct Object Reference):** Endpoints sensibles (como la visualización de facturas o detalles de pedidos personales) comprueban activamente en el controlador que el ID del propietario del recurso coincida con el ID de la sesión autenticada, a menos que el usuario sea administrador.

---

## 🌟 Funcionalidades Destacadas

### 🛋️ Showroom Mode (Dual Mode)
El sistema permite alternar mediante variables de entorno en el archivo `.env` entre un portal de e-commerce totalmente funcional con checkout integrado y un catálogo de exhibición premium (Showroom). Al desactivar el carrito, los botones de compra se transforman de manera dinámica en enlaces directos y parametrizados a **WhatsApp**, permitiendo una atención artesanal directa.

### 🛠️ Artisan Panel (Gestión Administrativa)
Un backend de administración robusto e intuitivo diseñado para el artesano:
* **Dashboard Estadístico:** Monitoreo en tiempo real de facturación, carga de pedidos pendientes e inventario crítico.
* **Algoritmo de Prioridad Atómica:** Sistema de arrastre de prioridad para organizar el orden de fabricación en el taller de forma atómica en base de datos.
* **Gestión de Pedidos Personalizados:** Interfaz administrativa para registrar ventas de muebles a medida con notas detalladas que no están en el catálogo estándar.
* **Moderación de Galería:** Panel interactivo para aprobar o rechazar fotos de muebles enviadas por clientes satisfechos en sus hogares.

---

## 📂 Estructura Limpia del Directorio

```bash
├── app/
│   ├── Config/         # Configuración del Sistema (Seguridad, Filtros, Rutas)
│   ├── Controllers/    # Controladores delgados (Control de flujos de entrada/salida)
│   ├── Filters/        # Filtros de Seguridad (Auth y AdminAuth)
│   ├── Models/         # Modelos del ORM interactuando con la DB de forma segura
│   ├── Services/       # Capa de Servicios (Lógica de Negocio centralizada)
│   └── Views/          # Vistas (Back y Front Office, modulares y responsivas)
├── assets/
│   ├── css/            # Estilos estáticos segmentados por módulo (carrusel, catálogo)
│   ├── js/             # Funcionalidad y controladores AJAX en frontend
│   └── uploads/        # Directorio seguro de almacenamiento de imágenes
├── public/             # Punto de entrada público del servidor web
└── arce_acevedo.sql    # Estructura e inserciones demo de la Base de Datos
```

---

## 📝 Guía de Instalación y Puesta en Marcha (Entorno de Desarrollo)

1. **Clonar el Repositorio:**
   ```bash
   git clone https://github.com/TFacund0/Proyecto-CVA-Muebles.git
   cd Proyecto-CVA-Muebles
   ```

2. **Configurar el Entorno local (`.env`):**
   Renombra el archivo `.env.example` o edita el `.env` existente en la raíz y configura tus credenciales locales:
   ```env
   CI_ENVIRONMENT = development
   app.baseURL = 'http://localhost/Proyecto-CVA-Muebles/'
   
   # Modo de catálogo
   SHOPPING_CART_ENABLED = true
   ```

3. **Restaurar la Base de Datos:**
   * Crea una base de datos MySQL llamada `arce_acevedo` (cotejamiento preferido: `utf8mb4_general_ci`).
   * Importa el archivo `arce_acevedo.sql` disponible en la raíz del proyecto.
   * Ajusta los valores de conexión en `app/Config/Database.php` o en el archivo `.env`.

4. **Credenciales por Defecto de Demostración:**
   * **Administrador:**
     * **Email/Usuario:** `admin@cvamuebles.com` (o usuario `admin`)
     * **Contraseña:** `admin123`
   * **Cliente Demo:**
     * **Email/Usuario:** `cliente@gmail.com` (o usuario `cliente`)
     * **Contraseña:** `cliente123`

---

## 👨‍💻 Autor y Contacto de Desarrollo

Proyecto ideado, estructurado y programado para la optimización de procesos y showroom interactivo de **César Víctor Acevedo**. 

* **Contacto del Desarrollador Principal:** [LinkedIn](https://www.linkedin.com/in/tobiascesarfacundoacevedo/) | [GitHub](https://github.com/TFacund0)

---

> [!TIP]
> **Filosofía de Carpintería de Autor:** Cada veta de madera cuenta una historia única. Esta plataforma web asegura que la ingeniería digital detrás de cada pieza creada a mano sea igualmente impecable y precisa.
