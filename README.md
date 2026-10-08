# OdontoTrack

Sistema integral de citas y expedientes odontológicos para la Clínica Odontológica Sonrisas.
Proyecto del curso DWS901 (Universidad Don Bosco), Sprint II: Desarrollo Back-End y Persistencia.

## Stack

| Elemento | Tecnología |
|---|---|
| Lenguaje | PHP 8.3 o superior |
| Framework | Laravel 13 (arquitectura MVC) |
| Vistas | Blade, con Bootstrap 5 y FullCalendar por CDN |
| Base de datos | MySQL 8 (InnoDB), migraciones y seeders de Laravel |
| Acceso a datos | Eloquent ORM |
| Autenticación | Sesiones de Laravel y contraseñas con bcrypt (`Hash`) |
| Control de acceso | Middleware `rol` (RECEPCIONISTA / ODONTOLOGO) |
| Pruebas | PHPUnit (`php artisan test`) |

No se necesita Node.js ni compilar recursos: la interfaz usa Bootstrap y FullCalendar desde CDN.

## Requisitos previos

- PHP 8.3 o superior con las extensiones `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`, `xml`, `ctype` y `curl`
- Composer 2
- MySQL 8 en ejecución
- Git

Verificación rápida:

```bash
php -v
composer -V
mysql --version
php -m
```

## Instalación y ejecución

```bash
git clone https://github.com/jonhwill1209-wq/OdontoTrack.git
cd OdontoTrack
git checkout develop

composer install
copy .env.example .env          # en Git Bash o Linux/Mac: cp .env.example .env
php artisan key:generate
```

Crea las bases de datos vacías (desde MySQL Workbench o la consola de MySQL):

```sql
CREATE DATABASE odontotrack CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE odontotrack_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Edita `.env` y completa `DB_USERNAME` y `DB_PASSWORD`. Después:

```bash
php artisan migrate --seed
php artisan serve               # http://127.0.0.1:8000
```

Para reconstruir la base desde cero: `php artisan migrate:fresh --seed`.

## Credenciales de prueba (solo desarrollo)

| Rol | Correo | Contraseña |
|---|---|---|
| Recepcionista | recepcion@odontotrack.test | Odonto2026! |
| Odontólogo | doctor1@odontotrack.test | Odonto2026! |
| Odontólogo | doctor2@odontotrack.test | Odonto2026! |

Las cuentas las crea el seeder y no deben usarse fuera de un entorno local.

## Comandos útiles

| Comando | Descripción |
|---|---|
| `php artisan serve` | Servidor de desarrollo |
| `php artisan migrate --seed` | Crea las tablas y carga datos de prueba |
| `php artisan migrate:fresh --seed` | Reinicia la base y vuelve a cargar los datos |
| `php artisan route:list` | Lista las rutas registradas |
| `php artisan test` | Ejecuta las pruebas |
| `./vendor/bin/pint` | Da formato al código (estilo Laravel) |

## Estructura del proyecto

```
app/
  Enums/                 Rol
  Exceptions/            HorarioOcupadoException
  Http/
    Controllers/         Reciben la petición y devuelven la vista
    Middleware/          CheckRole (alias "rol")
    Requests/            Validación de formularios (FormRequest)
  Models/                Entidades Eloquent
  Services/              Reglas de negocio (CitaService, ...)
database/
  migrations/            Una migración por tabla
  seeders/               Datos de prueba
resources/views/         Plantillas Blade (layouts, partials y vistas por módulo)
routes/
  web.php                Rutas generales
  modules/               Un archivo de rutas por módulo
tests/Feature/           Pruebas por módulo
docs/                    Documento maestro, diagramas y evidencias
database/odontotrack.sql Respaldo de la base de datos
```

## Roles y permisos

| Funcionalidad | Recepcionista | Odontólogo |
|---|:---:|:---:|
| Iniciar sesión y Dashboard | ✓ | ✓ |
| Registrar, actualizar y dar de baja pacientes | ✓ | — |
| Consultar pacientes | ✓ | ✓ |
| Consultar, crear, modificar y cancelar citas | ✓ | ✓ |
| Expediente, historial médico, odontograma y tratamientos | — | ✓ |

La baja de un paciente es lógica (`activo = false`) para conservar su expediente.

## Regla de negocio principal: citas sin empalme

`App\Services\CitaService` crea y modifica citas dentro de una transacción que bloquea las filas del
odontólogo y del consultorio (`lockForUpdate`) y comprueba que no exista otra cita `PROGRAMADA` que se
superponga (`inicio_nuevo < fin_existente` y `fin_nuevo > inicio_existente`). Si hay choque, la cita no se
guarda y se muestra un mensaje claro.

## Flujo de trabajo en Git

- `main`: versión estable y demostrable. Solo recibe merges desde `develop`.
- `develop`: integración del equipo.
- `feature/<modulo>-<descripcion>`: una rama por tarea, creada desde `develop`.
- Commits con el formato `tipo(modulo): descripción` (por ejemplo, `feat(citas): valida solapamiento`).
- Todo cambio entra mediante Pull Request con al menos una aprobación.

## Pruebas

```bash
php artisan test
```

Las pruebas usan la base `odontotrack_test` y cubren conexión, rutas, autenticación, consultas y
operaciones CRUD. Las evidencias y las incidencias conocidas se documentan en `docs/`.

## Enlaces

- Product Backlog (Asana): _pendiente de completar_
- Prototipo (Figma): https://www.figma.com/proto/lmR505GqiY1BodqzwLWy9a/OdontoTrack---Prototipo-Web?node-id=5-15&t=IeuclG3tZn5Ervd2-1
- Repositorio: https://github.com/jonhwill1209-wq/OdontoTrack

## Integrantes

| Nombre | Carnet | Rol en el Sprint II |
|---|---|---|
| Jonathan Wilfredo Bonilla Erazo | MM120518 | |
| Francisco José Duran Crespín | DC253061 | |
| Norma Susana García Galdamez | GG253588 | |
| María Ester López Samour | LS253257 | |
| Darwin Ezequiel Martínez Rosales | MR251336 | |
| Sophia Marcela Guzman Ayala | GA231577 | |

## Limitaciones conocidas y tareas pendientes

_Se completa al cerrar el sprint._
