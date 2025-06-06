<?php

namespace Pterodactyl\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Laravel\Socialite\Facades\Socialite;
use Pterodactyl\Models\User;
use Pterodactyl\Facades\Activity;
use Pterodactyl\Events\Auth\DirectLogin;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

class OpenIDController extends AbstractLoginController
{
    /**
     * Redirect the user to the OpenID Connect provider authentication page.
     */
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('openid')->redirect();
    }

    /**
     * Obtain the user information from OpenID Connect provider.
     */
    public function callback(Request $request): JsonResponse|RedirectResponse
    {
        try {
            $socialiteUser = Socialite::driver('openid')->user();
            
            // Find or create user
            $user = $this->findOrCreateUser($socialiteUser);
            
            // Log the user in
            $this->auth->guard()->login($user, true);
            
            // Fire login event
            Event::dispatch(new DirectLogin($user, true));
            
            // Log successful authentication
            Activity::event('auth:openid.success')
                ->withRequestMetadata()
                ->subject($user)
                ->log();

            // Check if this is an API request or web request
            if ($request->wantsJson()) {
                return new JsonResponse([
                    'data' => [
                        'complete' => true,
                        'intended' => $this->redirectPath(),
                        'user' => $user->toVueObject(),
                    ],
                ]);
            }

            return redirect()->intended($this->redirectPath());
            
        } catch (\Exception $e) {
            Activity::event('auth:openid.fail')
                ->withRequestMetadata()
                ->property('error', $e->getMessage())
                ->log();
                
            if ($request->wantsJson()) {
                return new JsonResponse([
                    'error' => 'OpenID Connect authentication failed',
                    'message' => $e->getMessage()
                ], 400);
            }
            
            return redirect()->route('auth.login')->withErrors([
                'openid' => 'OpenID Connect authentication failed: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Find an existing user or create a new one based on OpenID Connect data.
     */
    protected function findOrCreateUser($socialiteUser): User
    {
        // Try to find user by external_id (sub claim)
        $user = User::where('external_id', $socialiteUser->getId())->first();
        
        if ($user) {
            return $user;
        }
        
        // Try to find user by email
        $user = User::where('email', $socialiteUser->getEmail())->first();
        
        if ($user) {
            // Link the external_id to existing user
            $user->update(['external_id' => $socialiteUser->getId()]);
            return $user;
        }
        
        // Check if registration is disabled
        if (config('services.openid.disable_registration', false)) {
            throw new \Exception('User registration via OpenID Connect is disabled. Please contact an administrator.');
        }
        
        // Create new user
        $nameParts = $this->parseFullName($socialiteUser->getName());
        
        return User::create([
            'external_id' => $socialiteUser->getId(),
            'uuid' => Uuid::uuid4()->toString(),
            'username' => $this->generateUniqueUsername($socialiteUser),
            'email' => $socialiteUser->getEmail(),
            'name_first' => $nameParts['first'],
            'name_last' => $nameParts['last'],
            'password' => bcrypt(Str::random(32)), // Random password since they use OpenID
            'language' => config('app.locale', 'en'),
        ]);
    }

    /**
     * Parse full name into first and last name components.
     */
    protected function parseFullName(?string $fullName): array
    {
        if (empty($fullName)) {
            return ['first' => 'User', 'last' => 'OpenID'];
        }
        
        $parts = explode(' ', trim($fullName), 2);
        
        return [
            'first' => $parts[0] ?? 'User',
            'last' => $parts[1] ?? 'OpenID'
        ];
    }

    /**
     * Generate a unique username based on OpenID Connect data.
     */
    protected function generateUniqueUsername($socialiteUser): string
    {
        // Try nickname first
        $preferred = $socialiteUser->getNickname() ?? 
                    explode('@', $socialiteUser->getEmail())[0] ?? 
                    'openid_user';
        
        $username = Str::slug($preferred, '_');
        $originalUsername = $username;
        $counter = 1;
        
        // Ensure username is unique
        while (User::where('username', $username)->exists()) {
            $username = $originalUsername . '_' . $counter;
            $counter++;
        }
        
        return $username;
    }
}
