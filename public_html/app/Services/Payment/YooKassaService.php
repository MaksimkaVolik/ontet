<?php
namespace App\Services\Payment;

use YooKassa\Client;
use Core\Config;

class YooKassaService {
    private Client $client;

    public function __construct() {
        $config = Config::get('payment.yookassa');
        $this->client = new Client();
        $this->client->setAuth($config['shop_id'], $config['secret_key']);
    }

    public function createPayment(
        float $amount,
        string $description,
        array $options = []
    ): string {
        $payment = $this->client->createPayment([
            'amount' => [
                'value' => number_format($amount, 2, '.', ''),
                'currency' => 'RUB'
            ],
            'confirmation' => [
                'type' => 'redirect',
                'return_url' => $options['return_url'] ?? Config::get('app.url')
            ],
            'capture' => true,
            'description' => $description,
            'metadata' => $options['metadata'] ?? []
        ]);

        return $payment->getConfirmation()->getConfirmationUrl();
    }

    public function handleWebhook(array $data): bool {
        // Валидация уведомления
        $payment = $this->client->getPaymentInfo($data['object']['id']);
        
        if ($payment->getStatus() === 'succeeded') {
            // Обновление статуса заказа в БД
            return true;
        }
        
        return false;
    }
}