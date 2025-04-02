<?php
declare(strict_types=1);

namespace App\Services\Auth;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;

class OAuthService {
    private array $providers = [];

    public function __construct(array $config) {
        foreach ($config as $provider => $settings) {
            $class = "League\\OAuth2\\Client\\Provider\\{$provider}";
            $this->providers[$provider] = new $class($settings);
        }
    }

    public function getAuthUrl(string $provider): string {
        return $this->getProvider($provider)->getAuthorizationUrl();
    }

    public function getUser(string $provider, string $code): array {
        $provider = $this->getProvider($provider);
        $token = $provider->getAccessToken('authorization_code', ['code' => $code]);
        
        return $provider->getResourceOwner($token)->toArray();
    }

    private function getProvider(string $name): AbstractProvider {
        if (!isset($this->providers[$name])) {
            throw new \InvalidArgumentException("Provider {$name} not configured");
        }
        
        return $this->providers[$name];
    }
}