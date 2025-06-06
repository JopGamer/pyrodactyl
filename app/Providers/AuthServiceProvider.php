<?php

namespace Pterodactyl\Providers;

use Laravel\Sanctum\Sanctum;
use Laravel\Socialite\Facades\Socialite;
use Pterodactyl\Models\ApiKey;
use Pterodactyl\Models\Server;
use Pterodactyl\Policies\ServerPolicy;
use Pterodactyl\Providers\OpenIDConnectProvider;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     */
    protected $policies = [
        Server::class => ServerPolicy::class,
    ];

    public function boot(): void
    {
        Sanctum::usePersonalAccessTokenModel(ApiKey::class);
        
        // Register OpenID Connect provider
        Socialite::extend('openid', function ($app) {
            $config = $app['config']['services.openid'];
            return new OpenIDConnectProvider(
                $app['request'],
                $config['client_id'],
                $config['client_secret'],
                $config['redirect']
            );
        });
    }
}
