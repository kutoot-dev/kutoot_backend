# Kutoot Backend - Agent Instructions

This document provides comprehensive guidance for working with the Kutoot Backend Laravel application to speed up development and maintain consistency.

---

## Project Overview

**Kutoot** is a Laravel 8-based API platform for a rewards and redemption e-commerce system. The platform allows:

- **Customers** to purchase coin campaigns, generate coupons, redeem coins at stores, and win prizes
- **Sellers/Stores** to manage products, orders, and participate in the redemption marketplace
- **Admins** to manage the entire platform, campaigns, users, and configurations
- **Delivery Personnel** to manage order deliveries

### Core Business Model

1. **Coin Campaigns** - Users purchase campaigns with coins that can be used for coupons or store redemptions
2. **Coupon System** - Generate unique coupon codes linked to campaigns with validation
3. **Store Redemption** - Users can redeem coins at participating stores for vouchers/products
4. **Prize Draws** - Campaigns may include prize draws for winners
5. **E-commerce** - Traditional product catalog with cart, checkout, and order management

---

## Technology Stack

### Core Framework
- **Laravel**: 8.x
- **PHP**: 7.3+ or 8.0+
- **Database**: MySQL 5.7+
- **Authentication**: JWT (tymon/jwt-auth)

### Key Dependencies
| Package | Purpose |
|---------|---------|
| `tymon/jwt-auth` | JWT authentication for API |
| `razorpay/razorpay` | Payment gateway integration |
| `bumbummen99/shoppingcart` | Shopping cart functionality |
| `maatwebsite/excel` | Excel import/export |
| `intervention/image` | Image processing |
| `laravel/sanctum` | API token authentication |
| `twilio/sdk` | SMS notifications |
| `stripe/stripe-php`, `paypal/rest-api-sdk-php` | Additional payment gateways |

### Frontend
- **Admin Panel**: Blade templates with Bootstrap
- **APIs**: RESTful JSON APIs for mobile/SPA clients

---

## Project Structure

```
kutoot_backend/
├── app/
│   ├── Console/        # Artisan commands
│   ├── Enums/          # Enum classes
│   ├── Events/         # Event classes
│   ├── Exports/        # Excel export classes
│   ├── Helpers/        # Helper functions (languageDynamic.php, helpers.php)
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/          # Admin panel controllers
│   │   │   ├── API/            # API controllers (Customer, OTP, SellerApplication)
│   │   │   ├── Seller/         # Seller/vendor panel controllers
│   │   │   ├── Deliveryman/    # Delivery personnel controllers
│   │   │   └── User/           # User-facing controllers
│   │   └── Middleware/         # Custom middleware
│   ├── Imports/        # Excel import classes
│   ├── Jobs/           # Queue jobs
│   ├── Library/        # Custom libraries
│   ├── Mail/           # Mailable classes
│   ├── Models/         # Eloquent models (121 models)
│   ├── Providers/      # Service providers
│   ├── Rules/          # Validation rules
│   └── Services/       # Business logic services
├── config/             # Configuration files
├── database/
│   ├── migrations/     # Database migrations (170+ migrations)
│   └── seeders/        # Database seeders
├── routes/
│   ├── api.php         # API routes (primary)
│   ├── web.php         # Web routes (admin panel)
│   ├── store_api.php   # Store-specific API routes
│   └── store_web.php   # Store web routes
├── resources/
│   └── views/          # Blade templates
├── storage/            # File storage, logs, cache
└── public/             # Public assets
```

---

## Key Models and Relationships

### Campaign & Coins
- `CoinCampaigns` - Main campaign model with prize configurations
- `Baseplans` - Base plans linked to campaigns
- `PurchasedCoins` - User coin purchase records
- `UserCoins` - User coin balance tracking
- `UserCoupons` - User-generated coupons from campaigns
- `Winners` - Prize draw winners

### Coupon System  
- `CouponCampaign` - Coupon campaign configuration
- `CouponTicket` - Individual coupon tickets/codes
- `Coupon` - Legacy coupon model (discounts)

