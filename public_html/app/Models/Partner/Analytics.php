<?php
namespace App\Models\Partner;

use Core\Database;
use Core\Exceptions\ValidationException;

class Analytics {
    private Database $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function trackClick(
        int $offerId,
        ?int $userId,
        ?int $partnerId,
        string $ip,
        string $userAgent,
        ?string $referrer
    ): int {
        return $this->db->query(
            "INSERT INTO offer_clicks 
             (offer_id, user_id, partner_id, ip, user_agent, referrer)
             VALUES (:offer_id, :user_id, :partner_id, :ip, :user_agent, :referrer)",
            [
                'offer_id' => $offerId,
                'user_id' => $userId,
                'partner_id' => $partnerId,
                'ip' => $ip,
                'user_agent' => $userAgent,
                'referrer' => $referrer
            ]
        )->lastInsertId();
    }

    public function registerConversion(
        int $clickId,
        float $amount,
        string $status = 'pending'
    ): int {
        $this->db->query(
            "INSERT INTO offer_conversions 
             (click_id, amount, status)
             VALUES (:click_id, :amount, :status)",
            [
                'click_id' => $clickId,
                'amount' => $amount,
                'status' => $status
            ]
        );
        
        return $this->db->lastInsertId();
    }

    public function getPartnerStats(int $partnerId, \DateTime $startDate, \DateTime $endDate): array {
        return $this->db->query(
            "SELECT 
                o.title,
                COUNT(c.id) as clicks,
                COUNT(cv.id) as conversions,
                SUM(IF(cv.status='approved', cv.amount, 0)) as revenue
             FROM offer_clicks c
             JOIN partner_offers o ON c.offer_id = o.id
             LEFT JOIN offer_conversions cv ON cv.click_id = c.id
             WHERE c.partner_id = :partner_id
             AND c.created_at BETWEEN :start_date AND :end_date
             GROUP BY o.id",
            [
                'partner_id' => $partnerId,
                'start_date' => $startDate->format('Y-m-d H:i:s'),
                'end_date' => $endDate->format('Y-m-d H:i:s')
            ]
        )->fetchAll();
    }
}