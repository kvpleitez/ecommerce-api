# API de E-commerce Segura

API REST desarrollada con Laravel 12 para gestionar las operaciones principales de una plataforma de comercio electrónico. Permite administrar usuarios, productos, órdenes y pagos, incorporando autenticación JWT, integración con Stripe y documentación interactiva mediante Swagger/OpenAPI.

## Tecnologías utilizadas

- **PHP 8.2+**
- **Laravel 12**
- **MySQL**
- **JWT Authentication:** `php-open-source-saver/jwt-auth`
- **Stripe:** `stripe/stripe-php`
- **Swagger/OpenAPI:** `darkaonline/l5-swagger`

## Funcionalidades principales

### Autenticación y usuarios
- Registro de usuarios.
- Inicio y cierre de sesión.
- Autenticación mediante tokens JWT.
- Consulta del perfil del usuario autenticado.

### Gestión de productos
- Listado público de productos activos.
- Consulta de productos individuales.
- Creación, actualización y eliminación de productos.
- Control de existencias.

### Gestión de órdenes
- Creación de órdenes con múltiples productos.
- Cálculo automático de subtotales y total.
- Validación de disponibilidad y existencias.
- Descuento automático del inventario.
- Historial de órdenes por usuario.
- Consulta individual de órdenes.

### Procesamiento de pagos
- Creación de PaymentIntents mediante Stripe.
- Procesamiento de pagos en modo de pruebas.
- Recepción y verificación de webhooks.
- Actualización automática del estado de pagos y órdenes.

### Validaciones y seguridad
- Validación de datos mediante Laravel Form Requests.
- Protección de rutas mediante autenticación JWT.
- Verificación de propiedad de las órdenes.
- Manejo de transacciones en operaciones de compra.
- Respuestas JSON con mensajes y códigos HTTP descriptivos.
- Uso de variables de entorno para credenciales y configuraciones sensibles.

## Requisitos previos

Antes de instalar el proyecto, asegúrate de contar con:

- PHP 8.2 o superior.
- Composer.
- MySQL.
- Una cuenta de Stripe para utilizar las credenciales de prueba.
- Stripe CLI para probar los webhooks.

## Instalación y configuración

### 1. Clonar el repositorio

```bash
git clone https://github.com/kvpleitez/ecommerce-api.git
cd ecommerce-api
```

### 2. Instalar dependencias

```bash
composer install
```

### 3. Configurar las variables de entorno

Crea una copia del archivo de ejemplo:

En Windows PowerShell:

```powershell
Copy-Item .env.example .env
```

En Linux o macOS:

```bash
cp .env.example .env
```

Genera la clave de la aplicación:

```bash
php artisan key:generate
```

Genera el secreto de autenticación JWT:

```bash
php artisan jwt:secret
```

### 4. Configurar la base de datos

Crea una base de datos MySQL llamada:

```sql
CREATE DATABASE ecommerce_api;
```

Configura las siguientes variables en el archivo `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ecommerce_api
DB_USERNAME=root
DB_PASSWORD=
```

Ajusta el usuario y la contraseña según tu configuración local de MySQL.

### 5. Configurar Stripe

Obtén las credenciales de prueba desde tu cuenta de Stripe y configúralas en el archivo `.env`:

```env
STRIPE_KEY=pk_test_REPLACE_WITH_YOUR_KEY
STRIPE_SECRET=sk_test_REPLACE_WITH_YOUR_SECRET
STRIPE_WEBHOOK_SECRET=whsec_REPLACE_WITH_YOUR_WEBHOOK_SECRET
```

Estas variables deben contener tus propias credenciales. No compartas ni publiques el archivo `.env`.

### 6. Ejecutar migraciones y seeders

```bash
php artisan migrate --seed
```

Este comando crea las tablas necesarias y carga los productos de ejemplo.

### 7. Iniciar el servidor

```bash
php artisan serve --port=8001
```

La API estará disponible en:

`http://127.0.0.1:8001/api`

## Documentación Swagger

El proyecto incorpora documentación interactiva mediante Swagger/OpenAPI.

Con el servidor en ejecución, accede a:

`http://127.0.0.1:8001/api/documentation`

Desde esta interfaz es posible consultar los endpoints, revisar sus parámetros, conocer los formatos de respuesta y realizar solicitudes de prueba.

Si necesitas regenerar la documentación:

```bash
php artisan l5-swagger:generate
```

## Autenticación JWT

Los endpoints protegidos requieren un token JWT.

Primero, realiza el inicio de sesión mediante:

`POST /api/login`

La respuesta incluye un token de autenticación.

Para acceder a las rutas protegidas, envía el siguiente encabezado HTTP:

```http
Authorization: Bearer TU_TOKEN
Accept: application/json
```

