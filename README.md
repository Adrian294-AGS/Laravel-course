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
    php artisan migrate
    ```

This creates the required Laravel tables and the `employees` table with `id`, `name`, `position`, and timestamps.

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

### Register

**POST** `http://127.0.0.1:8000/api/register`

```json
{
   "name": "Ada Lovelace",
   "email": "ada@example.com",
   "password": "password123"
}
```

Use a unique email. Password must be at least 8 characters.

### Log in and get your token

**POST** `http://127.0.0.1:8000/api/login`

```json
{
   "email": "ada@example.com",
   "password": "password123"
}
```

Copy the `token` value from the response. For every employee request and logout, open Postman's **Authorization** tab, choose **Bearer Token**, and paste the token. Registration does not show its generated token, so log in to get one.

### Create an employee

**POST** `http://127.0.0.1:8000/api/employees`

```json
{
   "name": "Grace Hopper",
   "position": "Engineer"
}
```

### List employees

**GET** `http://127.0.0.1:8000/api/employees`

No body. Returns the employees saved in the database.

### Get one employee

**GET** `http://127.0.0.1:8000/api/employees/1`

Replace `1` with the employee's ID. No body.

### Update an employee

**PUT** `http://127.0.0.1:8000/api/employees/1`

Replace `1` with the employee's ID. Send one or both fields:

```json
{
   "name": "Grace Hopper",
   "position": "Senior Engineer"
}
```

### Delete an employee

**DELETE** `http://127.0.0.1:8000/api/employees/1`

Replace `1` with the employee's ID. No body.

### Log out

**POST** `http://127.0.0.1:8000/api/logout`

No body. This revokes the current token. Log in again to get another token.

All employee endpoints and logout require the Bearer token. Register and login do not.

## If something goes wrong

- `401`: log in and set the Bearer token in Postman.
- `404`: check the employee ID.
- `422`: check the field names and values in the JSON body.
- Database connection error: make sure XAMPP MySQL is running and `.env` has the correct database settings.
