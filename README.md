# Meetup API

API REST para la gestión de partidos de fútbol. Permite a los usuarios crear partidos, apuntarse, comentar y ver estadísticas de actividad.

## Tecnologías

- Laravel 12
- PHP 8.2
- MySQL
- Laravel Passport (autenticación)
- Spatie Laravel Permission (roles)
- L5-Swagger (documentación)
- Pest (tests)

## Requisitos

- PHP 8.2+
- Composer
- MySQL

## Instalación

1. Clona el repositorio:
```bash
git clone https://github.com/gregoridelrio/meetup-api.git
cd meetup-api
```

2. Instala las dependencias:
```bash
composer install
```

3. Copia el archivo de entorno:
```bash
cp .env.example .env
```

4. Configura la base de datos en `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nombre_de_tu_bbdd
DB_USERNAME=root
DB_PASSWORD=tu_password
```

5. Genera la clave de la aplicación:
```bash
php artisan key:generate
```

6. Ejecuta las migraciones y seeders:
```bash
php artisan migrate --seed
```

7. Genera las claves de Passport y crea el cliente personal:
```bash
php artisan passport:keys
php artisan passport:client --personal --no-interaction
```

8. Genera la documentación Swagger:
```bash
php artisan l5-swagger:generate
```

9. Inicia el servidor:
```bash
php artisan serve
```

## Documentación

Una vez iniciado el servidor, accede a la documentación Swagger en:
```
http://127.0.0.1:8000/api/documentation
```

## Tests

Para ejecutar los tests asegúrate de tener configurado el `phpunit.xml` con SQLite:

```phpunit.xml
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
```

Luego ejecuta:

```bash
php artisan test
```

## Endpoints

### Auth
| Método | Endpoint | Descripción | Auth |
|--------|----------|-------------|------|
| POST | /api/auth/register | Registrar usuario | No |
| POST | /api/auth/login | Login | No |
| POST | /api/auth/logout | Logout | Sí |

### Matches
| Método | Endpoint | Descripción | Auth |
|--------|----------|-------------|------|
| GET | /api/matches | Listar partidos | No |
| GET | /api/matches/{id} | Ver partido | No |
| POST | /api/matches | Crear partido | Sí |
| PUT | /api/matches/{id} | Editar partido | Sí |
| DELETE | /api/matches/{id} | Eliminar partido | Admin |

### Players
| Método | Endpoint | Descripción | Auth |
|--------|----------|-------------|------|
| GET | /api/matches/{id}/players | Ver jugadores | Sí |
| POST | /api/matches/{id}/players | Apuntarse | Sí |
| DELETE | /api/matches/{id}/players | Desapuntarse | Sí |

### Comments
| Método | Endpoint | Descripción | Auth |
|--------|----------|-------------|------|
| GET | /api/matches/{id}/comments | Ver comentarios | No |
| POST | /api/matches/{id}/comments | Comentar | Sí |

### Users
| Método | Endpoint | Descripción | Auth |
|--------|----------|-------------|------|
| GET | /api/users/matches | Mis partidos | Sí |
| GET | /api/users/stats | Mis estadísticas | Sí |

## Roles

| Rol | Descripción |
|-----|-------------|
| player | Usuario por defecto, puede crear y gestionar sus propios partidos |
| admin | Acceso total, puede eliminar cualquier partido |

## Usuarios de prueba

| Email | Password | Rol |
|-------|----------|-----|
| admin@email.com | Admin1234 | admin |
| pepito@email.com | Password1234 | player |
| juanito@email.com | Password431 | player |
