---
description: Run database migrations
---

# Run Database Migrations

This workflow guides you through running database migrations in the Kutoot backend project.

## Steps

### 1. Check Migration Status

First, check which migrations have been run:

```bash
php artisan migrate:status
```

### 2. Run Pending Migrations

Run all pending migrations:

// turbo
```bash
php artisan migrate
```

### 3. Clear Config Cache (if needed)

If you encounter configuration errors:

// turbo
```bash
php artisan config:clear
```

### 4. Refresh Migrations (Development Only)

⚠️ **WARNING**: This will drop all tables and re-run migrations. Only use in development!

```bash
php artisan migrate:fresh
```

### 5. Seed Database (Optional)

After refreshing migrations, seed the database:

```bash
php artisan db:seed
```

## Common Options

- Run a specific migration:
  ```bash
  php artisan migrate --path=/database/migrations/2026_01_22_000000_migration_name.php
  ```

- Rollback last migration batch:
  ```bash
  php artisan migrate:rollback
  ```

- Rollback specific steps:
  ```bash
  php artisan migrate:rollback --step=2
  ```

## Troubleshooting

- **"Table already exists"** - The migration was already run. Check `migrations` table in database.
- **Connection refused** - Verify database credentials in `.env` file.
- **Syntax error in SQL** - Check the migration file for errors.