### E-commerce
- `Product`, `ProductGallery`, `ProductVariant`, `ProductVariantItem`
- `Category`, `SubCategory`, `ChildCategory`
- `Brand` - Can be created by sellers (requires approval)
- `Order`, `OrderProduct`, `OrderAddress`, `OrderAmount`
- `ShoppingCart`, `ShoppingCartVariant`
- `Wishlist`, `CompareProduct`

### User Management
- `User` - Customers with coin balances, Zoho integration
- `Vendor` - Store/seller accounts
- `Admin` - Admin users
- `DeliveryMan` - Delivery personnel

### Store/Redemption (New Feature)
- Located in `app/Models/Store/` subdirectory
- Handles coin redemption at physical/online stores

---

## API Architecture

### Authentication

**JWT Authentication** is used for API routes:
- Prefix: `/auth`
- Routes: `login`, `logout`, `refresh`, `me`
- Middleware: `auth:api` (users), `auth:admin-api` (admins), `deliverymanapi` (delivery)

### API Route Structure

| Prefix | Purpose | Auth |
|--------|---------|------|
| `/auth/*` | JWT authentication | Public |
| `/user/*` | Customer dashboard, orders, coupons, redemption | `auth:api` |
| `/seller/*` | Seller panel (products, orders, profile) | `checkseller` middleware |
| `/admin/*` | Admin panel operations | `auth:admin-api` |
| `/deliveryman/*` | Delivery personnel operations | `deliverymanapi` |
| `/otp/*` | OTP verification for phone/email | Public |
| `/v1/customer/*` | Customer redemption APIs | Mixed |

### Important API Endpoints

#### Coin Campaigns
- `GET /coin-campaigns` - List all campaigns
- `GET /coin-campaigns/details/{id}` - Campaign details
- `GET /homepage` - Homepage content
- `GET /winners` - Winners list

#### User Dashboard (requires auth)
- `GET /user/my-dashboard` - Dashboard statistics
- `GET /user/my-campaigns` - User's purchased campaigns
- `GET /user/mycoupons` - User's generated coupons
- `POST /user/coinpurchase` - Purchase coin campaign
- `GET /user/getcoupons` - Generate coupons
- `GET /user/coin-transactions` - Transaction history

#### Redemption APIs (requires auth)
- `GET /user/redeem/home` - Redemption home page
- `GET /user/stores` - List stores for redemption
- `GET /user/stores/{id}` - Store details
- `POST /user/redeem/preview` - Preview redemption calculation
- `POST /user/redeem/confirm` - Confirm redemption payment

#### Admin APIs (requires admin auth)
- `GET /admin/seller-applications` - Seller onboarding applications
- `PATCH /admin/seller-applications/{id}/approve` - Approve seller
- Campaign, user, order, product management routes

---

## Development Patterns

### Controller Organization

1. **Admin Controllers** (`app/Http/Controllers/Admin/`)
   - Follow resource controller pattern
   - Return JSON for API calls, Blade views for web
   - Use `changeStatus()` method pattern for toggling statuses

2. **API Controllers** (`app/Http/Controllers/API/`)
   - Always return JSON responses
   - Use API resources for data transformation
   - Follow RESTful conventions

3. **Seller Controllers** (`app/Http/Controllers/Seller/`)
   - Scoped to authenticated seller's data
   - Similar patterns to Admin but with seller restrictions

### Common Code Patterns

#### Status Toggle Pattern
```php
public function changeStatus($id) {
    $item = Model::findOrFail($id);
    $item->status = $item->status == 1 ? 0 : 1;
    $item->save();
    return response()->json(['message' => 'Status updated successfully']);
}
```

#### API Response Pattern
```php
return response()->json([
    'success' => true,
    'message' => 'Operation successful',
    'data' => $data
], 200);
```

#### Validation Pattern
- Use `$request->validate()` in controllers
- Custom rules in `app/Rules/`
- Form request classes for complex validation

### Database Conventions

- **Migrations**: Use descriptive names with timestamps
- **Foreign Keys**: Follow `{table}_id` convention
- **Status Fields**: Use `tinyInteger` (0 = inactive, 1 = active)
- **Soft Deletes**: Not heavily used, verify before implementing
- **Timestamps**: Laravel's `created_at`, `updated_at` are standard

