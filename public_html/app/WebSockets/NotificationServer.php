<?php
namespace App\WebSockets;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use SplObjectStorage;

class NotificationServer implements MessageComponentInterface {
    protected $clients;
    private $userConnections = [];

    public function __construct() {
        $this->clients = new SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        parse_str($conn->httpRequest->getUri()->getQuery(), $query);
        
        if (isset($query['userId'])) {
            $this->userConnections[$query['userId']] = $conn;
        }
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        // Обработка входящих сообщений (не требуется для уведомлений)
    }

    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
        $userId = array_search($conn, $this->userConnections, true);
        
        if ($userId !== false) {
            unset($this->userConnections[$userId]);
        }
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "Error: {$e->getMessage()}\n";
        $conn->close();
    }

    public function sendNotification(int $userId, array $data): void {
        if (isset($this->userConnections[$userId])) {
            $this->userConnections[$userId]->send(json_encode($data));
        }
    }
}