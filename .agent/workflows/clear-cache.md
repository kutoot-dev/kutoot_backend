---
description: Clear all application caches
---

# Clear Application Caches

This workflow clears all caches in the Laravel application to ensure fresh configurations and routes are loaded.

## Quick Clear (Recommended)

### Clear All Caches at Once

// turbo
```bash
php artisan optimize:clear
```

This single command clears:
- Configuration cache
- Route cache
- View cache
- Application cache
- Compiled classes

## Individual Cache Clearing

If you need to clear specific caches:

### 1. Configuration Cache

// turbo
```bash
php artisan config:clear
```

### 2. Route Cache

// turbo
```bash
php artisan route:clear
```

### 3. View Cache

// turbo
```bash
php artisan view:clear
```

### 4. Application Cache

// turbo
```bash
php artisan cache:clear
```

## When to Clear Caches

- After modifying `.env` file
- After changing route definitions
- After updating configuration files
- When troubleshooting unexpected behavior
- After pulling code changes that affect config/routes
- Before deploying to production

## Production Optimization

After clearing caches in production, rebuild them for better performance:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

⚠️ **Note**: Only cache in production, not in development!