En Swagger, utiliza la opción **Authorize** e introduce el token obtenido.

## Endpoints disponibles

La API utiliza el prefijo `/api`.

### Sistema y autenticación

| Método | Endpoint | Descripción | Acceso |
|---|---|---|---|
| GET | `/health` | Verificar el estado de la API | Público |
| POST | `/register` | Registrar un usuario | Público |
| POST | `/login` | Iniciar sesión | Público |
| GET | `/me` | Consultar usuario autenticado | JWT |
| POST | `/logout` | Cerrar sesión | JWT |

### Productos

| Método | Endpoint | Descripción | Acceso |
|---|---|---|---|
| GET | `/products` | Listar productos activos | Público |
| GET | `/products/{product}` | Consultar un producto | Público |
| POST | `/products` | Crear un producto | JWT |
| PUT | `/products/{product}` | Actualizar un producto | JWT |
| DELETE | `/products/{product}` | Eliminar un producto | JWT |

### Órdenes

| Método | Endpoint | Descripción | Acceso |
|---|---|---|---|
| GET | `/orders` | Consultar historial de órdenes | JWT |
| POST | `/orders` | Crear una orden | JWT |
| GET | `/orders/{id}` | Consultar una orden específica | JWT |

### Pagos

| Método | Endpoint | Descripción | Acceso |
|---|---|---|---|
| POST | `/payments/create-intent` | Crear un intento de pago con Stripe | JWT |
| POST | `/stripe/webhook` | Recibir eventos de Stripe | Firma Stripe |

## Ejemplos de solicitudes

### Registrar un usuario

`POST /api/register`

```json
{
    "name": "Usuario de prueba",
    "email": "usuario@example.com",
    "password": "Password123",
    "password_confirmation": "Password123"
}
```

### Iniciar sesión

`POST /api/login`

```json
{
    "email": "usuario@example.com",
    "password": "Password123"
}
```

### Crear una orden

`POST /api/orders`

```json
{
    "items": [
        {
            "product_id": 1,
            "quantity": 2
        },
        {
            "product_id": 2,
            "quantity": 1
        }
    ]
}
```

### Crear un intento de pago

`POST /api/payments/create-intent`

```json
{
    "order_id": 1
}
```

El endpoint devuelve el identificador del PaymentIntent y su `client_secret`, necesarios para continuar el proceso de pago mediante Stripe.

## Configuración de webhooks de Stripe

Para probar los eventos de Stripe localmente, inicia sesión en Stripe CLI:

```bash
stripe login
```

Luego ejecuta:

```bash
stripe listen --forward-to http://127.0.0.1:8001/api/stripe/webhook --events "payment_intent.succeeded,payment_intent.payment_failed"
```

Stripe CLI proporcionará una clave de firma para los webhooks. Configúrala en la variable `STRIPE_WEBHOOK_SECRET` del archivo `.env`.

El webhook procesa los siguientes eventos:

- `payment_intent.succeeded`: actualiza el pago y la orden a estado `paid`.
- `payment_intent.payment_failed`: actualiza el pago a estado `failed`.

El endpoint verifica la firma enviada por Stripe antes de procesar los eventos.

## Estructura de la base de datos

El proyecto utiliza las siguientes tablas principales:

| Tabla | Descripción |
|---|---|
| `users` | Información de los usuarios registrados |
| `products` | Catálogo de productos y existencias |
| `orders` | Órdenes realizadas por los usuarios |
| `order_items` | Productos y cantidades asociados a cada orden |
| `payments` | Información y estado de los pagos |

## Manejo de respuestas y errores

La API utiliza respuestas JSON y códigos HTTP para comunicar el resultado de las operaciones.

| Código HTTP | Descripción |
|---|---|
| 200 | Solicitud procesada correctamente |
| 201 | Recurso creado correctamente |
| 400 | Solicitud incorrecta o firma de webhook inválida |
| 401 | Autenticación requerida o credenciales incorrectas |
| 404 | Recurso no encontrado |
| 422 | Error de validación o solicitud no procesable |

## Consideraciones de seguridad

- Las credenciales y claves privadas se almacenan en variables de entorno.
- El archivo `.env` no debe publicarse en el repositorio.
- El archivo `.env.example` contiene únicamente valores de referencia.
- Los endpoints administrativos y de operaciones de usuario están protegidos mediante JWT.
- Los pagos utilizan credenciales de Stripe en modo de pruebas durante el desarrollo.
- Los webhooks verifican la firma de Stripe para validar la autenticidad de los eventos.

## Autoría

**Karla Pleitez**

Proyecto desarrollado como parte del programa Full Stack Jr. de Kódigo.

**Repositorio:** https://github.com/kvpleitez/ecommerce-api