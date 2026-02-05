# Kutoot Backend - Copilot Instructions

This is a Laravel 8.x multi-tenant marketplace API with JWT authentication, supporting multiple user roles (Admin, Seller, Customer, Deliveryman) with both API and web panel interfaces.

## Build, Test, and Lint Commands

### Setup
```bash
# Install dependencies
composer install
npm install

# Setup environment
php artisan key:generate
php artisan jwt:secret
php artisan storage:link

# Database
php artisan migrate
php artisan db:seed  # optional
```

### Testing
```bash
# Run all tests
php artisan test
# or
vendor/bin/phpunit

# Run specific test file
vendor/bin/phpunit tests/Feature/ExampleTest.php

# Run specific test method
vendor/bin/phpunit --filter test_method_name
```

### Development
```bash
# Start development server
php artisan serve

# Compile assets
npm run dev          # Development build
npm run watch        # Watch for changes
npm run production   # Production build

# Clear caches
php artisan optimize:clear  # All caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

### Store Module Commands
The application has a separate store/seller module with dedicated migration commands:

```bash
# Run store migrations only (tracked in separate store_migrations table)
php artisan store:migrate

# Reset and re-run store migrations
php artisan store:migrate:fresh
php artisan store:migrate:fresh --seed

# Run store seeders only
php artisan store:seed
```

### API Documentation
API documentation is generated using Scribe:
- Located in `.scribe/` directory
- See `.scribe/auth.md` and `.scribe/endpoints/` for details

## High-Level Architecture

### Multi-Guard Authentication System
The application uses **multiple authentication guards** for different user types:

| Guard | Driver | Provider | Used For |
|-------|--------|----------|----------|
| `api` | JWT | users | Customer API endpoints |
| `web` | Session | users | Customer web panel |
| `store` | Session | sellers | Seller/Store web panel |
| `store-api` | JWT | sellers | Seller/Store API endpoints |
| `admin-api` | JWT | admins | Admin API endpoints |
| `admin` | Session | admins | Admin web panel |
| `deliveryman-api` | JWT | deliverymans | Deliveryman API endpoints |
| `deliveryman` | Session | deliverymans | Deliveryman web panel |

**Key Point**: Each user role has **both** a session-based guard (for Blade panels) and a JWT guard (for API/mobile access).

### Routing Structure

#### 1. **API Routes** (`routes/api.php`)
- **Base prefix**: `/api`
- **Public routes**: `/auth/...` - JWT authentication
- **Customer routes**: `/user/...` - Shopping, checkout, orders
- **Seller routes**: `/seller/...` - Guard: `auth:seller-api`
- **Admin routes**: `/admin/...` - Guard: `auth:admin-api`
- **Deliveryman routes**: `/deliveryman/...` - Guard: `auth:deliveryman-api`

#### 2. **Store API Routes** (`routes/store_api.php`)
- **Dedicated seller/store API** (separate from main API)
- **Base prefix**: `/api/seller`
- **Guard**: `auth:store-api` (JWT)
- Endpoints: dashboard, profile, visitors, settings, bank info

#### 3. **Web Routes** (`routes/web.php`)
- **Blade-based panels** for all user types
- **Prefixes**: `/admin`, `/seller`, `/deliveryman`, `/user`
- **Session authentication** with respective guards
- Uses middleware stack: `['demo', 'XSS', 'maintenance']`

#### 4. **Store Web Routes** (`routes/store_web.php`)
- **Dedicated seller/store Blade panel**
- **Prefix**: `/store`
- **Guard**: `auth:store` (session)
- Mirrors store API functionality for web interface

### Directory Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── API/           # API controllers (JSON responses)
│   │   ├── WEB/           # Web controllers (Blade views)
│   │   ├── Admin/         # Legacy admin controllers
│   │   ├── Seller/        # Legacy seller controllers
│   │   ├── User/          # Customer controllers
│   │   └── Deliveryman/   # Delivery personnel controllers
│   └── Middleware/
│       ├── CheckSeller.php       # Custom: Verify seller role
│       ├── DeliveryManApi.php    # Custom: Verify deliveryman role
│       ├── XSSProtection.php     # Custom: XSS sanitization
│       ├── DemoHandler.php       # Custom: Demo mode restrictions
│       └── MaintainaceMode.php   # Custom: Maintenance mode check
├── Models/              # Eloquent models (100+ models)
├── Services/            # Business logic services
│   ├── ZohoService.php           # Zoho Books integration
│   ├── ShiprocketService.php     # Shiprocket shipping API
│   ├── CoinLedgerService.php     # Coin transaction handling
│   └── Store/                    # Store module services
└── Helpers/
    ├── helpers.php              # Global helper functions
    └── languageDynamic.php      # Dynamic language helpers
```

### Key Services & Integrations

