---
description: Set up development environment
---

# Development Environment Setup

Complete guide to set up the Kutoot backend development environment.

## Prerequisites

- PHP >= 7.4 (or 8.0+)
- Composer
- MySQL >= 5.7
- Git

## Installation Steps

### 1. Clone Repository (if not already cloned)

```bash
git clone <repository-url>
cd kutoot_backend
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Create Environment File

```bash
cp env.example .env
```

### 4. Configure Database

Edit `.env` file with your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kutoot_backend
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 5. Generate Application Key

// turbo
```bash
php artisan key:generate
```

### 6. Generate JWT Secret

```bash
php artisan jwt:secret
```

### 7. Create Database

Create the database in MySQL:

```sql
CREATE DATABASE kutoot_backend CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 8. Run Migrations

```bash
php artisan migrate
```

### 9. Seed Database (Optional)

```bash
php artisan db:seed
```

### 10. Create Storage Link

// turbo
```bash
php artisan storage:link
```

### 11. Set File Permissions (Linux/Mac)

```bash
chmod -R 775 storage bootstrap/cache
```

On Windows with Laravel Herd, permissions are usually handled automatically.

### 12. Clear Caches

// turbo
```bash
php artisan optimize:clear
```

### 13. Start Development Server

If using Laravel Herd, the site should be available at:
```
http://kutoot_backend.test
```

Or start the built-in server:
```bash
php artisan serve
```
Visit: http://localhost:8000

## Verify Installation

### Check Application Status
```bash
php artisan --version
```

### Test Database Connection
```bash
php artisan migrate:status
```

### View Routes
```bash
php artisan route:list
```

## Optional Configuration

### Configure Payment Gateway (Razorpay)

```env
RAZORPAY_KEY=your_razorpay_key
RAZORPAY_SECRET=your_razorpay_secret
```

### Configure Mail

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
```

### Configure SMS (Twilio)

```env
TWILIO_SID=your_sid
TWILIO_TOKEN=your_token
TWILIO_FROM=your_phone_number
```

## Troubleshooting

### "Class not found" errors
```bash
composer dump-autoload
```

### Database connection refused
- Verify MySQL is running
- Check `.env` database credentials
- Ensure database exists

### Permission denied errors (Linux/Mac)
```bash
sudo chown -R $USER:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

### JWT secret not set
```bash
php artisan jwt:secret
php artisan config:clear
```

## Testing the Setup

Test the API:
```bash
curl http://kutoot_backend.test/api/webinfo
```

Should return JSON with website information.

## Next Steps

- Import Postman collections from project root
- Review `instructions.md` in `.agent` folder
- Check available workflows in `.agent/workflows`
- Start development!
