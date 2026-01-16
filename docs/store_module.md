## Store Module (Blade Panel + Seller APIs)

This module adds:

- **Blade Store panel**: `/store/...` (session guard `store`)
- **Seller APIs** (per PDF spec): `/api/seller/...` (JWT guard `store-api`)

### Custom Artisan commands (store-only)

- Run store migrations only:
  - `php artisan store:migrate`
- Reset + re-run store migrations only:
  - `php artisan store:migrate:fresh`
  - `php artisan store:migrate:fresh --seed`
- Run store seeders only:
  - `php artisan store:seed`

Note: Store migrations are tracked in a separate table: `store_migrations`.

### Store API endpoints (base `/api/seller`)

- **Auth**
  - `POST /api/seller/auth/login`
  - `POST /api/seller/auth/logout` (auth required)
  - `GET /api/seller/me` (auth required)
- **Dashboard**
  - `GET /api/seller/dashboard/summary?from=YYYY-MM-DD&to=YYYY-MM-DD`
  - `GET /api/seller/dashboard/revenue-trend?days=7`
  - `GET /api/seller/dashboard/visitors-trend?days=7`
- **Store**
  - `GET /api/seller/store/profile`
  - `PUT /api/seller/store/profile`
  - `POST /api/seller/store/images/upload` (multipart, `images[]` or `image[]`)
  - `DELETE /api/seller/store/images/delete` (`{ "imageUrl": "..." }`)
- **Visitors**
  - `GET /api/seller/visitors?from=&to=&search=&page=&limit=`
- **Settings**
  - `PUT /api/seller/settings/change-password`
  - `GET /api/seller/settings/bank`
  - `PUT /api/seller/settings/bank`
  - `GET /api/seller/settings/notifications`
  - `PUT /api/seller/settings/notifications`
- **Master admin (read-only)**
  - `GET /api/seller/master-admin/settings`

### Store Blade panel routes (base `/store`)

- `GET /store/login`
- `POST /store/login`
- `GET /store/logout`
- `GET /store/dashboard`
- `GET/POST /store/store-profile`
- `GET /store/visitors`
- `GET/POST /store/settings/change-password`
- `GET/POST /store/settings/bank`
- `GET/POST /store/settings/notifications`


