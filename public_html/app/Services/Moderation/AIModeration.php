<?php
namespace App\Services\Moderation;

use Core\Http\Client;
use Core\Config;

class AIModeration {
    private Client $http;
    private string $apiUrl;
    private float $threshold;

    public function __construct() {
        $config = Config::get('ai_moderation');
        $this->http = new Client();
        $this->apiUrl = $config['url'];
        $this->threshold = $config['threshold'];
    }

    public function checkText(string $text): array {
        $response = $this->http->post($this->apiUrl, [
            'text' => $text,
            'lang' => 'ru'
        ]);

        $result = $response->json();
        
        return [
            'is_spam' => $result['spam_score'] >= $this->threshold,
            'is_toxic' => $result['toxicity_score'] >= $this->threshold,
            'scores' => $result
        ];
    }

    public function shouldQuarantine(array $scores): bool {
        return $scores['is_spam'] || $scores['is_toxic'];
    }
}