- **Payment Gateways**: Razorpay (primary), PayPal, Stripe, Mollie, Flutterwave, MyFatoorah, Instamojo, Paymongo, SSLCommerz
- **Shipping**: Shiprocket API integration (`ShiprocketService`)
- **Accounting**: Zoho Books integration (`ZohoService`, `ZohoBooksService`)
- **SMS**: Twilio SMS for notifications
- **Cart**: Shopping cart using `bumbummen99/shoppingcart` package
- **Image Processing**: Intervention Image library
- **API Documentation**: Scribe (`knuckleswtf/scribe`)
- **Real-time**: Pusher for broadcasting
- **Excel**: Import/export via `maatwebsite/excel`

### Database Patterns

- **Store migrations** are tracked separately in `store_migrations` table (not `migrations`)
- Migrations follow naming: `YYYY_MM_DD_HHMMSS_create_table_name.php`
- Recent additions: coin campaigns, coupon campaigns, newsletter subscriptions, store banners, sponsors, shipments

## Key Conventions

### File Naming & Structure

1. **Controller Namespaces**:
   - API controllers: `App\Http\Controllers\API\[Role]\`
   - Web controllers: `App\Http\Controllers\WEB\[Role]\`
   - Example: `API\Seller\DashboardController` vs `WEB\Seller\DashboardController`

2. **Route Files**:
   - Main API: `routes/api.php`
   - Store API: `routes/store_api.php` (dedicated seller endpoints)
   - Main Web: `routes/web.php`
   - Store Web: `routes/store_web.php` (dedicated seller panel)

3. **Model Location**: All models in `app/Models/` (100+ models, flat structure)

### Code Patterns

1. **Helper Functions**:
   - Loaded via `composer.json` autoload.files:
     ```json
     "files": [
       "app/Helpers/languageDynamic.php",
       "app/helpers.php"
     ]
     ```
   - Common helpers:
     - `handleImageUpload($request, $key, $folder)` - Upload images to `public/uploads/{folder}`
     - `routeIs($patterns)` - Check if current route matches pattern(s)
     - `activeClass($patterns, $class = 'active')` - Return CSS class for active routes

2. **Image Uploads**:
   - **Always** use `handleImageUpload()` helper for consistency
   - Files stored in `public/uploads/{folder}/`
   - Format: `{timestamp}_{original_name}`
   - Returns path like: `uploads/products/1234567890_image.jpg`

3. **API Response Structure**:
   - Use consistent JSON structure across all API endpoints
   - Controllers in `API/` namespace return JSON
   - Controllers in `WEB/` namespace return views

4. **Authentication Middleware**:
   - Specify guard explicitly: `middleware('auth:store-api')`
   - Custom middleware for roles: `checkseller`, `deliverymanapi`
   - Always use guard-specific redirect logic in middleware

5. **XSS Protection**:
   - Custom `XSSProtection` middleware applied globally
   - Sanitizes all incoming request data
   - Applied via middleware group: `['demo', 'XSS']`

6. **Multi-Currency Support**:
   - Model: `MultiCurrency`
   - Migration: `2024_01_25_000001_create_multi_currencies_table.php`

### Configuration Files

- **JWT Config**: `config/jwt.php` - JWT authentication settings
- **Cart Config**: `config/cart.php` - Shopping cart configuration
- **Payment Configs**:
  - Razorpay: `.env` keys `RAZORPAY_KEY`, `RAZORPAY_SECRET`
  - Multiple gateway models in `app/Models/` (e.g., `RazorpayPayment`, `StripePayment`)
- **Custom Config**: `config/kutoot.php` - Application-specific settings

### Testing Conventions

- Test files use suffix: `Test.php`
- Feature tests: `tests/Feature/`
- Unit tests: `tests/Unit/`
- PHPUnit config: `phpunit.xml` with coverage for `app/` directory

### Code Style

- **EditorConfig** settings:
  - Indent: 4 spaces (PHP/JS)
  - Indent: 2 spaces (YAML)
  - Charset: UTF-8
  - End of line: LF
  - Trim trailing whitespace (except markdown)
  - Insert final newline

### Common Gotchas

1. **Guard Confusion**: Always specify the correct guard for the user type you're working with
2. **Route Files**: Store-related features use separate route files (`store_api.php`, `store_web.php`)
3. **Migration Tables**: Regular migrations use `migrations` table, store migrations use `store_migrations`
4. **Image Paths**: Use public path helpers, images stored in `public/uploads/`
5. **Multiple Auth Systems**: Session-based (Blade) and JWT-based (API) run in parallel

### Postman Collections

Three Postman collections are included in the project root:
- `postman_collection.json` - Main API collection
- `postman_seller_onboarding.json` - Seller onboarding flows
- `postman_seller_panel.json` - Seller panel endpoints
