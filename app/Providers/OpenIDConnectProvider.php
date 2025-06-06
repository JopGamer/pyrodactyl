<?php

namespace Pterodactyl\Providers;

use GuzzleHttp\Client;
use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;
use Laravel\Socialite\Two\User;

class OpenIDConnectProvider extends AbstractProvider implements ProviderInterface
{
    protected $scopeSeparator = ' ';

    protected $scopes = ['openid', 'profile', 'email'];

    protected $discovery = null;

    protected function getAuthUrl($state)
    {
        $discovery = $this->getDiscoveryDocument();
        return $this->buildAuthUrlFromBase($discovery['authorization_endpoint'], $state);
    }

    protected function getTokenUrl()
    {
        $discovery = $this->getDiscoveryDocument();
        return $discovery['token_endpoint'];
    }

    protected function getUserByToken($token)
    {
        $discovery = $this->getDiscoveryDocument();
        $userinfoEndpoint = $discovery['userinfo_endpoint'] ?? $this->getConfig('issuer') . '/userinfo';
        
        $response = $this->getHttpClient()->get($userinfoEndpoint, [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);

        return json_decode($response->getBody(), true);
    }

    protected function mapUserToObject(array $user)
    {
        return (new User())->setRaw($user)->map([
            'id' => $user['sub'],
            'nickname' => $user['preferred_username'] ?? $user['username'] ?? null,
            'name' => $user['name'] ?? ($user['given_name'] ?? '') . ' ' . ($user['family_name'] ?? ''),
            'email' => $user['email'] ?? null,
            'avatar' => $user['picture'] ?? null,
        ]);
    }

    protected function getCodeFields($state = null)
    {
        $fields = [
            'client_id' => $this->clientId,
            'redirect_uri' => "$this->redirectUrl",
            'scope' => $this->formatScopes($this->getScopes(), $this->scopeSeparator),
            'response_type' => 'code',
        ];

        if ($this->usesState()) {
            $fields['state'] = $state;
        }

        return array_merge($fields, $this->parameters);
    }

    protected function getConfig($key)
    {
        return config('services.openid.' . $key);
    }

    protected function getDiscoveryDocument()
    {
        if ($this->discovery !== null) {
            return $this->discovery;
        }

        $discoveryUrl = $this->getConfig('discovery_url') ?? $this->getConfig('issuer') . '/.well-known/openid-configuration';
        
        try {
            $response = $this->getHttpClient()->get($discoveryUrl);
            $this->discovery = json_decode($response->getBody(), true);
            
            // Validate that we have the required endpoints
            if (!isset($this->discovery['authorization_endpoint'])) {
                throw new \Exception('Discovery document missing authorization_endpoint');
            }
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('OpenID Connect discovery failed: ' . $e->getMessage(), [
                'discovery_url' => $discoveryUrl,
                'issuer' => $this->getConfig('issuer'),
                'client_id' => $this->getConfig('client_id'),
            ]);
            
            // Fallback to standard OpenID Connect endpoints
            $issuer = $this->getConfig('issuer');
            $this->discovery = [
                'authorization_endpoint' => $issuer . '/protocol/openid-connect/auth',
                'token_endpoint' => $issuer . '/protocol/openid-connect/token',
                'userinfo_endpoint' => $issuer . '/protocol/openid-connect/userinfo',
            ];
        }

        return $this->discovery;
    }
}
