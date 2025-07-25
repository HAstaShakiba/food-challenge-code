# Food Challenge API

A clean, testable, and fully documented Laravel 12 API for Paya-like money transfer requests with operator review, atomic transactions, and Swagger documentation.

---

## Table of Contents

- [Quick Start](#quick-start)
- [API Examples](#api-examples)
- [API Documentation](#api-documentation)
- [Technical Overview](#technical-overview)
- [Project Structure](#project-structure)
- [How to Extend](#how-to-extend)
- [Useful Commands](#useful-commands)
- [Troubleshooting](#database--troubleshooting)
- [License & Contact](#license--contact)

---

## Quick Start

1. **Create the .env file**
   In the project root, create a `.env` file (or copy from `.env.example`) and set the database values as follows:
   ```
   DB_CONNECTION=mysql
   DB_HOST=db
   DB_PORT=3306
   DB_DATABASE=laravel
   DB_USERNAME=laravel
   DB_PASSWORD=secret
   ```

2. **Build and start the containers**
   ```sh
   docker-compose up --build -d
   ```

3. **Run the migrations**
   ```sh
   docker-compose exec app php artisan migrate
   ```

4. **Create a test user with sufficient balance**
   ```sh
   docker-compose exec app php artisan tinker
   >>> \App\Models\User::factory()->create(['id' => 1, 'balance' => 10000000]);
   exit
   ```
   Or, if the user already exists:
   ```sh
   docker-compose exec app php artisan tinker --execute="\App\Models\User::where('id', 1)->update(['balance' => 10000000]);"
   ```

5. **View the application**
   [http://localhost:8080](http://localhost:8080)

---

## API Examples

### 1. Create a Sheba transfer request
```sh
curl -X POST http://localhost:8080/api/sheba \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "user_id": 1,
    "price": 500000,
    "fromShebaNumber": "IR820540102680020817909002",
    "toShebaNumber": "IR062960000000100324200001",
    "note": "Test transfer"
  }'
```

### 2. List Sheba transfer requests
```sh
curl -H "Accept: application/json" http://localhost:8080/api/sheba
```

### 3. Confirm or cancel a Sheba request (e.g. id=2)
```sh
curl -X POST http://localhost:8080/api/sheba/2 \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"status": "confirmed", "note": "Test confirmation"}'
```

---

## API Documentation

- Interactive Swagger UI:  [http://localhost:8080/api/documentation](http://localhost:8080/api/documentation)
- To regenerate docs after any API change:
  ```sh
  docker-compose exec app php artisan l5-swagger:generate
  ```
- All main endpoints and models are fully documented.

---

## Technical Overview

- **Framework:** Laravel 12 (API-first, service-oriented)
- **Patterns:** Service Layer, Repository Pattern, DTOs, API Resources, Form Requests, Custom Rules, Centralized Exception Handling
- **Testing:** Unit (mocked), Feature (DB refresh), Code coverage (Xdebug)
- **API Docs:** OpenAPI 3 (Swagger), auto-generated

---

## Project Structure

- `app/Models/` — Eloquent models (User, ShebaRequest, Transaction)
- `app/Http/Controllers/` — API controllers (ShebaController)
- `app/Http/Requests/` — FormRequest classes for validation
- `app/Http/Resources/` — API Resource classes for output formatting
- `app/Services/` — Business logic (ShebaService)
- `app/Repositories/` — Data access abstraction (interfaces + implementations)
- `app/DTOs/` — Data Transfer Objects for structured, type-safe data
- `app/Rules/` — Custom validation rules (e.g., Sheba)
- `app/Exceptions/` — Custom exceptions (e.g., InsufficientBalanceException)
- `database/migrations/` — Database schema
- `tests/Unit/` — Unit tests (service logic)
- `tests/Feature/` — Feature/API tests
- `app/Swagger/` — OpenAPI/Swagger annotations (schemas, info)
- `docker/` — Docker-related files (Dockerfile, entrypoint, nginx config)

---

## How to Extend

- **Add new endpoints:** Create new Controller/Service/Repository/DTO/Resource as needed
- **Add new models:** Create migration, model, repository, and update service logic
- **Add new validation:** Create new Rule or extend FormRequest
- **Add new API docs:** Add OpenAPI annotations to controllers/resources/requests

---

## Useful Commands

| Command | Description |
| ------- | ----------- |
| `docker-compose up --build -d` | Build and start all containers |
| `docker-compose exec app php artisan migrate` | Run database migrations |
| `docker-compose exec app php artisan test` | Run all tests (unit & feature) |
| `docker-compose exec app php artisan l5-swagger:generate` | Generate Swagger API documentation |
| `docker-compose exec app composer install` | Install PHP dependencies |
| `docker-compose exec app composer dump-autoload` | Regenerate Composer autoload files |
| `docker-compose logs -f` | View container logs |
| `docker-compose down` | Stop all containers |

---

## Troubleshooting

- If you encounter Swagger errors, clear cache and regenerate docs.
- For logs:
  ```sh
  docker-compose logs -f
  ```

---

## License & Contact

- **License:** MIT
- **Contact:** mehrdad.shakibafar@gmail.com

---
