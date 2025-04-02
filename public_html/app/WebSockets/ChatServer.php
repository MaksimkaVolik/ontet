<?php
namespace App\WebSockets;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use SplObjectStorage;

class ChatServer implements MessageComponentInterface {
    protected $clients;
    private $userConnections = [];

    public function __construct() {
        $this->clients = new SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn) {
        parse_str($conn->httpRequest->getUri()->getQuery(), $query);
        $userId = (int)($query['user_id'] ?? 0);
        
        if ($userId > 0) {
            $this->userConnections[$userId] = $conn;
            $conn->userId = $userId;
        }
        
        $this->clients->attach($conn);
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $data = json_decode($msg, true);
        
        if (isset($data['to'], $data['message'])) {
            $this->sendPrivateMessage(
                $from->userId,
                (int)$data['to'],
                htmlspecialchars($data['message'])
            );
        }
    }

    private function sendPrivateMessage(int $from, int $to, string $message) {
        if (isset($this->userConnections[$to])) {
            $this->userConnections[$to]->send(json_encode([
                'from' => $from,
                'message' => $message,
                'time' => date('H:i')
            ]));
        }
        
        // Сохраняем в БД
        $this->saveMessage($from, $to, $message);
    }

    private function saveMessage(int $from, int $to, string $message) {
        $db = new \Core\Database();
        $db->query(
            "INSERT INTO private_messages 
             (sender_id, receiver_id, message, created_at)
             VALUES (:from, :to, :message, NOW())",
            ['from' => $from, 'to' => $to, 'message' => $message]
        );
    }

    public function onClose(ConnectionInterface $conn) {
        if (isset($conn->userId)) {
            unset($this->userConnections[$conn->userId]);
        }
        $this->clients->detach($conn);
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "Error: {$e->getMessage()}\n";
        $conn->close();
    }
}