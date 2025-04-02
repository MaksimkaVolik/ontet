<?php
namespace App\Models;

use Core\Database;

class Promocode {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function applyPromocode($code, $userId) {
        return $this->db->query(
            "SELECT p.*, o.title as offer_title 
             FROM promocodes p
             JOIN partner_offers o ON p.offer_id = o.id
             WHERE p.code = :code 
             AND (p.expires_at IS NULL OR p.expires_at > NOW())
             AND p.is_used = FALSE
             LIMIT 1",
            ['code' => $code]
        )->fetch();
    }

    public function markAsUsed($promocodeId, $userId) {
        $this->db->query(
            "UPDATE promocodes SET 
              is_used = TRUE,
              used_by = :user_id,
              used_at = NOW()
             WHERE id = :id",
            ['id' => $promocodeId, 'user_id' => $userId]
        );
    }
}