# LavaLust Framework

> A lightweight, fast PHP framework built for developers who want clean MVC architecture without unnecessary complexity or performance overhead.

[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](https://opensource.org/licenses/MIT)
[![PHP Version](https://img.shields.io/badge/PHP-%3E%3D7.4-8892BF)](https://www.php.net/)
[![GitHub Stars](https://img.shields.io/github/stars/ronmarasigan/lavalust?style=flat)](https://github.com/ronmarasigan/lavalust/stargazers)

---

## Overview

**LavaLust** is an open-source PHP framework that follows the **MVC (Model–View–Controller)** architectural pattern. It is designed for developers who need a structured, maintainable, and scalable foundation — without the bloat of heavier modern frameworks.

Whether you are building a simple web application, a REST API, or a teaching project, LavaLust provides the right tools with minimal friction.

---

## Features

| Feature | Description |
|---|---|
| **MVC Architecture** | Clean separation of Models, Views, and Controllers for organized, maintainable code |
| **Built-in Routing** | Flexible URL routing that maps requests to controllers with minimal configuration |
| **Libraries & Helpers** | Reusable components for sessions, forms, validation, and database access |
| **Modular Design** | Scalable structure that supports clean organization as your application grows |
| **REST API Support** | First-class support for building RESTful APIs using LavaLust conventions |
| **ORM-like Models** | Simplified, readable database interaction without a heavy abstraction layer |

---

## Requirements

- PHP 7.4 or higher
- A web server with URL rewriting support (Apache `.htaccess` or Nginx config)
- Composer (optional, for dependency management)

---

## Installation

**Clone the repository:**

```bash
git clone https://github.com/ronmarasigan/lavalust.git
cd lavalust
```

**Or download a release directly:**

```bash
wget https://github.com/ronmarasigan/lavalust/archive/refs/heads/main.zip
unzip main.zip
```

Configure your web server to point to the project root and ensure `mod_rewrite` (Apache) or equivalent is enabled.

---

## Quick Start

### 1. Define a Route

**File:** `app/config/routes.php`

```php
$router->get('/', 'Welcome::index');
$router->get('/about', 'Welcome::about');
$router->post('/users/store', 'Users::store');
```

### 2. Create a Controller

**File:** `app/controllers/Welcome.php`

```php
<?php

class Welcome extends Controller
{
    public function index()
    {
        $data['title'] = 'Home';
        $this->call->view('welcome', $data);
    }

    public function about()
    {
        $this->call->view('about');
    }
}
```

### 3. Create a View

**File:** `app/views/welcome.php`

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?></title>
</head>
<body>
    <h1>Welcome to LavaLust Framework</h1>
    <p>Lightweight. Fast. MVC.</p>
</body>
</html>
```

### 4. Create a Model

**File:** `app/models/User_model.php`

```php
<?php

class User_model extends Model
{
    protected $table = 'users';

    public function getAll()
    {
        return $this->db->table($this->table)->get()->getResult();
    }

    public function findById(int $id)
    {
        return $this->db->table($this->table)
                        ->where('id', $id)
                        ->get()
    }
}
```

---

## Project Structure

```
lavalust/
├── app/
│   ├── config/          # Application configuration (database, routes, etc.)
│   ├── controllers/     # Controller classes
│   ├── models/          # Model classes
│   ├── views/           # View templates
│   └── libraries/       # Custom libraries and helpers
├── scheme/              # Core framework files (do not modify)
├── public/              # Publicly accessible entry point
│   └── index.php
└── runtime/            # Cache, logs, and uploads (must be writable)
```

---

## Configuration

### Database

**File:** `app/config/database.php`

```php
$database['main'] = array(
    'driver'	=> getenv('DB_DRIVER') ?: '',
    'hostname'	=> getenv('DB_HOST') ?: '',
    'port'		=> getenv('DB_PORT') ?: '',
    'username'	=> getenv('DB_USER') ?: '',
    'password'	=> getenv('DB_PASSWORD') ?: '',
    'database'	=> getenv('DB_NAME') ?: '',
    'charset'	=> getenv('DB_CHARSET') ?: '',
    'dbprefix'	=> getenv('DB_PREFIX') ?: '',
    'ssl_ca'    => getenv('DB_SSL_CA') ?: '',
    // Optional for SQLite
    'path'      => ''
);
```

### Base URL

**File:** `app/config/config.php`

```php
$config['base_url'] = 'http://localhost:3000/';
```

---

## Building a REST API

LavaLust supports REST API development out of the box. Controllers can return JSON responses for API endpoints.

```php
<?php

class Api extends Controller
{
    $this->call->library('api');

    public function users()
    {
        $this->api->require_method('GET');
        $auth = $this->api->require_jwt(); 

        $this->call->model('User_model');
        $users = $this->User_model->getAll();

        $this->api->respond(['data' => $users]);
    }
}
```

Route definition:

```php
$router->get('/api/users', 'Api::users');
```

---

## Product Management Lab Application

This project includes a Vue 3 product-management UI at `/` and a LavaLust JSON API. The browser calls the API; it never connects directly to MySQL. API routes use `app/config/api.php`, and database credentials and signing keys belong in the untracked root `.env` file.

### Configure the database and API

Set these values in `.env` (do not commit the file):

```dotenv
DB_DRIVER=mysql
DB_HOST=your-aiven-host
DB_PORT=your-aiven-port
DB_USER=your-aiven-user
DB_PASSWORD=your-aiven-password
DB_NAME=your-aiven-database
DB_CHARSET=utf8mb4
DB_SSL_CA=
JWT_SECRET=your-random-secret-at-least-32-characters
REFRESH_TOKEN_KEY=a-different-random-secret-at-least-32-characters
```

Generate both signing keys with `php lava jwt:generate`. The API helper is enabled in `app/config/api.php`. The checked-in `003_rename_product_table` migration renames the existing singular `product` table to the assignment's `products` table while preserving its rows, or creates the table if neither name exists. It has already run against the configured database. If setting up a new database, temporarily enable migrations in `app/config/migration.php`, run `php lava migration run`, and disable migrations again. If both `product` and `products` tables already exist, reconcile their records before running the migration. Public HTTP migration routes are intentionally not registered.

For MySQL TLS, download the CA certificate for your Aiven service and set `DB_SSL_CA` to the path of that PEM file. When configured, LavaLust enables TLS and verifies the MySQL server certificate.

Run locally with:

```bash
php lava serve
```

Open `http://127.0.0.1:3000/`, register a user, then manage products. Passwords are hashed before storage. Access tokens expire after 15 minutes; the UI uses refresh tokens and logs out by revoking the refresh token.

### API routes

| Method | Endpoint | Access |
| --- | --- | --- |
| POST | `/api/auth/register` | Public; creates a user account |
| POST | `/api/auth/login` | Public; returns access and refresh tokens |
| POST | `/api/auth/refresh` | Public; rotates a valid refresh token |
| POST | `/api/auth/logout` | Bearer access token |
| GET | `/api/auth/me` | Bearer access token |
| GET | `/api/products` | Bearer access token |
| GET | `/api/products/{id}` | Bearer access token |
| POST | `/api/products` | Bearer access token |
| PUT, PATCH | `/api/products/{id}` | Bearer access token |
| DELETE | `/api/products/{id}` | Bearer access token |

Send JSON request bodies for registration and product writes. Product fields are `product_name`, `description`, `price`, and `quantity`. API responses use JSON; invalid inputs, missing records, and unauthenticated requests return appropriate HTTP errors. `PATCH` accepts partial fields; `PUT` sends the full product.

### Deploy to Render

Create a Render Blueprint from `render.yaml`. Add the Aiven CA PEM as a Render secret file (for example `/etc/secrets/aiven-ca.pem`) and set `DB_SSL_CA` to that exact path. Provide the Aiven host, port, username, password, database, and fresh, distinct `JWT_SECRET` and `REFRESH_TOKEN_KEY` values in Render's environment setup. Never copy `.env` into the repository or commit credentials; `.dockerignore` excludes it from the image build context. Apache listens on Render's default web-service port, `10000`. Deploy with HTTPS and use the service URL as the frontend/API origin. The included Vue UI uses the same origin for API calls, so it is deployed together with the API.

If hosting the frontend separately, set `ALLOW_ORIGIN` in the backend Render service's **Environment** settings to the frontend's exact origin, for example `https://your-frontend.onrender.com` (scheme and host only; do not include a path or trailing slash). The `render.yaml` Blueprint declares this variable as a value to provide. Also configure the frontend to send API requests to the backend service URL. Save the environment change and redeploy the backend for it to take effect. Register an account through the UI after deployment.

---

## Philosophy

LavaLust is built on a single principle: **minimal core, maximum control.**

Modern frameworks often add layers of abstraction that benefit large enterprise teams but get in the way of developers who want to understand exactly what their code is doing. LavaLust provides structure and utilities without hiding the underlying logic — making it an excellent choice for:

- **Rapid prototyping** — Get an application running in minutes
- **Learning MVC** — Understand how each architectural layer works
- **Lightweight production apps** — Deploy without dragging in unused dependencies
- **Teaching PHP development** — Clear conventions, readable source code

---

## Documentation

Full documentation is available at **[https://lavalust.netlify.app](https://lavalust.netlify.app)**

Topics covered include:

- Installation and server configuration
- Routing: static, dynamic, and grouped routes
- Controllers and request handling
- Models and query builder
- Views, layouts, and partials
- Built-in libraries (sessions, form validation, file upload)
- Helper functions
- REST API development
- Security best practices

---

## Contributing

Contributions are welcome. To contribute:

1. Fork the repository
2. Create a feature branch: `git checkout -b feature/your-feature-name`
3. Commit your changes: `git commit -m "Add your feature description"`
4. Push to your branch: `git push origin feature/your-feature-name`
5. Open a pull request against `main`

Please ensure your code follows the existing style conventions and includes relevant documentation or comments where appropriate.

---

## Roadmap

- [ ] CLI tool for generating controllers, models, and migrations
- [ ] Middleware support
- [ ] Improved query builder with relationship support
- [ ] Enhanced error handling and debugging tools

---

## License

LavaLust Framework is open-source software licensed under the **[MIT License](https://opensource.org/licenses/MIT)**.

---

## Links

- **GitHub Repository:** [https://github.com/ronmarasigan/lavalust](https://github.com/ronmarasigan/lavalust)
- **Documentation:** [https://lavalust.netlify.app](https://lavalust.netlify.app)
- **Report an Issue:** [https://github.com/ronmarasigan/lavalust/issues](https://github.com/ronmarasigan/lavalust/issues)