# Sistema Web de Gestión de Citas Médicas — Clínica Vida Salud

Laravel 12 · PHP 8.2+ · PostgreSQL · Blade + Tailwind CSS + Alpine · Pest

## Requisitos
PHP 8.2+ (extensión `pdo_pgsql`), Composer, Node.js 18+, PostgreSQL 14+.

## Instalación
```bash
composer install
cp .env.example .env          # ajuste DB_USERNAME y DB_PASSWORD
php artisan key:generate
# En PostgreSQL cree las bases:  clinica_vida_salud  y  clinica_vida_salud_test
#   CREATE DATABASE clinica_vida_salud;  CREATE DATABASE clinica_vida_salud_test;
#   \c clinica_vida_salud   ->  CREATE EXTENSION IF NOT EXISTS btree_gist;  (idem en la de pruebas)
php artisan migrate --seed
npm install && npm run build   # o npm run dev
php artisan serve              # http://127.0.0.1:8000
```

## Cuentas de prueba (contraseña: `password`)
| Rol | Correo |
|---|---|
| Administrador | admin@vidasalud.bo |
| Recepcionista | recepcion@vidasalud.bo |
| Médico | jperez@vidasalud.bo |
| Paciente | maria@correo.com |

## Pruebas
```bash
php artisan test
```
20 pruebas (cubren las 10 pruebas mínimas del examen).

## Estructura
- `app/Http/Controllers`, `app/Http/Requests`, `app/Http/Middleware/VerificarPermiso.php`
- `app/Services` — `CitaService` (programar con transacción) y `AgendaService` (horarios libres)
- `app/Models`, `app/Observers/BitacoraObserver.php`
- `database/migrations`, `database/seeders`
- `resources/views`, `resources/js` (`validaciones.js`, `citas.js`)
- `tests/Feature/ClinicaTest.php`

Casos de uso implementados (Ciclo #1): CU01 a CU08. Ciclo #2: CU09 a CU15.
