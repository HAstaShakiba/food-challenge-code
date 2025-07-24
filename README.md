# Food Challenge API

A clean, testable, and fully documented Laravel 12 API for Paya-like money transfer requests with operator review, atomic transactions, and Swagger documentation.

---

## Table of Contents

- [Quick Start](#quick-start)
- [API Documentation](#api-documentation)
- [Technical Overview](#technical-overview)
- [Project Structure](#project-structure)
- [Key Concepts](#key-concepts)
- [How to Extend](#how-to-extend)
- [Useful Commands](#useful-commands)
- [Notes & Troubleshooting](#notes--troubleshooting)
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
   ```bash
   docker-compose up --build -d
   ```

3. **Run the migrations**
   ```bash
   docker-compose exec app php artisan migrate
   ```

4. **View the application**
   ```
   http://localhost:8080
   ```

---

## API Documentation

- Interactive Swagger UI:  
  ```
  http://localhost:8080/api/documentation
  ```
- To regenerate docs after any API change:
  ```bash
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

---

## Key Concepts

- **Atomicity:** All money operations are atomic (DB transactions, lockForUpdate)
- **Constants:** All status/type values are defined as constants in models
- **Validation:** All input is validated via FormRequest and custom rules
- **Exception Handling:** Centralized, with custom error codes/messages
- **API Documentation:** Fully documented with Swagger (OpenAPI 3)
- **No authentication:** API is public for demo/testing

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

## Notes & Troubleshooting

- If this is your first time running the project, make sure to run the migrations.
- If you encounter Swagger errors, clear cache and regenerate docs.
- For logs:
  ```bash
  docker-compose logs -f
  ```

---

## License & Contact

- **License:** MIT
- **Contact:** mehrdad.shakibafar@gmail.com

---
