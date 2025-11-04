<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\PusherCredentail;
class PusherConfig extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->booted(function () {
            $shouldSetCustomConfig = false;
            if($shouldSetCustomConfig) {
                
                $pusher = PusherCredentail::first();
                $pusherConfig = [
                    'driver' => 'pusher',
                    'key' => $pusher->app_key,
                    'secret' => $pusher->app_secret,
                    'app_id' => $pusher->app_id,
                    'options' => [
                        'cluster' => $pusher->app_cluster,
                        'useTLS' => true,
                        'encrypted' => true
                    ],
                ];
                
                // config(['broadcasting.connections.pusher' => $pusherConfig]);
            }
        });

    }
}
