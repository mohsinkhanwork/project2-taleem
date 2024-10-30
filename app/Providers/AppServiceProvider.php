<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Contracts\Factory as SocialiteFactory;
use SocialiteProviders\Keycloak\Provider as KeycloakProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->make(SocialiteFactory::class)->extend(
            'keycloak',
            function ($app) {
                $config = $app['config']['services.keycloak'];

                return new KeycloakProvider(
                    $app['request'],
                    $config['client_id'],
                    $config['client_secret'],
                    $config['redirect'],
                    [
                        'base_url' => $config['base_url'],
                        'authorize_url' => $config['base_url'] . '/protocol/openid-connect/auth',
                        'token_url' => $config['base_url'] . '/protocol/openid-connect/token',
                        'userinfo_url' => $config['base_url'] . '/protocol/openid-connect/userinfo',
                    ]
                );
            }
        );
    }
}

