<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        // $this->registerPolicies();
        $this->app['auth']->viaRequest('api', function ($request) {
            return app('auth')->setRequest($request)->user();
        });

        // Gate Definition
        Gate::define('host', function ($user) {
            return $user->role === 'host';
        });

        // Gate::define('host-admin', function ($user) {
        //     return $user->role === 'host' || $user->role === 'admin';
        // });

        Gate::define('admin', function ($user) {
            return $user->role === 'admin';
        });
        //
    }
}
