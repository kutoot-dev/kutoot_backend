<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

# Kutoot Backend - Laravel API

A Laravel-based backend API for the Kutoot platform with JWT authentication, Razorpay payment integration, and campaign management.

## Installation Guide

Follow these steps to set up the project on your local machine:

### 1. Clone the Repository

```bash
git clone <repository-url>
cd kutoot_backend
```

### 2. Create Environment File

Copy the example environment file and create your own `.env` configuration:

```bash
cp env.example.txt .env
```

### 3. Install Dependencies

Install PHP dependencies using Composer:

```bash
composer install
```

### 4. Configure Environment

Open the `.env` file and configure the following settings:

#### Database Configuration
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_username
DB_PASSWORD=your_database_password
```

#### Application Configuration
```env
APP_NAME="Kutoot Backend"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost
```

#### JWT Configuration
```env
JWT_SECRET=
```

#### Payment Gateway (Razorpay)
```env
RAZORPAY_KEY=your_razorpay_key
RAZORPAY_SECRET=your_razorpay_secret
```

### 5. Generate Application Key

Generate the Laravel application key:

```bash
php artisan key:generate
```

### 6. Generate JWT Secret

Generate the JWT authentication secret:

```bash
php artisan jwt:secret
```

### 7. Run Database Migrations

Create the database tables by running migrations:

```bash
php artisan migrate
```

If you want to seed sample data (optional):

```bash
php artisan db:seed
```

### 8. Create Storage Link

Create a symbolic link from `public/storage` to `storage/app/public`:

```bash
php artisan storage:link
```

### 9. Clear Cache (Optional)

Clear all cached configuration and routes:

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

### 10. Start Development Server

Start the Laravel development server:

```bash
php artisan serve
```

The application will be available at `http://localhost:8000`

## Common Laravel Commands

### Artisan Commands

- **Run migrations**: `php artisan migrate`
- **Rollback migrations**: `php artisan migrate:rollback`
- **Refresh migrations**: `php artisan migrate:refresh`
- **Run specific migration**: `php artisan migrate --path=/database/migrations/filename.php`
- **Create new migration**: `php artisan make:migration create_table_name`
- **Create controller**: `php artisan make:controller ControllerName`
- **Create model**: `php artisan make:model ModelName`
- **Clear caches**: `php artisan optimize:clear`

### Testing

Run PHPUnit tests:

```bash
php artisan test
```

or

```bash
vendor/bin/phpunit
```

## Project Structure

```
kutoot_backend/
├── app/
│   ├── Http/Controllers/    # API Controllers
│   ├── Models/              # Eloquent Models
│   ├── Middleware/          # Custom Middleware
│   └── Helpers/             # Helper Functions
├── config/                  # Configuration Files
├── database/
│   ├── migrations/          # Database Migrations
│   └── seeders/            # Database Seeders
├── routes/
│   ├── api.php             # API Routes
│   └── web.php             # Web Routes
├── storage/                # File Storage
└── public/                 # Public Assets
```

## API Features

- JWT Authentication
- Campaign Management
- Coupon Generation & Validation
- Payment Integration (Razorpay)
- User Dashboard & Statistics
- Newsletter Subscription
- Account Management

## Requirements

- PHP >= 7.4
- MySQL >= 5.7
- Composer
- Laravel 8.x
- OpenSSL PHP Extension
- PDO PHP Extension
- Mbstring PHP Extension
- Tokenizer PHP Extension
- XML PHP Extension

## Troubleshooting

### Permission Issues

If you encounter permission errors, set proper permissions:

```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Database Connection Issues

- Verify database credentials in `.env`
- Ensure MySQL service is running
- Check if database exists

### JWT Token Issues

- Regenerate JWT secret: `php artisan jwt:secret`
- Clear config cache: `php artisan config:clear`

## Support

For support and questions, please contact the development team.

---

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
