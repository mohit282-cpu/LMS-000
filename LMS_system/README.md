# Enterprise LMS

Production-oriented PHP 8.3/MySQL 8 learning management system foundation using MVC, PDO, CSRF protection, RBAC, audit logging, and Bootstrap 5.

## Requirements

- PHP 8.3 or newer with PDO MySQL
- MySQL 8 or newer
- Apache or Nginx pointing the web root to `public/`

## Installation

1. Copy `.env.example` to `.env` and update database credentials.
2. Create the MySQL database named in `.env`.
3. Import `database/schema.sql`.
4. Import `database/seed.sql`.
5. Create the first administrator:

```bash
php bin/create-admin.php
```

6. Serve the application with `public/` as the document root.

For local PHP testing:

```bash
php -S localhost:8000 -t public
```

## Included Modules

- Secure authentication and logout
- CSRF-protected forms
- Session hardening
- Role and permission management
- User management
- Student onboarding with user account creation
- Teacher onboarding with user account creation
- Course management
- Dashboard metrics and audit activity
- Normalized schema foundation for attendance, timetable, lessons, assignments, quizzes, exams, results, library, hostel, transport, fees, accounting, HR, payroll, notifications, settings, backups, and audit logs

## Security Notes

- No default administrator password is shipped.
- All database writes use prepared statements through PDO.
- Passwords are hashed with `password_hash()`.
- RBAC is enforced in controllers and navigation.
- All form writes require CSRF tokens.
- Output is escaped with `e()` in views.
- Audit logs are recorded for authentication and administrative writes.

## Apache

Use `public/` as the document root. The included `public/.htaccess` routes requests to `public/index.php`.

## Nginx

```nginx
root /path/to/LMS_system/public;
index index.php;

location / {
    try_files $uri $uri/ /index.php?$query_string;
}

location ~ \.php$ {
    include fastcgi_params;
    fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
    fastcgi_pass unix:/run/php/php8.3-fpm.sock;
}
```

