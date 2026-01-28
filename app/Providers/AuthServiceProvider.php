<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    // public function boot()
    // {
    //     $this->registerPolicies();

    //     //
    // }


    public function boot()
    {
        Gate::define('ecommerce-manager', function ($admin) {
            $admin = Auth::guard('admin')->user();
        return $admin && $admin->role_id == 1;
        });

        Gate::define('marketing-manager', function ($admin) {
            $admin = Auth::guard('admin')->user();
            return $admin->role_id == 2;
        });
    }
}
