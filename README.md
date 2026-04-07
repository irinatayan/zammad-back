# Zammad Back

PHP REST API backend for a Zammad-based ticket management system. Acts as middleware between the frontend and the Zammad API — handles local user authentication (JWT), role-based access control, ticket ownership tracking, and proxies operations to the Zammad API.

## Related Repository

**[zammad-ui](https://github.com/irinatayan/zammad-ui)** — React frontend that consumes this API.

---

## Features

- JWT authentication with access and refresh tokens
- Role-based access control (admin / user)
- Ticket management: list, filter, search, update state / priority / owner
- Bulk ticket operations
- File and voice message attachments
- Local user management (create, list)
- Proxies requests to Zammad API via official PHP client

## Tech Stack

- **PHP 8.2+** with custom lightweight MVC framework
- **MySQL / MariaDB** via PDO
- **Zammad API client** (`zammad/zammad-api-client-php`)
- **JWT** (HS256) for authentication
- **phpdotenv** for environment configuration
- Apache / Nginx as web server

## Project Structure

```
├── public/
│   └── index.php                   # Entry point
├── src/
│   ├── App/
│   │   ├── Config/
│   │   │   ├── Routes.php          # Route definitions
│   │   │   └── Middleware.php      # Middleware registration
│   │   ├── Controllers/            # Request handlers
│   │   ├── Services/               # Business logic
│   │   ├── Middleware/             # Auth, CSRF, session middleware
│   │   ├── Models/                 # Data models
│   │   └── container-definitions.php  # Dependency injection config
│   └── Framework/                  # Router, DI container, Database, Validator
├── db.sql                          # Database schema
├── .env.example                    # Environment variables template
└── composer.json
```

## Getting Started

### Prerequisites

- PHP 8.2+
- MySQL or MariaDB
- Composer
- Apache or Nginx (with URL rewriting enabled)
- A running [Zammad](https://zammad.org) instance

### Installation

```bash
git clone https://github.com/irinatayan/zammad-back.git
cd zammad-back
composer install
cp .env.example .env
```

Edit `.env` with your configuration (see [Environment Variables](#environment-variables)).

```bash
mysql -u<user> -p<password> <database_name> < db.sql
```

Point your web server document root to the `public/` directory.

For local development:

```bash
php -S localhost:8000 -t public/
```

## Environment Variables

| Variable | Description | Example |
|---|---|---|
| `APP_ENV` | Application environment | `development` |
| `DB_DRIVER` | Database driver | `mysql` |
| `DB_HOST` | Database host | `localhost` |
| `DB_PORT` | Database port | `3306` |
| `DB_NAME` | Database name | `zammad_db` |
| `DB_USER` | Database user | `db_user` |
| `DB_PASS` | Database password | `secret` |
| `SECRET_KEY` | JWT signing secret (min 32 chars) | `your_random_secret` |
| `ZAMMAD_URL` | Zammad instance URL (with trailing slash) | `https://your.zammad.com/` |
| `ZAMMAD_USERNAME` | Zammad API user email | `admin@example.com` |
| `ZAMMAD_PASSWORD` | Zammad API user password | `password` |
| `ALLOWED_ORIGIN` | CORS allowed origin (frontend URL) | `http://localhost:5173` |

## API Endpoints

### Authentication

| Method | Path | Description |
|---|---|---|
| `POST` | `/backend/login` | Login, returns access + refresh tokens |
| `POST` | `/backend/refresh` | Refresh access token |
| `GET` | `/logout` | Logout (requires auth) |

### Tickets (require auth)

| Method | Path | Description |
|---|---|---|
| `GET` | `/backend/tickets` | List tickets (paginated, filterable, sortable) |
| `GET` | `/backend/ticket` | Get single ticket with articles |
| `GET` | `/backend/ticket/search` | Full-text search |
| `GET` | `/backend/tickets/bulk-update-info` | Get states, priorities, agents |
| `POST` | `/backend/ticket/owner` | Update ticket owner |
| `POST` | `/backend/ticket/priority` | Update ticket priority |
| `POST` | `/backend/ticket/state` | Update ticket state |
| `POST` | `/backend/tickets/owner` | Bulk update owners |
| `POST` | `/backend/tickets/priority` | Bulk update priorities |
| `POST` | `/backend/tickets/state` | Bulk update states |

### Files & Comments

| Method | Path | Description |
|---|---|---|
| `POST` | `/backend/test` | Add note/comment to ticket |
| `POST` | `/backend/upload` | Upload file (FilePond) |
| `GET` | `/backend/voice` | Retrieve voice attachment |

### Users (require auth + admin role)

| Method | Path | Description |
|---|---|---|
| `GET` | `/backend/users` | List all users |
| `POST` | `/backend/users/create` | Create new user |

## Database Schema

Three tables:

- **`user`** — local user accounts with bcrypt-hashed passwords and roles (`admin` / `user`)
- **`ticket_user`** — maps local users to Zammad ticket IDs (ticket ownership)
- **`refresh_token`** — whitelist of valid refresh tokens (HMAC-SHA256 hashes)
