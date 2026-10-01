# Setup and Postman Guide

Follow these steps to download the project, run it on Windows with XAMPP, and test its API in Postman. You only need Git for the download step; you do not need other Git commands to run the project.

## What you need

- Git
- XAMPP with PHP 8.3 or newer and MySQL
- Composer
- Postman

Node.js and npm are not needed for this API.

## 1. Download the project

Open a terminal in the folder where you want the project saved. Run:

```powershell
git clone https://github.com/Adrian294-AGS/Laravel-course.git
cd Laravel-course
```

The `cd` command opens the downloaded project folder. Keep using this terminal for the commands below.

## 2. Install the project

Run these commands from the project folder:

```powershell
composer install
Copy-Item .env.example .env
php artisan key:generate
```

If you already have a `.env` file, do not run `Copy-Item` again because it would replace your settings.

## 3. Create and connect the database

1. Open the XAMPP Control Panel and start **MySQL**.
2. Open [http://localhost/phpmyadmin](http://localhost/phpmyadmin).
3. Click **New**, enter `santiagoDb` as the database name, and click **Create**.
4. Open the `.env` file in the project folder. Set these values (change the username or password if your MySQL setup is different):

    ```dotenv
    APP_URL=http://127.0.0.1:8000
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=santiagoDb
    DB_USERNAME=root
    DB_PASSWORD=
    ```

    Save the file. Do not share or commit `.env`; it can contain private database settings.

5. Back in the terminal, create the tables:

    ```powershell
    php artisan config:clear
      php artisan migrate --seed
    ```

This creates the departments, employees, users, and Sanctum token tables, then seeds the development administrator and sample department and employee records. Existing employee rows from the earlier schema are retained and assigned generated legacy values by the migration.

## 4. Start the API

Run:

```powershell
php artisan serve
```

Leave this terminal open while using Postman. The base URL is usually `http://127.0.0.1:8000`. If Artisan prints a different address, use that address instead. The API base URL is:

```text
http://127.0.0.1:8000/api
```

## 5. Use the API in Postman

For each request with a body, choose **Body > raw > JSON** and add these headers:

```text
Accept: application/json
Content-Type: application/json
```

### Log in and get your token

**POST** `http://127.0.0.1:8000/api/login`

```json
{
   "email": "admin@example.com",
   "password": "CollegeAdmin123!"
}
```

The seeded account is for development only; change its password before deploying. Copy `data.token` from the response. For every write request and logout, open Postman's **Authorization** tab, choose **Bearer Token**, and paste the token. Department and employee read requests are public.

### List departments

**GET** `http://127.0.0.1:8000/api/departments`

No body. Use a department's `id` in the create-employee request.

### List and search employees

**GET** `http://127.0.0.1:8000/api/employees`

No body. Search and status filters can be combined as query parameters:

**GET** `http://127.0.0.1:8000/api/employees?search=admissions&employment_status=Active&per_page=10`

Search is case-insensitive across employee number, first name, last name, email, and department name. `employment_status` accepts `Active` or `Inactive`; `per_page` is optional and capped at 100.

### Create an employee

**POST** `http://127.0.0.1:8000/api/employees`

```json
{
   "department_id": 1,
   "employee_number": "EMP-2001",
   "first_name": "Jordan",
   "last_name": "Lee",
   "email": "jordan.lee@example.edu",
   "position": "Records Officer",
   "employment_status": "Active"
}
```

Use an existing department ID from `GET /api/departments`; employee number and email must be unique.

### List employees

**GET** `http://127.0.0.1:8000/api/employees`

No body. Returns the employees saved in the database.

### Get one employee

**GET** `http://127.0.0.1:8000/api/employees/1`

Replace `1` with the employee's ID. No body.

### Update an employee

**PUT** `http://127.0.0.1:8000/api/employees/1`

Replace `1` with the employee's ID. A `PATCH` can send only the fields being changed:

```json
{
   "position": "Senior Records Officer",
   "employment_status": "Inactive"
}
```

`PUT` and `PATCH` are both supported. `PUT` should include all required employee fields; `PATCH` can include a subset.

### Delete an employee

**DELETE** `http://127.0.0.1:8000/api/employees/1`

Replace `1` with the employee's ID. No body.

### Log out

**POST** `http://127.0.0.1:8000/api/logout`

No body. This revokes the current token. Log in again to get another token.

The employee create, update, and delete endpoints and logout require a Bearer token. Login and all department and employee read endpoints are public. There is no public registration endpoint; the seeded administrator is used to obtain a token.

## Endpoint summary

| Method | URI | Access | Success status | Purpose |
| --- | --- | --- | --- | --- |
| POST | `/api/login` | Public | 200 | Issue a Sanctum token |
| POST | `/api/logout` | Bearer token | 200 | Revoke the current token |
| GET | `/api/departments` | Public | 200 | List departments |
| GET | `/api/employees` | Public | 200 | Paginated, searchable employee list |
| GET | `/api/employees/{employee}` | Public | 200 | Read one employee with department |
| POST | `/api/employees` | Bearer token | 201 | Create an employee |
| PUT/PATCH | `/api/employees/{employee}` | Bearer token | 200 | Update an employee |
| DELETE | `/api/employees/{employee}` | Bearer token | 204 | Delete an employee |

Success responses use `message`, `data`, and `errors`; employee-list responses also include pagination in `meta`. Validation errors return 422, missing employees return 404, and unauthenticated protected requests return 401. Responses do not include password hashes, token hashes, or exception traces.

## Architecture and production note

Request → API route → Sanctum middleware on write routes → controller → Form Request validation → Eloquent model and relationship → database → Employee Resource → JSON response. For production, add rate limiting to login and write routes to reduce credential guessing and abusive requests; use a shared cache store when running multiple app instances.

## If something goes wrong

- `401`: log in and set the Bearer token in Postman.
- `404`: check the employee ID.
- `422`: check the field names and values in the JSON body.
- Database connection error: make sure XAMPP MySQL is running and `.env` has the correct database settings.
