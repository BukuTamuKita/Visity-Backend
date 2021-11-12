# Visity-backend
Visity adalah aplikasi appointment online dengan fitur KTP extractor

How To Install This Project
===

Use PHP Composer + Artisan
---

1. Install this Project Dependencies
```bash
> composer install
```

2. Update Project Dependencies
```bash
> composer update
```

3. Copy .env.example to .env

4. Generate Laravel Project Key
```bash
> php artisan key:generate
```

5. Generate JWT Secret Key
```bash
> php artisan jwt:secret
```

6. Configure settings in .env
```bash
> DB Settings
> SMTP Settings (optional, if you want to enable mail notification)
```  

7. Create database file according to .env settings

8. Generate Storage Link
```bash
> php artisan storage:link
```

9. Laravel Migrate and Seed
```cmd
> php artisan migrate --seed
```

10. Laravel Artisan Serve
```bash
> php artisan serve
```