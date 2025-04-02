<?php
namespace App\Models;

use Core\Database;

class User {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function register($data) {
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        $this->db->query(
            "INSERT INTO users (username, email, password, created_at) 
            VALUES (:username, :email, :password, NOW())",
            [
                'username' => $data['username'],
                'email' => $data['email'],
                'password' => $hashedPassword
            ]
        );
        return $this->db->lastInsertId();
    }

    public function login($email, $password) {
        $user = $this->db->query(
            "SELECT * FROM users WHERE email = :email",
            ['email' => $email]
        )->fetch();

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }

    // Другие методы работы с пользователями...
}