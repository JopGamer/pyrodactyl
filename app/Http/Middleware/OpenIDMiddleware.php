<?php

namespace Pterodactyl\Http\Middleware;

use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class OpenIDMiddleware
{
    /**
     * Handle an incoming request for OpenID authentication.
     * This middleware can be used to disable OpenID authentication entirely
     * or restrict it based on configuration.
     */
    public function handle(Request $request, \Closure $next): mixed
    {
        // Check if OpenID is enabled
        if (!config('services.openid.client_id')) {
            throw new AccessDeniedHttpException('OpenID Connect authentication is not configured.');
        }

        // Optional: Check if OpenID registration is allowed
        if (config('services.openid.disable_registration', false) && $request->routeIs('auth.openid.callback')) {
            // This would require additional logic in the controller to handle existing users only
        }

        return $next($request);
    }
}
