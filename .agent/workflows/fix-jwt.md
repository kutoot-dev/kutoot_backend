---
description: Fix JWT token issues
---

# Fix JWT Token Issues

Common solutions for JWT authentication problems in the Kutoot backend.

## Symptoms

- "Token not provided" error
- "Token invalid" error
- "Token expired" error
- Authentication fails even with valid credentials

## Solutions

### 1. Regenerate JWT Secret

The most common fix - regenerate the JWT secret key:

```bash
php artisan jwt:secret
```

This will:
- Generate a new JWT secret
- Update your `.env` file automatically

### 2. Clear Configuration Cache

After regenerating the secret, clear config cache:

// turbo
```bash
php artisan config:clear
```

### 3. Verify .env Configuration

Check that your `.env` file has the JWT secret:

```env
JWT_SECRET=your_generated_secret_here
JWT_TTL=60
JWT_REFRESH_TTL=20160
```

### 4. Check JWT Configuration File

Verify `config/jwt.php` exists and is properly configured. If missing, publish the config:

```bash
php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"
```

### 5. Test Authentication

Try logging in again:

```bash
curl -X POST http://kutoot_backend.test/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"user@example.com","password":"password"}'
```

### 6. Check User Model

Ensure your `User` model implements JWT contract:

```php
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
```

### 7. Verify Auth Guard Configuration

Check `config/auth.php` has the API guard configured:

```php
'guards' => [
    'api' => [
        'driver' => 'jwt',
        'provider' => 'users',
    ],
],
```

## Testing JWT Tokens

### Get a Token
```bash
POST /api/auth/login
Body: {"email": "test@example.com", "password": "password"}
```

### Use the Token
```bash
GET /api/auth/me
Header: Authorization: Bearer YOUR_TOKEN_HERE
```

### Refresh Token
```bash
POST /api/auth/refresh
Header: Authorization: Bearer YOUR_TOKEN_HERE
```

### Logout
```bash
POST /api/auth/logout
Header: Authorization: Bearer YOUR_TOKEN_HERE
```

## Still Not Working?

1. Check Laravel logs: `storage/logs/laravel.log`
2. Ensure database connection is working
3. Verify user exists and password is correct
4. Check middleware is applied correctly to routes
5. Try clearing all caches: `php artisan optimize:clear`
