# Embassy CMS

This project is a PHP/MySQL content management system for the Embassy of the Republic of Congo in Kenya.

## Requirements
- PHP 8+
- MySQL 8+
- Apache or Nginx
- XAMPP / Laragon / MAMP

## Setup
1. Start Apache and MySQL.
2. Create a database named `embassy_cms`.
3. Import [database/schema.sql](database/schema.sql).
4. Ensure the `uploads` directory is writable.
5. The legacy schema dump has been archived at `database/archive/deprecated_embassy.sql` for reference only.
6. Open `/admin/login.php` in your browser.

## Default admin
- Username: `admin`
- Password: `password`

> Change this password after the first login.

## Structure
- `admin/` – admin pages
- `api/` – JSON endpoints for public content
- `config/` – database and app configuration
- `database/` – SQL schema
- `uploads/` – uploaded images and documents

## Security notes
- Keep PHP and MySQL updated.
- Do not expose database credentials to the frontend.
- Restrict the admin area to trusted users.
