# Setup and Postman API Guide

This guide covers setting up the Laravel API with XAMPP MySQL and testing it in Postman.

## Requirements

- PHP 8.3 or newer, with the PDO MySQL extension enabled
- Composer
- MySQL (for example, the MySQL service included with XAMPP)
- Postman

Node.js and npm are not required to run these API endpoints.

## Install and configure

1. Clone or download the project, then open a terminal in the project directory (the directory containing `artisan` and `composer.json`).

2. Install PHP dependencies:

   ```powershell
   composer install
   ```

3. Create `.env` from the example if this is a fresh setup. Do not overwrite an existing `.env`:

   ```powershell
   if (!(Test-Path .env)) { Copy-Item .env.example .env }
   ```

4. Create the MySQL database. Start MySQL from the XAMPP control panel, then run this SQL in phpMyAdmin's SQL tab or a MySQL client:

   ```sql
   CREATE DATABASE santiagoDb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

5. Edit `.env` and set the database connection values. Replace the username or password if your MySQL account differs from the XAMPP default:

   ```dotenv
   APP_URL=http://127.0.0.1:8000
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=santiagoDb
   DB_USERNAME=root
   DB_PASSWORD=
   ```

   Keep `.env` private; do not commit database credentials.

6. Generate the Laravel application key and apply the migrations:

   ```powershell
   php artisan key:generate
   php artisan config:clear
   php artisan migrate
   ```

   Migrations create the Laravel support tables, users, Sanctum tokens, and the `employees` table (`id`, `name`, `position`, timestamps). Check the result with:

   ```powershell
   php artisan migrate:status
   ```

7. Start the API server:

   ```powershell
   php artisan serve
   ```

   Unless Artisan prints a different address, use `http://127.0.0.1:8000` as the base URL. The API base URL is `http://127.0.0.1:8000/api`.

## Postman headers and authentication

For requests with JSON bodies, choose **Body > raw > JSON**. Send these headers:

```text
Accept: application/json
Content-Type: application/json
```

The register and login endpoints are public. Logout and all employee endpoints require a Sanctum token. Register first, then log in: registration currently creates a token but does not include it in its response. Copy the `token` returned by login and set Postman's **Authorization** type to **Bearer Token** for protected requests.

## API endpoints

### 1. Register

`POST http://127.0.0.1:8000/api/register`

Body:

```json
{
  "name": "Ada Lovelace",
  "email": "ada@example.com",
  "password": "password123"
}
```

The name is required (up to 100 characters), the email must be unique, and the password must be at least 8 characters. A successful response has status `201`.

### 2. Log in

`POST http://127.0.0.1:8000/api/login`

Body:

```json
{
  "email": "ada@example.com",
  "password": "password123"
}
```

Copy the `token` property from the successful response for use as the Bearer token.

### 3. List employees

`GET http://127.0.0.1:8000/api/employees`

Requires Bearer token. No body. Returns an `employees` array.

### 4. Create an employee

`POST http://127.0.0.1:8000/api/employees`

Requires Bearer token.

Body:

```json
{
  "name": "Grace Hopper",
  "position": "Engineer"
}
```

Both fields are required. A successful response has status `201` and includes the created `employee`.

### 5. Get one employee

`GET http://127.0.0.1:8000/api/employees/1`

Replace `1` with the employee ID. Requires Bearer token. No body.

### 6. Update an employee

`PUT http://127.0.0.1:8000/api/employees/1`

Replace `1` with the employee ID. Requires Bearer token. Send one or both fields; any provided field is required to be a non-empty string of up to 255 characters.

```json
{
  "name": "Grace Hopper",
  "position": "Senior Engineer"
}
```

### 7. Delete an employee

`DELETE http://127.0.0.1:8000/api/employees/1`

Replace `1` with the employee ID. Requires Bearer token. No body.

### 8. Log out

`POST http://127.0.0.1:8000/api/logout`

Requires Bearer token. No body. This revokes the current token; log in again to get a new one.

## Common responses

- `401 Unauthorized`: missing or invalid Bearer token on a protected endpoint, or incorrect login credentials.
- `404 Not Found`: the requested employee ID does not exist.
- `422 Unprocessable Content`: request validation failed. The response includes details for the invalid fields.
