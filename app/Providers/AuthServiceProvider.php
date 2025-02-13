<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
         'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::routes();
        Passport::personalAccessClientId(
            config('passport.personal_access_client.id', 1)
        );
    
        Passport::personalAccessClientSecret(
            config('passport.personal_access_client.secret', '0r1TIUMgz42XzhBk1e5BFPGXBFmOz0yoMwjiTgp7')
        );
    }
}
