---
description: Create a new API endpoint
---

# Create a New API Endpoint

This workflow guides you through creating a new API endpoint following Kutoot backend conventions.

## Steps

### 1. Define the Route

Add route to `routes/api.php` in the appropriate section:

```php
// For user-authenticated routes
Route::group(['as' => 'user.', 'prefix' => 'user'], function () {
    Route::get('/your-endpoint', [YourController::class, 'yourMethod'])->name('your-endpoint');
});

// For admin routes
Route::group(['as' => 'admin.', 'prefix' => 'admin'], function () {
    Route::get('/your-endpoint', [YourController::class, 'yourMethod'])->name('admin.your-endpoint');
});

// For public routes
Route::get('/your-endpoint', [YourController::class, 'yourMethod'])->name('your-endpoint');
```

### 2. Create Controller (if needed)

```bash
php artisan make:controller API/YourController
```

Or for admin controllers:

```bash
php artisan make:controller Admin/YourController
```

### 3. Implement Controller Method

```php
<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class YourController extends Controller
{
    public function yourMethod(Request $request)
    {
        try {
            // Validate input
            $validated = $request->validate([
                'field' => 'required|string|max:255',
            ]);

            // Business logic here
            $data = // ... your logic

            // Return success response
            return response()->json([
                'success' => true,
                'message' => 'Operation successful',
                'data' => $data
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Operation failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
```

### 4. Add Controller Import

At the top of `routes/api.php`, add:

```php
use App\Http\Controllers\API\YourController;
```

### 5. Clear Route Cache

// turbo
```bash
php artisan route:clear
```

### 6. Test the Endpoint

#### View all routes to verify:
```bash
php artisan route:list | grep your-endpoint
```

#### Test with curl or Postman:
```bash
curl http://kutoot_backend.test/api/your-endpoint
```

### 7. Add to Postman Collection (Optional)

Add the new endpoint to the appropriate Postman collection in the project root for documentation.

## Endpoint Patterns

### GET Endpoint (List)
```php
public function index(Request $request)
{
    $items = YourModel::paginate(15);
    return response()->json(['success' => true, 'data' => $items]);
}
```

### GET Endpoint (Single Item)
```php
public function show($id)
{
    $item = YourModel::findOrFail($id);
    return response()->json(['success' => true, 'data' => $item]);
}
```

### POST Endpoint (Create)
```php
public function store(Request $request)
{
    $validated = $request->validate([...]);
    $item = YourModel::create($validated);
    return response()->json(['success' => true, 'data' => $item], 201);
}
```

### PUT/PATCH Endpoint (Update)
```php
public function update(Request $request, $id)
{
    $validated = $request->validate([...]);
    $item = YourModel::findOrFail($id);
    $item->update($validated);
    return response()->json(['success' => true, 'data' => $item]);
}
```

### DELETE Endpoint
```php
public function destroy($id)
{
    YourModel::findOrFail($id)->delete();
    return response()->json(['success' => true, 'message' => 'Deleted successfully']);
}
```

## Authentication

For authenticated endpoints, use appropriate middleware:

```php
// JWT authenticated (users)
Route::middleware(['auth:api'])->group(function () {
    Route::get('/protected', [Controller::class, 'method']);
});

// Admin authenticated
Route::middleware(['auth:admin-api'])->group(function () {
    Route::get('/admin-only', [Controller::class, 'method']);
});
```

## Best Practices

✅ Always validate input
✅ Use try-catch for error handling
✅ Return consistent JSON structure
✅ Use appropriate HTTP status codes
✅ Follow existing patterns in the codebase
✅ Add meaningful error messages
✅ Use database transactions for multi-step operations