### File Uploads

- Use `Intervention/Image` for image processing
- Store in `storage/app/public/` and symlink via `php artisan storage:link`
- Use helper functions from `app/helpers.php` for file operations

---

## Important Business Logic

### Coupon Generation
- **File**: Check controllers in `app/Http/Controllers/User/CheckoutController.php`
- Coupons are generated per campaign with unique codes
- Each coupon must be unique per campaign and series
- Handle duplicate entry errors with database transactions

### Coin Balance Management
- `UserCoins` model tracks user coin balances
- Debit/credit operations should use transactions
- Track all coin movements in `PurchasedCoins` for audit

### Store Redemption Flow
1. User selects store and amount to redeem
2. `POST /user/redeem/preview` calculates coins needed
3. `POST /user/redeem/confirm` processes payment and deducts coins
4. Creates transaction record

### Admin Approval Workflows
- **Seller Applications**: Verify → Approve/Reject flow
- **Products**: Sellers create → Admin approves (approval_status field)
- **Brands**: Sellers can create brands → Admin approval required

### Payment Integration
- **Razorpay**: Primary payment gateway (see `RazorpayPayment` model)
- Payment status tracking in orders table
- Handle webhooks for payment confirmation

---

## Common Tasks & Workflows

### Adding a New API Endpoint

1. **Define Route** in `routes/api.php`
2. **Create Controller Method** (or new controller)
3. **Add Validation** using `$request->validate()`
4. **Implement Business Logic** (consider using Services)
5. **Return JSON Response** with proper status codes
6. **Test** using Postman (collections in project root)

### Creating a Migration

```bash
php artisan make:migration create_table_name --create=table_name
php artisan make:migration add_column_to_table --table=table_name
```

### Creating a Model

```bash
php artisan make:model ModelName
php artisan make:model ModelName -m  # with migration
php artisan make:model ModelName -mcr  # with migration, controller, resource controller
```

### Running Migrations

```bash
php artisan migrate                    # Run pending migrations
php artisan migrate:rollback           # Rollback last batch
php artisan migrate:fresh --seed       # Drop all tables and reseed
php artisan migrate --path=/database/migrations/filename.php  # Run specific migration
```

### Clearing Caches

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear  # Clears all caches
```

---

## Testing & Validation

### Postman Collections
Located in project root:
- `postman_collection.json` - General APIs
- `kutoot_my_orders_apis.postman_collection.json` - Order APIs
- `kutoot_redemption_apis.postman_collection.json` - Redemption APIs
- `postman_seller_onboarding.json` - Seller onboarding
- `postman_seller_panel.json` - Seller panel APIs

### Development Server
```bash
php artisan serve  # http://localhost:8000
# Or use Laravel Herd (as indicated by project path)
```

### Database Inspection
- **Adminer** is included: `adminer.php` in root
- Access via: `http://kutoot_backend.test/adminer.php`

---

## Environment Configuration

### Required Environment Variables

```env
# App
APP_NAME="Kutoot Backend"
APP_URL=http://localhost

# Database
DB_CONNECTION=mysql
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password

# JWT
JWT_SECRET=xxx  # Generate with: php artisan jwt:secret

# Payment Gateways
RAZORPAY_KEY=xxx
RAZORPAY_SECRET=xxx

# SMS (Twilio)
TWILIO_SID=xxx
TWILIO_TOKEN=xxx
TWILIO_FROM=xxx

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=xxx
MAIL_PORT=xxx
MAIL_USERNAME=xxx
MAIL_PASSWORD=xxx
```

---

## Coding Standards

### Laravel Best Practices
- Follow PSR-12 coding standards
- Use Eloquent ORM for database operations
- Keep controllers thin, move business logic to Services
- Use Form Requests for complex validation
- Use Route Model Binding where appropriate
- Always validate user input

