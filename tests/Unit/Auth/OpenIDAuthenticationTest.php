<?php

namespace Tests\Unit\Auth;

use Tests\TestCase;
use Pterodactyl\Models\User;
use Pterodactyl\Http\Controllers\Auth\OpenIDController;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;

class OpenIDAuthenticationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Mock OpenID configuration
        config([
            'services.openid' => [
                'client_id' => 'test-client-id',
                'client_secret' => 'test-client-secret',
                'issuer' => 'https://test-provider.com',
                'redirect' => 'https://test-app.com/auth/openid/callback',
                'disable_registration' => false,
            ]
        ]);
    }

    public function test_openid_redirect_works()
    {
        $response = $this->get('/auth/openid');
        
        // Should redirect to external provider
        $this->assertTrue($response->isRedirection());
    }

    public function test_user_creation_from_openid_data()
    {
        $controller = new OpenIDController();
        
        // Mock Socialite user
        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getId')->andReturn('openid-123');
        $socialiteUser->shouldReceive('getEmail')->andReturn('test@example.com');
        $socialiteUser->shouldReceive('getName')->andReturn('John Doe');
        $socialiteUser->shouldReceive('getNickname')->andReturn('johndoe');

        // Use reflection to test the protected method
        $reflection = new \ReflectionClass($controller);
        $method = $reflection->getMethod('findOrCreateUser');
        $method->setAccessible(true);

        $user = $method->invoke($controller, $socialiteUser);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('openid-123', $user->external_id);
        $this->assertEquals('test@example.com', $user->email);
        $this->assertEquals('John', $user->name_first);
        $this->assertEquals('Doe', $user->name_last);
    }

    public function test_existing_user_linking_by_email()
    {
        // Create existing user
        $existingUser = User::factory()->create([
            'email' => 'test@example.com',
            'external_id' => null,
        ]);

        $controller = new OpenIDController();
        
        // Mock Socialite user with same email
        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getId')->andReturn('openid-123');
        $socialiteUser->shouldReceive('getEmail')->andReturn('test@example.com');
        $socialiteUser->shouldReceive('getName')->andReturn('John Doe');
        $socialiteUser->shouldReceive('getNickname')->andReturn('johndoe');

        $reflection = new \ReflectionClass($controller);
        $method = $reflection->getMethod('findOrCreateUser');
        $method->setAccessible(true);

        $user = $method->invoke($controller, $socialiteUser);

        $this->assertEquals($existingUser->id, $user->id);
        $this->assertEquals('openid-123', $user->external_id);
    }

    public function test_registration_disabled_prevents_new_users()
    {
        config(['services.openid.disable_registration' => true]);

        $controller = new OpenIDController();
        
        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getId')->andReturn('openid-123');
        $socialiteUser->shouldReceive('getEmail')->andReturn('newuser@example.com');
        $socialiteUser->shouldReceive('getName')->andReturn('New User');

        $reflection = new \ReflectionClass($controller);
        $method = $reflection->getMethod('findOrCreateUser');
        $method->setAccessible(true);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('User registration via OpenID Connect is disabled');

        $method->invoke($controller, $socialiteUser);
    }
}
