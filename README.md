# Contact API

API REST desarrollada con **Laravel** para la gestión de contactos. Permite a los usuarios autenticarse y administrar su propia agenda de contactos mediante operaciones CRUD (crear, consultar, actualizar y eliminar).

La API utiliza **Laravel Sanctum** para la autenticación mediante tokens Bearer.

## Tecnologías utilizadas

- PHP
- Laravel
- MySQL
- Laravel Sanctum
- Composer
- PHPUnit / Pest
- Git y GitHub
- Insomnia para pruebas de endpoints

## Características principales

- Autenticación de usuarios mediante correo electrónico y contraseña.
- Generación de tokens de acceso con Laravel Sanctum.
- Cierre de sesión y revocación del token utilizado.
- Consulta de información del usuario autenticado.
- Creación de contactos.
- Consulta de todos los contactos del usuario.
- Consulta de un contacto específico.
- Actualización parcial de contactos.
- Eliminación de contactos.
- Validación de datos.
- Protección de rutas mediante autenticación.
- Aislamiento de contactos por usuario.

## Requisitos previos

Antes de instalar el proyecto, debes tener:

- PHP compatible con la versión de Laravel del proyecto.
- Composer.
- MySQL.
- Git.
- Un entorno local como XAMPP, Laragon u otro servidor compatible.

## Instalación

### 1. Clonar el repositorio

```bash
git clone https://github.com/yendrismartinez55-hue/contact-api.git
```

### 2. Ingresar a la carpeta del proyecto

```bash
cd contact-api
```

### 3. Instalar las dependencias

```bash
composer install
```

### 4. Crear el archivo de configuración

```bash
cp .env.example .env
```

En Windows también puedes crear una copia de `.env.example` y nombrarla `.env`.

### 5. Generar la clave de la aplicación

```bash
php artisan key:generate
```

### 6. Configurar la base de datos

Abre el archivo `.env` y configura la conexión con MySQL:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=contac_api
DB_USERNAME=root
DB_PASSWORD=
```

Ajusta el usuario y la contraseña según tu instalación de MySQL.

Crea previamente la base de datos `contac_api`.

### 7. Ejecutar las migraciones

```bash
php artisan migrate
```

### 8. Iniciar el servidor

```bash
php artisan serve
```

La API estará disponible en:

```text
http://127.0.0.1:8000
```

## Endpoints de la API

La API utiliza el prefijo de versión `v1`.

**URL base:**

```text
http://127.0.0.1:8000/api/v1
```

### 1. Estado de la API

| Método | Endpoint | Descripción | Autenticación |
|---|---|---|---|
| GET | `/health` | Verificar el funcionamiento de la API | No |

### 2. Autenticación

| Método | Endpoint | Descripción | Autenticación |
|---|---|---|---|
| POST | `/auth/login` | Iniciar sesión y obtener un token | No |
| GET | `/auth/me` | Consultar el usuario autenticado | Sí |
| POST | `/auth/logout` | Cerrar sesión y revocar el token actual | Sí |

### 3. Gestión de contactos

| Método | Endpoint | Descripción | Autenticación |
|---|---|---|---|
| GET | `/contacts` | Listar los contactos del usuario | Sí |
| POST | `/contacts` | Crear un contacto | Sí |
| GET | `/contacts/{contact}` | Consultar un contacto específico | Sí |
| PATCH | `/contacts/{contact}` | Actualizar un contacto | Sí |
| DELETE | `/contacts/{contact}` | Eliminar un contacto | Sí |

## Autenticación

La API utiliza Laravel Sanctum para proteger los endpoints privados.

### Iniciar sesión

**Método:** POST

**Endpoint:**

```text
/api/v1/auth/login
```

**Ejemplo de solicitud:**

```json
{
    "email": "ana@example.com",
    "password": "secreta123",
    "device_name": "Insomnia"
}
```

Al iniciar sesión correctamente, la API devuelve un token de acceso.

### Utilizar el token

Para acceder a los endpoints protegidos, debes enviar el token en el encabezado HTTP:

```text
Authorization: Bearer TU_TOKEN
```

También debes enviar:

```text
Accept: application/json
```

### Cerrar sesión

**Método:** POST

**Endpoint:**

```text
/api/v1/auth/logout
```

Esta operación revoca el token utilizado en la solicitud.

## Gestión de contactos

### Crear un contacto

**Método:** POST

**Endpoint:**

```text
/api/v1/contacts
```

**Ejemplo de solicitud:**

```json
{
    "name": "Ana Torres",
    "phone_number": "3001234567",
    "address": "Calle 10 # 20-30",
    "email": "ana@example.com"
}
```

### Consultar contactos

**Método:** GET

```text
/api/v1/contacts
```

Devuelve los contactos pertenecientes al usuario autenticado.

### Consultar un contacto específico

**Método:** GET

```text
/api/v1/contacts/1
```

### Actualizar un contacto

**Método:** PATCH

```text
/api/v1/contacts/1
```

**Ejemplo de solicitud:**

```json
{
    "name": "Ana María Torres",
    "phone_number": "3019876543"
}
```

### Eliminar un contacto

**Método:** DELETE

```text
/api/v1/contacts/1
```

La operación devuelve el código HTTP `204 No Content` cuando se elimina correctamente.

## Validaciones y seguridad

- Las rutas privadas requieren autenticación.
- Cada usuario solamente puede consultar y administrar sus propios contactos.
- Un usuario no puede acceder a los contactos pertenecientes a otro usuario.
- Se validan los datos enviados al crear y actualizar contactos.
- Se controla la duplicidad de correo electrónico y teléfono dentro de la agenda del usuario.
- Los tokens se gestionan mediante Laravel Sanctum.

## Códigos de respuesta HTTP

| Código | Descripción |
|---|---|
| 200 | Solicitud procesada correctamente |
| 201 | Recurso creado correctamente |
| 204 | Recurso eliminado correctamente |
| 401 | Usuario no autenticado |
| 403 | Acceso no autorizado |
| 404 | Recurso no encontrado |
| 405 | Método HTTP no permitido |
| 422 | Error de validación |
| 429 | Demasiadas solicitudes |

## Pruebas

Para ejecutar las pruebas automatizadas del proyecto:

```bash
php artisan test
```

Para ejecutar las pruebas de contactos:

```bash
php artisan test --filter=WorkshopContactsTest
```

## Documentación de la API

Si Scramble está habilitado en el proyecto, puedes consultar la documentación interactiva en:

```text
http://127.0.0.1:8000/docs/api
```

## Autor

Proyecto desarrollado como parte del proceso de formación en **Análisis y Desarrollo de Software (ADSO)**.

**Repositorio:** [Contact API - GitHub](https://github.com/yendrismartinez55-hue/contact-api)

---

**Contact API** — API REST para la gestión de contactos con autenticación y protección de datos mediante Laravel Sanctum.