### Naming Conventions
- **Controllers**: PascalCase, suffix with `Controller`
- **Models**: PascalCase, singular
- **Migrations**: snake_case with timestamp prefix
- **Routes**: kebab-case
- **Variables**: camelCase
- **Database Tables**: snake_case, plural
- **Database Columns**: snake_case

### Security Practices
- Never expose sensitive data in API responses
- Use middleware for authorization checks
- Validate all input data
- Use parameterized queries (Eloquent does this)
- Hash passwords (User model uses bcrypt)
- Protect against XSS (custom XSS middleware exists)

---

## Debugging & Troubleshooting

### Common Issues

**JWT Token Issues**
```bash
php artisan jwt:secret
php artisan config:clear
```

**Permission Errors**
```bash
chmod -R 775 storage bootstrap/cache
```

**Database Connection Issues**
- Verify `.env` credentials
- Ensure MySQL service is running
- Check database exists

**Asset Loading Issues (Admin Panel)**
- Ensure `APP_URL` matches your local domain
- Check for HTTPS/HTTP mismatches
- Run `npm run dev` if using Mix

### Error Logs
- Located in `storage/logs/laravel.log`
- Use `tail -f storage/logs/laravel.log` to monitor in real-time
- Also check `error_log` in project root (if present)

---

## Integration Points

### Zoho Integration
- Models have `zoho_customer_id`, `zoho_invoice_id` fields
- `ZohoController` and `ZohoInvoiceController` handle syncing
- OAuth tokens stored in `zoho_tokens` table

### Shiprocket Integration
- `ShiprocketService` in `app/Services/`
- Used for order shipping management
- Check pincode availability before order

### SMS Services
- **Twilio**: Primary SMS service
- **BiztechSms**: Alternative SMS provider
- Templates in `sms_templates` table

---

## Feature Flags & Configurations

Many features are configurable via database:
- `settings` table - Global app settings
- `home_page_one_visibilities` - Control homepage sections
- `menu_visibilities` - Control menu items
- `maintainance_texts` - Maintenance mode settings
- `cookie_consents` - Cookie consent configuration
- `google_recaptchas` - reCAPTCHA settings

---

## When Making Changes

### Before Starting
1. ✅ Understand the business requirement
2. ✅ Check existing similar implementations
3. ✅ Review related models and relationships
4. ✅ Identify affected API endpoints
5. ✅ Plan database schema changes if needed

### During Development
1. ✅ Follow existing code patterns
2. ✅ Use transactions for multi-step database operations
3. ✅ Add proper validation
4. ✅ Handle errors gracefully with try-catch
5. ✅ Return consistent JSON response structures
6. ✅ Log important operations

### After Changes
1. ✅ Test API endpoints with Postman
2. ✅ Verify database changes are applied
3. ✅ Check for breaking changes in related features
4. ✅ Update API documentation if endpoints changed
5. ✅ Clear caches if config/routes modified

---

## Quick Reference Commands

```bash
# Development
php artisan serve
php artisan migrate
php artisan db:seed
php artisan storage:link

# Cache Management
php artisan optimize:clear
php artisan config:cache
php artisan route:cache

# JWT
php artisan jwt:secret

# Code Generation
php artisan make:controller Name
php artisan make:model Name -m
php artisan make:migration name
php artisan make:seeder Name
php artisan make:middleware Name

# Queue (if using)
php artisan queue:work
php artisan queue:restart

# Maintenance
php artisan down
php artisan up
```

---

## Additional Resources

- **Laravel 8 Docs**: https://laravel.com/docs/8.x
- **JWT Auth**: https://jwt-auth.readthedocs.io/
- **Razorpay Docs**: https://razorpay.com/docs/

---

## Summary

This is a complex multi-tenant e-commerce platform with a unique coin-based campaign and redemption system. When working on this codebase:

- **Always authenticate** - Most APIs require JWT tokens
- **Check existing patterns** - The codebase has established patterns, follow them
- **Use transactions** - Especially for coin/payment operations
- **Test thoroughly** - Use the Postman collections
- **Consider multi-role access** - Admin, Seller, User, Delivery all have different permissions

Focus on understanding the business context (campaigns, coupons, redemptions) as this drives most of the custom logic beyond standard e-commerce features.
